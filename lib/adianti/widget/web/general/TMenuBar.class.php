<?php
/**
 * Menubar Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenuBar extends TElement
{
    public function __construct()
    {
        parent::__construct('div');
        $this->{'style'} = 'margin: 0;';
        $this->{'class'} = 'btn-toolbar';
    }
    
    /**
     * Build a MenuBar from a XML file
     * @param $xml_file path for the file
     */
    public static function newFromXML($xml_file)
    {
        TPage::include_css('lib/bootstrap/css/bootstrap-buttons.css');
        TPage::include_js('lib/bootstrap/js/bootstrap-dropdown.js');
        
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
                
                $button_div = new TElement('div');
                $button_div->{'class'} = 'btn-group';
                
                $button = new TElement('button');
                $button->{'data-toggle'} = 'dropdown';
                $button->{'class'} = 'btn dropdown-toggle';
                $button->add($label);
                
                $span = new TElement('span');
                $span->{'class'} = 'caret';
                $span->add('');
                $button->add($span);
                $menu = new TMenu($xmlElement-> menu-> menuitem);
                
                $button_div->add($button);
                $button_div->add($menu);
                $menubar->add($button_div);
            }
            
            return $menubar;
        }
    }
}
?>