<?php 

namespace App\Controller;

use App\Dao\PostDao;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Post as Post;
use App\Exception\PropetyTypesNotFoundException;
use App\Exception\UnmappedPropertyException;
use App\Exception\ValidationViolationException;
use App\Service\DeepJsonEncoder;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandlerInterface;
use ReflectionClass;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use TheSeer\Tokenizer\Exception;

/**
 * Controller for the Post API
 * 
 */
class PostController extends AbstractFOSRestController{

    /**
     * API function to get all posts ordered by creation date in reverse order
     * 
     * @Rest\Get("/api/posts/depth/{depth}")
     * 
     * @return Response All posts ordered reverse by createion date if successfull, , status 'failed' in case of errors with following error codes
     * Error code 1: Unspecified error, see error message
     */
    public function getPosts($depth){
        //wrap with try catch to ensure any exception can be handled in a 
        //controller maner. 
        try{
            $posts = PostDao::getInstance()->getPostsSortDateReverseOrder($this->getDoctrine()->getManager());
            // $normalizer = new ObjectNormalizer();
            // $normalizer->setIgnoredAttributes(array('created', 'jointDate'));
            // $normalizer->setCircularReferenceHandler(function ($object) {
            //     return $object->getId();
            // });
            // $serializer = new Serializer(array($normalizer), array(new JsonEncoder(new JsonEncode(JSON_UNESCAPED_UNICODE))));
            // $data = $serializer->serialize($posts, 'json');
            // die($data);
            // return $data;

            //The following is required to avoid that all (lazy-loaded) related objects of posts are loaded automatically;
            //if I just execute $handledView = $this->handleView($this->view(['status' => 'ok', 'posts' => $posts]));
            //all related objects will be loaded from the database and transfered to the caller
            //https://symfony.com/doc/master/bundles/FOSRestBundle/2-the-view-layer.html#custom-handler 
            //=> general approach to use registerHandler, implementation with didn't work though
            //=> $handler->createResponse($view, $request, new Response($view->getData(), Response::HTTP_OK));
            //=> didn't work
            //https://stackoverflow.com/questions/50107999/how-to-properly-add-a-view-handler-to-fosrestbundle-to-return-file <= took the handler class from h ere
            // TODO: Use build in function to serialize, magic is happening in Symfony\Component\Serializer\Normalizer\AbstractObjetNormalizer, details to be figured out
            $handler = $this->get('fos_rest.view_handler');
            $handler->registerHandler('json', function($handler, $view, $request) use ($posts, $depth) {
                return $respone = new Response(json_encode(["status" => "ok", "posts" => (new DeepJsonEncoder())->getJsonAsArrayFromArray($posts, $depth)]), Response::HTTP_OK, $view->getHeaders());
            });
            return $this->handleView($this->view(/*["posts" => $posts]*/));
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