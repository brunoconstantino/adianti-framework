<?php
Namespace Adianti\Widget\Menu;

use Adianti\Core\AdiantiCoreApplication;
use Adianti\Widget\Menu\TMenu;
use Adianti\Widget\Util\TImage;

use Gtk;
use GtkImageMenuItem;
use GtkImage;

/**
 * MenuItem Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage menu
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenuItem extends GtkImageMenuItem
{
    private $label;
    private $action;
    private $image;
    
    /**
     * Class Constructor
     * @param $label  The menu label
     * @param $action The menu action
     * @param $image  The menu image
     */
    public function __construct($label, $action, $image = NULL)
    {
        parent::__construct($label); // converts into ISO
        parent::set_image(null);
        if (OS=='WIN')
        {
            parent::set_border_width(3);
        }
        $this->label  = $label;
        $this->action = $action;
        $this->image  = $image;
        
        if (file_exists($image))
        {
            parent::set_image(GtkImage::new_from_file($image));
        }
        $inst = AdiantiCoreApplication::getInstance();
        if ($inst instanceof AdiantiCoreApplication)
        {
            parent::connect_simple('activate', array($inst, 'run'), $action);
        }
    }
    
    
    /**
     * Returns the item's label
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Returns the item's action
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Returns the item's image
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Define the submenu for the item
     * @param $menu A TMenu object
     */
    public function setMenu(TMenu $menu)
    {
        parent::set_submenu($menu);
    }
}
