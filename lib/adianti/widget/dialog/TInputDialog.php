<?php
Namespace Adianti\Widget\Dialog;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Wrapper\TQuickForm;

use Exception;
use Gtk;
use Gdk;
use GtkDialog;

/**
 * Input Dialog
 *
 * @version    2.0
 * @package    widget
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TInputDialog extends GtkDialog
{
    /**
     * Class Constructor
     * @param $caption Button caption
     * @param $form    Dialog form body
     * @param $action  Action to be processed when closing the dialog
     * @param $title_msg  Dialog Title
     */
    public function __construct($title_msg, TForm $form, TAction $action = NULL, $caption = '')
    {
        parent::__construct('', NULL, Gtk::DIALOG_MODAL);
        parent::set_position(Gtk::WIN_POS_CENTER);
        parent::set_title($title_msg ? $title_msg : AdiantiCoreTranslator::translate('Input'));
        
        $actions = array();
        $action_counter = 0;
        if ($form instanceof TQuickForm)
        {
            $form->delActions();
            $actionButtons = $form->getActionButtons();
            
            foreach ($actionButtons as $key => $button)
            {
                parent::add_button($button->getLabel(), $action_counter);
                $actions[] = $button->getAction();
                $action_counter ++;
            }
        }
        else
        {
            parent::add_button($caption, $action_counter);
            $actions[] = $action;
        }
        
        $this-> vbox->pack_start($form);
        
        $form->show();
        $this->show_all();
        parent::connect('key_press_event', array($this, 'onClose'));
        
        $result = parent::run();
        foreach ($actions as $actionIndex => $buttonAction)
        {
            if ($result == $actionIndex)
            {
                if ($buttonAction)
                {
                    $parameters = $buttonAction->getParameters();
                    $data = $form->getData();
                    foreach ($data as $key => $value)
                    {
                        $parameters[$key] = $value;
                    }
                    parent::destroy();
                    call_user_func_array($buttonAction->getAction(), array($parameters));
                    return;
                }
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
