<?php
namespace EtwScaffolding\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use EtwScaffolding\Service\DBIntrospection;
use EtwScaffolding\Service\CodeGenerator;

/**
 * TableGatewayController
 *
 * @author
 *
 * @version
 *
 */
class TableGatewayController extends AbstractActionController
{
    /**
     * 
     * @var DBIntrospection
     */
    protected $dbIntrospection;
    
    /**
     * 
     * @var CodeGenerator
     */
    protected $codeGenerator;
    
    /**
     * The default action - show the home page
     */
    public function generateAction()
    {
        $dbAdapter=$this->params()->fromRoute('dbAdapter',null);
        $module=$this->params()->fromRoute('module',null);
        $table=$this->params()->fromRoute('table',null);
        $allTables=$this->params()->fromRoute('allTables',null);
        $errorMessages=array();
        
        if($this->validateParams($dbAdapter, $module, $table,$allTables, $errorMessages)===false) {
            return implode("\r",$errorMessages);
        } else {
            if($allTables===true) {
                $tableCreated=array();
                foreach ($this->getDbIntrospection()->getTables($dbAdapter) as $tableIterated) {
                    $tableCreated[]=$this->getCodeGenerator()->generateTableGateway($tableIterated,$module);
                }
                return implode("\r",$tableCreated);
            } else {
                return $this->getCodeGenerator()->generateTableGateway($table,$module);
            }            
        }
    }
	
	
    /**
     * This method validate the console params if its return false means that there are
	 * errors, and add the error Message to the $errorMessage variable
     * @param string $dbAdapter
     * @param string $module
     * @param string $table
     * @param bool $allTables
     * @param array $errorMessages
     * @return boolean
     */
    private function validateParams ($dbAdapter, $module, $table,$allTables, &$errorMessages)
    {    
        //Check the DB adapter    
        if($dbAdapter===null) {            
            $errorMessages[]="\tYou need to specify the dbAdapter name param [--dbAdapter=].\n\r";    
        } else {
            if($this->getServiceLocator()->has($dbAdapter)===false) {
                $errorMessages[]="\tThere is no service configured with that name for the dbAdapter[$dbAdapter], please check your config files.\n\r";
            }
        }
        
        //Check the Module where the files will be generated
        if($module===null) {            
            $errorMessages[]="\tYou need to specify the module name param [--module=].\n\r";
        } else {
            $modules=$this->getServiceLocator()->get('applicationconfig')['modules'];
            if(!in_array($module, $modules)) {
                $errorMessages[]="\tThere is no module with that name[$module], please check your modules that are being loaded.\n\r";
            }
        }
        
        //Check the specified tables
        if($allTables===false) {
            if($table===null) {
                $errorMessages[]="\tYou need to specify the table name param [--table=] or activate the all Tables flag [--allTables].\n\r";
            } else {                
                if($this->getDbIntrospection()->searchTable($table,$dbAdapter)==false) {
                    $errorMessages[]="\tThere is no table with that name[$table], please check your tables names.\n\r";
                }
            }
        }    
        
        if(count($errorMessages)>0) {
            return false;
        } else {
            return true;
        }
    }    

    /**
     * @return DBIntrospection $dbIntrospection
     */
    public function getDbIntrospection()
    {
        if($this->dbIntrospection===null) {
            $this->dbIntrospection=$this->getServiceLocator()->get('etwscaffolding-service-dbintrospection');
        }        
        return $this->dbIntrospection;
    }
    
	/**
     * @return CodeGenerator $codeGenerator
     */
    public function getCodeGenerator ()
    {
         if($this->codeGenerator===null) {
            $this->codeGenerator=$this->getServiceLocator()->get('etwscaffolding-service-codegenerator');
        }        
        return $this->codeGenerator;
    }
}