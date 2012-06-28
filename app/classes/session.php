<?php
/**
 *  $_SESSION wrapper
 *
 *  @author Adrian Soluch adrian@soluch.at
 */

final class Session
{
    private static $instance = NULL;
    
    private static  $sessArrayKey = "__sessiondata";
    
    /**
     *  Init Session
     */
    private function __construct()
    {
        session_start();
        if(!isset($_SESSION[Session::$sessArrayKey])) {

            $_SESSION[Session::$sessArrayKey] = array();
        }
    }
    
    /**
     * @access  private
     */
    private function __clone() {}
    
    /**
     * generate a session instance
     */
    public static function getInstance()
    {
        if(self::$instance === NULL) {

            self::$instance = new Session();
        }
        return self::$instance;
    } 
    
    /**
     *  Save SessionVars
     */
    public function __set($key, $value)
    {
        $_SESSION[Session::$sessArrayKey][$key] = $value;
    }
    
    /**
     *  Unset SessionVars
     */
    public function __unset($key)
    {
        if($this->exists($key)) {

            unset($_SESSION[Session::$sessArrayKey][$key]);
            return TRUE;
        } else {
          return FALSE;
        }
    }
    
    /**
     * read SessionVars
     * 
     * @param   String      $key
     * @return  Mixed|NULL
     */
    public function __get($key)
    {
        if($this->exists($key)) {

            return $_SESSION[Session::$sessArrayKey][$key];
        } else {
          return NULL;
        }
    }
    
    /**
     * Returns TRUE when $keys exists
     *
     * @param   String  $key
     * @return  Bool
     */
    public function exists($key)
    {
        return (isset($_SESSION[Session::$sessArrayKey][$key]) ? TRUE : FALSE);
    }
    
    /**
     * ALIAS for Session::exists($key)
     *
     * @param   String  $key
     * @return  Bool
     */
    public function __isset($key)
    {
        return $this->exists($key);
    }
    
    /**
     * Close the Session
     *
     * @access public
     */
    public function close()
    {
        session_write_close();
    }
    
    /**
     * Generate new Session-ID
     */
    public function renew($del = FALSE)
    {
        session_regenerate_id($del);
    }
    
    /**
     * Returns the Session-ID
     *
     * @return string
     */
    public function getID()
    {
        return session_id();
    }

    public function getSessionCookie() 
    {
        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();
            return $params;
        }
        return FALSE;
    }

    /**
     * Destroy the Session
     */
    public function destroy()
    {
        setcookie(session_name(), '', time()-42000, '/');
        session_destroy();
    }
    
    public function __destruct()
    {
        $this->close();
    }
}
