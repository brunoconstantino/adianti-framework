<?php
/**
 * Standard Page controller for Seek buttons
 *
 * @version    1.0
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TStandardSeek extends TWindow
{
    private $form;      // search form
    private $datagrid;  // listing
    private $pageNavigation;
    private $parentForm;
    private $loaded;
    
    /**
     * Constructor Method
     * Creates the page, the search form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates a new form
        $this->form = new TForm('form_busca_generica');
        // creates a new table
        $table = new TTable;
        
        // adds the table into the form
        $this->form->add($table);
        
        // create the form fields
        $display_field= new TEntry('display_field');
        
        // keeps the field's value
        $display_field->setValue(TSession::getValue('tstandardseek_display_value'));
        
        // add a row for the filter field
        $row=$table->addRow();
        $row->addCell(new TLabel('Field:'));
        $row->addCell($display_field);
        
        // create the action button
        $find_button = new TButton('busca');
        // define the button action
        $find_button->setAction(new TAction(array($this, 'onSearch')), TAdiantiCoreTranslator::translate('Search'));
        $find_button->setImage('ico_find.png');
        
        // add a row for the button in the table
        $row=$table->addRow();
        $row->addCell($find_button);
        
        // define wich are the form fields
        $this->form->setFields(array($display_field, $find_button));
        
        // creates a new datagrid
        $this->datagrid = new TDataGrid;
        
        // create two datagrid columns
        $id      = new TDataGridColumn('id',            'ID',    'right',  70);
        $display = new TDataGridColumn('display_field', 'Field', 'left',  220);
        
        // add the columns to the datagrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($display);
        
        // create a datagrid action
        $action1 = new TDataGridAction(array($this, 'onSelect'));
        $action1->setLabel('Selecionar');
        $action1->setImage('ico_apply.png');
        $action1->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the paginator
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // creates the table-based layout
        $table = new TTable;
        // create a row for the form
        $row = $table->addRow();
        $row->addCell($this->form);
        // create a row for the datagrid
        $row = $table->addRow();
        $row->addCell($this->datagrid);
        // creat a row for the paginator
        $row = $table->addRow();
        $row->addCell($this->pageNavigation);
        // add the table to the page
        parent::add($table);
    }
    
    /**
     * Register the user filter in the section
     */
    function onSearch()
    {
        // get the form data
        $dados = $this->form->getData();
        
        // check if the user has filled the form
        if (isset($dados-> display_field))
        {
            // creates a filter using the form content
            $display_field = TSession::getValue('standard_seek_display_field');
            $filter = new TFilter($display_field, 'like', "%{$dados-> display_field}%");
            
            // store the filter in section
            TSession::setValue('tstandardseek_filter',        $filter);
            TSession::setValue('tstandardseek_display_value', $dados-> display_field);
            
            // set the data back to the form
            $this->form->setData($dados);
        }
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with the active record objects
     */
    function onReload($param = NULL)
    {
        try
        {
            $model    = TSession::getValue('standard_seek_model');
            $database = TSession::getValue('standard_seek_database');
            
            // begins the transaction with database
            TTransaction::open($database);
            
            // creates a repository for the model
            $repository = new TRepository($model);
            $limit = 10;
            
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('tstandardseek_filter'))
            {
                // add the filter to the criteria
                $criteria->add(TSession::getValue('tstandardseek_filter'));
            }
            
            // load all objects according with the criteria
            $clientes = $repository->load($criteria);
            $this->datagrid->clear();
            if ($clientes)
            {
                $display_field = TSession::getValue('standard_seek_display_field');
                foreach ($clientes as $cliente)
                {
                    $item = $cliente;
                    $item-> display_field = $cliente->$display_field; 
                    // add the object into the datagrid
                    $this->datagrid->addItem($item);
                }
            }
            
            // clear the crieteria to count the records
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // closes the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception genearated message
            new TMessage('error', '<b>Erro</b> ' . $e->getMessage());
            // rollback all the database operations 
            TTransaction::rollback();
        }
    }
    
    /**
     * define the standars seek parameters
     */
    function onSetup($param=NULL)
    {
        // store the parameters in the section
        TSession::setValue('tstandardseek_filter', NULL);
        TSession::setValue('tstandardseek_display_value', NULL);
        TSession::setValue('standard_seek_receive_key',   $param['receive_key']);
        TSession::setValue('standard_seek_receive_field', $param['receive_field']);
        TSession::setValue('standard_seek_display_field', $param['display_field']);
        TSession::setValue('standard_seek_model',         $param['model']);
        TSession::setValue('standard_seek_database',      $param['database']);
        TSession::setValue('standard_seek_parent',        $param['parent']);
        $this->onReload();
    }
    
    /**
     * Select the register by ID and return the information to the main form
     *     When using onblur signal, AJAX passes all needed parameters via GET
     *     instead of calling onSetup before.
     */
    public function onSelect($param)
    {
        $key = $param['key'];
        $database = isset($param['database'])   ? $param['database'] : TSession::getValue('standard_seek_database');
        
        try
        {
            TTransaction::open($database);
            // load the active record
            $model = isset($param['model']) ? $param['model'] : TSession::getValue('standard_seek_model');
            $activeRecord = new $model($key);
            TTransaction::close();

            $receive_key   = isset($param['receive_key'])   ? $param['receive_key']   : TSession::getValue('standard_seek_receive_key');
            $receive_field = isset($param['receive_field']) ? $param['receive_field'] : TSession::getValue('standard_seek_receive_field');
            $display_field = isset($param['display_field']) ? $param['display_field'] : TSession::getValue('standard_seek_display_field');
            $parent        = isset($param['parent'])        ? $param['parent']        : TSession::getValue('standard_seek_parent');
            
            $object = new StdClass;
            $object->$receive_key   = $activeRecord->{'id'};
            $object->$receive_field = $activeRecord->$display_field;
            
            TForm::sendData($parent, $object);
            parent::closeWindow(); // closes the window
        }
        catch (Exception $e) // em caso de exceção
        {
            // exibe a mensagem gerada pela exceção
            new TMessage('error', $e->getMessage());
            // desfaz todas alterações no banco de dados
            TTransaction::rollback();
        }
    }
}
?>
