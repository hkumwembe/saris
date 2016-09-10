<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Assessmentitem
 *
 * @ORM\Table(name="assessmentitem", indexes={@ORM\Index(name="Assessment_owner", columns={"FK_STAFFID"}), @ORM\Index(name="module_allocated", columns={"FK_CLASSMODULEID"}), @ORM\Index(name="assessmenttype", columns={"FK_ATID"})})
 * @ORM\Entity
 */
class Assessmentitem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_AIID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkAiid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATEDON", type="datetime", nullable=false)
     */
    private $createdon;

    /**
     * @var string
     *
     * @ORM\Column(name="ASSESSMENT_TITLE", type="string", length=100, nullable=false)
     */
    private $assessmentTitle;

    /**
     * @var integer
     *
     * @ORM\Column(name="WEIGHTING", type="integer", nullable=false)
     */
    private $weighting;

    /**
     * @var string
     *
     * @ORM\Column(name="SHORT_NAME", type="string", length=25, nullable=true)
     */
    private $shortName;

    /**
     * @var \Application\Entity\Assessmenttype
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Assessmenttype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_ATID", referencedColumnName="PK_ATID")
     * })
     */
    private $fkAtid;

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
     * @var \Application\Entity\Classmodule
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Classmodule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_CLASSMODULEID", referencedColumnName="PK_CLASSMODULEID")
     * })
     */
    private $fkClassmoduleid;



    /**
     * Get pkAiid
     *
     * @return integer
     */
    public function getPkAiid()
    {
        return $this->pkAiid;
    }

    /**
     * Set createdon
     *
     * @param \DateTime $createdon
     *
     * @return Assessmentitem
     */
    public function setCreatedon($createdon)
    {
        $this->createdon = $createdon;

        return $this;
    }

    /**
     * Get createdon
     *
     * @return \DateTime
     */
    public function getCreatedon()
    {
        return $this->createdon;
    }

    /**
     * Set assessmentTitle
     *
     * @param string $assessmentTitle
     *
     * @return Assessmentitem
     */
    public function setAssessmentTitle($assessmentTitle)
    {
        $this->assessmentTitle = $assessmentTitle;

        return $this;
    }

    /**
     * Get assessmentTitle
     *
     * @return string
     */
    public function getAssessmentTitle()
    {
        return $this->assessmentTitle;
    }

    /**
     * Set weighting
     *
     * @param integer $weighting
     *
     * @return Assessmentitem
     */
    public function setWeighting($weighting)
    {
        $this->weighting = $weighting;

        return $this;
    }

    /**
     * Get weighting
     *
     * @return integer
     */
    public function getWeighting()
    {
        return $this->weighting;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Assessmentitem
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set fkAtid
     *
     * @param \Application\Entity\Assessmenttype $fkAtid
     *
     * @return Assessmentitem
     */
    public function setFkAtid(\Application\Entity\Assessmenttype $fkAtid = null)
    {
        $this->fkAtid = $fkAtid;

        return $this;
    }

    /**
     * Get fkAtid
     *
     * @return \Application\Entity\Assessmenttype
     */
    public function getFkAtid()
    {
        return $this->fkAtid;
    }

    /**
     * Set fkStaffid
     *
     * @param \Application\Entity\Staff $fkStaffid
     *
     * @return Assessmentitem
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
     * Set fkClassmoduleid
     *
     * @param \Application\Entity\Classmodule $fkClassmoduleid
     *
     * @return Assessmentitem
     */
    public function setFkClassmoduleid(\Application\Entity\Classmodule $fkClassmoduleid = null)
    {
        $this->fkClassmoduleid = $fkClassmoduleid;

        return $this;
    }

    /**
     * Get fkClassmoduleid
     *
     * @return \Application\Entity\Classmodule
     */
    public function getFkClassmoduleid()
    {
        return $this->fkClassmoduleid;
    }
}
