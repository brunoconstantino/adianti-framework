<?php
/**
 * Base class for Active Records
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TRecord
{
    protected $data;  // array containing the data of the object
    protected $attributes; // array of attributes
    
    /**
     * Class Constructor
     * Instantiates the Active Record
     * @param [$id] Optional Object ID, if passed, load this object
     */
    public function __construct($id = NULL)
    {
        $this->attributes = array();
        
        if ($id) // if the user has informed the $id
        {
            // load the object identified by ID
            $object = $this->load($id);
            if ($object)
            {
                $this->fromArray($object->toArray());
            }
            else
            {
                throw new Exception(TAdiantiCoreTranslator::translate('Object not found'));
            }
        }
    }
    
    /**
     * Executed when the programmer clones an Active Record
     * In this case, we have to clear the ID, to generate a new one
     */
    public function __clone()
    {
        $pk = $this->getPrimaryKey();
        unset($this->$pk);
    }
    
    /**
     * Executed whenever a property is accessed
     * @param $property Name of the object property
     * @return          The value of the property
     */
    public function __get($property)
    {
        // check if exists a method called get_<property>
        if (method_exists($this, 'get_'.$property))
        {
            // execute the method get_<property>
            return call_user_func(array($this, 'get_'.$property));
        }
        else
        {
            // returns the property value
            if (isset($this->data[$property]))
            {
                return $this->data[$property];
            }
        }
    }
    
    /**
     * Executed whenever a property is assigned
     * @param $property Name of the object property
     * @param $value    Value of the property
     */
    public function __set($property, $value)
    {
        // check if exists a method called set_<property>
        if (method_exists($this, 'set_'.$property))
        {
            // executed the method called set_<property>
            call_user_func(array($this, 'set_'.$property), $value);
        }
        else
        {
            if ($value === NULL)
            {
                unset($this->data[$property]);
            }
            else
            {
                // assign the property's value
                $this->data[$property] = $value;
            }
        }
    }
    
    /**
     * Returns if a property is assigned
     * @param $property Name of the object property
     */
    public function __isset($property)
    {
        return isset($this->data[$property]) or method_exists($this, 'get_'.$property);
    }
    
    /**
     * Unset a property
     * @param $property Name of the object property
     */
    public function __unset($property)
    {
        unset($this->data[$property]);
    }
    
    /**
     * Returns the name of database entity
     * @return A String containing the name of the entity
     */
    protected function getEntity()
    {
        // get the Active Record class name
        $class = get_class($this);
        // return the TABLENAME Active Record class constant
        return constant("{$class}::TABLENAME");
    }
    
    /**
     * Returns the the name of the primary key for that Active Record
     * @return A String containing the primary key name
     */
    private function getPrimaryKey()
    {
        // get the Active Record class name
        $class = get_class($this);
        // returns the PRIMARY KEY Active Record class constant
        return constant("{$class}::PRIMARYKEY");
    }
    
    /**
     * Fill the Active Record properties from another Active Record
     * @param $object An Active Record
     */
    public function mergeObject(TRecord $object)
    {
        $data = $object->toArray();
        foreach ($data as $key => $value)
        {
            $this->data[$key] = $value;
        }
    }
    
    /**
     * Fill the Active Record properties from an indexed array
     * @param $data An indexed array containing the object properties
     */
    public function fromArray($data)
    {
        $this->data = $data;
    }
    
    /**
     * Return the Active Record properties as an indexed array
     * @return An indexed array containing the object properties
     */
    public function toArray()
    {
        return $this->data;
    }
    
    /**
     * Limit the attributes that will be stored in the Active Record
     */
    public function addAttribute($attribute)
    {
        $this->attributes[] = $attribute;
    }
    
    /**
     * Store the objects into the database
     * @return      The number of affected rows
     * @exception   Exception if there's no active transaction opened
     */
    public function store()
    {
        // get the Active Record class name
        $class = get_class($this);
        
        // check if the object has an ID or exists in the database
        $pk = $this->getPrimaryKey();
        
        if (empty($this->data[$pk]) or (!self::load($this->$pk)))
        {
            // increments the ID
            if (empty($this->data[$pk]))
            {
                if ((defined("{$class}::IDPOLICY")) AND (constant("{$class}::IDPOLICY") == 'serial'))
                {
                    unset($this->$pk);
                }
                else
                {
                    $this->$pk = $this->getLast() +1;
                }
            }
            // creates an INSERT instruction
            $sql = new TSqlInsert;
            $sql->setEntity($this->getEntity());
            // iterate the object data
            foreach ($this->data as $key => $value)
            {
                // check if the field is a calculated one
                if (!method_exists($this, 'get_' . $key))
                {
                    if (count($this->attributes) > 0)
                    {
                        // set just attributes defined by the addAttribute()
                        if ((in_array($key, $this->attributes)) OR ($key == $pk))
                        {
                            // pass the object data to the SQL
                            $sql->setRowData($key, $this->$key);
                        }
                    }
                    else
                    {
                        // pass the object data to the SQL
                        $sql->setRowData($key, $this->$key);
                    }
                }
            }
        }
        else
        {
            // creates an UPDATE instruction
            $sql = new TSqlUpdate;
            $sql->setEntity($this->getEntity());
            // creates a select criteria based on the ID
            $criteria = new TCriteria;
            $criteria->add(new TFilter($pk, '=', $this->$pk));
            $sql->setCriteria($criteria);
            // interate the object data
            foreach ($this->data as $key => $value)
            {
                if ($key !== $pk) // there's no need to change the ID value
                {
                    // check if the field is a calculated one
                    if (!method_exists($this, 'get_' . $key))
                    {
                        if (count($this->attributes) > 0)
                        {
                            // set just attributes defined by the addAttribute()
                            if ((in_array($key, $this->attributes)) OR ($key == $pk))
                            {
                                // pass the object data to the SQL
                                $sql->setRowData($key, $this->$key);
                            }
                        }
                        else
                        {
                            // pass the object data to the SQL
                            $sql->setRowData($key, $this->$key);
                        }
                    }
                }
            }
        }
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            // (if the user has registered a TLogger)
            TTransaction::log($sql->getInstruction());
            $result = $conn->exec($sql->getInstruction());
            
            if ((defined("{$class}::IDPOLICY")) AND (constant("{$class}::IDPOLICY") == 'serial'))
            {
                if ($sql instanceof TSqlInsert)
                {
                    $this->$pk = $conn->lastInsertId();
                }
            }
            
            // return the result of the exec() method
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(TAdiantiCoreTranslator::translate('No active transactions'));
        }
    }
    
    /**
     * ReLoad an Active Record Object from the database
     */
    public function reload()
    {
        // discover the primary key name 
        $pk = $this->getPrimaryKey();
        
        $this->load($this->$pk);
    }
    
    /**
     * Load an Active Record Object from the database
     * @param $id  The object ID
     * @return     The Active Record Object
     * @exception  Exception if there's no active transaction opened
     */
    public function load($id)
    {
        // creates a SELECT instruction
        $sql = new TSqlSelect;
        $sql->setEntity($this->getEntity());
        $sql->addColumn('*');
        
        // discover the primary key name 
        $pk = $this->getPrimaryKey();
        
        // creates a select criteria based on the ID
        $criteria = new TCriteria;
        $criteria->add(new TFilter($pk, '=', $id));
        // define the select criteria
        $sql->setCriteria($criteria);
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $result= $conn->Query($sql->getInstruction());
            // if there's a result
            if ($result)
            {
                // returns the data as an object of this class
                $object = $result->fetchObject(get_class($this));
            }
            return $object;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(TAdiantiCoreTranslator::translate('No active transactions'));
        }
    }
    
    /**
     * Delete an Active Record object from the database
     * @param [$id]     The Object ID
     * @exception       Exception if there's no active transaction opened
     */
    public function delete($id = NULL)
    {
        // discover the primary key name
        $pk = $this->getPrimaryKey();
        // if the user has not passed the ID, take the object ID
        $id = $id ? $id : $this->$pk;
        // creates a DELETE instruction
        $sql = new TSqlDelete;
        $sql->setEntity($this->getEntity());
        
        // creates a select criteria
        $criteria = new TCriteria;
        $criteria->add(new TFilter($pk, '=', $id));
        // assign the criteria to the delete instruction
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $result = $conn->exec($sql->getInstruction());
            // return the result of the exec() method
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(TAdiantiCoreTranslator::translate('No active transactions'));
        }
    }
    
    /**
     * Returns the LAST Object ID from database
     * @return      An Integer containing the LAST Object ID from database
     * @exception   Exception if there's no active transaction opened
     */
    private function getLast()
    {
        $pk = $this->getPrimaryKey();
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // instancia instrução de SELECT
            $sql = new TSqlSelect;
            $sql->addColumn("max({$pk}) as {$pk}");
            $sql->setEntity($this->getEntity());
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $result= $conn->Query($sql->getInstruction());
            // retorna os dados do banco
            $row = $result->fetch();
            return $row[0];
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(TAdiantiCoreTranslator::translate('No active transactions'));
        }
    }
}
?>