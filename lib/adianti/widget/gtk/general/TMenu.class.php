<?php
/**
 * Menu Widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
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
            $atts   = $xmlElement->attributes();
            $label  = (string) $atts['label'];
            $action = (string) $xmlElement-> action;
            $icon   = (string) $xmlElement-> icon;
             
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
?>