<?php
Namespace Adianti\Widget\Container;

use Gtk;
use GtkExpander;

/**
 * Expander Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TExpander extends GtkExpander
{
    private $content;
    private $wrapper;
    
    /**
     * Class Constructor
     * @param  $value text label
     */
    public function __construct($label = '')
    {
        parent::__construct($label);
    }
    
    /**
     * Add content to the expander
     * @param $content Any Object that implements show() method
     */
    public function add($content)
    {
        $this->content = $content;
        parent::add($content);
    }
    
    /**
     * Define a button property.
     */
    public function setButtonProperty($property, $value)
    {
        // Just for BC.
    }
    
    public function show()
    {
        $this->content->show();
        parent::show();
    }
}
