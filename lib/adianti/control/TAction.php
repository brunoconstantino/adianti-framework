<?php
Namespace Adianti\Control;

use Adianti\Core\AdiantiCoreTranslator;
use Exception;
use ReflectionMethod;

/**
 * Structure to encapsulate an action
 *
 * @version    2.0
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TAction
{
    protected $action;
    protected $param;
    
    /**
     * Class Constructor
     * @param $action Callback to be executed
     */
    public function __construct($action)
    {
        $this->action = $action;
        if (!is_callable($this->action))
        {
            $action_string = $this->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Method ^1 must receive a paremeter of type ^2', __METHOD__, 'Callback'). ' <br> '.
                                AdiantiCoreTranslator::translate('Check if the action (^1) exists', $action_string));
        }
    }
    
    /**
     * Returns the action as a string
     */
    public function toString()
    {
        $action_string = '';
        if (is_string($this->action))
        {
            $action_string = $this->action;
        }
        else if (is_array($this->action))
        {
            if (is_object($this->action[0]))
            {
                $action_string = get_class($this->action[0]) . '::' . $this->action[1];
            }
            else
            {
                $action_string = $this->action[0] . '::' . $this->action[1];
            }
        }
        return $action_string;
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
     * Set the parameters for the action
     * @param  $parameters = array of parameters
     */
    public function setParameters($parameters)
    {
        // does not override the action
        unset($parameters['class']);
        unset($parameters['method']);
        $this->param = $parameters;
    }
    
    /**
     * Returns a parameter
     * @param  $param = parameter name
     */
    public function getParameter($param)
    {
        if (isset($this->param[$param]))
        {
            return $this->param[$param];
        }
        return NULL;
    }
    
    /**
     * Return the Action Parameters
     */
    public function getParameters()
    {
        return $this->param;
    }
    
    /**
     * Returns the current calback
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Returns if the action is static
     */
    public function isStatic()
    {
        if (is_callable($this->action) AND is_array($this->action))
        {
            $class  = is_string($this->action[0])? $this->action[0]: get_class($this->action[0]);
            $method = $this->action[1];
            
            $rm = new ReflectionMethod( $class, $method );
            return $rm-> isStatic ();
        }
        return FALSE;
    }
}
