<?php
/**
 * Record Lookup Widget: Creates a lookup field used to search values from associated entities
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSeekButton extends TEntry
{
    private $action;
    private $auxiliar;
    private $formName;
    
    /**
     * Define the action for the SeekButton
     * @param $action Action taken when the user
     * clicks over the Seek Button (A TAction object)
     */
    public function setAction(TAction $action)
    {
        $this->action = $action;
    }
    
    /**
     * Define an auxiliar field
     * @param $object any TField object
     */
    public function setAuxiliar(TField $object)
    {
        $this->auxiliar = $object;
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
     * Show the widget
     */
    public function show()
    {
        // check if it's not editable
        if (parent::getEditable())
        {
            TPage::include_js('lib/sack/sack.js');
            TPage::include_js('lib/adianti/include/lookup.js');
            $serialized_action = '';
            if ($this->action)
            {
                // get the action class name
                if (is_array($callback = $this->action->getAction()))
                {
                    $classname  = get_class($callback[0]);
                    $inst       = new $classname;
                    $ajaxAction = new TAction(array($inst, 'onSelect'));
                    
                    if ($classname == 'TStandardSeek')
                    {
                        $ajaxAction->setParameter('parent',  $this->action->getParameter('parent'));
                        $ajaxAction->setParameter('database',$this->action->getParameter('database'));
                        $ajaxAction->setParameter('model',   $this->action->getParameter('model'));
                        $ajaxAction->setParameter('display_field', $this->action->getParameter('display_field'));
                        $ajaxAction->setParameter('receive_key',   $this->action->getParameter('receive_key'));
                        $ajaxAction->setParameter('receive_field', $this->action->getParameter('receive_field'));
                    }
                    $string_action = $ajaxAction->serialize(FALSE);
                    $this->setProperty('onBlur', "serialform=(\$('#{$this->formName}').serialize());
                                                  ajaxLookup('$string_action&'+serialform, this)");
                }
                $serialized_action = $this->action->serialize(FALSE);
            }
            parent::show();
            
            $image = new TImage('lib/adianti/images/ico_find.png');
            
            $link = new TElement('a');
            $link-> onmouseover = 'style.cursor = \'pointer\'';
            $link-> onmouseout  = 'style.cursor = \'default\'';
            $link-> onclick = "javascript:serialform=(\$('#{$this->formName}').serialize());
                  __adianti_append_page('engine.php?{$serialized_action}&'+serialform+'&encoding=utf8')";
            $link->add($image);
            $link->show();
            
            if ($this->auxiliar)
            {
                echo '&nbsp;';
                $this->auxiliar->show();
            }
        }
        else
        {
            parent::show();
        }
    }
}
?>