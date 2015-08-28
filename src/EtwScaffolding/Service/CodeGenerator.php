<?php
namespace EtwScaffolding\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Code\Generator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\PropertyGenerator;

/**
 *
 * @author gnr
 *        
 */
class CodeGenerator implements ServiceLocatorAwareInterface  
{
    
    /**
     * 
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
        
    /**
     * 
     * @param string $table
     * @param string $module
     * @return string
     */
    public function generateTableGateway($table,$module)
    {        
        $fileName=ucfirst($table)."Table.php";
        $path=__DIR__."/../../../../../data/etw-scaffolding/";
        $fullPath=$this->createPath($path,$module,true);
        
        $fileGenerated=new Generator\FileGenerator();
        $fileGenerated->setFilename($fileName);
        $fileGenerated->setNamespace(ucfirst($module)."\\Model\\Table");
        $fileGenerated->setUse("Zend\\Db\\TableGateway\\TableGateway");        
        
        $docblock = DocBlockGenerator::fromArray(array(
                'shortDescription' => 'TableGateway generated with EtwScaffolding Module',                                
        ));
        
        $tableClass=new Generator\ClassGenerator(ucfirst($table)."Table");
        $tableClass->setExtendedClass("TableGateway");        
        $tableClass->setDocBlock($docblock);
                
        $saveMethod=new Generator\MethodGenerator("save",array('info'));
        $saveMethod->setBody('$this->insert($info);'."\n".'return $this;');        
        $findAllMethod=new Generator\MethodGenerator("findAll");
        $findAllMethod->setBody('$resultSet = $this->select();'."\n".'return $resultSet;');        
        $findByIdMethod=new Generator\MethodGenerator("findById",array('id'));
        $findByIdMethod->setBody('$id=(int)$id; '."\n".'$resultSet = $this->select(array(\'id\'=>$id)); '."\n".'return $resultSet->current();');
        
        $tableClass->addMethodFromGenerator($saveMethod);
        $tableClass->addMethodFromGenerator($findAllMethod);
        $tableClass->addMethodFromGenerator($findByIdMethod);
        
        $fileGenerated->setClass($tableClass);
        file_put_contents($fullPath."/".$fileName, $fileGenerated->generate());
        
        return $fileName." has been generated!\n";        
    }
    
    
    public function generateModel($table,$module,$fields)
    {
        $fileName=ucfirst($table).".php";
        $path=__DIR__."/../../../../../data/etw-scaffolding/";
        $fullPath=$this->createPath($path,$module,false);
        
        $fileGenerated=new Generator\FileGenerator();
        $fileGenerated->setFilename($fileName);
        $fileGenerated->setNamespace(ucfirst($module)."\\Model");
        
        $docblock = DocBlockGenerator::fromArray(array(
                'shortDescription' => 'Model generated with EtwScaffolding Module',
        ));
        
        $modelClass=new Generator\ClassGenerator(ucfirst($table));        
        $modelClass->setDocBlock($docblock);
        
        foreach ($fields as $property) {            
            $modelClass->addProperty($property['name'],null,PropertyGenerator::FLAG_PROTECTED);
            
            $setMethod=new Generator\MethodGenerator("set".ucfirst($property['name']),array($property['name']));
            $setMethod->setBody("\$this->{$property['name']}=\${$property['name']};");            
            $getMethod=new Generator\MethodGenerator("get".ucfirst($property['name']));
            $getMethod->setBody("return \$this->{$property['name']};");
            $modelClass->addMethodFromGenerator($setMethod)
                ->addMethodFromGenerator($getMethod);            
        }        
        
        $fileGenerated->setClass($modelClass);
        file_put_contents($fullPath."/".$fileName, $fileGenerated->generate());
        
        return $fileName." has been generated!\n";
    }
    
	/**
	 * A method that create the Path structure for the generated files
	 * 
	 * @param string $fullPath
	 * @param string $module
	 * @param bool $table
	 */
    protected function createPath ($fullPath,$module,$table)
    {
        if(!file_exists($fullPath)){
            mkdir($fullPath);
        }
        
        if(!file_exists($fullPath."/".ucfirst($module))) {
            mkdir($fullPath."/".ucfirst($module));
        }
        
        if(!file_exists($fullPath."/".ucfirst($module)."/Model/")) {
            mkdir($fullPath."/".ucfirst($module)."/Model/");
        }
        
        if($table===true) {
            if(!file_exists($fullPath."/".ucfirst($module)."/Model/Table/")) {
               mkdir($fullPath."/".ucfirst($module)."/Model/Table/");
            }           
           return $fullPath."/".ucfirst($module)."/Model/Table";
        } else {
           return $fullPath."/".ucfirst($module)."/Model/";
        } 
    }  
    
    
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator=$serviceLocator;
        
        return $this;
    }

	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
     */
    public function getServiceLocator ()
    {
       return $this->serviceLocator; 
    }

    
}