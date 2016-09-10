<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gradeflow
 *
 * @ORM\Table(name="gradeflow", indexes={@ORM\Index(name="stagerole", columns={"FK_ROLEID"})})
 * @ORM\Entity
 */
class Gradeflow
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_GRADEFLOWID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkGradeflowid;

    /**
     * @var integer
     *
     * @ORM\Column(name="LEVEL", type="integer", nullable=false)
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=150, nullable=false)
     */
    private $description;

    /**
     * @var \Application\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_ROLEID", referencedColumnName="PK_ROLEID")
     * })
     */
    private $fkRoleid;



    /**
     * Get pkGradeflowid
     *
     * @return integer
     */
    public function getPkGradeflowid()
    {
        return $this->pkGradeflowid;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return Gradeflow
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Gradeflow
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

    /**
     * Set fkRoleid
     *
     * @param \Application\Entity\Role $fkRoleid
     *
     * @return Gradeflow
     */
    public function setFkRoleid(\Application\Entity\Role $fkRoleid = null)
    {
        $this->fkRoleid = $fkRoleid;

        return $this;
    }

    /**
     * Get fkRoleid
     *
     * @return \Application\Entity\Role
     */
    public function getFkRoleid()
    {
        return $this->fkRoleid;
    }
}
