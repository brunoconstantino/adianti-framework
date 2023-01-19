<?php
/**
 * A group of CheckButton's
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCheckGroup extends TField
{
    private $layout = 'vertical';
    private $items;

    /**
     * Define the direction (vertical or horizontal)
     * @param $direction A string 'vertical' or 'horizontal'
     */
    public function setLayout($dir)
    {
        $this->layout = $dir;
    }
    
    /**
     * Add items to the check group
     * @param $items An indexed array containing the options
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
            return $_POST[$this->name];
        }
        else
        {
            return array();
        }
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        if ($this->items)
        {
            // iterate the checkgroup options
            foreach ($this->items as $index => $label)
            {
                // instantiates a new CheckButton
                $button = new TCheckButton("{$this->name}[]");
                $button->setIndexValue($index);
                $button->setProperty('checkgroup', $this->name);
                
                // verify if the checkbutton is checked
                if (@in_array($index, $this->value))
                {
                    //$button->setProperty('checked', '1');
                    $button->setValue($index); // value=indexvalue (checked)
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
                    echo "\n";
                }
            }
        }
    }
}
?>