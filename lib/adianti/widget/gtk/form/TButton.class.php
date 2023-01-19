<?php
/**
 * Button Widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TButton extends GtkButton
{
    private $wname;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct();
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
    
    // empty, for compability reasons
    public function setFormName() {}
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($size)
    {
        $this->set_size_request($size,-1);
    }
    
    /**
     * Define if the widget is editable
     * @param $boolean A boolean
     */
    public function setEditable($bool)
    {
        $this->set_sensitive($bool);
    }
    
    /**
     * Define the action of the button
     * @param  $action  TAction object
     * @param  $label   Button's label
     */
    public function setAction(TAction $action, $label)
    {
        parent::set_label($label);
        parent::connect_simple('clicked', array($this, 'onExecute' ), $action);
    }
    
    /**
     * Define the icon of the button
     * @param  $image  image path
     */
    public function setImage($image)
    {
        if (file_exists('lib/adianti/images/'.$image))
        {
            $imagem = GtkImage::new_from_file('lib/adianti/images/'.$image);
        }
        else
        {
            $imagem = GtkImage::new_from_file('app/images/'.$image);
        }
        parent::set_image($imagem);
    }
    
    /**
     * Define the label of the button
     * @param  $label button label
     */
    public function setLabel($label)
    {
        parent::set_label($label);
    }
    
    /**
     * Execute the action
     * @param  $action callback to be executed
     * @ignore-autocomplete on
     */
    public function onExecute($action)
    {
        $callb = $action->getAction();
        
        if (is_object($callb[0]))
        {
            $object = $callb[0];
            call_user_func($callb, $action->getParameters());
            
            //aquip, este IF estava acima do call_user_func
            if (method_exists($object, 'show'))
            {
                if ($object->get_child())
                {
                    $object->show();
                }
            }
        }
        else
        {
            $class  = $callb[0];
            $method = $callb[1];
            TApplication::executeMethod($class, $method, $action->getParameters());
        }
    }
}
?>