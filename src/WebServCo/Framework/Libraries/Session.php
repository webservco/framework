<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\ArrayStorage;
use WebServCo\Framework\Exceptions\SessionException;
use WebServCo\Framework\Settings;

final class Session extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\SessionInterface
{

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $data
     */
    public function add($setting, $data): bool
    {
        $this->checkSession();

        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        $_SESSION = ArrayStorage::add($_SESSION, $setting, $data);
        return true;
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     */
    public function clear($setting): bool
    {
        return $this->set($setting, null);
    }

    public function destroy(): bool
    {
        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        $_SESSION = [];
        \WebServCo\Framework\Helpers\CookieLibraryHelper::library()->set(
            (string) \session_name(),
            '',
            \time() - 3600,
            $this->setting(\sprintf('cookie%spath', Settings::DIVIDER), '/'),
            $this->setting(\sprintf('cookie%sdomain', Settings::DIVIDER), ''),
            $this->setting(\sprintf('cookie%ssecure', Settings::DIVIDER), true),
            $this->setting(\sprintf('cookie%shttponly', Settings::DIVIDER), true),
            $this->setting(\sprintf('cookie%ssamesite', Settings::DIVIDER), 'Lax'),
        );
        \session_destroy();
        return true;
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($setting, $defaultValue = null)
    {
        $this->checkSession();

        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        return ArrayStorage::get($_SESSION, $setting, $defaultValue);
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     */
    public function has($setting): bool
    {
        $this->checkSession();

        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        return ArrayStorage::has($_SESSION, $setting);
    }

    public function regenerate(): bool
    {
        return \session_regenerate_id(true);
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     */
    public function remove($setting): bool
    {
        $this->checkSession();

        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        $_SESSION = ArrayStorage::remove($_SESSION, $setting);
        return true;
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     */
    public function set($setting, $value): bool
    {
        $this->checkSession();

        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        $_SESSION = ArrayStorage::set($_SESSION, $setting, $value);
        return true;
    }

    public function start(string $storagePath = ''): bool
    {
        if (\WebServCo\Framework\Helpers\PhpHelper::isCli()) {
            throw new SessionException('Not starting session in CLI mode.');
        }

        if (\PHP_SESSION_ACTIVE === \session_status()) {
            throw new SessionException('Unable to start session: already started.');
        }

        /**
         * Set cache limiter.
         */
        \session_cache_limiter('public, must-revalidate');

        /**
         * Set cache expire (minutes).
         */
        \session_cache_expire($this->setting('expire', '36000') / 60);

        /**
         * Set garbage collector timeout (seconds).
         */
        \ini_set('session.gc_maxlifetime', (string) $this->setting('expire', '36000'));

        /**
        * Set custom session storage path.
        */
        $this->setStoragePath($storagePath);

        /**
         * Make sure garbage collector visits us.
         */
        \ini_set('session.gc_probability', '1');

        \session_set_cookie_params([
            'lifetime' => $this->setting(\sprintf('cookie%slifetime', Settings::DIVIDER), 60 * 60 * 24 * 14),
            'path' => $this->setting(\sprintf('cookie%spath', Settings::DIVIDER), '/'),
            'domain' => $this->setting(\sprintf('cookie%sdomain', Settings::DIVIDER), ''),
            'secure' => $this->setting(\sprintf('cookie%ssecure', Settings::DIVIDER), true),
            'httponly' => $this->setting(\sprintf('cookie%shttponly', Settings::DIVIDER), true),
            'samesite' => $this->setting(\sprintf('cookie%ssamesite', Settings::DIVIDER), 'Lax'),
        ]);

        \session_name('webservco');

        if (!\session_start()) {
            throw new SessionException('Unable to start session.');
        }

        return true;
    }

    protected function checkSession(): bool
    {
        if (\PHP_SESSION_NONE === \session_status()) {
            throw new SessionException('Session is not started.');
        }
        return true;
    }

    protected function setStoragePath(string $storagePath): bool
    {
        if (empty($storagePath)) {
            return false;
        }

        \ini_set('session.save_path', (string) $storagePath);
        $actualStoragePath = \session_save_path($storagePath);

        if ($actualStoragePath !== $storagePath) {
            if ($this->setting('strict_custom_path', true)) {
                throw new SessionException(
                    'Unable to set custom session storage path. ' .
                    \sprintf('Current path: %s.', $actualStoragePath),
                );
            }
            return false;
        }
        return true;
    }
}
