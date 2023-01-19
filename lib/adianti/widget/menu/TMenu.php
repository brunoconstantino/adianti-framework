<?php
Namespace Adianti\Widget\Menu;

use Adianti\Widget\Menu\TMenuItem;

use SimpleXMLElement;
use Gtk;
use GtkMenu;

/**
 * Menu Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage menu
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenu extends GtkMenu
{
    /**
     * Class Constructor
     * @param $xml SimpleXMLElement parsed from XML Menu
     */
    public function __construct($xml)
    {
        parent::__construct();
        $this->items = array();
        
        if ($xml instanceof SimpleXMLElement)
        {
            $this->parse($xml);
        }
    }
    
    /**
     * Add a MenuItem
     * @param $menuitem A TMenuItem Object
     */
    public function addMenuItem(TMenuItem $menuItem)
    {
        parent::append($menuItem);
    }
    
    /**
     * Parse a XMLElement reading menu entries
     * @param $xml A SimpleXMLElement Object
     * @ignore-autocomplete on
     */
    private function parse($xml)
    {
        $i = 0;
        foreach ($xml as $xmlElement)
        {
            $atts    = $xmlElement->attributes();
            $label   = (string) $atts['label'];
            $action  = (string) $xmlElement-> action;
            $icon    = (string) $xmlElement-> icon;
            $toolkit = (string) $xmlElement-> toolkit;
            
            if ($toolkit !== 'web')
            {
                if (in_array(ini_get('php-gtk.codepage'), array('ISO8859-1', 'ISO-8859-1') ) )
                {
                    $label = utf8_decode($label);
                }
                
                $menuItem = new TMenuItem($label, $action, $icon);
                $this->addMenuItem($menuItem);
                 
                if ($xmlElement->menu)
                {
                    $menu=new TMenu($xmlElement-> menu-> menuitem);
                    $menuItem->setMenu($menu);
                }
                $i ++;
            }
        }
    }
}
