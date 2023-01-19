<?php
/**
 * SourceCode View
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSourceCode extends GtkSourceView
{
    private $buffer;
    
    /**
     * Constructor Method
     */
    public function __construct()
    {
        parent::__construct();
        $lang_manager = new GtkSourceLanguagesManager;
        $lang = $lang_manager->get_language_from_mime_type('application/x-php');
        
        $this->buffer = new GtkSourceBuffer;
        $this->buffer->set_language($lang);
        $this->buffer->set_highlight(true);
        $this->buffer->set_check_brackets(TRUE);
        $this->set_buffer($this->buffer);
        
        $default_font = (OS=='WIN') ? 'Courier 10' : 'Monospace 10';
        $pango = new PangoFontDescription($default_font);
        $this->modify_font($pango);
        
        $this->set_auto_indent(TRUE);
        $this->set_insert_spaces_instead_of_tabs(TRUE);
        $this->set_tabs_width(4);
        $this->set_show_margin(FALSE);
        $this->set_show_line_numbers(true);
        $this->set_show_line_markers(true);
        $this->set_highlight_current_line(TRUE);
        $this->set_smart_home_end(TRUE);
    }
    
    /**
     * Load a PHP file
     * @param $file Path to the PHP file
     */
    public function loadFile($file)
    {
        if (!file_exists($file))
        {
            return;
        }
        
        $content = file_get_contents($file);
        if (utf8_encode(utf8_decode($content)) == $content ) // SE UTF8
        {
            $content = utf8_decode($content);
        }
        
        $this->buffer->begin_not_undoable_action();
        $this->buffer->insert_at_cursor($content);
        $this->buffer->end_not_undoable_action();
        return TRUE;
    }
}
?>