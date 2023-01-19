<?php
Namespace Adianti\Widget\Dialog;

use Adianti\Control\TAction;

use Gtk;
use GtkHbox;
use GtkDialog;
use GtkTextView;
use GtkTextTag;
use GtkImage;
use GtkScrolledWindow;
use PangoFontDescription;

/**
 * Question Dialog
 *
 * @version    2.0
 * @package    widget
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TQuestion extends GtkDialog
{
    /**
     * Class Constructor
     * @param  $message    A string containint the question
     * @param  $action_yes Action taken for YES response
     * @param  $action_no  Action taken for NO  response
     */
    public function __construct($message, TAction $action_yes, TAction $action_no = NULL)
    {
        $buttons = array(Gtk::STOCK_YES, Gtk::RESPONSE_YES);
        if ($action_no instanceof TAction)
        {
            $buttons[] = Gtk::STOCK_NO;
            $buttons[] = Gtk::RESPONSE_NO;
        }
        $buttons[] = Gtk::STOCK_CANCEL;
        $buttons[] = Gtk::RESPONSE_CANCEL;
        
        parent::__construct('', NULL, Gtk::DIALOG_MODAL, $buttons);
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

        $tagBegin= $textbuffer->create_mark('tagBegin', $textbuffer->get_end_iter(), true);
        $textbuffer->insert_at_cursor("\n   ".$message);
        $tagEnd = $textbuffer->create_mark('tagEnd', $textbuffer->get_end_iter(), true);
        $start  = $textbuffer->get_iter_at_mark($tagBegin);
        $end    = $textbuffer->get_iter_at_mark($tagEnd);
        $textbuffer->apply_tag($customTag, $start, $end);
        
        $textview->set_editable(FALSE);
        $textview->set_cursor_visible(FALSE);
        $pango = new PangoFontDescription('Sans 14');
        $textview->modify_font($pango);
        $image = GtkImage::new_from_stock(Gtk::STOCK_DIALOG_QUESTION, Gtk::ICON_SIZE_DIALOG);
        
        $scroll = new GtkScrolledWindow;
        $scroll->set_policy(Gtk::POLICY_NEVER, Gtk::POLICY_ALWAYS);
        $scroll->add($textview);
        $hbox = new GtkHBox;
        $this-> vbox->pack_start($hbox);
        $hbox->pack_start($image, FALSE, FALSE);
        $hbox->pack_start($scroll, TRUE, TRUE);
        $this->show_all();
        
        $result = parent::run();
        
        if ($result == Gtk::RESPONSE_YES)
        {
            parent::destroy();
            call_user_func_array($action_yes->getAction(),
                     array($action_yes->getParameters()));
        }
        else if ($result == Gtk::RESPONSE_NO)
        {
            parent::destroy();
            call_user_func_array($action_no->getAction(),
                     array($action_no->getParameters()));
        }
        else
        {
            parent::destroy();
        }
    }
}
