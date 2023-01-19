<?php
/**
 * FileChooser widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TFile extends TField
{
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        TPage::include_css('lib/valums/fileuploader.css');
        TPage::include_js('lib/valums/fileuploader.js');
        
        // define the tag properties
        $this->tag-> name  = $this->name;  // tag name
        $this->tag-> value = $this->value; // tag value
        $this->tag-> type  = 'text';       // input type
        $this->tag-> style = "width:{$this->size}px";  // size
        
        // verify if the widget is editable
        if (!parent::getEditable())
        {
            // make the field read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        
        $div = new TElement('div');
        $div-> style="display:inline";
        $div-> id = 'div_file_'.uniqid();
        
        $table = new TTable;
        $table-> cellspacing = 0;
        $row = $table->addRow();
        $row->addCell($this->tag);
        $row->addCell($div);
        $table->show();
        
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $class = 'TFileUploader';
        $script->add('
            new qq.FileUploader({
                element: document.getElementById("'.$div-> id.'"),
                action: "engine.php?class='.$class.'",
                debug: true,
                onComplete: function(id, fileName, responseJSON)
                {
                    document.getElementsByName("'.$this->name.'")[0].value= responseJSON.target;
                }
            });');
        $script->show();
    }
}
?>