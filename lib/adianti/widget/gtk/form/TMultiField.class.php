<?php
/**
 * MultiField Widget: takes a group of input fields and gives them the possibility to register many occurrences
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMultiField extends GtkVBox
{
    private $table_fields;
    private $view;
    private $model;
    private $count;
    private $types;
    private $created;
    private $className;
    private $editing;
    private $fields;
    private $allfields;
    private $columns;
    private $height;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct();
        $this->wname = $name;
        $this->count = 0;
        $this->types = array();
        $this->created = FALSE;
        $this->table_fields = new TTable;
        $this->editing = FALSE;
        parent::pack_start($this->table_fields, false, false);
        
        $this->view  = new GtkTreeView;
        $this->model = new GtkListStore;
        
        $button_bar = new GtkHBox;
        $add = GtkButton::new_from_stock(Gtk::STOCK_SAVE);
        $del = GtkButton::new_from_stock(Gtk::STOCK_DELETE);
        $can = GtkButton::new_from_stock(Gtk::STOCK_CANCEL);
        
        $add->connect_simple('clicked', array($this, 'onSave'));
        $del->connect_simple('clicked', array($this, 'onDelete'));
        $can->connect_simple('clicked', array($this, 'onCancel'));
        
        $button_bar->pack_start($add, FALSE, FALSE);
        $button_bar->pack_start($del, FALSE, FALSE);
        $button_bar->pack_start($can, FALSE, FALSE);
        
        parent::pack_start($button_bar, false, false);
        $scroll = new GtkScrolledWindow;
        $scroll->add($this->view);
        $scroll->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
        parent::pack_start($scroll, false, false);
        $this->view->set_size_request(400,140);
        $this->height = 140;
        $this->view->connect_simple('button_release_event', array($this, 'onClick'));
    }
    
    /**
     * Define the widget's name 
     * @param $name Widget's Name
     */
    public function setName($name)
    {
        $this->wname = $name;
    }
    
    /**
     * Returns the name of the widget
     */
    public function getName()
    {
        return $this->wname;
    }
    
    /**
     * Add a field to the MultiField
     * @param $name   Widget's name
     * @param $text   Widget's label
     * @param $object Widget
     * @param $size   Widget's size
     * @param $inform Show the Widget in the form
     */
    public function addField($name, $text, GtkObject $object, $size, $inform = TRUE)
    {
        if ($inform)
        {
            $row = $this->table_fields->addRow();
            $label = new TLabel("<i>$text</i>");
            $n = $this->count;
            $object->setName("{$this->name}_text{$n}");
            $row->addCell($label);
            $row->addCell($object);
            $this->fields[$name] = array($text, $object, $size, FALSE);
        }
        $this->allfields[$name] = array($text, $object, $size, FALSE);
        if (get_class($object)=='TComboCombined')
        {
            $column = new GtkTreeViewColumn('ID');
        }
        else
        {
            $column = new GtkTreeViewColumn($text);
        }
        $cell_renderer = new GtkCellRendererText;
        $column->pack_start($cell_renderer, true);
        $column->add_attribute($cell_renderer, 'text', $this->count);
        $this->types[] = GObject::TYPE_STRING;
        $this->view->append_column($column);
        $this->columns[$name] = $this->count;
        $this->count ++;
        // combocombined, need to add one more column and treat different
        if (get_class($object) == 'TComboCombined')
        {
            $tname = $object->getTextName();
            $this->fields[$tname] = array($text, $object, $size, TRUE);
            $column = new GtkTreeViewColumn($text);
            $cell_renderer = new GtkCellRendererText;
            $column->pack_start($cell_renderer, true);
            $column->add_attribute($cell_renderer, 'text', $this->count);
            $this->types[] = GObject::TYPE_STRING;
            $this->view->append_column($column);
            $this->allfields[$tname] = array($text, $object, $size, TRUE);
            $this->columns[$tname] = $this->count;
            $this->count ++;
        }
    }
    
    /**
     * Define the class name for the Active Records returned by this component
     * @param $class Class Name
     */
    public function setClass($class)
    {
        $this->className = $class;
    }
    
    /**
     * Returns the class name defined by the setClass() method
     */
    public function getClass()
    {
        return $this->className;
    }
    
    /**
     * Define the MultiField content
     * @param $objects  A Collection of Active Records
     */
    public function setValue($value)
    {
        if (!$this->created)
        {
            $this->createModel();
        }
        
        $this->items = $value;
        $this->model->clear();
        if ($this->items)
        {
            foreach ($this->items as $item)
            {
                $iter = $this->model->append();
                $n = 0;
                foreach ($this->fields as $name => $vetor)
                {
                    $this->model->set($iter, $n, $item->$name);
                    $n ++;
                }
            }
        }
        $this->onCancel();
    }
    
    /**
     * Define the Multifield form data
     * @param $objects An object
     */
    public function setFormData($object)
    {
        foreach ($object as $property => $value)
        {
            if (isset($this->allfields[$property]))
            {
                $field = $this->allfields[$property][1];
                if (is_object($field))
                {
                    $field->setValue($object->$property);
                }
            }
        }
    }
    
    /**
     * Return the widget's content
     */
    public function getValue()
    {
        $vetor = array();
        foreach ($this->model as $row)
        {
            $linha = array();
            
            $className=$this->getClass() ? $this->getClass() : 'StdClass';
            $object = new $className;
            foreach ($this->fields as $name => $properties)
            {
                $index = $this->columns[$name];
                $is_combo_combined = $properties[3];
                if (!$is_combo_combined) // combo text
                {
                    $object->$name = $row[$index];
                }
            }
            $vetor[] = $object;
        }
        return $vetor;
    }
    
    /**
     * Define the MultiField height
     * @param $height Height in pixels
     */
    public function setHeight($height)
    {
        $this->height = $height;
        if ($this->created)
        {
            $this->view->set_size_request($this->getWidth(), $height);
        }
    }
    
    /**
     * Creates the TreeView Model
     */
    public function createModel()
    {
        $this->model->set_column_types($this->types);
        $this->view->set_model($this->model);
        $this->created = TRUE;
        
        $this->view->set_size_request($this->getWidth(), $this->height);
    }
    
    /**
     * Returns the TMultiField width after createModel()
     * @ignore-autocomplete on
     */
    private function getWidth()
    {
        $width = 0;
        if ($this->created)
        {
            if ($this->allfields)
            {
                foreach ($this->allfields as $field)
                {
                    $object = $field[1];
                    $size   = $field[2];
                    
                    // in order to TComboCombined not count twice
                    if (!$object instanceof TComboCombined)
                    {
                        $width += $size;
                    }
                }
            }
        }
        return $width;
    }
    
    /**
     * Execute when the user add a new record
     * @ignore-autocomplete on
     */
    public function onAdd()
    {
        foreach ($this->allfields as $name => $vetor)
        {
            $object = $vetor[1];
            $is_combo_combined = $vetor[3];
            if ($is_combo_combined)
            {
                $content[] = $object->getTextValue();
            }
            else
            {
                $content[] = $object->getValue();
            }
        }
        $this->model->append($content);
    }
    
    /**
     * Execute when the user save a record
     * @ignore-autocomplete on
     */
    public function onSave()
    {
        if ($this->editing)
        {
            $treeselection = $this->view->get_selection();
            list($model, $iter) = $treeselection->get_selected();
            if (!$iter instanceof GtkTreeIter)
            {
                $iter = $this->model->append();
            }
        }
        else
        {
            $iter = $this->model->append();
        }
        
        foreach ($this->allfields as $name => $vetor)
        {
            $object = $vetor[1];
            $is_combo_combined = $vetor[3];
            $index = $this->columns[$name];
            if ($is_combo_combined)
            {
                $content = $object->getTextValue();
            }
            else
            {
                $content = $object->getValue();
            }
            $this->model->set($iter, $index, $content);
        }
        $this->onCancel();
    }
    
    /**
     * Execute when the user clicks over a record
     * @ignore-autocomplete on
     */
    public function onClick()
    {
        $treeselection = $this->view->get_selection();
        list($model, $iter) = $treeselection->get_selected();
        if ($iter instanceof GtkTreeIter)
        {
            foreach ($this->allfields as $name => $vetor)
            {
                $object = $vetor[1];
                $index = $this->columns[$name];
                $object->setValue($model->get_value($iter, $index));
            }
            $this->editing = TRUE;
        }
    }
    
    /**
     * Execute when the user deletes a record
     * @ignore-autocomplete on
     */
    public function onDelete()
    {
        $treeselection = $this->view->get_selection();
        list($model, $iter) = $treeselection->get_selected();
        if ($iter instanceof GtkTreeIter)
        {
            $model->remove($iter);
        }
    }
    
    /**
     * Execute when the user cancel editing
     * @ignore-autocomplete on
     */
    public function onCancel()
    {
        if ($this->fields)
        {
            foreach ($this->allfields as $name => $vetor)
            {
                $object = $vetor[1];
                $object->setValue('');
            }
        }
        
        $this->editing = FALSE;
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        if (!$this->created)
        {
            $this->createModel();
        }
        
        $this->table_fields->show();
        parent::show_all();
    }
}
?>