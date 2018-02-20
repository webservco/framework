<?php
namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\Settings as S;
use WebServCo\Framework\Exceptions\ApplicationException;

final class Session extends \WebServCo\Framework\AbstractLibrary
{
    protected function checkSession()
    {
        if (session_status() === \PHP_SESSION_NONE) {
            throw new ApplicationException(
                'Session is not started.'
            );
        }
    }
    
    public function start($storagePath = null)
    {
        if (session_status() === \PHP_SESSION_ACTIVE) {
            throw new ApplicationException(
                'Could not start session, already started.'
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
        if (!empty($storagePath)) {
            ini_set('session.save_path', $storagePath);
            session_save_path($storagePath);
        }
    
        /**
         * Make sure garbage collector visits us.
         */
        ini_set('session.gc_probability', 1);

        session_set_cookie_params(
            $this->setting(sprintf('cookie%slifetime', S::DIVIDER), 60 * 60 * 24 * 14),
            $this->setting(sprintf('cookie%spath', S::DIVIDER), '/'),
            $this->setting(sprintf('cookie%sdomain', S::DIVIDER), ''),
            $this->setting(sprintf('cookie%ssecure', S::DIVIDER), false),
            $this->setting(sprintf('cookie%shttponly', S::DIVIDER), true)
        );
        
        session_name('webservco');
        
        if (!session_start()) {
            throw new ApplicationException('Failed to start session');
        }
        
        return true;
    }
    
    public function set($setting, $value)
    {
        $this->checkSession();
        
        $_SESSION = \WebServCo\Framework\ArrayStorage::set(
            $_SESSION,
            $setting,
            $value
        );
        return true;
    }
    
    public function get($setting, $defaultValue = false)
    {
        $this->checkSession();
        
        return \WebServCo\Framework\ArrayStorage::get(
            $_SESSION,
            $setting,
            $defaultValue
        );
    }
    
    public function has($setting)
    {
        return (bool) $this->get($setting);
    }
    
    public function remove($setting)
    {
        $this->checkSession();
        
        $_SESSION = \WebServCo\Framework\ArrayStorage::remove(
            $_SESSION,
            $setting
        );
        return true;
    }
    
    public function clear($setting)
    {
        return $this->set($setting, null);
    }
}
