<?php
/**
 * Image Widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TImage extends GtkImage
{
    /**
     * Class Constructor
     * @param $image Image path
     */
    public function __construct($image)
    {
        parent::__construct();
        $pixbuf = GdkPixbuf::new_from_file($image);
        parent::set_from_pixbuf($pixbuf);
    }
}
?>