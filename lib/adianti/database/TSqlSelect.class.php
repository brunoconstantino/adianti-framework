<?php
/**
 * Provides an Interface to create SELECT statements
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
final class TSqlSelect extends TSqlStatement
{
    private $columns;   // array with the column names to be returned
    
    /**
     * Add a column name to be returned
     * @param $column   A string containing a column name
     */
    public function addColumn($column)
    {
        // add the column name to the array
        $this->columns[] = $column;
    }
    
    /**
     * Returns the SELECT statement as an string according to the database driver
     */
    public function getInstruction()
    {
        $conn = TTransaction::get();
        $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        if (($driver == 'mssql') or ($driver == 'dblib'))
        {
            return $this->getInstructionSqlServer();
        }
        else
        {
            return $this->getInstructionStandard();
        }
    }
    
    /**
     * Returns the SELECT statement as an string for standard open source drivers
     */
    public function getInstructionStandard()
    {
        // creates the SELECT instruction
        $this->sql  = 'SELECT ';
        // concatenate the column names
        $this->sql .= implode(',', $this->columns);
        // concatenate the entity name
        $this->sql .= ' FROM ' . $this->entity;
        
        // concatenate the criteria (WHERE)
        if ($this->criteria)
        {
            $expression = $this->criteria->dump();
            if ($expression)
            {
                $this->sql .= ' WHERE ' . $expression;
            }
            
            // get the criteria properties
            $order     = $this->criteria->getProperty('order');
            $limit     = (int) $this->criteria->getProperty('limit');
            $offset    = (int) $this->criteria->getProperty('offset');
            $direction = in_array($this->criteria->getProperty('direction'), array('asc', 'desc')) ? $this->criteria->getProperty('direction') : '';
            
            if ($order)
            {
                $this->sql .= ' ORDER BY ' . $order . ' ' . $direction;
            }
            if ($limit)
            {
                $this->sql .= ' LIMIT ' . $limit;
            }
            if ($offset)
            {
                $this->sql .= ' OFFSET ' . $offset;
            }
        }
        // return the SQL statement
        return $this->sql;
    }
    
    /**
     * Returns the SELECT statement as an string for mssql/dblib drivers
     */
    public function getInstructionSqlServer()
    {
        // obtém a cláusula WHERE do objeto criteria.
        if ($this->criteria)
        {
            $expression = $this->criteria->dump();
            
            if ($expression)
            {
                $sql_where = ' WHERE ' . $expression;
            }
            
            // obtém as propriedades do critério
            $order    = $this->criteria->getProperty('order');
            $limit    = (int) $this->criteria->getProperty('limit');
            $offset   = (int) $this->criteria->getProperty('offset');
            $direction= in_array($this->criteria->getProperty('direction'), array('asc', 'desc')) ? $this->criteria->getProperty('direction') : '';
            
            // obtém a ordenação do SELECT
            if ($order)
            {
                $sql_order = ' ORDER BY ' . $order . ' ' . $direction;
            }
        }
        $this->sql  = " SELECT " . implode(',', $this->columns) . " FROM {$this->entity} {$sql_where} {$sql_order}";
        
        if (isset($limit) and !isset($offset))
        {
            $this->sql  = " SELECT TOP {$limit} " . implode(',', $this->columns) . " FROM {$this->entity} {$sql_where} {$sql_order}";
        }
        else if (isset($limit) and isset($offset))
        {
            $sum = $limit + $offset;
            $select_top = " SELECT TOP {$sum} "   . implode(',', $this->columns) . ' FROM ' . $this->entity . " {$sql_where} ORDER BY 1 ASC";
            $select_lim = " SELECT TOP {$limit} " . implode(',', $this->columns) . " FROM ({$select_top}) AS TAB ORDER BY 1 DESC";
            
            // monsta a instrução de SELECT
            $this->sql  = " SELECT " . implode(',', $this->columns) . " FROM ({$select_lim}) AS TAB2 {$sql_order}";
        }
        
        return $this->sql;
    }
}
?>