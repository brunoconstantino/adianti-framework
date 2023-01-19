<?php
/**
 * Singleton manager for database connections
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
final class TConnection
{
    /**
     * Class Constructor
     * There'll be no instances of this class
     */
    private function __construct() {}
    
    /**
     * Opens a database connection
     * 
     * @param $database Name of the database (an INI file).
     * @return          A PDO object if the $database exist,
     *                  otherwise, throws an exception
     * @exception       Exception
     *                  if the $database is not found
     * @author          Pablo Dall'Oglio
     */
    public static function open($database)
    {
        // check if the database configuration file exists
        if (file_exists("app/config/{$database}.ini"))
        {
            // read the INI and retuns an array
            $db = parse_ini_file("app/config/{$database}.ini");
        }
        else
        {
            // if the database doesn't exists, throws an exception
            throw new Exception(TAdiantiCoreTranslator::translate('File not found') . ': ' ."'{$database}.ini'");
        }
        
        // read the database properties
        $user  = isset($db['user']) ? $db['user'] : NULL;
        $pass  = isset($db['pass']) ? $db['pass'] : NULL;
        $name  = isset($db['name']) ? $db['name'] : NULL;
        $host  = isset($db['host']) ? $db['host'] : NULL;
        $type  = isset($db['type']) ? $db['type'] : NULL;
        $port  = isset($db['port']) ? $db['port'] : NULL;
        
        // each database driver has a different instantiation process
        switch ($type)
        {
            case 'pgsql':
                $port = $port ? $port : '5432';
                $conn = new PDO("pgsql:dbname={$name};user={$user}; password={$pass};host=$host;port={$port}");
                break;
            case 'mysql':
                $port = $port ? $port : '3306';
                $conn = new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass);
                break;
            case 'sqlite':
                $conn = new PDO("sqlite:{$name}");
                break;
            case 'ibase':
                $name = isset($host) ? "{$host}:{$name}" : $name;
                $conn = new PDO("firebird:dbname={$name}", $user, $pass);
                break;
            case 'oci8':
                $conn = new PDO("oci:dbname={$name}", $user, $pass);
                break;
            case 'mssql':
                $conn = new PDO("mssql:host={$host},1433;dbname={$name}", $user, $pass);
                break;
            case 'dblib':
                $conn = new PDO("dblib:host={$host},1433;dbname={$name}", $user, $pass);
                break;
        }
        
        // define wich way will be used to report errors (EXCEPTION)
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // return the PDO object
        return $conn;
    }
}
?>