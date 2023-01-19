<?php
/**
 * Image Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TImage extends TElement
{
    private $source; // image path
    
    /**
     * Class Constructor
     * @param $source Image path
     */
    public function __construct($source)
    {
        parent::__construct('img');
        // assign the image path
        $this-> src = $source;
        $this-> border = 0;
    }
}
?>