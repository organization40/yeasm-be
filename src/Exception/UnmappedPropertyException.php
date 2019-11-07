<?php
namespace App\Exception;

class UnmappedPropertyException extends \Exception {

    /**
     * Property which could not be mapped
     * 
     * $property String
     */
    private $property;

    public function __construct($property){
        $this->property  = $property;
    }
}
?>