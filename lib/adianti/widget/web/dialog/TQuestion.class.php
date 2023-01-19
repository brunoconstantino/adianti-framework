<?php
/**
 * Question Dialog
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TQuestion
{
    private $id;
    
    /**
     * Class Constructor
     * @param  $message    A string containint the question
     * @param  $action_yes Action taken for YES response
     * @param  $action_no  Action taken for NO  response
     */
    public function __construct($message, TAction $action_yes = NULL, TAction $action_no = NULL)
    {
        $this->id = uniqid();
        
        if (TPage::isMobile())
        {
            $img = new TElement('img');
            $img-> src = "lib/adianti/images/question.png";
            
            $yes = new TElement('a');
            $yes-> href      = $action_yes->serialize();
            $yes-> generator = 'adianti';
            $yes->add(TAdiantiCoreTranslator::translate('Yes'));
            
            $no = new TElement('a');
            $no-> href      = $action_no->serialize();
            $no-> generator = 'adianti';
            $no->add(TAdiantiCoreTranslator::translate('No'));
            
            $table = new TTable;
            $table-> width='250px';
            $table-> bgcolor='#E5E5E5';
            $table-> style="border-collapse:collapse";
            $row = $table->addRow();
            $row->addCell($img);
            $table2=new TTable;
            $row->addCell($table2);
            $row=$table2->addRow();
            $c=$row->addCell($message);
            $c-> colspan=2;
            $row=$table2->addRow();
            $row->addCell($yes);
            $row->addCell($no);
            $table->show();
        }
        else
        {
            // creates the dialog style
            $style = new TStyle('tquestion');
            $style-> position     = 'fixed';
            $style-> font_family  = 'sans-serif,arial,verdana';
            $style-> font_size    = '10pt';
            $style-> left         = '30%';
            $style-> top          = '30%';
            $style-> width        = '430px';
            $style-> height       = '214px';
            $style-> border_width = '1px';
            $style-> color        = 'black';
            $style-> background   = 'url("lib/adianti/images/tmessage.png") no-repeat top center';
            $style-> overflow     = 'none';
            $style-> z_index      = '3000';
            
            // show the style
            $style->show();
            
            // creates a layer to show the dialog
            $painel = new TElement('div');
            $painel->{'class'} = "tquestion";
            $painel-> id    = 'tquestion_'.$this->id;
            
            $button1 = new TButton(NULL, NULL);
            $button2 = new TButton(NULL, NULL);
            $button3 = new TButton(NULL, NULL);
            $button1->setLabel(TAdiantiCoreTranslator::translate('Yes'));
            $button2->setLabel(TAdiantiCoreTranslator::translate('No'));
            $button3->setLabel(TAdiantiCoreTranslator::translate('Cancel'));
            $button1->setImage('ico_ok.png');
            $button2->setImage('ico_no.png');
            $button3->setImage('ico_close.png');
            
            if ($action_yes)
            {
                // convert the actions into URL's
                $url_yes = $action_yes->serialize();
                $button1->addFunction($url_yes);
            }
            else
            {
                $button1->addFunction("document.getElementById('{$painel-> id}').style.display='none';");
            }
            
            if ($action_no)
            {
                // convert the actions into URL's
                $url_no = $action_no->serialize();
                $button2->addFunction($url_no);
            }
            else
            {
                $button2->addFunction("document.getElementById('{$painel-> id}').style.display='none';");
            }
            
            $button3->addFunction("document.getElementById('{$painel-> id}').style.display='none';");
            
            // creates a table for the layout
            $table = new TTable;
            $table-> style = "font-family:sans-serif,arial,verdana;font-size:10pt";
            $table-> align = 'center';
            $table-> cellspacing = 10;
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
            $row->addCell(new TImage('lib/adianti/images/question.png'));
            $scroll=new TScroll;
            $scroll->setSize(300,120);
            $scroll->add($message);
            $row->addCell($scroll);
            $cell = new StdClass;
            $cell-> width='300px';
            $row-> height='130px';
            $row->addCell('&nbsp;');
            
            $table_buttons = new TTable;
            $row=$table_buttons->addRow();
            $row->addCell($button1);
            $row->addCell($button2);
            $row->addCell($button3);
            
            // creates a row for the buttons
            $row=$table->addRow();
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            $cell=$row->addCell($table_buttons);
            $cell-> align='right';
            $cell=$row->addCell('&nbsp;');
            $cell-> width='40px';
            
            $row=$table->addRow();
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            $row->addCell('&nbsp;');
            
            // add the table to the panel
            $painel->add($table);
            // show the panel
            $painel->show();
        }
    }
}
?>