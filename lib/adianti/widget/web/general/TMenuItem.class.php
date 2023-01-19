<?php
/**
 * MenuItem Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenuItem extends TElement
{
    private $label;
    private $action;
    private $image;
    private $menu;
    
    /**
     * Class constructor
     * @param $label  The menu label
     * @param $action The menu action
     * @param $image  The menu image
     */
    public function __construct($label, $action, $image = NULL)
    {
        parent::__construct('li');
        $this->label  = $label;
        $this->action = $action;
        if (file_exists($image))
        {
            $this->image  = $image;
        }
    }
    
    /**
     * Define the submenu for the item
     * @param $menu A TMenu object
     */
    public function setMenu(TMenu $menu)
    {
        $this->menu = $menu;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $link = new TElement('a');
        
        if ($this->action)
        {
            $url['class'] = $this->action;
            $url_str = http_build_query($url);
            $link-> href = "index.php?{$url_str}";
            $link-> generator = 'adianti';
        }
        else
        {
            $link-> href = '#';
        }
        
        if (isset($this->image))
        {
            $image = new TImage($this->image);
            $image->style = 'padding-right: 8px';
            $link->add($image);
        }
        $link->add($this->label); // converts into ISO
        $this->add($link);
        
        if ($this->menu instanceof TMenu)
        {
            $this->{'class'} = 'dropdown-submenu';
            parent::add($this->menu);
        }
        
        parent::show();
    }
}
?>