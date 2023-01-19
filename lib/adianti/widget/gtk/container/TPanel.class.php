<?php
/**
 * Panel Container: Allows to organize the widgets using fixed (absolute) positions
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
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
?>