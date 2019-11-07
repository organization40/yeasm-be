<?php

namespace App\Controller;

use App\Dao\UserDao;
use App\Exception\PropetyTypesNotFoundException;
use App\Exception\RecordNotFoundException;
use App\Exception\UnmappedPropertyException;
use App\Exception\ValidationViolationException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller for the User API
 */
class UserController extends AbstractFOSRestController
{
    /**
     * API function to get the current user
     * @Rest\Get("/api/current_user")
     */
    public function getCurrentUser(){
        // TODO: Currently dummy funtion / call only. I think the current shouldn't be stored in the API at all
        //instead a global getUser function should be called with the current user id which is stored in the frontend
        return $this->handleView($this->view(['status'=>'ok', "user" => UserDao::getInstance()->getCurrentUser($this->getDoctrine()->getManager())[0]]));
    }
    /**
     * API funcction to update user details / attributes
     * @Rest\Post("/api/user")
     * 
     * @return Response status 'ok' in case attributes are update, status 'failed' ['status'=>'failed', 'errorCode' => $code, 'errorMessage' => '$message']) in case of errors with following error codes
     * Error code 1: Unspecified error, see error message
     * Error code 2: Property requested to get updated was not found (e.g. numberOfChildren requested to get updated but not found in the backend entity)
     * Error code 3: Type could not be identified for all properties (i.e. specified type in column definition of respective entity attribute is not known), 
     * therefore mapping is incomplete
     * Error code 4: record which was requested to get updated was not found in the database
     * Error code 5: validation violations where identified contains an additional field "validationErrors" => $arrayOfValidationViolations; 
     * structure of $arrayOfValidationViolations elements: ['field': $field, 'value': $value]
     * 
     */
    public function updateUser(Request $request, ValidatorInterface $validator){
        //wrap with try catch to ensure any exception can be handled in a 
        //controller maner. 
        try{
            //get entity manager for database access
            $em = $this->getDoctrine()->getManager();
            $resultObject = json_decode($request->getContent());
            //get the user that is being updated
            $user = UserDao::getInstance()->getUser($em, $resultObject->id);
            //update the user attribues
            $user->updateAttributes($resultObject->attributes);
            //validate the user
            $validationViolations = $validator->validate($user);
            if (count($validationViolations) > 0){
                throw new ValidationViolationException($validationViolations);
            }
            //save user details
            UserDao::getInstance()->persist($em, $user);
            return $this->handleView($this->view(['status'=>'ok']));
        } catch (UnmappedPropertyException $e){
            return $this->handleView($this->view(['status'=>'failed', 'errorCode' => 2, 'errorMessage' => $e->getMessage()]));
        } catch (PropetyTypesNotFoundException $e){
            return $this->handleView($this->view(['status'=>'failed', 'errorCode' => 3, 'errorMessage' => $e->getMessage()]));
        } catch (RecordNotFoundException $e){
            return $this->handleView($this->view(['status'=>'failed', 'errorCode' => 4, 'errorMessage' => $e->getMessage()]));
        } catch (ValidationViolationException $e){
            return $this->handleView(
                $this->view(
                    [
                        'status'=>'failed', 
                        'errorCode' => 5, 
                        'errorMessage' => $e->getMessage(), 
                        "validationErrors" => $e->getValidationViolationsAsArray()
                    ]
                )
            );
        } catch (\Exception $e){
            //return error, error code 
            return $this->handleView($this->view(['status'=>'failed', 'errorCode' => 1, 'errorMessage' => $e->getMessage()]));
        }
    }
}
?>