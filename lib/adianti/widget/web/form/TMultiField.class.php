<?php
/**
 * MultiField Widget: Takes a group of input fields and gives them the possibility to register many occurrences
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMultiField extends TField
{
    private $fields;
    private $objects;
    private $height;
    private $width;
    private $className;
    private $formName;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        // define some default properties
        self::setEditable(TRUE);
        self::setName($name);
        $this->fields = array();
        $this->height = 100;
    }

    /**
     * Define the name of the form to wich the multifield is attached
     * @param $name    A string containing the name of the form
     * @ignore-autocomplete on
     */
    public function setFormName($name)
    {
        $this->formName = $name;
        
        if ($this->fields)
        {
            foreach($this->fields as $name => $field)
            {
                $obj = $field->{'field'};
                if ($obj instanceof TSeekButton)
                {
                    $obj->setFormName($this->formName);
                }
            }
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
    public function addField($name, $text, TField $object, $size, $inform = TRUE)
    {
        $obj = new StdClass;
        $obj-> name      = $name;
        $obj-> text      = $text;
        $obj-> field     = $object;
        $obj-> size      = $size;
        $obj-> inform    = $inform;
        $this->width   += $size;
        $this->fields[$name] = $obj;
    }
    
    /**
     * Define the class for the Active Records returned by this component
     * @param $class Class Name
     */
    public function setClass($class)
    {
        $this->className = $class;
    }
    
    /**
     * Returns the class defined by the setClass() method
     * @return the class for the Active Records returned by this component
     */
    public function getClass()
    {
        return $this->className;
    }
    
    /**
     * Define the MultiField content
     * @param $objects A Collection of Active Records
     */
    public function setValue($objects)
    {
        $this->objects = $objects;
        
        // This block is executed just to call the
        // getters like get_virtual_property()
        // inside the transaction (when the attribute)
        // is set, and not after all (during the show())
        if ($objects)
        {
            foreach ($this->objects as $object)
            {
                foreach($this->fields as $name => $obj)
                {
                    $object->$name; // regular attribute
                    if ($obj-> field instanceof TComboCombined)
                    {
                        $attribute = $obj-> field->getTextName();
                        $object->$attribute; // auxiliar attribute
                    }
                }
            }
        }
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            $val = $_POST[$this->name];
            $className = $this->getClass() ? $this->getClass() : 'stdClass';
            $decoded   = JSON_decode(stripslashes($val));
            unset($items);
            unset($obj_item);
            $items = array();
            foreach ($decoded as $std_object)
            {
                $obj_item = new $className;
                foreach ($std_object as $subkey=>$value)
                {
                    //substitui pq o ttable gera com quebra de linha no multifield
                    //$obj_item->$subkey = URLdecode($value);
                    $obj_item->$subkey = str_replace("\n",'',URLdecode($value));
                }
                $items[] = $obj_item;
            }
            return $items;
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Define the MultiField height
     * @param $height Height in pixels
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        // include the needed libraries and styles
        TPage::include_css('lib/adianti/include/tmultifield/tmultifield.css');
        TPage::include_js('lib/adianti/include/tmultifield/tmultifield.js');
        
        if ($this->fields)
        {
            $table = new TTable;
            
            $mdr=array(); // mandatory
            $fields=array();
            $i=0;
            foreach($this->fields as $name => $obj)
            {
                $row=$table->addRow();
                $label = new TLabel($obj-> text);
                if ($obj-> inform)
                {
                    $row->addCell($label);
                    $row->addCell($obj-> field);
                }
                
                $fields[] = $name;
                $post_fields[$name] = 1;
                
                $obj-> field->setName($this->name.'_'.$name);
                if (get_class($obj-> field) == 'TComboCombined')
                {
                    $fields[] = $obj-> field->getTextName();
                    $obj-> field->setTextName($this->name.'_'.$obj-> field->getTextName());
                    
                    $i++;
                }
                $i++;
            }
            $table->show();
        }
        // check whether the widget is non-editable
        if (parent::getEditable())
        {
            // create three buttons to control the MultiField
            $add = new TButton("{$this->name}btnStore");
            $add->setLabel(TAdiantiCoreTranslator::translate('Register'));
            //$add->setName("{$this->name}btnStore");
            $add->setImage('ico_save.png');
            $add->addFunction("mtf{$this->name}.addRowFromFormFields()");
            
            $del = new TButton("{$this->name}btnDelete");
            $del->setLabel(TAdiantiCoreTranslator::translate('Delete'));
            $del->setImage('ico_delete.png');
            
            $can = new TButton("{$this->name}btnCancel");
            $can->setLabel(TAdiantiCoreTranslator::translate('Cancel'));
            $can->setImage('ico_close.png');
            
            $table = new TTable;
            $row=$table->addRow();
            $row->addCell($add);
            $row->addCell($del);
            $row->addCell($can);
            $table->show();
        }
        
        // create the MultiField Panel
        $panel = new TElement('div');
        $panel->{'class'} = "multifieldDiv";
        
        $input = new THidden($this->name);
        $panel->add($input);
        
        // create the MultiField DataGrid Header
        $table = new TTable;
        $table-> id="{$this->name}mfTable";
        $head = new TElement('thead');
        $table->add($head);
        $row = new TTableRow;
        $head->add($row);
        
        // fill the MultiField DataGrid
        foreach($this->fields as $obj)
        {
            $c = $obj-> text;
            if (get_class($obj-> field) == 'TComboCombined')
            {
                $row->addCell('ID');
            }
            $cell = $row->addCell($c);
            $cell-> width=$obj-> size.'px';
        }
        $body = new TElement('tbody');
        $table->add($body);
        
        if ($this->objects)
        {
            foreach($this->objects as $obj)
            {
                if (isset($obj-> id))
                {
                    $row = new TTableRow;
                    $row-> dbId=$obj-> id;
                    $body->add($row);
                }
                else
                {
                    $row = new TTableRow;
                    $body->add($row);
                }
                foreach($fields as $name)
                {
                    $cell = $row->addCell($obj->$name);
                    if (isset($obj-> size))
                    {
                        $cell-> width=$obj-> size.'px';
                    }
                }
            }
        }
        $panel->add($table);
        $panel->show();
        
        echo '<script type="text/javascript">';
        echo "var mtf{$this->name};";
        //echo '$(document).ready(function() {';
        echo "mtf{$this->name} = new MultiField('{$this->name}mfTable',{$this->width},{$this->height});\n";
        $s = implode("','",$fields);
        echo "mtf{$this->name}.formFieldsAlias = Array('{$s}');\n";
        $fields = implode("','{$this->name}_",$fields);
        echo "mtf{$this->name}.formFieldsName = Array('{$this->name}_{$fields}');\n";
        echo "mtf{$this->name}.formPostFields = Array();\n";
        foreach ($post_fields as $col =>$value)
        {
            echo "mtf{$this->name}.formPostFields['{$col}'] = '$value';\n";
        }
            
        $mdr = implode(',',$mdr);
        echo "mtf{$this->name}.formFieldsMandatory = Array({$mdr});\n";
        echo "mtf{$this->name}.storeButton  = document.getElementsByName('{$this->name}btnStore')[0];\n";
        echo "mtf{$this->name}.deleteButton = document.getElementsByName('{$this->name}btnDelete')[0];\n";
        echo "mtf{$this->name}.cancelButton = document.getElementsByName('{$this->name}btnCancel')[0];\n";
        echo "mtf{$this->name}.inputResult  = document.getElementsByName('{$this->name}')[0];\n";
        //echo '});';
        echo '</script>';
    }
}
?>