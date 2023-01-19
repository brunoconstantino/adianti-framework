<?php
/**
 * ComboBox Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCombo extends TField
{
    protected $items; // array containing the combobox options
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name)
    {
        // executes the parent class constructor
        parent::__construct($name);
        
        // creates the default field style
        $style1 = new TStyle('tcombo');
        /*
        $style1-> border                = 'solid';
        $style1-> border_color          = '#a0a0a0';
        $style1-> border_width          = '1px';
        $style1-> _webkit_border_radius = '3px';
        $style1-> _moz_border_radius    = '3px';
        */
        $style1-> height                = '24px';
        $style1-> z_index               = '1';
        $style1->show();
        
        // creates a <select> tag
        $this->tag = new TElement('select');
        $this->tag->{'class'} = 'tcombo'; // CSS
    }
    
    /**
     * Add items to the combo box
     * @param $items An indexed array containing the combo options
     */
    public function addItems($items)
    {
        $this->items = $items;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            $val = $_POST[$this->name];
            
            if ($val == '') // empty option
            {
                return '';
            }
            else
            {
                if (strpos($val, '::'))
                {
                    $tmp = explode('::', $val);
                    return trim($tmp[0]);
                }
                else
                {
                    return $val;
                }
            }
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Shows the widget
     */
    public function show()
    {
        // define the tag properties
        $this->tag-> name  = $this->name;    // tag name
        $this->tag-> style = "width:{$this->size}px";  // size in pixels
        
        // creates an empty <option> tag
        $option = new TElement('option');
        $option->add('');
        $option-> value = '';   // tag value
        // add the option tag to the combo
        $this->tag->add($option);
        
        if ($this->items)
        {
            // iterate the combobox items
            foreach ($this->items as $chave => $item)
            {
                // creates an <option> tag
                $option = new TElement('option');
                $option-> value = $chave;  // define the index
                $option->add($item);      // add the item label
                
                if (substr($chave, 0, 3) == '>>>')
                {
                    $option-> disabled = 1;
                }
                // verify if this option is selected
                if (($chave == $this->value) AND ($this->value !== NULL))
                {
                    // mark as selected
                    $option-> selected = 1;
                }
                // add the option to the combo
                $this->tag->add($option);
            }
        }
        
        // verify whether the widget is editable
        if (!parent::getEditable())
        {
            // make the widget read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        // shows the combobox
        $this->tag->show();
    }
}
?>