<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Roomcategory
 *
 * @ORM\Table(name="roomcategory")
 * @ORM\Entity
 */
class Roomcategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_RCID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkRcid;

    /**
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=30, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=240, nullable=true)
     */
    private $description;



    /**
     * Get pkRcid
     *
     * @return integer
     */
    public function getPkRcid()
    {
        return $this->pkRcid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Roomcategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Roomcategory
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
