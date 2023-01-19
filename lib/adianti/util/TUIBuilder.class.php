<?php
/**
 * Interface builder that takes a XML file save by Adianti Studio Designer and renders the form into the interface.
 *
 * @version    1.0
 * @package    util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TUIBuilder extends TPanel
{
    protected $controller;
    protected $form;
    protected $fields;
    protected $actions;
    protected $fieldsByName;
    
    /**
     * Class Constructor
     * @param $width Panel width
     * @param $height Panel height
     */
    public function __construct($width, $height)
    {
        parent::__construct($width, $height);
        $this->fields       = array();
        $this->actions      = array();
        $this->fieldsByName = array();
    }
    
    /**
     * Return the found actions
     */
    public function getActions()
    {
        return $this->actions;
    }
    
    /**
     * Parse XML form file
     * @param $filename XML form file path
     */
    public function parseFile($filename)
    {
        $xml = new SimpleXMLElement(file_get_contents($filename));
        $this->parseElement($xml);
    }
    
    /**
     * 
     */
    public function makeTLabel($properties)
    {
        $widget = new TLabel((string) $properties->{'name'});
        $widget->setValue((string) $properties->{'value'});
        $widget->setFontColor((string) $properties->{'color'});
        $widget->setFontSize((string) $properties->{'size'}.'pt');
        $widget->setFontStyle((string) $properties->{'style'});
        $this->fieldsByName[(string) $properties->{'name'}] = $widget;
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTButton($properties)
    {
        $widget = new TButton((string) $properties->{'name'});
        $widget->setImage((string) $properties->{'icon'});
        $widget->setAction(new TAction(array($this->controller, (string) $properties->{'action'})), (string) $properties->{'value'});
        $this->fields[] = $widget;
        $this->fieldsByName[(string) $properties->{'name'}] = $widget;
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTEntry($properties)
    {
        $widget = new TEntry((string) $properties->{'name'});
        $widget->setValue((string) $properties->{'value'});
        $widget->setMask((string) $properties->{'mask'});
        $widget->setSize((int) $properties->{'width'});
        $widget->setEditable((string) $properties->{'editable'});
        $this->fields[] = $widget;
        $this->fieldsByName[(string)$properties->{'name'}] = $widget;
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTPassword($properties)
    {
        $widget = new TPassword((string) $properties->{'name'});
        $widget->setValue((string) $properties->{'value'});
        $widget->setSize((int) $properties->{'width'});
        $widget->setEditable((string) $properties->{'editable'});
        $this->fields[] = $widget;
        $this->fieldsByName[(string) $properties->{'name'}] = $widget;
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTDate($properties)
    {
        $widget = new TDate((string) $properties->{'name'});
        $widget->setValue((string) $properties->{'value'});
        $widget->setSize((int) $properties->{'width'});
        $widget->setEditable((string) $properties->{'editable'});
        if ((string) $properties->{'mask'})
        {
            $widget->setMask((string) $properties->{'mask'});
        }
        $this->fields[] = $widget;
        $this->fieldsByName[(string) $properties->{'name'}] = $widget;
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTSeekButton($properties)
    {
        $widget = new TSeekButton((string) $properties->{'name'});
        $widget->setSize((int) $properties->{'width'});
        
        if ( ($properties->{'database'}) AND ($properties->{'model'}) )
        {
            $obj = new TStandardSeek;
            $action = new TAction(array($obj, 'onSetup'));
            $action->setParameter('database',      (string) $properties->{'database'});
            if (isset($this->form))
            {
                if ($this->form instanceof TForm)
                {
                    $action->setParameter('parent', $this->form->getName());
                }
            }
            
            $action->setParameter('model',         (string) $properties->{'model'});
            $action->setParameter('display_field', (string) $properties->{'display'});
            $action->setParameter('receive_key',   (string) $properties->{'name'});
            $action->setParameter('receive_field', (string) $properties->{'receiver'});
            $widget->setAction($action);
        }

        $this->fields[] = $widget;
        $this->fieldsByName[(string) $properties->{'name'}] = $widget;
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTImage($properties)
    {
        $widget = new TImage((string) $properties->{'image'});
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTText($properties)
    {
        $widget = new TText((string) $properties->{'name'});
        $widget->setValue((string) $properties->{'value'});
        $widget->setSize((int) $properties->{'width'}, (int) $properties->{'height'});
        $this->fields[] = $widget;
        $this->fieldsByName[(string) $properties->{'name'}] = $widget;
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTCheckGroup($properties)
    {
        $widget = new TCheckGroup((string) $properties->{'name'});
        $widget->setLayout('vertical');
	    $pieces = explode("\n", (string) $properties->{'items'});
	    $items = array();
	    if ($pieces)
	    {
	        foreach ($pieces as $line)
	        {
    	        $part = explode(':', $line);
    	        $items[$part[0]] = $part[1];
	        }
	    }
	    $widget->addItems($items);
	    if (isset($properties->{'value'}))
	    {
	        $widget->setValue(explode(',', (string) $properties->{'value'}));
	    }
	    $this->fields[] = $widget;
	    $this->fieldsByName[(string) $properties->{'name'}] = $widget;
	    
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTDBCheckGroup($properties)
    {
        $widget = new TDBCheckGroup((string) $properties->{'name'},
                                    (string) $properties->{'database'},
                                    (string) $properties->{'model'},
                                    (string) $properties->{'key'},
                                    (string) $properties->{'display'} );
        $widget->setLayout('vertical');
	    $this->fields[] = $widget;
	    $this->fieldsByName[(string) $properties->{'name'}] = $widget;
	    
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTRadioGroup($properties)
    {
        $widget = new TRadioGroup((string) $properties->{'name'});
        $widget->setLayout('vertical');
	    $pieces = explode("\n", (string) $properties->{'items'});
	    $items = array();
	    if ($pieces)
	    {
	        foreach ($pieces as $line)
	        {
    	        $part = explode(':', $line);
    	        $items[$part[0]] = $part[1];
	        }
	    }
	    $widget->addItems($items);
	    $widget->setValue((string) $properties->{'value'});
	    $this->fields[] = $widget;
	    $this->fieldsByName[(string) $properties->{'name'}] = $widget;
	    
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTDBRadioGroup($properties)
    {
        $widget = new TDBRadioGroup((string) $properties->{'name'},
                                    (string) $properties->{'database'},
                                    (string) $properties->{'model'},
                                    (string) $properties->{'key'},
                                    (string) $properties->{'display'} );
        $widget->setLayout('vertical');
	    $this->fields[] = $widget;
	    $this->fieldsByName[(string) $properties->{'name'}] = $widget;
	    
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTCombo($properties)
    {
        $widget = new TCombo((string) $properties->{'name'});
	    $pieces = explode("\n", (string) $properties->{'items'});
	    $items = array();
	    if ($pieces)
	    {
	        foreach ($pieces as $line)
	        {
    	        $part = explode(':', $line);
    	        $items[$part[0]] = $part[1];
	        }
	    }
	    $widget->addItems($items);
	    if (isset($properties->{'value'}))
	    {
	        $widget->setValue((string) $properties->{'value'});
	    }
	    $widget->setSize((int) $properties->{'width'});
	    $this->fields[] = $widget;
	    $this->fieldsByName[(string) $properties->{'name'}] = $widget;
	    
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTDBCombo($properties)
    {
        $widget = new TDBCombo((string) $properties->{'name'},
                               (string) $properties->{'database'},
                               (string) $properties->{'model'},
                               (string) $properties->{'key'},
                               (string) $properties->{'display'} );
	    $widget->setSize((int) $properties->{'width'});
	    $this->fields[] = $widget;
	    $this->fieldsByName[(string) $properties->{'name'}] = $widget;
	    
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTNotebook($properties)
    {
        $width  = (int) $properties->{'width'};
        $height = (int) $properties->{'height'} - 30; // correction for sheet tabs
        $widget = new TNotebook($width, $height);
        if ($properties->{'pages'})
        {
            foreach ($properties->{'pages'} as $page)
            {
                $attributes = $page->attributes();
                $name  = $attributes['tab'];
                $class = get_class($this); // for inheritance
                $panel = new $class((int) $properties->{'width'} -2, (int) $properties->{'height'} -23);
                
                // pass the controller and form ahead.
                $panel->setController($this->controller);
                $panel->setForm($this->form);
                // parse element
                $panel->parseElement($page);
                // integrate the notebook' fields
                $this->fieldsByName = array_merge( (array) $this->fieldsByName, (array) $panel->getWidgets());
                $this->fields       = array_merge( (array) $this->fields,       (array) $panel->getFields());
                
                $widget->appendPage((string) $name, $panel);
            }
        }
        
        $this->fieldsByName[(string) $properties->{'name'}] = $widget;
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTFrame($properties)
    {
        $width  = PHP_SAPI == 'cli' ? (int) $properties->{'width'} -2  : (int) $properties->{'width'} -12;
        $height = PHP_SAPI == 'cli' ? (int) $properties->{'height'} -2 : (int) $properties->{'height'} -12;
        $widget = new TFrame($width, $height);
        $class = get_class($this); // for inheritance
        $panel = new $class($width, $height);
        // pass the controller and form ahead.
        $panel->setController($this->controller);
        $panel->setForm($this->form);
        
        if ($properties->{'child'})
        {
            foreach ($properties->{'child'} as $child)
            {
                $panel->parseElement($child);
                
                // integrate the frame' fields
                $this->fieldsByName = array_merge( (array) $this->fieldsByName, (array) $panel->getWidgets());
                $this->fields       = array_merge( (array) $this->fields,       (array) $panel->getFields());
            }
        }
        $widget->setLegend((string) $properties->{'title'});
        $widget->add($panel);
        $this->fieldsByName[(string) $properties->{'name'}] = $widget;
        
        return $widget;
    }
    
    /**
     * 
     */
    public function makeTDataGrid($properties)
    {
        $table  = new TTable;
        $widget = new TDataGrid;
        $widget->setHeight((string) $properties->{'height'});
        
        if ($properties->{'columns'})
        {
            foreach ($properties->{'columns'} as $Column)
            {
                $dgcolumn = new TDataGridColumn((string) $Column->{'name'},
                                                (string) $Column->{'label'},
                                                (string) $Column->{'align'},
                                                (string) $Column->{'width'} );
                $widget->addColumn($dgcolumn);
                $this->fieldsByName[(string)$Column->{'name'}] = $dgcolumn;
            }
        }
        
        if ($properties->{'actions'})
        {
            foreach ($properties->{'actions'} as $Action)
            {
                $dgaction = new TDataGridAction(array($this->controller, (string) $Action->{'method'}));
                $dgaction->setLabel((string) $Action->{'label'});
                $dgaction->setImage((string) $Action->{'image'});
                $dgaction->setField((string) $Action->{'field'});
                
                $widget->addAction($dgaction);
                //$this->fieldsByName[(string)$properties->Name] = $column;
            }
        }
        
        if ((string)$properties->{'pagenavigator'} == 'yes')
        {
            $loader = (string) $properties->{'loader'} ? (string) $properties->{'loader'} : 'onReload';
            $pageNavigation = new TPageNavigation;
            $pageNavigation->setAction(new TAction(array($this->controller, $loader)));
            $pageNavigation->setWidth($widget->getWidth());
        }
        
        $widget->createModel();
        
        $row = $table->addRow();
        $row->addCell($widget);
        if (isset($pageNavigation))
        {
            $row = $table->addRow();
            $row->addCell($pageNavigation);
            $widget->setPageNavigation($pageNavigation);
        }
        $this->fieldsByName[(string) $properties->{'name'}] = $widget;
        
        $widget = $table;
        
        return $widget;
    }
    
    /**
     * parse a xml element 
     * @param $xml SimpleXMLElement object
     * @ignore-autocomplete on
     */
    private function parseElement($xml)
    {
        try
        {
            foreach ($xml as $object)
            {
                $class = (string)$object->{'class'};
                $properties = (array)$object;
                if (in_array(ini_get('php-gtk.codepage'), array('ISO8859-1', 'ISO-8859-1') ) )
                {
                    array_walk_recursive($properties, array($this, 'arrayToIso8859'));
                }
                $properties = (object)$properties;
                
                $widget = NULL;
                
                switch ($class)
                {
                    case 'T'.'Label':
                        $widget = $this->makeTLabel($properties);
                        break;
                    case 'T'.'Button':
                        $widget = $this->makeTButton($properties);
                        break;
                    case 'T'.'Entry':
                        $widget = $this->makeTEntry($properties);
                        break;
                    case 'T'.'Password':
                        $widget = $this->makeTPassword($properties);
                        break;
                    case 'T'.'Date':
                        $widget = $this->makeTDate($properties);
                        break;
                    case 'T'.'SeekButton':
                        $widget = $this->makeTSeekButton($properties);
                        break;
                    case 'T'.'Image':
                        $widget = $this->makeTImage($properties);
                        break;
                    case 'T'.'Text':
                        $widget = $this->makeTText($properties);
                        break;
                    case 'T'.'CheckGroup':
                        $widget = $this->makeTCheckGroup($properties);
                        break;
                    case 'T'.'DBCheckGroup':
                        $widget = $this->makeTDBCheckGroup($properties);
                        break;
                    case 'T'.'RadioGroup':
                        $widget = $this->makeTRadioGroup($properties);
                        break;
                    case 'T'.'DBRadioGroup':
                        $widget = $this->makeTDBRadioGroup($properties);
                        break;
                    case 'T'.'Combo':
                        $widget = $this->makeTCombo($properties);
                        break;
                    case 'T'.'DBCombo':
                        $widget = $this->makeTDBCombo($properties);
                        break;
                    case 'T'.'Notebook':
                        $widget = $this->makeTNotebook($properties);
                        break;
                    case 'T'.'Frame':
                        $widget = $this->makeTFrame($properties);
                        break;
                    case 'T'.'DataGrid':
                        $widget = $this->makeTDataGrid($properties);
                        break;
                }
                
                if ($widget)
                {
                    parent::put($widget, (int) $properties->{'x'}, (int) $properties->{'y'});
                }
            }
        }
        catch(Exception $e)
        {
            return;
        }
    }
    
    /**
     * Converts an array to iso8859
     * @param $value current value
     * @param $key current key
     * @ignore-autocomplete on
     */
    private function arrayToIso8859(&$value, $key)
    {
        if (is_scalar($value))
        {
            $value = utf8_decode($value);
        }
    }
    
    /**
     * Defines the UI controller
     * @param $object Controller Object
     */
    public function setController($object)
    {
        $this->controller = $object;
    }
    
    /**
     * Defines the Parent Form
     * @param $object TForm
     */
    public function setForm($form)
    {
        $this->form = $form;
    }
    
    /**
     * Return the UI widgets (form fields)
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * Return the parsed widgets
     */
    public function getWidgets()
    {
        return $this->fieldsByName;
    }
    
    /**
     * Return the widget by name
     * @param $name Widget name
     */
    public function getWidget($name)
    {
        if (isset($this->fieldsByName[$name]))
        {
            return $this->fieldsByName[$name];
        }
        else
        {
            throw new Exception("Widget {$name} not found");
        } 
    }
}
?>