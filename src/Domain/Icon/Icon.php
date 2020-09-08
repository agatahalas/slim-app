<?php
declare(strict_types=1);

namespace App\Domain\Icon;

use JsonSerializable;

class Icon implements JsonSerializable
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $category;

   /**
    * @var string
    */
   private $src;

    /**
     * @param int|null  $id
     * @param string    $label
     * @param string    $country
     * @param string    $category
     * @param string    $src
     */
    public function __construct(?int $id, string $label, string $country, string $category, string $src)
    {
        $this->id = $id;
        $this->label = strtolower($label);
        $this->country = ucfirst($country);
        $this->category = ucfirst($category);
        $this->src = ucfirst($src);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

  /**
   * @return string
   */
  public function getSrc(): string
  {
    return $this->src;
  }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'country' => $this->country,
            'category' => $this->category,
            'src' => $this->src,
        ];
    }
}
