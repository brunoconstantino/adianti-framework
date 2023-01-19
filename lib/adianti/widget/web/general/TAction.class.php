<?php
/**
 * Structure to encapsulate an action
 *
 * @version    1.0
 * @package    widget_web
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
     * Returns the current calback
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Converts the action into an URL
     * @param  $format_action = format action with document or javascript (ajax=no)
     */
    public function serialize($format_action = TRUE)
    {
        // check if the callback is a method of an object
        if (is_array($this->action))
        {
            // get the class name
            $url['class'] = is_object($this->action[0]) ? get_class($this->action[0]) : $this->action[0];
            // get the method name
            $url['method'] = $this->action[1];
        }
        // otherwise the callback is a function
        else if (is_string($this->action))
        {
            // get the function name
            $url['method'] = $this->action;
        }
        
        // check if there are parameters
        if ($this->param)
        {
            $url = array_merge($url, $this->param);
        }
        
        if ($format_action)
        {
            return 'index.php?'.http_build_query($url);
            /*
            if (isset($_REQUEST['isajax']) AND $_REQUEST['isajax'] == '1') // create ajax flag
            {
                $url_str = http_build_query($url);
                return "javascript:__adianti_load_page('engine.php?{$url_str}')";
            }
            else
            {
                return "document.location='?" . http_build_query($url)."'";
            }
            */
        }
        else
        {
            return http_build_query($url);
        }
    }
}
?>