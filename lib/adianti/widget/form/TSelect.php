<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;

use Exception;
use Gtk;
use GObject;
use GtkTreeView;
use GtkListStore;
use GtkTreeViewColumn;
use GtkCellRendererText;
use GtkScrolledWindow;

/**
 * Select Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSelect extends TField implements AdiantiWidgetInterface
{
    private $columns;
    private $tree;
    private $model;
    protected $widget;
    protected $formName;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    function __construct($name)
    {
        parent::__construct($name);
        $this->widget = new GtkScrolledWindow;
        $this->widget->set_policy(GTK::POLICY_AUTOMATIC, GTK::POLICY_ALWAYS);
        parent::add($this->widget);
        
        $this->tree = new GtkTreeView;
        $this->tree->connect_simple('select-cursor-row', array($this, 'onPreExecuteExitAction'));
        $this->tree->connect_simple('button-press-event', array($this, 'onPreExecuteExitAction'));
        
        $this->model = new GtkListStore(GObject::TYPE_STRING, GObject::TYPE_STRING);
        $this->tree->set_model($this->model);
        $this->tree->set_headers_visible(FALSE);
        $this->tree->set_rubber_banding(TRUE);
        $this->tree->get_selection()->set_mode(Gtk::SELECTION_MULTIPLE);
        
        $column = new GtkTreeViewColumn;
        
        $cell_renderer = new GtkCellRendererText;
        $column->pack_start($cell_renderer, true);
        
        $column->add_attribute($cell_renderer, 'text', 1);
        $this->tree->append_column($column);
        
        $this->widget->add($this->tree);
        parent::show_all();
    }
    
    /**
     * Disable multiple selection
     */
    public function disableMultiple()
    {
        $this->tree->get_selection()->set_mode(Gtk::SELECTION_SINGLE);
    }
    
    /**
     * Define the field's selected values
     * @param $value An array of option indexes
     */
    function setValue($values)
    {
        $this->tree->get_selection()->unselect_all();
        
        foreach ($this->model as $row)
        {
            if (in_array($row[0], (array) $values))
            {
                $this->tree->get_selection()->select_path( $row->{'path'} );
            }
        }
    }
    
    /**
     * Returns the field's value
     */
    function getValue()
    {
        $treeselection = $this->tree->get_selection();
        list($model, $rows) = $treeselection->get_selected_rows();
        
        $selected = array();
        if ($rows)
        {
            foreach ($rows as $path)
            {
                $iter = $this->model->get_iter($path);
                $selected[] = $model->get_value($iter, 0);
            }
        }
        
        if ($this->tree->get_selection()->get_mode() == Gtk::SELECTION_SINGLE)
        {
            return $selected[0];
        }
        else
        {
            return $selected;
        }
    }
    
    /**
     * Define the Field's size
     * @param $width Field's width in pixels
     * @param $height Field's height in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->tree->set_size_request($width, $height);
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
     * Add items to the select
     * @param $items An indexed array containing the combo options
     */
    function addItems($items)
    {
        if (is_array($items))
        {
            foreach ($items as $key => $item)
            {
                $iter = $this->model->append();
                $this->model->set($iter, 0, (string) $key);
                $this->model->set($iter, 1, (string) $item);
            }
        }
    }
    
    /**
     * Clear the select
     */
    public function clear()
    {
        $this->model->clear();
    }
    
    /**
     * Define the action to be executed when the user changes the combo
     * @param $action TAction object
     */
    function setChangeAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->changeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Execute the exit action
     */
    public function onPreExecuteExitAction()
    {
        Gtk::timeout_add(10, array($this, 'onExecuteExitAction'));
    }
    
    /**
     * Execute the exit action
     */
    public function onExecuteExitAction()
    {
        if (isset($this->changeAction) AND $this->changeAction)
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->wname, 'TForm::setFields()') );
            }
            
            $callback = $this->changeAction->getAction();
            $param = (array) TForm::retrieveData($this->formName);
            call_user_func($callback, $param);
        }
    }
    
    /**
     * Reload combobox items after it is already shown
     * @param $formname form name (used in gtk version)
     * @param $name field name
     * @param $items array with items
     */
    public static function reload($formname, $name, $items)
    {
        $form = TForm::getFormByName($formname);
        $select = $form->getField($name);
        $select->clear();
        $select->addItems($items);
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
                $field->setValue(array(0));
            }
        }
    }
}
