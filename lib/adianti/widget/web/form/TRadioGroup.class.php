<?php
/**
 * A group of RadioButton's
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TRadioGroup extends TField
{
    private $layout = 'vertical';
    private $items;
    
    /**
     * Define the direction of the options
     * @param $direction String (vertical, horizontal)
     */
    public function setLayout($dir)
    {
        $this->layout = $dir;
    }
    
    /**
     * Add Items to the RadioButton
     * @param $items An array containing the options
     */
    public function addItems($items)
    {
        $this->items = $items;
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        if ($this->items)
        {
            // iterate the RadioButton options
            foreach ($this->items as $index => $label)
            {
                $button = new TRadioButton($this->name);
                $button->setValue($index);
                
                // check if contains any value
                if ($this->value == $index)
                {
                    // mark as checked
                    $button->setProperty('checked', '1');
                }
                // check whether the widget is non-editable
                if (!parent::getEditable())
                {
                    $button->setEditable(FALSE);
                }
                // create the label for the button
                $obj = new TLabel($label);
                $obj->add($button);
                $obj->show();
                
                if ($this->layout == 'vertical')
                {
                    // shows a line break
                    $br = new TElement('br');
                    $br->show();
                }
                echo "\n";
            }
        }
    }
}
?>