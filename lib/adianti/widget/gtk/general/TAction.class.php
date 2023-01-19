<?php
/**
 * Structure to encapsulate an action
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TAction
{
    private $action;
    private $param;
    
    /**
     * Class Constructor
     * @param $action Callback to be executed
     */
    public function __construct($action)
    {
        $this->action = $action;
    }
    
    /**
     * Adds a parameter to the action
     * @param  $param = parameter name
     * @param  $value = parameter value
     */
    public function setParameter($param, $value)
    {
        $this->param[$param] = $value;
    }
    
    /**
     * Returns a parameter
     * @param  $param = parameter name
     */
    public function getParameter($param)
    {
        return $this->param[$param];
    }
    
    /**
     * Return the Action Parameters
     */
    public function getParameters()
    {
        return $this->param;
    }
    
    /**
     * Return the Action Callback
     * @return  The Action Callback
     */
    public function getAction()
    {
        return $this->action;
    }
}
?>