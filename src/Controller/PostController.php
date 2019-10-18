<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\Post as PostEntity;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class PostController extends FOSRestController{

    public function __construct(){
    }

    /**
     * @Rest\Post("/post")
     */
    public function createPost(){
        $movie = new PostEntity();
        $movie->setPostText("Moinsen");
        $em=$this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();
        return new Response("Post was created successfully");
    }
}

?>