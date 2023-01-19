<?php
/**
 * SourceCode View
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage general
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSourceCode
{
    private $content;
    
    /**
     * Load a PHP file
     * @param $file Path to the PHP file
     */
    public function loadFile($file)
    {
        if (!file_exists($file))
        {
            return FALSE;
        }
        
        $this->content = file_get_contents($file);
        if (utf8_encode(utf8_decode($this->content)) !== $this->content ) // SE NÃO UTF8
        {
            $this->content = utf8_encode($this->content);
        }
        return TRUE;
    }
    
    /**
     * Show the highlighted source code
     */
    public function show()
    {
        $span = new TElement('span');
        $span->style = 'font-size:10pt';
        $span->add(highlight_string($this->content, TRUE));
        $span->show();
    }
}
?>