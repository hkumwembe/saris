<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostelcategory
 *
 * @ORM\Table(name="hostelcategory")
 * @ORM\Entity
 */
class Hostelcategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_HTID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkHtid;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=45, nullable=false)
     */
    private $description;



    /**
     * Get pkHtid
     *
     * @return integer
     */
    public function getPkHtid()
    {
        return $this->pkHtid;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Hostelcategory
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
