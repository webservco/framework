<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\AbstractLibrary;
use WebServCo\Framework\ArrayStorage;
use WebServCo\Framework\Exceptions\SessionException;
use WebServCo\Framework\Helpers\CookieLibraryHelper;
use WebServCo\Framework\Helpers\PhpHelper;
use WebServCo\Framework\Interfaces\SessionInterface;
use WebServCo\Framework\Settings;

use function ini_set;
use function session_cache_expire;
use function session_cache_limiter;
use function session_destroy;
use function session_name;
use function session_regenerate_id;
use function session_save_path;
use function session_set_cookie_params;
use function session_start;
use function session_status;
use function sprintf;
use function time;

use const PHP_SESSION_ACTIVE;
use const PHP_SESSION_NONE;

final class Session extends AbstractLibrary implements
    SessionInterface
{
    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function add(mixed $setting, mixed $data): bool
    {
        $this->checkSession();


        $_SESSION = ArrayStorage::add($_SESSION, $setting, $data);

        return true;
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function clear(mixed $setting): bool
    {
        return $this->set($setting, null);
    }

    public function destroy(): bool
    {

        $_SESSION = [];
        CookieLibraryHelper::library()->set(
            (string) session_name(),
            '',
            time() - 3600,
            $this->setting(sprintf('cookie%spath', Settings::DIVIDER), '/'),
            $this->setting(sprintf('cookie%sdomain', Settings::DIVIDER), ''),
            $this->setting(sprintf('cookie%ssecure', Settings::DIVIDER), true),
            $this->setting(sprintf('cookie%shttponly', Settings::DIVIDER), true),
            $this->setting(sprintf('cookie%ssamesite', Settings::DIVIDER), 'Lax'),
        );
        session_destroy();

        return true;
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function get(mixed $setting, mixed $defaultValue = null): mixed
    {
        $this->checkSession();

        return ArrayStorage::get($_SESSION, $setting, $defaultValue);
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function has(mixed $setting): bool
    {
        $this->checkSession();

        return ArrayStorage::has($_SESSION, $setting);
    }

    public function regenerate(): bool
    {
        return session_regenerate_id(true);
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function remove(mixed $setting): bool
    {
        $this->checkSession();


        $_SESSION = ArrayStorage::remove($_SESSION, $setting);

        return true;
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     */
    public function set(mixed $setting, mixed $value): bool
    {
        $this->checkSession();


        $_SESSION = ArrayStorage::set($_SESSION, $setting, $value);

        return true;
    }

    public function start(string $storagePath = ''): bool
    {
        if (PhpHelper::isCli()) {
            throw new SessionException('Not starting session in CLI mode.');
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new SessionException('Unable to start session: already started.');
        }

        /**
         * Set cache limiter.
         */
        session_cache_limiter('public, must-revalidate');

        /**
         * Set cache expire (minutes).
         */
        session_cache_expire($this->setting('expire', '36000') / 60);

        /**
         * Set garbage collector timeout (seconds).
         */
        ini_set('session.gc_maxlifetime', (string) $this->setting('expire', '36000'));

        /**
        * Set custom session storage path.
        */
        $this->setStoragePath($storagePath);

        /**
         * Make sure garbage collector visits us.
         */
        ini_set('session.gc_probability', '1');

        session_set_cookie_params([
            'domain' => $this->setting(sprintf('cookie%sdomain', Settings::DIVIDER), ''),
            'httponly' => $this->setting(sprintf('cookie%shttponly', Settings::DIVIDER), true),
            'lifetime' => $this->setting(sprintf('cookie%slifetime', Settings::DIVIDER), 60 * 60 * 24 * 14),
            'path' => $this->setting(sprintf('cookie%spath', Settings::DIVIDER), '/'),
            'samesite' => $this->setting(sprintf('cookie%ssamesite', Settings::DIVIDER), 'Lax'),
            'secure' => $this->setting(sprintf('cookie%ssecure', Settings::DIVIDER), true),
        ]);

        session_name('webservco');

        if (!session_start()) {
            throw new SessionException('Unable to start session.');
        }

        return true;
    }

    protected function checkSession(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            throw new SessionException('Session is not started.');
        }

        return true;
    }

    protected function setStoragePath(string $storagePath): bool
    {
        ini_set('session.save_path', (string) $storagePath);
        $actualStoragePath = session_save_path($storagePath);

        if ($actualStoragePath !== $storagePath) {
            // true unless otherwise specified
            if ($_SERVER['APP_STRICT_CUSTOM_PATH'] ?? true) {
                throw new SessionException(
                    'Unable to set custom session storage path. ' .
                    sprintf('Current path: %s.', $actualStoragePath),
                );
            }

            return false;
        }

        return true;
    }
}
