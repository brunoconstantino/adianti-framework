<?php
/**
 * Message Dialog
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMessage
{
    private $id;
    
    /**
     * Class Constructor
     * @param $type    Type of the message (info, error)
     * @param $message Message to be shown
     */
    public function __construct($type, $message)
    {
        $this->id = uniqid();
        
        if (TPage::isMobile())
        {
            $img = new TElement('img');
            $img-> src = "lib/adianti/images/{$type}.png";
            
            $table = new TTable;
            $table-> width='250px';
            $table-> bgcolor='#E5E5E5';
            $table-> style="border-collapse:collapse";
            
            $row = $table->addRow();
            $row->addCell($img);
            $row->addCell($message);
            $table->show();
        }
        else
        {
            $style = new TStyle('tmessage');
            $style-> position     = 'fixed';
            $style-> font_family  = 'sans-serif,arial,verdana';
            $style-> font_size    = '10pt';
            $style-> left         = '30%';
            $style-> top          = '30%';
            $style-> width        = '430px';
            $style-> height       = '214px';
            $style-> color        = 'black';
            $style-> background   = 'url("lib/adianti/images/tmessage.png") no-repeat top center';
            $style-> align        = 'center';
            $style-> vertical_align='middle';
            $style-> z_index      = '3000';
            $style-> overflow     = 'none';
            
            // show the style
            $style->show();
            
            // creates a pannel to show the dialog
            $painel = new TElement('div');
            $painel->{'class'} = 'tmessage';
            $painel-> id    = 'tmessage_'.$this->id;
            
            $button = new TButton(NULL, NULL);
            $button->addFunction("document.getElementById('{$painel-> id}').style.display='none';");
            $button->setLabel('Fechar');
            $button->setImage('ico_close.png');
            // creates a button to close the dialog
            
            // creates a table for layout
            $table = new TTable;
            $table-> style = "font-family:sans-serif,arial,verdana;font-size:10pt";
            $table-> align = 'center';
            $table-> width = '400px';
            $table-> height= '212px';
            $table-> cellspacing= '7';
            
            $row=$table->addRow();
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            
            // creates a row for the icon and the message
            $row=$table->addRow();
            $row->addCell('&nbsp;');
            $row->addCell(new TImage("lib/adianti/images/{$type}.png"));
            
            $scroll=new TScroll;
            $scroll->setSize(300,120);
            $scroll->add($message);
            $cell=$row->addCell($scroll);
            $cell-> width='300px';
            $row-> height='130px';
            $row->addCell('&nbsp;');
            
            // creates a row for the button
            $row=$table->addRow();
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            $cell=$row->addCell($button);
            $cell-> align='right';
            $cell=$row->addCell('&nbsp;');
            $cell-> width='40px';
            
            $row=$table->addRow();
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            
            // add the table to the pannel
            $painel->add($table);
            // show the pannel
            $painel->show();
        }
    }
}
?>