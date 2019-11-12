<?php

namespace App\Entity;

use App\Exception\PropetyTypesNotFoundException;
use App\Exception\UnmappedPropertyException;
use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;

class EntityBase{
    /**
     * Function to update the attributes of the object with values provided in an array
     * The keys of the array elements have to align with the attribute name of this class
     * Example: $properties[0]['firstName'] = 'Armen' will override the 'firstName' attribute of this object with the value 'Armen'
     * 
     * @param array $properties Properties to update; contains $key => $value pair with $key => property name of the object and $value => value to update
     */
    public function updateAttributes($propertiesToUpdate){
        //create an annotationsreader, required to identify the type
        $parser = new AnnotationReader();
        //get a reflection object for the entity class
        $clsProperties = (new \ReflectionClass(get_class($this)))->getProperties();
        //array to store properties which are handed over for change but where type could not be identified
        $propertyTypesNotFound = array();
        //Loop through properties which should be changed (handed over argument)
        foreach ( $propertiesToUpdate as $propertyNameToUpdate => $propertyValueToUpdate){
            if ( !empty($propertyValueToUpdate) ){
                //verify that the property handed over exists in the class
                if(!in_array($propertyNameToUpdate, array_column($clsProperties, 'name')) ){
                    //if throw new unmapped property Exception
                    throw new UnmappedPropertyException($propertyValueToUpdate);
                }
                //search for the property name in the reflection instance and get the name 
                foreach(
                    //get annotaions of the propery object
                    $parser->getPropertyAnnotations(
                        //get the propery object based on belows search result
                        $clsProperties[
                            //search in the reflection instance for the property with the 'name' of the property to be changed
                            array_search($propertyNameToUpdate, array_column($clsProperties, 'name'))
                        ]
                ) as $annotation){
                    //we need to get the column annotation where the type is set
                    if ( get_class($annotation) == "Doctrine\ORM\Mapping\Column"){
                        //Take the key of the element to identify the attribute to be changes and set the value accordingly   
                        switch ($annotation->type){
                            case "string": 
                            case "integer":
                                //Take the key of the element to identify the attribute to be changes and set the value accordingly
                                $setter = 'set' . ucfirst($propertyNameToUpdate);
                                $this->$setter($propertyValueToUpdate);
                                break;
                            case "datetime":
                            case "date":
                                $setter = 'set' . ucfirst($propertyNameToUpdate);
                                $this->$setter(DateTime::createFromFormat("d.m.Y h:i:s", $propertyValueToUpdate));
                                break;
                            case "datetime":
                                $setter = 'set' . ucfirst($propertyNameToUpdate);
                                $this->$setter(DateTime::createFromFormat("d.m.Y h:i:s", $propertyValueToUpdate));
                                break;
                            default:
                                $propertyTypesNotFound[] = $propertyValueToUpdate;
                                break;
                        }
                    }
                }
            }
        }
        if ( count($propertyTypesNotFound) > 0 ){
            throw new PropetyTypesNotFoundException($propertyTypesNotFound);
        }
    }
    
}
?>