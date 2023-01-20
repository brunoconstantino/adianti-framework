<?php
namespace Adianti\Widget\Menu;

use Adianti\Widget\Menu\TMenuItem;
use Adianti\Widget\Base\TElement;

use SimpleXMLElement;

/**
 * Menu Widget
 *
 * @version    5.5
 * @package    widget
 * @subpackage menu
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenu extends TElement
{
    private $items;
    private $menu_class;
    private $item_class;
    private $menu_level;
    private $link_class;
    
    /**
     * Class Constructor
     * @param $xml SimpleXMLElement parsed from XML Menu
     */
    public function __construct($xml, $permission_callback = NULL, $menu_level = 1, $menu_class = 'dropdown-menu', $item_class = '', $link_class = 'dropdown-toggle')
    {
        parent::__construct('ul');
        $this->items = array();
        
        $this->{'class'}  = $menu_class . " level-{$menu_level}";
        $this->menu_class = $menu_class;
        $this->menu_level = $menu_level;
        $this->item_class = $item_class;
        $this->link_class = $link_class;
        
        if ($xml instanceof SimpleXMLElement)
        {
            $this->parse($xml, $permission_callback);
        }
    }
    
    /**
     * Add a MenuItem
     * @param $menuitem A TMenuItem Object
     */
    public function addMenuItem(TMenuItem $menuitem)
    {
        $this->items[] = $menuitem;
    }
    
    /**
     * Return the menu items
     */
    public function getMenuItems()
    {
        return $this->items;
    }
    
    /**
     * Parse a XMLElement reading menu entries
     * @param $xml A SimpleXMLElement Object
     * @param $permission_callback check permission callback
     */
    public function parse($xml, $permission_callback = NULL)
    {
        $i = 0;
        foreach ($xml as $xmlElement)
        {
            $atts     = $xmlElement->attributes();
            $label    = (string) $atts['label'];
            $action   = (string) $xmlElement-> action;
            $icon     = (string) $xmlElement-> icon;
            $menu     = NULL;
            $menuItem = new TMenuItem($label, $action, $icon, $this->menu_level);
            $menuItem->setLinkClass($this->link_class);
            
            if ($xmlElement->menu)
            {
                $menu_atts = $xmlElement->menu->attributes();
                $menu_class = !empty( $menu_atts['class'] ) ? $menu_atts['class']: $this->menu_class;
                $menu = new TMenu($xmlElement-> menu-> menuitem, $permission_callback, $this->menu_level +1, $menu_class, $this->item_class, $this->link_class);
                $menuItem->setMenu($menu);
                if ($this->item_class)
                {
                    $menuItem->{'class'} = $this->item_class;
                }
            }
            
            // just child nodes have actions
            if ( $action )
            {
                if ( !empty($action) AND $permission_callback )
                {
                    // check permission
                    $parts = explode('#', $action);
                    $className = $parts[0];
                    if (call_user_func($permission_callback, $className))
                    {
                        $this->addMenuItem($menuItem);
                    }
                }
                else
                {
                    // menus without permission check
                    $this->addMenuItem($menuItem);
                }
            }
            // parent nodes are shown just when they have valid children (with permission)
            else if ( isset($menu) AND count($menu->getMenuItems()) > 0)
            {
                $this->addMenuItem($menuItem);
            }
            
            $i ++;
        }
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {    
        if ($this->items)
        {
            foreach ($this->items as $item)
            {
                parent::add($item);
            }
        }
        parent::show();
    }
}
