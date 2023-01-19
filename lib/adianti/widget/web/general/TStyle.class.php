<?php
/**
 * StyleSheet Manager
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TStyle
{
    private $name;           // stylesheet name
    private $properties;     // properties
    static  private $loaded; // array of loaded styles
    static  private $styles;
    
    /**
     * Class Constructor
     * @param $mame Name of the style
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    /**
     * Find a style by its properties
     * @object style object
     */
    public static function findStyle($object)
    {
        if (self::$styles)
        {
            foreach (self::$styles as $stylename => $style)
            {
                if ((array)$style->properties === (array)$object->properties)
                {
                    return $stylename;
                }
            }
        }
    }
    
    /**
     * Executed whenever a property is assigned
     * @param  $name    = property's name
     * @param  $value   = property's value
     */
    public function __set($name, $value)
    {
        // replaces "_" by "-" in the property's name
        $name = str_replace('_', '-', $name);
        
        // store the assigned tag property
        $this->properties[$name] = $value;
    }
    
    /**
     * Returns the style content
     */
    public function getContent()
    {
        // open the style
        $style = '';
        $style.= "    .{$this->name}\n";
        $style.= "    {\n";
        if ($this->properties)
        {
            // iterate the style properties
            foreach ($this->properties as $name=>$value)
            {
                $style.= "        {$name}: {$value};\n";
            }
        }
        $style.= "    }\n";
        return $style;
    }
    
    /**
     * Show the style
     */
    public function show()
    {
        // check if the style is already loaded
        if (!isset(self::$loaded[$this->name]))
        {
            $style = $this->getContent();
            TPage::register_css($this->name, $style);
            // mark the style as loaded
            self::$loaded[$this->name] = TRUE;
            self::$styles[$this->name] = $this;
        }
    }
}
?>