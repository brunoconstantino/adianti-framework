<?php
/**
 * Provides an abstract interface to register LOG files
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TLogger
{
    protected $filename; // path for LOG file
    
    /**
     * Class Constructor
     * @param  $filename path for LOG file
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
        // clear the file contents
        file_put_contents($filename, '');
    }
    
    // force method rewrite in child classes
    abstract function write($message);
}
?>