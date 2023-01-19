<?php
/**
 * Provides an interface for filtering criteria definition
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCriteria extends TExpression
{
    private $expressions;  // store the list of expressions
    private $operators;    // store the list of operators
    private $properties;   // criteria properties
    
    /**
     * Constructor Method
     * @author Pablo Dall'Oglio
     */
    public function __construct()
    {
        $this->expressions = array();
        $this->operators   = array();
    }
    
    /**
     * Adds a new Expression to the Criteria
     * 
     * @param   $expression  TExpression object
     * @param   $operator    Logic Operator Constant
     * @author               Pablo Dall'Oglio
     */
    public function add(TExpression $expression, $operator = self::AND_OPERATOR)
    {
        // the first time, we don't need a logic operator to concatenate
        if (empty($this->expressions))
        {
            $operator = NULL;
        }
        
        // aggregates the expression to the list of expressions
        $this->expressions[] = $expression;
        $this->operators[]   = $operator;
    }
    
    /**
     * Returns the final expression
     * 
     * @return  A string containing the resulting expression
     * @author  Pablo Dall'Oglio
     */
    public function dump()
    {
        // concatenates the list of expressions
        if (is_array($this->expressions))
        {
            if (count($this->expressions) > 0)
            {
                $result = '';
                foreach ($this->expressions as $i=> $expression)
                {
                    $operator = $this->operators[$i];
                    // concatenates the operator with its respective expression
                    $result .=  $operator. $expression->dump() . ' ';
                }
                $result = trim($result);
                return "({$result})";
            }
        }
    }
    
    /**
     * Define a Criteria property
     * 
     * @param $property Name of the property (LIMIT, OFFSET, ORDER)
     * @param $value    Value for the property
     * @author          Pablo Dall'Oglio
     */
    public function setProperty($property, $value)
    {
        if (isset($value))
        {
            $this->properties[$property] = $value;
        }
        else
        {
            $this->properties[$property] = NULL;
        }
        
    }
    
    /**
     * reset criteria properties
     */
    public function resetProperties()
    {
        $this->properties['limit']  = NULL;
        $this->properties['order']  = NULL;
        $this->properties['offset'] = NULL;
    }
    
    /**
     * Set properties form array
     * @param $properties array of properties
     */
    public function setProperties($properties)
    {
        $this->properties['order']     = isset($properties['order'])  ? addslashes($properties['order'])  : '';
        $this->properties['offset']    = isset($properties['offset']) ? (int) $properties['offset'] : 0;
        $this->properties['direction'] = isset($properties['direction']) ? $properties['direction'] : '';
    }
    
    
    /**
     * Return a Criteria property
     * 
     * @param $property Name of the property (LIMIT, OFFSET, ORDER)
     * @return          A String containing the property value
     * @author          Pablo Dall'Oglio
     */
    public function getProperty($property)
    {
        if (isset($this->properties[$property]))
        {
            return $this->properties[$property];
        }
    }
}
?>