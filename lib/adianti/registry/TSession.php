<?php

Namespace Adianti\Registry;

/**
 * Session Data Handler
 *
 * @version    2.0
 * @package    registry
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSession
{
    static private $content;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        self::$content = array();
    }
    
    /**
     * Define the value for a variable
     * @param $var   Variable Name
     * @param $value Variable Value
     */
    public function setValue($var, $value)
    {
        self::$content[$var] = $value;
    }
    
    /**
     * Returns the value for a variable
     * @param $var Variable Name
     */
    public function getValue($var)
    {
        if (isset(self::$content[$var]))
        {
            return self::$content[$var];
        }
    }
    
    /**
     * Destroy the session data
     */
    public function freeSession()
    {
        self::$content = array();
    }
}
