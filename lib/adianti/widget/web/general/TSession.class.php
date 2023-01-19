<?php
/**
 * Session Data Handler (Registry Pattern)
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSession
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        // if there's no opened session
        if (!session_id())
        {
            session_start();
        }
    }
    
    /**
     * Define the value for a variable
     * @param $var   Variable Name
     * @param $value Variable Value
     */
    static public function setValue($var, $value)
    {
        if (defined('APPLICATION_NAME'))
        {
            $_SESSION[APPLICATION_NAME][$var] = $value;
        }
        else
        {
            $_SESSION[$var] = $value;
        }
    }
    
    /**
     * Returns the value for a variable
     * @param $var Variable Name
     */
    static public function getValue($var)
    {
        if (defined('APPLICATION_NAME'))
        {
            if (isset($_SESSION[APPLICATION_NAME][$var]))
            {
                return $_SESSION[APPLICATION_NAME][$var];
            }
        }
        else
        {
            if (isset($_SESSION[$var]))
            {
                return $_SESSION[$var];
            }
        }
    }
    
    /**
     * Destroy the session data
     */
    static public function freeSession()
    {
        if (defined('APPLICATION_NAME'))
        {
            $_SESSION[APPLICATION_NAME] = array();
            //session_destroy();
        }
        else
        {
            $_SESSION[] = array();
            //session_destroy();
        }
    }
}
?>