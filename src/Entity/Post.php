<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="post")
 */
class Post {
  public function __construct(){
      $this->created = new DateTime();
      
  }
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;
  /**
   * @ORM\Column(type="string", length=100)
   * @Assert\NotBlank()
   *
   */
  private $postText;
  /**
   * @ORM\Column(type="datetime")
   * @Assert\NotBlank()
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $created;
  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * @param mixed $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return mixed
   */
  public function getPostText()
  {
    return $this->postText;
  }
  /**
   * @param mixed $postText
   */
  public function setPostText($postText)
  {
    $this->postText = $postText;
  }
  /**
   * return $created
   */
  public function getCreated(){
      return $this->created;
  }
  /**
   * @param date $created
   */
  public function setCreated($created){
      $this->created = $created;
  }
}