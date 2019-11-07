<?php
namespace App\Exception;

class RecordNotFoundException extends \Exception{

    public function __construct($entityName, $id){
        parent::__construct("Entry for " . $entityName . " with id " . $id . " not found");
    }
}

?>