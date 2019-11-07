<?php 

namespace App\Controller;

use App\Dao\PostDao;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Post as Post;
use App\Exception\PropetyTypesNotFoundException;
use App\Exception\UnmappedPropertyException;
use App\Exception\ValidationViolationException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use TheSeer\Tokenizer\Exception;

/**
 * Controller for the Post API
 * 
 */
class PostController extends AbstractFOSRestController{

    /**
     * API function to get all posts ordered by creation date in reverse order
     * 
     * @Rest\Get("/api/posts")
     * 
     * @return Response All posts ordered reverse by createion date if successfull, , status 'failed' in case of errors with following error codes
     * Error code 1: Unspecified error, see error message
     */
    public function getPosts(){
        //wrap with try catch to ensure any exception can be handled in a 
        //controller maner. 
        try{
            return $this->handleView($this->view(['status' => 'ok', 'posts' => PostDao::getInstance()->getPostsSortDateReverseOrder($this->getDoctrine()->getManager())]));
        } catch( \Exception $e ){
            return $this->handleView($this->view(['status'=>'failed', 'errorCode' => 1, 'errorMessage' => $e->getMessage()]));
        }
    }


    /**
     * API function to create a new post
     * 
     * @Rest\Post("/api/post")
     * 
     * @return Response ID of the created post and status 'ok' in case post is created succesfully, status 'failed' in case of errors with following error codes
     * Error code 1: Unspecified error, see error message
     * Error code 2: Property requested to get updated was not found (e.g. numberOfChildren requested to get updated but not found in the backend entity)
     * Error code 3: Type could not be identified for all properties (i.e. specified type in column definition of respective entity attribute is not known), 
     * therefore mapping is incomplete
     * Error code 5: validation violations where identified contains an additional field "validationErrors" => $arrayOfValidationViolations; 
     * structure of $arrayOfValidationViolations elements: ['field': $field, 'value': $value]
     */
    public function createPost(Request $request){
        //wrap with try catch to ensure any exception can be handled in a 
        //controlled maner. 
        try{
            $resultObject = json_decode($request->getContent());
            $post = new Post($resultObject);
            //persist the 
            PostDao::getInstance()->persist($this->getDoctrine()->getManager(), $post);
            if ( !is_numeric($post->getId()) or $post->getId() < 1 ){
                throw new \Exception("Unknown error while saving");
            }
            return $this->handleView($this->view(['status'=>'ok', 'id' => $post->getId()]));
        } catch (UnmappedPropertyException $e){
            return $this->handleView($this->view(['status'=>'failed', 'errorCode' => 2, 'errorMessage' => $e->getMessage()]));
        } catch (PropetyTypesNotFoundException $e){
            return $this->handleView($this->view(['status'=>'failed', 'errorCode' => 3, 'errorMessage' => $e->getMessage()]));
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
            return $this->handleView($this->view(['status'=>'failed', 'errorCode' => 1, 'errorMessage' => $e->getMessage()]));
        }
    }

}

?>