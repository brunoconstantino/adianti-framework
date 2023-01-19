<?php
Namespace Adianti\Widget\Container;

use Gtk;
use GtkFixed;

/**
 * Panel Container: Allows to organize the widgets using fixed (absolute) positions
 *
 * @version    2.0
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPanel extends GtkFixed
{
    private $showed;
    
    /**
     * Class Constructor
     * @param  $width   Panel's width
     * @param  $height  Panel's height
     */
    public function __construct($width, $height)
    {
        parent::__construct();
        parent::set_size_request($width, $height);
        $this->showed = FALSE;
    }
    
    /**
     * Set the panel size
     * @param $width Panel widgh
     * @param $height Panel height
     */
    public function setSize($width, $height)
    {
        parent::set_size_request($width, $height);
    }
    
    /**
     * Show the table and all aggregated rows
     */
    public function show()
    {
        $children = parent::get_children();
        foreach ($children as $child)
        {
            $child->show();
        }
        $this->showed = TRUE;
        parent::show();
    }
}
