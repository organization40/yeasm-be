<?php
namespace App\Exception;



class ValidationViolationException extends \Exception{

    private $validationViolations;

    public function __construct($validationViolations){

        $this->validationViolations = $validationViolations;
    }
    /**
     * Method to convert the required details of the validation violations to an array to transfer to API caller (required to have thin json layer)
     */
    public function getValidationViolationsAsArray(){
        $resultArray = array();
        foreach ( $this->validationViolations as $validationViolation){
            $validationViolationArray = array();
            
            $validationViolationArray['field'] = $validationViolation->getPropertyPath();
            $validationViolationArray['value'] = $validationViolation->getInvalidValue();
            // TODO: Add type to allow api caller to crate a custom error message
            $resultArray[] = $validationViolationArray;
        }
        return $resultArray;
    }
}
?>