<?php

namespace App\Service;

use Doctrine\Common\Util\ClassUtils;
use ReflectionClass;
use Symfony\Component\Debug\Exception\UndefinedMethodException;

class DeepJsonEncoder{
    /**
     * Function to create a json object
     */
    public function getJsonAsArrayFromArray($objectArr, $relationDepth){
        $data = array();
        foreach ($objectArr as $object){
            $data[] =  $this->getJsonAsArray($object, $relationDepth);
        }
        return $data;
    }
    public function getJsonAsArray($object, $relationDepth, $currentDepth = 0){
        $data = array();
        $reflClass = new \ReflectionClass($object);
        if( StringService::startsWith($reflClass->getName(), "Proxies\__CG__") ){
            $reflClass = new \ReflectionClass(ClassUtils::getRealClass($reflClass->getName()));
        }
        $properties = $reflClass->getProperties();
        foreach ($properties as $property ){
            //validate if getter exists
            $getter = 'get' . ucfirst($property->getName());
            if ($reflClass->hasMethod($getter)){
                //retrieve value
                $propertyValue = $object->$getter();
                //check if its a simple type or another object
                if ( is_scalar($propertyValue) ){
                    $data[$property->getName()] = $propertyValue;
                } else {
                    if ( $propertyValue != null ){
                        $propReflClass = new \ReflectionClass($propertyValue);
                        switch ($propReflClass->getName()){
                            case 'DateTime':
                                $data[$property->getName()] = $propertyValue->format('Y-m-d H:i:s');
                                break;
                            default:
                                if ( $currentDepth < $relationDepth){
                                    $data[$property->getName()] = $this->getJsonAsArray($propertyValue, $relationDepth, $currentDepth + 1);
                                } else {
                                    try{
                                        $data[$property->getName()] = ["id" => $propertyValue->getId()];
                                    } catch (\Throwable $e){
                                        // TODO: Search for id based on the ORM annotations
                                        $data[$property->getName()] = [
                                            "id" => "getter 'getId()' expected but not found in class " . (new \ReflectionClass(ClassUtils::getRealClass($propReflClass->getName())))->getName()
                                        ];
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }
        return $data;
    }
}
?>