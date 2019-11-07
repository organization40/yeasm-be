<?php
namespace App\Dao;

use App\Exception\RecordNotFoundException;

class UserDao{
        
        private static $instance = null;
  
        public static function getInstance(){
            if ( UserDao::$instance == null ){
                UserDao::$instance = new UserDao();
            }
            return UserDao::$instance;
        }

        public function persist($em, $entity){
            $em->persist($entity);
            $em->flush();
        }
        
        public function getCount($em){
            $query = $em->createQuery("SELECT COUNT(u) FROM App\Entity\User u");
            return $query->getSingleScalarResult();;
        }
        
        public function getPostsSortDateReverseOrder($em){
            $repository= $em->getRepository(User::class);
            return $repository->findBy(array(), array('created' => 'DESC'));
        }

        public function getCurrentUser($em){
            $query = $em->createQuery("SELECT u FROM App\Entity\User u WHERE u.id = 4")->setMaxResults(1);
            $result = $query->getResult();
            return $result;
        }
        public function getUser($em, $id){
            $query = $em->createQuery("SELECT u FROM App\Entity\User u WHERE u.id = :id")->setParameter('id', $id)->setMaxResults(1);
            $result = $query->getResult();
            if ( count($result) == 0 ){
                throw new RecordNotFoundException("User", $id);
            }
            return $result[0];
        }
        public function updateUser($em, $user){
            
        }
    }
?>