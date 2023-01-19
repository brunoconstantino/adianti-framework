<?php
/**
 * Provides an Interface to create DELETE statements
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
final class TSqlDelete extends TSqlStatement
{
    protected $sql;
    protected $criteria;    // stores the select criteria
    
    /**
     * Returns a string containing the DELETE plain statement
     */
    public function getInstruction()
    {
        // creates the DELETE instruction
        $this->sql  = "DELETE FROM {$this->entity}";
        
        // concatenates with the criteria (WHERE)
        if ($this->criteria)
        {
            $expression = $this->criteria->dump();
            if ($expression)
            {
                $this->sql .= ' WHERE ' . $expression;
            }
        }
        return $this->sql;
    }
}
?>