<?php
/**
 * JQuery dialog container
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TJQueryDialog extends TElement
{
    private $actions;
    private $width;
    private $height;
    private $useOKButton;
    private static $counter;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct()
    {
        parent::__construct('div');
        self::$counter ++;
        $this->useOKButton = TRUE;
        $this->{'id'} = 'jquery_dialog'.self::$counter.'_'.uniqid();
        $this->{'widget'} = 'T'.'Window';
        $this->{'style'}="overflow:auto";
    }
    
    /**
     * Define se vai ter botão de OK
     * @param $bool booleano
     */
    public function setUseOKButton($bool)
    {
        $this->useOKButton = $bool;
    }
    
    /**
     * Define o título do diálogo
     * @param $title título do diálogo
     */
    public function setTitle($title)
    {
        $this->{'title'} = $title;
    }
    
    /**
     * Retorna o ID
     */
    public function getId()
    {
        return $this->{'id'};
    }
    
    /**
     * Define o tamanho do diálogo
     * @param $width largura
     * @param $height altura
     */
    public function setSize($width, $height)
    {
        $this->width  = $width;
        $this->height = $height;
    } 
    
    /**
     * Adiciona um botão de ação JS ao diálogo
     * @param $label rótulo do botão
     * @param $action ação JS do botão
     */
    public function addAction($label, $action)
    {
        $this->actions[] = array($label, $action);
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $action_code = '';
        if ($this->actions)
        {
            foreach ($this->actions as $action_array)
            {
                $label  = $action_array[0];
                $action = $action_array[1];
                $action_code .= "\"{$label}\": function() {  $action },";
            }
        }
        
        $ok_button = '';
        if ($this->useOKButton)
        {
            $ok_button = '  OK: function() {
                				$( this ).remove();
                			}';
        }
        
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $script->add( '
    		$(document).ready(function()
    		{
            	$( "#' . $this->{'id'} . '" ).dialog({
            		modal: true,
            		height:'.$this->height.',
            		width:'.$this->width.',
            		close: function(ev, ui) { $(this).remove(); },
            		buttons: {
            		    ' . $action_code . $ok_button . 
            		    '
            		}
            	});
    		});' );
        parent::add($script);
        parent::show();
    }
    
    /**
     * Closes the dialog
     */
    public function close()
    {
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $script->add( '$( "#' . $this->{'id'} . '" ).remove();');
        parent::add($script);
    }
    
    /**
     Close all TJQueryDialog
     */
    public static function closeAll()
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseLineBreaks(FALSE);
        $script->setUseSingleQuotes(TRUE);
        $script->add( '$(\'[widget="T'.'Window"]\').remove();');
        $script->show();
    }
}
?>