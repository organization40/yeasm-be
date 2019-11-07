<?php

namespace App\Exception;

class PropetyTypesNotFoundException extends \Exception{
    
    private $propertyTypesNotFound;

    public function __construct($propertyTypesNotFound){
        $this->propertyTypesNotFound = $propertyTypesNotFound;
    }
}
?>