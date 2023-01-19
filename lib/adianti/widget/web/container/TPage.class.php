<?php
/**
 * Page Controller Pattern: used as container for all elements inside a page and also as a page controller
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPage extends TElement
{
    private $body;
    static private $loadedjs;
    static private $loadedcss;
    static private $registeredcss;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('div');
    }
    
    /**
     * Interprets an action based at the URL parameters
     */
    public function run()
    {
        if ($_GET)
        {
            $class  = isset($_GET['class'])  ? $_GET['class']  : NULL;
            $method = isset($_GET['method']) ? $_GET['method'] : NULL;
            
            if ($class)
            {
                $object = $class == get_class($this) ? $this : new $class;
                if (is_callable(array($object, $method) ) )
                {
                    call_user_func(array($object, $method), $_REQUEST);
                }
            }
            else if (function_exists($method))
            {
                call_user_func($method, $_REQUEST);
            }
        }
    }
    
    /**
     * Close the current page
     */
    public function close()
    {
        echo "<script language='JavaScript'>window.close();</script>";
    }
    
    /**
     * Include a specific JavaScript function to this page
     * @param $js JavaScript location
     */
    static public function include_js($js)
    {
        self::$loadedjs[$js] = TRUE;
    }
    
    /**
     * Include a specific Cascading Stylesheet to this page
     * @param $css  Cascading Stylesheet 
     */
    static public function include_css($css)
    {
        self::$loadedcss[$css] = TRUE;
    }
    
    /**
     * Register a specific Cascading Stylesheet to this page
     * @param $cssname  Cascading Stylesheet Name
     * @param $csscode  Cascading Stylesheet Code
     */
    static public function register_css($cssname, $csscode)
    {
        self::$registeredcss[$cssname] = $csscode;
    }
    
    /**
     * Open a File Dialog
     * @param $file File Name
     */
    static public function openFile($file)
    {
        echo "\n<script language='javascript'>";
        echo "sWidth  = screen.width - 10;\n";
        echo "sHeight = screen.height - 120;\n";
        echo "window.open('download.php?file={$file}',";
        echo " 'NEWWINDOW', ";
        echo " 'width='+sWidth+',height='+sHeight+',top=0,left=0,status=yes,scrollbars=yes,toolbar=yes,resizable=yes,maximized=yes,menubar=yes,location=yes')";
        echo "\n</script>";
    }
    
    /**
     * Return the loaded Cascade Stylesheet files
     * @ignore-autocomplete on
     */
    static public function getLoadedCSS()
    {
        $css = self::$loadedcss;
        $csc = self::$registeredcss;
        $css_text = '';
        
        if ($css)
        {
            foreach ($css as $cssfile => $bool)
            {
                $css_text .= "    <link rel='stylesheet' type='text/css' href='$cssfile'/>\n";
            }
        }
        
        if ($csc)
        {
            $css_text .= "    <style type='text/css' media='screen'>\n";
            foreach ($csc as $cssname => $csscode)
            {
                $css_text .= $csscode;
            }
            $css_text .= "    </style>\n";
        }
        
        return $css_text;
    }
    
    /**
     * Return the loaded JavaScript files
     * @ignore-autocomplete on
     */
    static public function getLoadedJS()
    {
        $js = self::$loadedjs;
        $js_text = '';
        if ($js)
        {
            foreach ($js as $jsfile => $bool)
            {
                $js_text .= "    <script language='JavaScript' src='$jsfile'></script>\n";;
            }
        }
        return $js_text;
    }
    
    /**
     * Discover if the browser is mobile device
     */
    static public function isMobile()
    {
        $isMobile = FALSE;
        
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']))
        {
            $isMobile = TRUE;
        }
        
        $mobiBrowsers = array('android',   'audiovox', 'blackberry', 'epoc',
                              'ericsson', ' iemobile', 'ipaq',       'iphone', 'ipad', 
                              'ipod',      'j2me',     'midp',       'mmp',
                              'mobile',    'motorola', 'nitro',      'nokia',
                              'opera mini','palm',     'palmsource', 'panasonic',
                              'phone',     'pocketpc', 'samsung',    'sanyo',
                              'series60',  'sharp',    'siemens',    'smartphone',
                              'sony',      'symbian',  'toshiba',    'treo',
                              'up.browser','up.link',  'wap',        'wap',
                              'windows ce','htc');
                              
        foreach ($mobiBrowsers as $mb)
        {
            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),$mb) !== FALSE)
            {
            	$isMobile = TRUE;
            }
        }
        
        return $isMobile;
    }
    
    /**
     * Intercepts whenever someones assign a new property's value
     * @param $name     Property Name
     * @param $value    Property Value
     */
    public function __set($name, $value)
    {
        parent::__set($name, $value);
        $this->$name = $value;
    }
    
    /**
     * Decide wich action to take and show the page
     */
    public function show()
    {
        $this->run();
        parent::show();
    }
}
?>