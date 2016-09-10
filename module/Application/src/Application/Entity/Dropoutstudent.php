<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dropoutstudent
 *
 * @ORM\Table(name="dropoutstudent", indexes={@ORM\Index(name="dropoutstatus_idx", columns={"FK_DSTATUSID"}), @ORM\Index(name="selectedyear_idx", columns={"SELECTEDYEAR"}), @ORM\Index(name="loggedby", columns={"LOGGEDBY"})})
 * @ORM\Entity
 */
class Dropoutstudent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_DSID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkDsid;

    /**
     * @var integer
     *
     * @ORM\Column(name="FK_UPLOADID", type="integer", nullable=true)
     */
    private $fkUploadid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_DROUPEDOUT", type="datetime", nullable=true)
     */
    private $dateDroupedout;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \Application\Entity\Dropoutstatus
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Dropoutstatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_DSTATUSID", referencedColumnName="PK_DSTATUSID")
     * })
     */
    private $fkDstatusid;

    /**
     * @var \Application\Entity\Staff
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Staff")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="LOGGEDBY", referencedColumnName="PK_STAFFID")
     * })
     */
    private $loggedby;

    /**
     * @var \Application\Entity\Academicyear
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Academicyear")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="SELECTEDYEAR", referencedColumnName="PK_ACADEMICPERIODID")
     * })
     */
    private $selectedyear;



    /**
     * Get pkDsid
     *
     * @return integer
     */
    public function getPkDsid()
    {
        return $this->pkDsid;
    }

    /**
     * Set fkUploadid
     *
     * @param integer $fkUploadid
     *
     * @return Dropoutstudent
     */
    public function setFkUploadid($fkUploadid)
    {
        $this->fkUploadid = $fkUploadid;

        return $this;
    }

    /**
     * Get fkUploadid
     *
     * @return integer
     */
    public function getFkUploadid()
    {
        return $this->fkUploadid;
    }

    /**
     * Set dateDroupedout
     *
     * @param \DateTime $dateDroupedout
     *
     * @return Dropoutstudent
     */
    public function setDateDroupedout($dateDroupedout)
    {
        $this->dateDroupedout = $dateDroupedout;

        return $this;
    }

    /**
     * Get dateDroupedout
     *
     * @return \DateTime
     */
    public function getDateDroupedout()
    {
        return $this->dateDroupedout;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Dropoutstudent
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
     * Set fkDstatusid
     *
     * @param \Application\Entity\Dropoutstatus $fkDstatusid
     *
     * @return Dropoutstudent
     */
    public function setFkDstatusid(\Application\Entity\Dropoutstatus $fkDstatusid = null)
    {
        $this->fkDstatusid = $fkDstatusid;

        return $this;
    }

    /**
     * Get fkDstatusid
     *
     * @return \Application\Entity\Dropoutstatus
     */
    public function getFkDstatusid()
    {
        return $this->fkDstatusid;
    }

    /**
     * Set loggedby
     *
     * @param \Application\Entity\Staff $loggedby
     *
     * @return Dropoutstudent
     */
    public function setLoggedby(\Application\Entity\Staff $loggedby = null)
    {
        $this->loggedby = $loggedby;

        return $this;
    }

    /**
     * Get loggedby
     *
     * @return \Application\Entity\Staff
     */
    public function getLoggedby()
    {
        return $this->loggedby;
    }

    /**
     * Set selectedyear
     *
     * @param \Application\Entity\Academicyear $selectedyear
     *
     * @return Dropoutstudent
     */
    public function setSelectedyear(\Application\Entity\Academicyear $selectedyear = null)
    {
        $this->selectedyear = $selectedyear;

        return $this;
    }

    /**
     * Get selectedyear
     *
     * @return \Application\Entity\Academicyear
     */
    public function getSelectedyear()
    {
        return $this->selectedyear;
    }
}
