<?php
namespace App\Dao;

use App\Entity\Post;


class PostDao {
  
    private static $instance = null;
  
    public static function getInstance(){
      if ( PostDao::$instance == null ){
          PostDao::$instance = new PostDao();
      }
      return PostDao::$instance;
    }

    public function persist($em, $entity){
        $em->persist($entity);
        $em->flush();
    }
    
    public function getCount($em){
      $query = $em->createQuery("SELECT COUNT(p) FROM App\Entity\Post p");
      return $query->getSingleScalarResult();;
    }
    
    public function getPostsSortDateReverseOrder($em){
      // $sqlLogger = new \Doctrine\DBAL\Logging\EchoSQLLogger();
      // $em->getConnection()
      //   ->getConfiguration()
      //   ->setSQLLogger($sqlLogger)
      // ;
      $repository= $em->getRepository(Post::class);
      $result = $repository->findBy(array(), array('created' => 'DESC'));
      return $result;
    }
}