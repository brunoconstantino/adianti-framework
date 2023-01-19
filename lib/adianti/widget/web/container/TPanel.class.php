<?php
/**
 * Panel Container: Allows to organize the widgets using fixed (absolute) positions
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPanel extends TElement
{
    static private $counter;
    private $panelId;
    
    /**
     * Class Constructor
     * @param  $width   Panel's width
     * @param  $height  Panel's height
     */
    public function __construct($width, $height)
    {
        self::$counter ++;
        
        // creates the panel style
        $painel_style = new TStyle('tpanel'.self::$counter);
        $painel_style-> position         = 'relative';
        $painel_style-> width            = $width.'px';
        $painel_style-> height           = $height.'px';
        
        // show the style
        $painel_style->show();
        $this->panelId = self::$counter;
        
        parent::__construct('div');
        $this->{'class'} = 'tpanel'.self::$counter;
    }
    
    /**
     * Put a widget inside the panel
     * @param  $widget = widget to be shown
     * @param  $col    = column in pixels.
     * @param  $row    = row in pixels.
     */
    public function put($widget, $col, $row)
    {
        // creates a layer to put the widget inside
        $camada = new TElement('div');
        // define the layer position
        $camada-> style = "position:absolute; left:{$col}px; top:{$row}px;";
        // add the widget to the layer
        $camada->add($widget);
        
        // add the widget to the element's array
        parent::add($camada);
    }
}
?>