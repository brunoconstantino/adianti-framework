<?php
/**
 * Notebook Widget: A container area with tabs that allows you to append pages and put any element inside each page
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TNotebook extends GtkNotebook
{
    private $contents;
    
    /**
     * Class Constructor
     * @param $width  Notebook's width
     * @param $height Notebook's height
     */
    public function __construct($width = 500, $height = 650)
    {
        parent::__construct();
        parent::set_size_request($width, $height+30);
    }
    
    /**
     * Set the notebook size
     * @param $width  Notebook's width
     * @param $height Notebook's height
     */
    public function setSize($width, $height)
    {
        parent::set_size_request($width, $height+30);
    }
    
    /**
     * Define the current page to be shown
     * @param $i An integer representing the page number (start at 0)
     */
    public function setCurrentPage($i)
    {
        parent::set_current_page($i);
    }

    /**
     * Return the Page count
     */
    public function getPageCount()
    {
        return parent::get_n_pages();
    }

    /**
     * Add a tab to the notebook
     * @param $title   tab's title
     * @param $object  tab's content
     */
    public function appendPage($title, $object)
    {
        if (method_exists($object, 'set_border_width'))
        {
            $object->set_border_width(4);
        }
        parent::append_page($object, new GtkLabel($title));
        $this->contents[] = $object;
    }
    
    /**
     * Show the notebook at the screen
     */
    public function show()
    {
        foreach ($this->contents as $content)
        {
            $content->show();
        }
        parent::show();
    }
}
?>