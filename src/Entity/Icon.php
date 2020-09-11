<?php

namespace App\Entity;

use App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Category as Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="icon")
 */
class Icon
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
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     */
    protected $category;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
     protected $status;

    /**
     * @ORM\Column(type="blob")
     */
    protected $src;

    /**
     * Get id.
     *
     * @ORM\return integer
     */
    public function getId() {
        return $this->id;
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
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Assign to category.
     */
    public function assignToCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category.
     *
     * @ORM\return integer
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get status.
     *
     * @ORM\return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set status.
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * Get src.
     *
     * @ORM\return string
     */
     public function getSrc() {
        return $this->src;
    }

    /**
     * Set src.
     */
    public function setSrc($src) {
        $this->src = $src;
    }

  /**
   * Get array with icon's data.
   *
   * @return array
   */
    public function getArrayIcon() {
        $icon = [
          'id' => $this->getId(),
          'name' => $this->getName(),
          'category' => $this->getCategory()->getId(),
          'status' => $this->getStatus(),
          'src' => is_resource($this->getSrc()) ? stream_get_contents($this->getSrc()) : $this->getSrc(),
        ];
        return $icon;
    }

}
