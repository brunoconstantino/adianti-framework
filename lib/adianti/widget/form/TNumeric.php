<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TEntry;

/**
 * Numeric Widget
 *
 * @version    5.7
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TNumeric extends TEntry implements AdiantiWidgetInterface
{
    public function __construct($name, $decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost = true)
    {
        parent::__construct($name);
        $this->tag->{'pattern'}   = '[0-9]*';
        $this->tag->{'inputmode'} = 'numeric';
        
        parent::setNumericMask($decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost);
    }
}
