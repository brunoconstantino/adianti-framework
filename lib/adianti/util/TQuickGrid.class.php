<?php
/**
 * Create quick datagrids through its simple interface
 *
 * @version    1.0
 * @package    util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TQuickGrid extends TDataGrid
{
    /**
     * Add a column
     * @param $label  Field Label
     * @param $object Field Object
     * @param $size   Field Size
     */
    public function addQuickColumn($label, $name, $align = 'left', $size = 200, TAction $action = NULL, $param = NULL)
    {
        // creates a new column
        $object = new TDataGridColumn($name, $label, $align, $size);
        
        if ($action instanceof TAction)
        {
            // create ordering
            $action->setParameter($param[0], $param[1]);
            $object->setAction($action);
        }
        // add the column to the datagrid
        parent::addColumn($object);
    }
    
    /**
     * Add action to the datagrid
     * @param $label  Action Label
     * @param $action TAction Object
     * @param $icon   Action Icon
     */
    public function addQuickAction($label, TDataGridAction $action, $field, $icon = 'ico_save.png')
    {
        $action->setLabel($label);
        $action->setImage($icon);
        $action->setField($field);
        
        // add the datagrid action
        parent::addAction($action);
    }
}
?>