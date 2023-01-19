<?php
Namespace Adianti\Widget\Util;

use Adianti\Widget\Container\TTable;
use Adianti\Widget\Container\TScroll;
use Adianti\Control\TWindow;

use Gtk;
use GtkLabel;
use GdkColor;

/**
 * Exception visualizer
 *
 * @version    2.0
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TExceptionView extends TWindow
{
    /**
     * Constructor method
     */
    function __construct($e)
    {
        parent::__construct('Error Report');
        parent::connect_simple('destroy', array('Gtk', 'main_quit'));
        parent::setPosition(200,100);
        parent::setSize(700,400);
        $this->e = $e;
        
        $this->show();
    }
    
    /**
     * Shows the exception stack
     */
    function show()
    {
        $error_array = $this->e->getTrace();
        $table = new TTable;
        $row=$table->addRow();
        $message = $this->e->getMessage();
        $message = str_replace('<br>', "\n", $message);
        $title = new GtkLabel;
        $title->set_markup('<b>General Error: ' . $message .'</b>'."\n");
        $row->addCell($title);
        
        foreach ($error_array as $error)
        {
            $file = isset($error['file']) ? $error['file'] : '';
            $line = isset($error['line']) ? $error['line'] : '';
            $file = str_replace(PATH, '', $file);
            
            $row=$table->addRow();
            $row->addCell('File: '.$file. ' : '. $line);
            $row=$table->addRow();
            $args = array();
            if ($error['args'])
            {
                foreach ($error['args'] as $arg)
                {
                    if (is_object($arg))
                    {
                        $args[] = get_class($arg). ' object';
                    }
                    else if (is_array($arg))
                    {
                        $array_param = array();
                        foreach ($arg as $value)
                        {
                            if (is_object($value))
                            {
                                $array_param[] = get_class($value);
                            }
                            else if (is_array($value))
                            {
                                $array_param[] = 'array';
                            }
                            else
                            {
                                $array_param[] = $value;
                            }
                        }
                        $args[] = implode(',', $array_param);
                    }
                    else
                    {
                        $args[] = (string) $arg;
                    }
                }
            }
            $label = new GtkLabel;
            $row->addCell($label);
            
            $class = isset($error['class']) ? $error['class'] : '';
            $type  = isset($error['type'])  ? $error['type']  : '';
            
            $label->set_markup('  <i>'.'<span foreground="#78BD4C">'.$class.'</span>'.
                               '<span foreground="#600097">'.$type.'</span>'.
                               '<span foreground="#5258A3">'.$error['function'].'</span>'.
                               '('.'<span foreground="#894444">'.implode(',', $args).'</span>'.')</i>');
        }
        $scroll=new TScroll;
        $scroll->setSize(690,390);
        $scroll->add($table);
        $scroll-> child->modify_bg(GTK::STATE_NORMAL, GdkColor::parse('#ffffff'));
        parent::add($scroll);
        parent::show();
    }
}
