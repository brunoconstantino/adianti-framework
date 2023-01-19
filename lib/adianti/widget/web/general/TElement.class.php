<?php
/**
 * Base class for all HTML Elements
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TElement
{
    private $name;        // tag name
    private $properties;  // tag properties
    protected $children;
    private $useLineBreaks;
    private $useSingleQuotes;
    
    /**
     * Class Constructor
     * @param $name  tag name
     */
    public function __construct($name)
    {
        // define the element name
        $this->name = $name;
        $this->useLineBreaks = TRUE;
        $this->useSingleQuotes = FALSE;
    }
    
    /**
     * Intercepts whenever someones assign a new property's value
     * @param $name     Property Name
     * @param $value    Property Value
     */
    public function __set($name, $value)
    {
        // objects and arrays are not set as properties
        if (is_scalar($value))
        {              
            // store the property's value
            $this->properties[$name] = $value;
        }
    }
    
    /**
     * Returns a property's value
     * @param $name     Property Name
     */
    public function __get($name)
    {
        if (isset($this->properties[$name]))
        {              
            return $this->properties[$name];
        }
    }
    
    /**
     * Add an child element
     * @param $child Any object that implements the show() method
     */
    public function add($child)
    {
        $this->children[] = $child;
    }
    
    /**
     * Set the use of linebreaks
     * @param $linebreaks boolean
     */
    public function setUseLineBreaks($linebreaks)
    {
        $this->useLineBreaks = $linebreaks;
    }
    
    /**
     * Set the use of single quotes
     * @param $singlequotes boolean
     */
    public function setUseSingleQuotes($singlequotes)
    {
        $this->useSingleQuotes = $singlequotes;
    }
    
    /**
     * Del an child element
     * @param $child Any object that implements the show() method
     */
    public function del($object)
    {
        foreach ($this->children as $key => $child)
        {
            if ($child === $object) // same instance
            {
                unset($this->children[$key]);
            }
        }
    }

    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Opens the tag
     */
    private function open()
    {
        // exibe a tag de abertura
        echo "<{$this->name}";
        if ($this->properties)
        {
            // percorre as propriedades
            foreach ($this->properties as $name=>$value)
            {
                if ($this->useSingleQuotes)
                {
                    echo " {$name}='{$value}'";
                }
                else
                {
                    echo " {$name}=\"{$value}\"";
                }
            }
        }
        
        if (count($this->children) == 0)
        {
            echo '/>';
        }
        else
        {
            echo '>';
        }
    }
    
    /**
     * Shows the tag
     */
    public function show()
    {
        // open the tag
        $this->open();
        
        // verify if the tag has child elements
        if ($this->children)
        {
            if (count($this->children)>1)
            {
                if ($this->useLineBreaks)
                {
                    echo "\n";
                }
            }
            // iterate all child elements
            foreach ($this->children as $child)
            {
                // verify if the child is an object
                if (is_object($child))
                {
                    $child->show();
                }
                // otherwise, the child is a scalar
                else if ((is_string($child)) or (is_numeric($child)))
                {
                    echo $child;
                }
            }
            // closes the tag
            $this->close();
        }
    }
    
    /**
     * Closes the tag
     */
    private function close()
    {
        echo "</{$this->name}>";
        if ($this->useLineBreaks)
        {
            echo "\n";
        }
    }
}
?>