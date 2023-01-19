<?php
/**
 * Framework class autoloader
 *
 * @version    1.0
 * @package    util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TAdiantiLoader
{
    /**
     * autoloader for web toolkit
     * @param $class classname
     */
    static function autoload_web($class)
    {
        self::autoload($class, 'web');
    }
    
    /**
     * autoloader for gtk toolkit
     * @param $class classname
     */
    static function autoload_gtk($class)
    {
        self::autoload($class, 'gtk');
    }
    
    /**
     * autoloader
     * @param $class classname
     * @param $toolkit tookit (web,gtk)
     */
    static function autoload($class, $toolkit = 'web')
    {
        $folders = array('lib/adianti/widget/' . $toolkit,
                         'lib/adianti/database',
                         'lib/adianti/control',
                         'lib/adianti/util',
                         'lib/adianti/validator',
                         'app/model',
                         'app/control',
                         'app/lib');
                        
        // search in appp root
        if (file_exists("{$class}.class.php"))
        {
            include_once "{$class}.class.php";
            return;
        }
        
        foreach ($folders as $folder)
        {
            if (file_exists("{$folder}/{$class}.class.php"))
            {
                include_once "{$folder}/{$class}.class.php";
                return;
            }
            if (file_exists("{$folder}/{$class}.php"))
            {
                include_once "{$folder}/{$class}.php";
                return;
            }
            else if (file_exists("{$folder}/{$class}.iface.php"))
            {
                include_once "{$folder}/{$class}.iface.php";
                return;
            }
            else
            {
                try
                {
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder),
                                                           RecursiveIteratorIterator::SELF_FIRST) as $entry)
                    {
                        if (is_dir($entry))
                        {
                            if (file_exists("{$entry}/{$class}.class.php"))
                            {
                                include_once "{$entry}/{$class}.class.php";
                                return;
                            }
                            else if (file_exists("{$entry}/{$class}.php"))
                            {
                                include_once "{$entry}/{$class}.php";
                                return;
                            }
                            else if (file_exists("{$entry}/{$class}.iface.php"))
                            {
                                include_once "{$entry}/{$class}.iface.php";
                                return;
                            }
                        }
                    }
                }
                catch(Exception $e)
                {
                    new TMessage('error', $e->getMessage());
                }
            }
        }
    }
}
?>