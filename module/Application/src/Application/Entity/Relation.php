<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Relation
 *
 * @ORM\Table(name="relation")
 * @ORM\Entity
 */
class Relation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_RELATIONID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkRelationid;

    /**
     * @var string
     *
     * @ORM\Column(name="RELATIONSHIP", type="string", length=45, nullable=false)
     */
    private $relationship;



    /**
     * Get pkRelationid
     *
     * @return integer
     */
    public function getPkRelationid()
    {
        return $this->pkRelationid;
    }

    /**
     * Set relationship
     *
     * @param string $relationship
     *
     * @return Relation
     */
    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;

        return $this;
    }

    /**
     * Get relationship
     *
     * @return string
     */
    public function getRelationship()
    {
        return $this->relationship;
    }
}
