<?php
/**
 * Provides an Interface to create an INSERT statement
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
final class TSqlInsert extends TSqlStatement
{
    protected $sql;         // stores the SQL
    private $columnValues;
    
    /**
     * Assign values to the database columns
     * @param $column   Name of the database column
     * @param $value    Value for the database column
     */
    public function setRowData($column, $value)
    {
        // get the current connection
        $conn = TTransaction::get();
        
        // store just scalar values (string, integer, ...)
        if (is_scalar($value))
        {
            // if is a string
            if (is_string($value) and (!empty($value)))
            {
                // fill an array indexed by the column names
                $this->columnValues[$column] = $conn->quote($value);
            }
            else if (is_bool($value)) // if is a boolean
            {
                // fill an array indexed by the column names
                $this->columnValues[$column] = $value ? 'TRUE': 'FALSE';
            }
            else if ($value!== '') // if its another data type
            {
                // fill an array indexed by the column names
                $this->columnValues[$column] = $value;
            }
            else
            {
                // if the value is NULL
                $this->columnValues[$column] = "NULL";
            }
        }
    }
    
    /**
     * this method doesn't exist in this class context
     * @param $criteria A TCriteria object, specifiyng the filters
     * @exception       Exception in any case
     */
    public function setCriteria(TCriteria $criteria)
    {
        throw new Exception("Cannot call setCriteria from " . __CLASS__);
    }
    
    /**
     * Returns the INSERT plain statement
     */
    public function getInstruction()
    {
        $this->sql = "INSERT INTO {$this->entity} (";
        // concatenates the column names
        $columns = implode(', ', array_keys($this->columnValues));
        // concatenates the column values
        $values  = implode(', ', array_values($this->columnValues));
        $this->sql .= $columns . ')';
        $this->sql .= " values ({$values})";
        // returns the string
        return $this->sql;
    }
}
?>