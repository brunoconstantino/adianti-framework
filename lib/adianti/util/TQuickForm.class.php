<?php
/**
 * Create quick forms for input data with a standard container for elements
 *
 * @version    1.0
 * @package    util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TQuickForm extends TForm
{
    protected $fields; // array containing the form fields
    protected $name;   // form name
    private   $table;
    private   $actions;
    private   $action_row;
    private   $has_action;
    
    /**
     * Class Constructor
     * @param $name Form Name
     */
    public function __construct($name = 'my_form')
    {
        parent::__construct($name);
        
        // creates a table
        $this->table = new TTable;
        $this->has_action = FALSE;
        
        // add the table to the form
        parent::add($this->table);
    }
    
    /**
     * Returns the form container
     */
    public function getContainer()
    {
        return $this->table;
    }
    
    /**
     * Add a form field
     * @param $label  Field Label
     * @param $object Field Object
     * @param $size   Field Size
     */
    public function addQuickField($label, $object, $size = 200)
    {
        $object->setSize($size, $size);
        parent::addField($object);
        
        // adiciona uma linha para o campo código
        $row=$this->table->addRow();
        $row->addCell(new TLabel($label));
        $row->addCell($object);
    }
    
    /**
     * Add a form action
     * @param $label  Action Label
     * @param $action TAction Object
     * @param $icon   Action Icon
     */
    public function addQuickAction($label, $action, $icon = 'ico_save.png')
    {
        // cria um botão de ação (salvar)
        $button=new TButton('save');
        parent::addField($button);
        
        // define the button action
        $button->setAction($action, $label);
        $button->setImage($icon);
        
        if (!$this->has_action)
        {
            // creates the action table
            $this->actions = new TTable;
            $this->action_row = $this->actions->addRow();
            
            $row  = $this->table->addRow();
            $cell = $row->addCell($this->actions);
            $cell->colspan=2;
        }
        // add cell for button
        $this->action_row->addCell($button);
        
        $this->has_action = TRUE;
    }
}
?>