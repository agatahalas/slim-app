<?php

namespace App\Entity;

use App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $machine_name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $name;

    /**
     * Get machine name.
     *
     * @ORM\return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get machine name.
     *
     * @ORM\return string
     */
    public function getMachineName() {
        return $this->machine_name;
    }

    /**
     * Set machine name.
     *
     * @ORM\return string
     */
    public function setMachineName($machine_name) {
        $this->machine_name = $machine_name;
    }

    /**
     * Get name.
     *
     * @ORM\return string
     */
    public function getName() {
        return $this->name;
    }

    /**
    * Set name.
    *
    * @ORM\return string
    */
    public function setName($name) {
        $this->name = $name;
    }

  /**
   * Get array with category's data.
   *
   * @return array
   */
    public function getArrayCategory() {
        $category = [
          'id' => $this->getId(),
          'machine_name' => $this->getMachineName(),
          'name' => $this->getName(),
        ];
        return $category;
   }

}
