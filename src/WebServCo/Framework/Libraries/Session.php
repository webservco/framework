<?php
namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\ArrayStorage;
use WebServCo\Framework\Exceptions\SessionException;
use WebServCo\Framework\Settings;

final class Session extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\SessionInterface
{
    public function add($setting, $data)
    {
        $this->checkSession();

        $_SESSION = ArrayStorage::add(
            $_SESSION,
            $setting,
            $data
        );
        return true;
    }

    public function clear($setting)
    {
        return $this->set($setting, null);
    }

    public function destroy()
    {
        $_SESSION = [];
        $cookie = \WebServCo\Framework\Framework::library('Cookie');
        $cookie->set(
            session_name(),
            '',
            time() - 3600,
            $this->setting(sprintf('cookie%spath', Settings::DIVIDER), '/'),
            $this->setting(sprintf('cookie%sdomain', Settings::DIVIDER), ''),
            $this->setting(sprintf('cookie%ssecure', Settings::DIVIDER), true),
            $this->setting(sprintf('cookie%shttponly', Settings::DIVIDER), true),
            $this->setting(sprintf('cookie%ssamesite', Settings::DIVIDER), 'Lax')
        );
        session_destroy();
        return true;
    }

    public function get($setting, $defaultValue = false)
    {
        $this->checkSession();

        return ArrayStorage::get(
            $_SESSION,
            $setting,
            $defaultValue
        );
    }

    public function has($setting)
    {
        $this->checkSession();

        return ArrayStorage::has(
            $_SESSION,
            $setting
        );
    }

    public function regenerate()
    {
        return session_regenerate_id(true);
    }

    public function remove($setting)
    {
        $this->checkSession();

        $_SESSION = ArrayStorage::remove(
            $_SESSION,
            $setting
        );
        return true;
    }

    public function set($setting, $value)
    {
        $this->checkSession();

        $_SESSION = ArrayStorage::set(
            $_SESSION,
            $setting,
            $value
        );
        return true;
    }

    public function start($storagePath = null)
    {
        if (\WebServCo\Framework\Framework::isCli()) {
            throw new SessionException('Not starting session in CLI mode.');
        }

        if (session_status() === \PHP_SESSION_ACTIVE) {
            throw new SessionException(
                'Unable to start session: already started.'
            );
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
        ini_set('session.gc_maxlifetime', $this->setting('expire', '36000'));

        /**
        * Set custom session storage path.
        */
        $this->setStoragePath($storagePath);

        /**
         * Make sure garbage collector visits us.
         */
        ini_set('session.gc_probability', '1');

        if (PHP_VERSION_ID < 70300) {
            session_set_cookie_params(
                $this->setting(sprintf('cookie%slifetime', Settings::DIVIDER), 60 * 60 * 24 * 14),
                sprintf(
                    '%s; SameSite=%s',
                    $this->setting(sprintf('cookie%spath', Settings::DIVIDER), '/'),
                    $this->setting(sprintf('cookie%ssamesite', Settings::DIVIDER), 'Lax')
                ),
                $this->setting(sprintf('cookie%sdomain', Settings::DIVIDER), ''),
                $this->setting(sprintf('cookie%ssecure', Settings::DIVIDER), true),
                $this->setting(sprintf('cookie%shttponly', Settings::DIVIDER), true)
            );
        } else {
            session_set_cookie_params([
                'lifetime' => $this->setting(sprintf('cookie%slifetime', Settings::DIVIDER), 60 * 60 * 24 * 14),
                'path' => $this->setting(sprintf('cookie%spath', Settings::DIVIDER), '/'),
                'domain' => $this->setting(sprintf('cookie%sdomain', Settings::DIVIDER), ''),
                'secure' => $this->setting(sprintf('cookie%ssecure', Settings::DIVIDER), true),
                'httponly' => $this->setting(sprintf('cookie%shttponly', Settings::DIVIDER), true),
                'samesite' => $this->setting(sprintf('cookie%ssamesite', Settings::DIVIDER), 'Lax'),
            ]);
        }

        session_name('webservco');

        if (!session_start()) {
            throw new SessionException('Unable to start session.');
        }

        return true;
    }

    protected function checkSession()
    {
        if (session_status() === \PHP_SESSION_NONE) {
            throw new SessionException(
                'Session is not started.'
            );
        }
    }

    protected function setStoragePath($storagePath)
    {
        if (empty($storagePath)) {
            return false;
        }

        ini_set('session.save_path', $storagePath);
        $actualStoragePath = session_save_path($storagePath);

        if ($actualStoragePath != $storagePath) {
            if ($this->setting('strict_custom_path', true)) {
                throw new SessionException(
                    'Unable to set custom session storage path. ' .
                    sprintf('Current path: %s.', $actualStoragePath)
                );
            }
            return false;
        }
        return true;
    }
}
