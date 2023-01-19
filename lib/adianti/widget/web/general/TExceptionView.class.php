<?php
/**
 * Exception visualizer
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
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
        parent::setPosition(200,100);
        parent::setSize(700,400);
        $this->e = $e;
    }
    
    /**
     * Shows an exception stack
     */
    function show()
    {
        $error_array = $this->e->getTrace();
        $table = new TTable;
        $row=$table->addRow();
        $row->addCell('<b>General Error: ' . $this->e->getMessage(). '<br>');
        $row=$table->addRow();
        $row->addCell('&nbsp;');
        
        foreach ($error_array as $error)
        {
            $file = isset($error['file']) ? $error['file'] : '';
            $line = isset($error['line']) ? $error['line'] : '';
            
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
            $class = isset($error['class']) ? $error['class'] : '';
            $type  = isset($error['type'])  ? $error['type']  : '';
            
            $row->addCell('&nbsp;&nbsp;<i>'.'<font color=green>'.$class.'</font>'.
                                            '<font color=olive>'.$type.'</font>'.
                                            '<font color=darkblue>'.$error['function'].'</font>'.
                                            '('.'<font color=maroon>'.implode(',', $args).'</font>'.')</i>');
        }
        $scroll=new TScroll;
        $scroll->setSize(690,390);
        $scroll->add($table);
        parent::add($scroll);
        parent::show();
    }
}
?>