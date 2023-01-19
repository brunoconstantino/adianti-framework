<?php
Namespace Adianti\Widget\Util;

use Gtk;
use GtkFrame;
use GtkHBox;
use GtkImage;
use GtkLabel;
use GtkArrow;

/**
 * BreadCrumb
 *
 * @version    2.0
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TBreadCrumb extends GtkFrame
{
    protected static $homeController;
    protected $container;
    protected $items;
    
    /**
     * Handle paths from a XML file
     * @param $xml_file path for the file
     */
    public function __construct()
    {
        parent::__construct();
        parent::set_border_width(4);
        
        $this->container = new GtkHBox;
        $this->container->set_border_width(4);
        parent::add($this->container);
    }
    
    /**
     * Add the home icon
     */
    public function addHome()
    {
        $image = GtkImage::new_from_file('lib/adianti/include/tbreadcrumb/home.png');
        $this->container->pack_start($image, FALSE, FALSE);
    }
    
    /**
     * Add an item
     * @param $path Path to be shown
     * @param $last If the item is the last one
     */
    public function addItem($path, $last = FALSE)
    {
        $this->items[$path] = new GtkLabel($path);
        $this->container->pack_start(new GtkArrow(Gtk::ARROW_RIGHT,Gtk::SHADOW_NONE), FALSE, FALSE);
        $this->container->pack_start($this->items[$path], FALSE, FALSE);
    }
    
    /**
     * Mark one breadcrumb item as selected
     */
    public function select($path)
    {
        foreach ($this->items as $key => $label)
        {
            if ($key == $path)
            {
                $label->set_markup("<span foreground=\"blue\"> $key </span>");
            }
            else
            {
                $label->set_text($key);
            }
        }
    }
    
    /**
     * Define the home controller
     * @param $class Home controller class
     */
    public static function setHomeController($className)
    {
        self::$homeController = $className;
    }
}
