<?php
Namespace Adianti\Database;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TRecord;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TSqlSelect;

use PDO;
use Exception;
use ReflectionMethod;

/**
 * Implements the Repository Pattern to deal with collections of Active Records
 *
 * @version    2.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
final class TRepository
{
    private $class; // Active Record class to be manipulated
    private $criteria; // buffered criteria to use with fluent interfaces
    
    /**
     * Class Constructor
     * @param $class = Active Record class name
     */
    public function __construct($class)
    {
        if (class_exists($class))
        {
            if (is_subclass_of($class, 'TRecord'))
            {
                $this->class = $class;
                $this->criteria = new TCriteria;
            }
            else
            {
                throw new Exception(AdiantiCoreTranslator::translate('The class ^1 must be subclass of ^2', $class, 'TRecord'));
            }
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('The class ^1 must be defined', $class));
        }
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
     * Add a run time criteria using fluent interfaces
     * 
     * @param  $variable = variable
     * @param  $operator = comparison operator (>,<,=)
     * @param  $value    = value to be compared
     * @param  $logicOperator = logical operator (TExpression::AND_OPERATOR, TExpression::OR_OPERATOR)
     */
    public function where($variable, $operator, $value, $logicOperator = TExpression::AND_OPERATOR)
    {
        $this->criteria->add(new TFilter($variable, $operator, $value), $logicOperator);
        
        return $this;
    }
    
    /**
     * Load a collection       of objects from database using a criteria
     * @param $criteria        An TCriteria object, specifiyng the filters
     * @param $callObjectLoad  If load() method from Active Records must be called to load object parts
     * @return                 An array containing the Active Records
     */
    public function load(TCriteria $criteria = NULL, $callObjectLoad = TRUE)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
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
            TTransaction::log($sql->getInstruction());
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $result = $conn-> prepare( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result-> execute( $criteria->getPreparedVars() );
            }
            else
            {
                // execute the query
                $result= $conn-> query($sql->getInstruction());
            }
            $results = array();
            
            $class = $this->class;
            $callback = array($class, 'load'); // bypass compiler
            
            // Discover if load() is overloaded
            $rm = new ReflectionMethod($class, $callback[1]);
            
            if ($result)
            {
                // iterate the results as objects
                while ($row = $result->fetchObject($this->class))
                {
                    if ($callObjectLoad)
                    {
                        // reload the object because its load() method may be overloaded
                        if ($rm->getDeclaringClass()-> getName () !== 'Adianti\Database\TRecord')
                        {
                            $row->reload();
                        }
                        
                        if ( $cache = $row->getCacheControl() )
                        {
                            $pk = $row->getPrimaryKey();
                            $record_key = $class . '['. $row->$pk . ']';
                            if ($cache::setValue( $record_key, $row->toArray() ))
                            {
                                TTransaction::log($record_key . ' stored in cache');
                            }
                        }
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
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Delete a collection of Active Records from database
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @return           The affected rows
     */
    public function delete(TCriteria $criteria = NULL)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
        $class = $this->class;
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            
            // first, clear cache
            $record = new $class;
            if ( $cache = $record->getCacheControl() )
            {
                $pk = $record->getPrimaryKey();
                
                // creates a SELECT statement
                $sql = new TSqlSelect;
                $sql->addColumn( $pk );
                $sql->setEntity($this->getEntity());
                // assign the criteria to the SELECT statement
                $sql->setCriteria($criteria);
                
                if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
                {
                    $result = $conn-> prepare( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $result-> execute( $criteria->getPreparedVars() );
                }
                else
                {
                    $result = $conn-> query($sql->getInstruction());
                }
                
                if ($result)
                {
                    // iterate the results as objects
                    while ($row = $result->fetchObject())
                    {
                        $record_key = $class . '['. $row->$pk . ']';
                        if ($cache::delValue( $record_key ))
                        {
                            TTransaction::log($record_key . ' deleted from cache');
                        }
                    }
                }
            }
            
            // creates a DELETE statement
            $sql = new TSqlDelete;
            $sql->setEntity($this->getEntity());
            // assign the criteria to the DELETE statement
            $sql->setCriteria($criteria);
            
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $result = $conn-> prepare( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result-> execute( $criteria->getPreparedVars() );
            }
            else
            {
                // execute the DELETE statement
                $result = $conn->exec($sql->getInstruction());
            }
            
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Return the amount of objects that satisfy a given criteria
     * @param $criteria  An TCriteria object, specifiyng the filters
     * @return           An Integer containing the amount of objects that satisfy the criteria
     */
    public function count(TCriteria $criteria = NULL)
    {
        if (!$criteria)
        {
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
        }
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
            
            $dbinfo = TTransaction::getDatabaseInfo(); // get dbinfo
            if (isset($dbinfo['prep']) AND $dbinfo['prep'] == '1') // prepared ON
            {
                $result = $conn-> prepare( $sql->getInstruction( TRUE ) , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $result-> execute( $criteria->getPreparedVars() );
            }
            else
            {
                // executes the SELECT statement
                $result= $conn-> query($sql->getInstruction());
            }
            
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
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
}
