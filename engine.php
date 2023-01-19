<?php
include_once 'lib/adianti/util/TAdiantiLoader.class.php';
spl_autoload_register(array('TAdiantiLoader', 'autoload_web'));
define('APPLICATION_NAME', 'framework');

class TApplication
{
    static public function run()
    {
        new TSession;
        
        if (TSession::getValue('started'))
        {
            date_default_timezone_set(TSession::getValue('timezone'));
            $lang = TSession::getValue('language');
        }
        else
        {
            $ini  = parse_ini_file('application.ini');
            $lang = $ini['language'];
            date_default_timezone_set($ini['timezone']);
            TSession::setValue('timezone', $ini['timezone']);
            TSession::setValue('language', $ini['language']);
            TSession::setValue('started', TRUE);
        }
        
        TAdiantiCoreTranslator::setLanguage($lang);
        TApplicationTranslator::setLanguage($lang);
        
        $content = '';
        if ($_REQUEST)
        {
            $class   = isset($_REQUEST['class'])    ? $_REQUEST['class']   : '';
            $static  = isset($_REQUEST['static'])   ? $_REQUEST['static']  : '';
            $method  = isset($_REQUEST['method'])   ? $_REQUEST['method']  : '';
            $encoding= isset($_REQUEST['encoding']) ? $_REQUEST['encoding']: '';
            
            if (class_exists($class))
            {
                if ($static)
                {
                    call_user_func(array($class, $method),$_REQUEST);
                }
                else
                {
                    try
                    {
                        $page = new $class($_GET);
                        ob_start();
                        $page->show();
    	                $content = ob_get_contents();
    	                ob_end_clean();
                    }
                    catch(Exception $e)
                    {
                        ob_start();
                        new TMessage('error', $e->getMessage());
                        $content = ob_get_contents();
                        ob_end_clean();
                    }
                }
            }
            else if (function_exists($method))
            {
                call_user_func($method, $_REQUEST);
            }
            else
            {
                new TMessage('error', "<b>Error</b>: class <b><i><u>{$class}</u></i></b> not found");
            }
            
            $css = TPage::getLoadedCSS();
            $js  = TPage::getLoadedJS();
            echo $css;
            echo $js;
            
            echo $content;
        }
    }
    
    /**
     * Execute a specific method of a class with parameters
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    static public function executeMethod($class, $method = NULL, $parameters = NULL)
    {
        $url = array();
        $url['class']  = $class;
        $url['method'] = $method;
        unset($parameters['class']);
        unset($parameters['method']);
        $url = array_merge($url, (array) $parameters);
        
        if (isset($_REQUEST['isajax']) AND $_REQUEST['isajax'] == '1') // create ajax flag
        {
            echo "<script language='JavaScript'> __adianti_goto_page('index.php?".http_build_query($url)."'); </script>";
        }
        else
        {
            echo "<script language='JavaScript'> window.location='?".http_build_query($url)."'; </script>";
        }
    }
}
TApplication::run();
?>