<?php
/**
 * Frame Widget: creates a kind of bordered area with a title located at its top-left corner
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TFrame extends TElement
{
    private $frameId;
    private static $counter;
    
    /**
     * Class Constructor
     * @param  $value text label
     */
    public function __construct($width = NULL, $height = NULL)
    {
        parent::__construct('fieldset');
        self::$counter ++;
        $this->frameId = self::$counter;
        
        // creates the default field style
        $style = new TStyle('tfieldset'.self::$counter);
        $style-> border          = 'solid';
        $style-> border_color    = '#a0a0a0';
        $style-> border_width    = '1px';
        $style-> padding_left    = '3px';
        $style-> padding_right   = '3px';
        $style-> padding_top     = '3px';
        $style-> padding_bottom  = '3px';
        if ($width)
        {
            $style-> width  = $width.'px';
        }
        if ($height)
        {
            $style-> height  = $height.'px';
        }
        $style->show();
        
        $this-> id    = 'tfieldset'.self::$counter;
        $this->{'class'} = 'tfieldset'.self::$counter;
    }
    
    /**
     * Set Legend
     * @param  $legend frame legend
     */
    public function setLegend($legend)
    {
        $obj = new TElement('legend');
        $obj->add(new TLabel($legend));
        parent::add($obj);
    }
    
    /**
     * returns js code to show frame contents recursivelly
     * used just along with TUIBuilder
     * @ignore-autocomplete on
     */
    public function _getShowCode()
    {
        $panel_id = $this->getId();
        $code = "document.getElementById('tfieldset{$panel_id}').style.visibility='visible';";
        $children = $this->getChildren();
        $uibuilder = (isset($children[1]) AND !$children[1] instanceof TLabel) ? $children[1] : $children[0];
        if ($uibuilder)
        {
            if ($uibuilder instanceof TNotebook OR $uibuilder instanceof TFrame)
            {
                $code .= $uibuilder->_getHideCode();
            }
            else if (method_exists($uibuilder, 'getChildren'))
            {
                if ($uibuilder->getChildren())
                {
                    foreach ($uibuilder->getChildren() as $object) // run through telement conteiners (position)
                    {
                        if (method_exists($object, 'getChildren'))
                        {
                            if ($object->getChildren())
                            {
                                foreach ($object->getChildren() as $child)
                                {
                                    if (($child instanceof TFrame) or ($child instanceof TNotebook))
                                    {
                                        $code.=$child->_getShowCode();
                                    }
                                }
                             }
                        }
                    }
                }
            }
        }
        return $code;
    }
    
    /**
     * returns js code to hide frame contents recursivelly
     * used just along with TUIBuilder
     * @ignore-autocomplete on
     */
    public function _getHideCode()
    {
        $panel_id = $this->getId();
        $code = "document.getElementById('tfieldset{$panel_id}').style.visibility='hidden';";
        $children = $this->getChildren();
        $uibuilder = (isset($children[1]) AND !$children[1] instanceof TLabel) ? $children[1] : $children[0];
        if ($uibuilder)
        {
            if ($uibuilder instanceof TNotebook OR $uibuilder instanceof TFrame)
            {
                $code .= $uibuilder->_getHideCode();
            }
            else if (method_exists($uibuilder, 'getChildren'))
            {
                if ($uibuilder->getChildren())
                {
                    foreach ($uibuilder->getChildren() as $object) // run through telement conteiners (position)
                    {
                        if (method_exists($object, 'getChildren'))
                        {
                            if ($object->getChildren())
                            {
                                foreach ($object->getChildren() as $child)
                                {
                                    if (($child instanceof TFrame) or ($child instanceof TNotebook))
                                    {
                                        $code.=$child->_getHideCode();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $code;
    }
    
    /**
     * return the ID's of every child notebook
     * @ignore-autocomplete on
     */
    public function _getSubNotes()
    {
        $panel_id = $this->getId();
        $children = $this->getChildren();
        $uibuilder = (isset($children[1]) AND !$children[1] instanceof TLabel) ? $children[1] : $children[0];
        $returnValue = array();
        if ($uibuilder)
        {
            if ($uibuilder instanceof TNotebook OR $uibuilder instanceof TFrame)
            {
                $returnValue = array_merge($returnValue, $uibuilder->_getSubNotes());
            }
            else if (method_exists($uibuilder, 'getChildren'))
            {
                foreach ($uibuilder->getChildren() as $object) // run through telement conteiners (position)
                {
                    if (method_exists($object, 'getChildren'))
                    {
                        foreach ($object->getChildren() as $child)
                        {
                            if ($child instanceof TNotebook)
                            {
                                $returnValue = array_merge($returnValue, array($child->id), (array)$child->_getSubNotes());
                            }
                        }
                    }
                }
            }
        }
        return $returnValue;
    }
    
    /**
     * Return the Frame ID
     * @ignore-autocomplete on
     */
    public function getId()
    {
        return $this->frameId;
    }
}
?>