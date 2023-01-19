<?php
Namespace Adianti\Widget\Dialog;

use Adianti\Control\TAction;

use Gtk;
use Gdk;
use GtkHBox;
use GtkDialog;
use GtkTextView;
use GtkTextTag;
use GtkImage;
use GtkScrolledWindow;
use PangoFontDescription;

/**
 * Message Dialog
 *
 * @version    2.0
 * @package    widget
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMessage extends GtkDialog
{
    /**
     * Class Constructor
     * @param $type    Type of the message (info, error)
     * @param $message Message to be shown
     * @param $action  Action to be processed when closing the dialog
     */
    public function __construct($type, $message, TAction $action = NULL)
    {
        parent::__construct('', NULL, Gtk::DIALOG_MODAL, 
                                  array(Gtk::STOCK_OK, Gtk::RESPONSE_OK));
        parent::set_position(Gtk::WIN_POS_CENTER);
        parent::set_size_request(500, 300);
        
        $textview=new GtkTextView;
        $textview->set_wrap_mode(Gtk::WRAP_WORD);
        $textview->set_border_width(12);
        $textbuffer=$textview->get_buffer();
        
        $tagtable=$textbuffer->get_tag_table();
        $customTag = new GtkTextTag;
        $tagtable->add($customTag);
        $customTag->set_property('foreground', '#525252');
        
        $message = "\n   " . str_replace('<br>', "\n   ", $message);
        $tagBegin= $textbuffer->create_mark('tagBegin', $textbuffer->get_end_iter(), true);
        $textbuffer->insert_at_cursor(strip_tags($message));
        $tagEnd = $textbuffer->create_mark('tagEnd', $textbuffer->get_end_iter(), true);
        $start  = $textbuffer->get_iter_at_mark($tagBegin);
        $end    = $textbuffer->get_iter_at_mark($tagEnd);
        $textbuffer->apply_tag($customTag, $start, $end);
        
        $textview->set_editable(FALSE);
        $textview->set_cursor_visible(FALSE);
        $pango = new PangoFontDescription('Sans 14');
        $textview->modify_font($pango);
        $image = $type=='info' ? GtkImage::new_from_stock(Gtk::STOCK_DIALOG_INFO, Gtk::ICON_SIZE_DIALOG):
                                 GtkImage::new_from_stock(Gtk::STOCK_DIALOG_ERROR,Gtk::ICON_SIZE_DIALOG);
        $scroll = new GtkScrolledWindow;
        $scroll->set_policy(Gtk::POLICY_NEVER, Gtk::POLICY_ALWAYS);
        $scroll->add($textview);
        $hbox = new GtkHBox;
        $this-> vbox->pack_start($hbox);
        $hbox->pack_start($image, FALSE, FALSE);
        $hbox->pack_start($scroll, TRUE, TRUE);
        $this->show_all();
        parent::connect('key_press_event', array($this, 'onClose'));
        
        $result = parent::run();
        if ($result == Gtk::RESPONSE_OK)
        {
            if ($action)
            {
                $parameters = $action->getParameters();
                parent::destroy();
                call_user_func_array($action->getAction(), array($parameters));
                return;
            }
        }
        parent::destroy();
    }
    
    /**
     * Executed when the user hits any key
     * @param $widget Source widget of the event
     * @param $event  GdkEvent associated
     * @ignore-autocomplete on
     */
    public function onClose($widget, $event)
    {
        if ($event->keyval == Gdk::KEY_Escape)
        {
            parent::hide();
        }
    }
}
