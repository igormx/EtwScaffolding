<?php
namespace EtwScaffolding\Service;

use Zend\Db\Metadata\Metadata;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 *
 * @author gnr
 *        
 */
class DBIntrospection implements ServiceLocatorAwareInterface
{
    /**
     * 
     * @var ServiceLocatorInterface
     */
    public $serviceLocator;
    
    /**
     * This function look for the table inside of the DB
     * @param string $table
     * @param string $dbAdapterName
     * @return boolean
     */
    public function searchTable($table,$dbAdapterName)
    {
        $dbAdapter=$this->getServiceLocator()->get($dbAdapterName);
        $metadata=new Metadata($dbAdapter);
        $tables=$metadata->getTableNames();
    
        return in_array($table, $tables);
    }
    
    /**
     * 
     * @param string $dbAdapterName
     * @return multitype:
     */
    public function getTables($dbAdapterName)
    {
        $dbAdapter=$this->getServiceLocator()->get($dbAdapterName);
        $metadata=new Metadata($dbAdapter);
        $tables=$metadata->getTableNames();
        
        return $tables;
    }
    
    /**
     * This method return the info of the table columns
     * @param unknown $table
     * @param unknown $dbAdapterName
     * @return multitype:
     */
    public function getTableFields($table,$dbAdapterName)
    {
        $dbAdapter=$this->getServiceLocator()->get($dbAdapterName);
        $metadata=new Metadata($dbAdapter);
        $arrColumns=array();
        $c=0;
        foreach ($metadata->getColumns($table) as $column){
            $arrColumns[$c]['name']=$column->getName();
            $arrColumns[$c]['type']=$column->getDataType();
            $c++;
        }
        
        return $arrColumns;
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator=$serviceLocator;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
}