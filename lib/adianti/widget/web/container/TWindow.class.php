<?php
/**
 * Window Container (JQueryDialog wrapper)
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TWindow extends TPage
{
    private $content;   // window's content
    
    function __construct()
    {
        parent::__construct();
        $this->wrapper = new TJQueryDialog;
        $this->wrapper->setUseOKButton(FALSE);
        $this->wrapper->setTitle('');
        $this->wrapper->setSize(1000, 500);
        parent::add($this->wrapper);
    }
    
    /**
     * Define the window's size
     * @param  $width  Window's width
     * @param  $height Window's height
     */
    public function setSize($width, $height)
    {
        $this->wrapper->setSize($width, $height);
    }
    
    /**
     * Define the window's title
     * @param  $title Window's title
     */
    public function setTitle($title)
    {
        $this->wrapper->setTitle($title);
    }
    
    /**
     * Add some content to the window
     * @param $content Any object that implements the show() method
     */
    public function add($content)
    {
        $this->wrapper->add($content);
    }
    
    /**
     * Close TJQueryDialog's
     */
    static public function closeWindow()
    {
        TJQueryDialog::closeAll();
    }
}
?>