<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TComboCombined;
use Adianti\Widget\Container\TTable;

use StdClass;
use Gtk;
use GObject;
use GtkVBox;
use GtkTreeView;
use GtkListStore;
use GtkHBox;
use GtkButton;
use GtkScrolledWindow;
use GtkObject;
use GtkTreeViewColumn;
use GtkCellRendererText;
use GtkTreeIter;

/**
 * MultiField Widget: Takes a group of input fields and gives them the possibility to register many occurrences
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMultiField extends TField implements AdiantiWidgetInterface
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
    private $row_label;
    private $row_field;
    private $orientation;
    protected $widget;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkVBox;
        parent::add($this->widget);
        $this->orientation = 'vertical';
        
        $this->count = 0;
        $this->types = array();
        $this->created = FALSE;
        $this->table_fields = new TTable;
        $this->editing = FALSE;
        $this->widget->pack_start($this->table_fields, false, false);
        
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
        
        $this->widget->pack_start($button_bar, false, false);
        $scroll = new GtkScrolledWindow;
        $scroll->add($this->view);
        $scroll->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
        $this->widget->pack_start($scroll, false, false);
        $this->view->set_size_request(400,140);
        $this->height = 140;
        $this->view->connect_simple('button_release_event', array($this, 'onClick'));
    }
    
    /**
     * Define form orientation
     * @param $orientation (vertical, horizontal)
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
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
                //if (!$is_combo_combined) // combo text
                {
                    $object->$name = $row[$index];
                }
            }
            
            $vetor[] = $object;
        }
        return $vetor;
    }
    
    /**
     * Define the Field's width
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->setHeight($height);
    }
    
    /**
     * Not implemented
     */
    public function setProperty($name, $value, $replace = TRUE)
    {}
    
    /**
     * Not implemented
     */
    public function getProperty($name)
    {}
    
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
     * Add a field to the MultiField
     * @param $name   Widget's name
     * @param $text   Widget's label
     * @param $object Widget
     * @param $size   Widget's size
     * @param $inform Show the Widget in the form
     */
    public function addField($name, $text, GtkObject $object, $size, $mandatory = FALSE)
    {
        if ($this->orientation == 'horizontal')
        {
            if (count($this->fields) == 0)
            {
                $this->row_label = $this->table_fields->addRow();
                $this->row_field = $this->table_fields->addRow();
            }
        }
        else
        {
            $row = $this->table_fields->addRow();
            $this->row_label = $row;
            $this->row_field = $row;
        }
        
        $label = new TLabel("<i>$text</i>");
        if ($mandatory)
        {
            $label->setFontColor('#FF0000');
        }
        $n = $this->count;
        $object->setName("{$this->name}_text{$n}");
        
        $this->row_label->addCell($label);
        $this->row_field->addCell($object);
        
        $this->fields[$name] = array($text, $object, $size, FALSE, $mandatory);
        $this->allfields[$name] = array($text, $object, $size, FALSE, $mandatory);
        
        if (in_array(get_class($object), array('TComboCombined', 'Adianti\Widget\Form\TComboCombined')))
        {
            $column = new GtkTreeViewColumn('ID');
        }
        else
        {
            $column = new GtkTreeViewColumn($text);
        }
        $cell_renderer = new GtkCellRendererText;
        $cell_renderer->set_property('width', $size);
        $column->set_fixed_width($size);
        $column->pack_start($cell_renderer, true);
        $column->add_attribute($cell_renderer, 'text', $this->count);
        
        $this->types[] = GObject::TYPE_STRING;
        $this->view->append_column($column);
        $this->columns[$name] = $this->count;
        $this->count ++;
        // combocombined, need to add one more column and treat different
        if (in_array(get_class($object), array('TComboCombined', 'Adianti\Widget\Form\TComboCombined')))
        {
            $cell_renderer->set_property('width', 20);
            $column->set_fixed_width(20);
            
            $tname = $object->getTextName();
            $this->fields[$tname] = array($text, $object, $size, TRUE, $mandatory);
            $this->fields[$name][2] = 20;
            $this->allfields[$name][2] = 20;
            $column = new GtkTreeViewColumn($text);
            $cell_renderer = new GtkCellRendererText;
            $cell_renderer->set_property('width', $size);
            $column->set_fixed_width($size);
            $column->pack_start($cell_renderer, true);
            $column->add_attribute($cell_renderer, 'text', $this->count);
            $this->types[] = GObject::TYPE_STRING;
            $this->view->append_column($column);
            $this->allfields[$tname] = array($text, $object, $size, TRUE, $mandatory);
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
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        $form = TForm::getFormByName($form_name);
        if ($form instanceof TForm)
        {
            $field = $form->getField($field);
            if ($field instanceof AdiantiWidgetInterface)
            {
                $field->model->clear();
            }
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
                    $width += $size;
                    
                    // in order to TComboCombined not count twice
                    if (!$object instanceof TComboCombined)
                    {
                        
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
        $contents = array();
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
            
            if (($vetor[4]) AND !$content)
            {
                new TMessage('error', AdiantiCoreTranslator::translate('The field ^1 is required', $name) );
                return;
            }
            else
            {
                $contents[$index] = $content;
            }
        }
        
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
            $this->model->set($iter, $index, $contents[$index]);
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
