<?php
/**
 * Button Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TButton extends TField
{
    private $action;
    private $label;
    private $image;
    private $formName;
    private $function;
    
    /**
     * Define the action of the button
     * @param  $action TAction object
     * @param  $label  Button's label
     */
    public function setAction(TAction $action, $label)
    {
        $this->action = $action;
        $this->label  = $label;
    }
    
    /**
     * Define the icon of the button
     * @param  $image  image path
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    
    /**
     * Define the label of the button
     * @param  $label button label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Add a JavaScript function to be executed by the button
     * @param $function A piece of JavaScript code
     * @ignore-autocomplete on
     */
    public function addFunction($function)
    {
        $this->function = $function;
    }

    /**
     * Define the name of the form to wich the button is attached
     * @param $name    A string containing the name of the form
     * @ignore-autocomplete on
     */
    public function setFormName($name)
    {
        $this->formName = $name;
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        // creates the button style
        $style1 = new TStyle('tbutton_div');
        $style1-> border                = 'solid';
        $style1-> _webkit_border_radius = '3px';
        $style1-> _moz_border_radius    = '3px';
        $style1-> border_color          = '#a4a4a4';
        $style1-> border_width          = '1px';
        $style1-> float                 = 'left';
        $style1-> height                = '19px';
        $style1-> color                 = '#333';
        $style1-> padding               = '3px';
        $style1-> padding_top           = '3px';
        $style1-> padding_bottom        = '1px';
        $style1-> border_radius         = '4px 4px 4px 4px';
        $style1-> box_shadow            = '0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05)';
        $style1-> border_style          = 'solid';
        $style1-> background            = "url('lib/adianti/images/tbutton_back.gif') repeat-x #f0f0f0;";
        $style1-> cursor                = "default";
        $style1->show();
        
        $style2 = new TStyle('tbutton_inner');
        $style2-> color            = '#5E5E5E';
        $style2-> font_size        = '12px';
        $style2-> _moz_user_select = 'none';
        $style2-> user_select      = 'none';
        $style2-> text_shadow      = '0 1px 0 #FFFFFF';
        $style2-> font_weight      = 'bold';
        $style2->show();
        
        $href = FALSE;
        
        if ($this->action)
        {
            if (isset($_REQUEST['isajax']) AND $_REQUEST['isajax'] == '1') // create ajax flag
            {
                // get the action as URL
                $this->action->setParameter('encoding', 'utf8');
                $this->action->setParameter('isajax',   '1');
                $url = $this->action->serialize(FALSE);
                
                $wait_message = TAdiantiCoreTranslator::translate('Loading');
                // define the button's action (ajax post)
                $action = "
                          $.blockUI({ 
                                message: '<h1>{$wait_message}</h1>',
                                css: { 
                                    border: 'none', 
                                    padding: '15px', 
                                    backgroundColor: '#000', 
                                    'border-radius': '5px 5px 5px 5px',
                                    opacity: .5, 
                                    color: '#fff' 
                                }
                            });
                           {$this->function};
                           $.post('engine.php?{$url}',
                                  \$('#{$this->formName}').serialize(),
                                  function(result)
                                  {
                                      __adianti_load_html(result);
                                      $.unblockUI();
                                  });
                           return false;";
            }
            else
            {
                $url = $this->action->serialize(FALSE);
                
                // define the button's action (standard post)
                $action = "document.{$this->formName}.action='engine.php?{$url}'; ".
                          "{$this->function};".
                          "document.{$this->formName}.submit()";
            }
                        
            $button = new TElement('button');
            $button-> onclick   = $action;
            $button-> style   = 'padding-left:0px; padding-right:0px;';
            $action = '';
        }
        else
        {
            $action = $this->function;
            // creates the button using a div
            $button = new TElement('div');
            $button->{'class'} = 'tbutton_div';
            if (substr($action, 0, 9) == 'index.php')
            {
                $href = TRUE;
            }
        }
        
        $table = new TTable;
        $table-> cellspacing = 1;
        $table-> cellpadding = 0;
        $table->{'class'}= 'tbutton_inner';
        
        $row=$table->addRow();
        if ($this->image)
        {
            if (file_exists('lib/adianti/images/'.$this->image))
            {
                $image = new TImage('lib/adianti/images/'.$this->image);
            }
            else
            {
                $image = new TImage('app/images/'.$this->image);
            }
            $cell1=$row->addCell($image);
            if ($href)
            {
                $cell1-> href      = $action;
                $cell1-> generator = 'adianti';
            }
            else
            {
                $cell1-> onclick   = $action;
            }
        }
        $cell2 = $row->addCell('&nbsp;'.$this->label.'&nbsp;');
        $cell2-> id = $this->name;
        if ($href)
        {
            $cell2-> href      = $action;
            $cell2-> generator = 'adianti';
        }
        else
        {
            $cell2-> onclick   = $action;
        }
        $cell2-> name      = $this->name;
        
        $row-> onmouseover = 'style.cursor = \'pointer\'';
        $row-> onmouseout  = 'style.cursor = \'default\'';
        $button->add($table);
        $button->show();
    }
}
?>