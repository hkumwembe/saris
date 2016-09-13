<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity
 *
 * @ORM\Table(name="entity", indexes={@ORM\Index(name="parententity", columns={"PARENT_ENTITY"}), @ORM\Index(name="entityhead", columns={"FK_STAFFID"})})
 * @ORM\Entity
 */
class Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_ENTITYID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkEntityid;

    /**
     * @var string
     *
     * @ORM\Column(name="ENTITY_CODE", type="string", length=10, nullable=false)
     */
    private $entityCode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ENTITY_NAME", type="string", length=45, nullable=false)
     */
    private $entityName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="LEVEL", type="text", nullable=true)
     */
    private $level;

    /**
     * @var \Application\Entity\Staff
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Staff")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_STAFFID", referencedColumnName="PK_STAFFID")
     * })
     */
    private $fkStaffid;

    /**
     * @var \Application\Entity\Entity
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Entity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PARENT_ENTITY", referencedColumnName="PK_ENTITYID")
     * })
     */
    private $parentEntity;



    /**
     * Get pkEntityid
     *
     * @return integer
     */
    public function getPkEntityid()
    {
        return $this->pkEntityid;
    }

    /**
     * Set entityCode
     *
     * @param string $entityCode
     *
     * @return Entity
     */
    public function setEntityCode($entityCode)
    {
        $this->entityCode = $entityCode;

        return $this;
    }

    /**
     * Get entityCode
     *
     * @return string
     */
    public function getEntityCode()
    {
        return $this->entityCode;
    }

    /**
     * Set entityName
     *
     * @param string $entityName
     *
     * @return Entity
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Get entityName
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * Set level
     *
     * @param string $level
     *
     * @return Entity
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set fkStaffid
     *
     * @param \Application\Entity\Staff $fkStaffid
     *
     * @return Entity
     */
    public function setFkStaffid(\Application\Entity\Staff $fkStaffid = null)
    {
        $this->fkStaffid = $fkStaffid;

        return $this;
    }

    /**
     * Get fkStaffid
     *
     * @return \Application\Entity\Staff
     */
    public function getFkStaffid()
    {
        return $this->fkStaffid;
    }

    /**
     * Set parentEntity
     *
     * @param \Application\Entity\Entity $parentEntity
     *
     * @return Entity
     */
    public function setParentEntity(\Application\Entity\Entity $parentEntity = null)
    {
        $this->parentEntity = $parentEntity;

        return $this;
    }

    /**
     * Get parentEntity
     *
     * @return \Application\Entity\Entity
     */
    public function getParentEntity()
    {
        return $this->parentEntity;
    }
}
