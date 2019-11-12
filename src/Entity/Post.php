<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\EntityBase;

/**
 * @ORM\Entity
 * @ORM\Table(name="post")
 */
class Post extends EntityBase {
  /**
   * Constructor
   * Checks internally if arguments are provided and initialize, based on the arguments, the object properties
   * --> if no arguments: initialize the creation Date/Time
   * --> if 1 argument: initialize the creation Date/Time and the attributes of the Entity based on the Object attributes handed of as args
   */
  public function __construct(){
      $args = func_get_args();
      if (count($args) == 0 ){
        $this->setCreated(new DateTime());
      } else if (
        count($args) == 1 
        //verify that the argument handed over is an object
        and is_object($args[0])
      ){

        $this->updateAttributes(func_get_args()[0]);
        $this->setCreated(new DateTime());
      } else {
        throw new \Exception("Not defined how to read the input argument");
      }
  }
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id()
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
   * @ORM\Column(type="string", length=50, options={"default": ""})
   * @Assert\NotBlank()
   */
  private $title;
  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id")
   */
  private $createdBy;
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

  /**
   * @param string $title
   */
  public function setTitle($title){
    $this->title = $title;
  }
  /**
   * return $title
   */
  public function getTitle(){
    return $this->title;
  }

  /**
   * Get the value of createdBy
   */ 
  public function getCreatedBy()
  {
    return $this->createdBy;
  }

  /**
   * Set the value of createdBy
   *
   * @return  self
   */ 
  public function setCreatedBy($createdBy)
  {
    $this->createdBy = $createdBy;

    return $this;
  }
}