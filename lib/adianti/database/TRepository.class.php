<?php
/**
 * Implements the Repository Pattern to deal with collections of Active Records
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
final class TRepository
{
    private $class; // Active Record class to be manipulated
    
    /**
     * Class Constructor
     * @param $class = Active Record class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }
    
    /**
     * Returns the name of database entity
     * @return A String containing the name of the entity
     */
    protected function getEntity()
    {
        return constant($this->class.'::TABLENAME');
    }
    
    /**
     * Load a collection of objects from database using a criteria
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @return           An array containing the Active Records
     */
    public function load(TCriteria $criteria)
    {
        // creates a SELECT statement
        $sql = new TSqlSelect;
        $sql->addColumn('*');
        $sql->setEntity($this->getEntity());
        // assign the criteria to the SELECT statement
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            // (if the user has registered a TLogger file)
            TTransaction::log($sql->getInstruction());
            
            // execute the query
            $result= $conn->Query($sql->getInstruction());
            $results = array();
            
            // Discover if load() is overloaded
            $rm = new ReflectionMethod($this->class, 'load');
            
            if ($result)
            {
                // iterate the results as objects
                while ($row = $result->fetchObject($this->class))
                {
                    // reload the object because its load() method may be overloaded
                    if ($rm->getDeclaringClass()->getName() !== 'TRecord')
                    {
                        $row->reload();
                    }
                    // store the object in the $results array
                    $results[] = $row;
                }
            }
            return $results;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception('No active transactions !!');
        }
    }
    
    /**
     * Delete a collection of Active Records from database
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @return           The affected rows
     */
    public function delete(TCriteria $criteria)
    {
        // creates a DELETE statement
        $sql = new TSqlDelete;
        $sql->setEntity($this->getEntity());
        // assign the criteria to the DELETE statement
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            // execute the DELETE statement
            $result = $conn->exec($sql->getInstruction());
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception('No active transactions !!');
        }
    }
    
    /**
     * Return the amount of objects that satisfy a given criteria
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @return           An Integer containing the amount of objects that satisfy the criteria
     */
    public function count(TCriteria $criteria)
    {
        // creates a SELECT statement
        $sql = new TSqlSelect;
        $sql->addColumn('count(*)');
        $sql->setEntity($this->getEntity());
        // assign the criteria to the SELECT statement
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            // executes the SELECT statement
            $result= $conn->Query($sql->getInstruction());
            if ($result)
            {
                $row = $result->fetch();
            }
            // returns the result
            return $row[0];
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception('No active transactions !!');
        }
    }
}
?>