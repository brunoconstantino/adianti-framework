<?php
/**
 * Menubar Widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenuBar extends GtkHBox
{
    /**
     * Build a MenuBar from a XML file
     * @param $xml_file path for the file
     */
    public static function newFromXML($xml_file)
    {
        if (file_exists($xml_file))
        {
            $menu_string = file_get_contents($xml_file);
            if (utf8_encode(utf8_decode($menu_string)) == $menu_string ) // SE UTF8
            {
                $xml = new SimpleXMLElement($menu_string);
            }
            else
            {
                $xml = new SimpleXMLElement(utf8_encode($menu_string));
            }
            $menubar = new TMenuBar;
            foreach ($xml as $xmlElement)
            {
                $atts   = $xmlElement->attributes();
                $label  = (string) $atts['label'];
                $action = (string) $xmlElement-> action;
                $icon   = (string) $xmlElement-> icon;
                
                $menuItem = new TMenuItem($label, $action, $icon);
                $menubar->append($menuItem, $xmlElement-> menu-> menuitem);
            }
            
            return $menubar;
        }
    }
    
    /**
     * Append an item to the menu
     */
    public function append($item, $submenu)
    {
        $button = new GtkToggleButton;
        $button->set_label($item->getLabel());
        $handler = $button->connect('clicked', array($this, 'onExecute'), $item, $submenu);
        $button->set_data('handler', $handler);
        $this->pack_start($button, FALSE, FALSE);
    }
    
    /**
     * Execute an item callback
     */
    public function onExecute($widget, $item, $submenu = null)
    {
        $menu = new TMenu($submenu);
        $item->set_submenu($menu);
        $menu = $item->get_submenu();
        $menu->connect_simple('deactivate', array($widget, 'set_active'), FALSE);
        $menu->connect_simple('deactivate', array($widget, 'unblock'), $widget->get_data('handler'));
        $widget->block($widget->get_data('handler'));
        $menu->show_all();
        $menu->popup(null, null, array($this, 'popupGetPosition'), 0, 0, $widget);
    }
    
    /**
     * Obtém as coordenadas para o menu
     */
    function popupGetPosition($w)
    {
        $plusx=0;
        $plusy=0;
        
        if (OS == 'WIN')
        {
            $plusx=10;
            $plusy=10;
        }
        
        if (OS == 'WIN')
        {
            $position = $w->get_toplevel()->get_position(); // posição Y sem as decorações
            $position[1] += 20;
        }
        else
        {
            $w->get_toplevel()->realize();
            $position = $w->get_toplevel()->window->get_origin(); // posição Y com as decorações
        }
        
        return array($position[0] + $w->get_allocation()-> x + $plusx,   // trick
                     $position[1] + $w->get_allocation()-> y + $plusy +  // trick
                                    $w->get_allocation()-> height, true);
    }
}
?>