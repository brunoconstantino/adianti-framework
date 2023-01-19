<?php
/**
 * Provides an Interface to create UPDATE statements
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
final class TSqlUpdate extends TSqlStatement
{
    protected $sql;         // stores the SQL statement
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
     * Returns the INSERT statement
     */
    public function getInstruction()
    {
        // creates the UPDATE statement
        $this->sql = "UPDATE {$this->entity}";
        
        // concatenate the column pairs COLUMN=VALUE
        if ($this->columnValues)
        {
            foreach ($this->columnValues as $column => $value)
            {
                $set[] = "{$column} = {$value}";
            }
        }
        $this->sql .= ' SET ' . implode(', ', $set);
        
        // concatanates the criteria (WHERE)
        if ($this->criteria)
        {
            $this->sql .= ' WHERE ' . $this->criteria->dump();
        }
        
        // returns the SQL statement
        return $this->sql;
    }
}
?>