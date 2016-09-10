<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Selectionlistupload
 *
 * @ORM\Table(name="selectionlistupload", indexes={@ORM\Index(name="UPLOADEDBY", columns={"UPLOADED_BY"}), @ORM\Index(name="ACADEMICPERIOD_IDX", columns={"FK_ACADEMICPERIODID"})})
 * @ORM\Entity
 */
class Selectionlistupload
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_UPLOADID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkUploadid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_UPLOADED", type="datetime", nullable=true)
     */
    private $dateUploaded;

    /**
     * @var string
     *
     * @ORM\Column(name="FILE_CONTENTS", type="text", nullable=true)
     */
    private $fileContents;

    /**
     * @var string
     *
     * @ORM\Column(name="FILE_NAME", type="string", length=254, nullable=true)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="REGNUMBER_ASSIGNED", type="text", nullable=true)
     */
    private $regnumberAssigned = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="SAVED", type="text", nullable=true)
     */
    private $saved = '0';

    /**
     * @var \Application\Entity\Academicyear
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Academicyear")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_ACADEMICPERIODID", referencedColumnName="PK_ACADEMICPERIODID")
     * })
     */
    private $fkAcademicperiodid;

    /**
     * @var \Application\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="UPLOADED_BY", referencedColumnName="PK_USERID")
     * })
     */
    private $uploadedBy;



    /**
     * Get pkUploadid
     *
     * @return integer
     */
    public function getPkUploadid()
    {
        return $this->pkUploadid;
    }

    /**
     * Set dateUploaded
     *
     * @param \DateTime $dateUploaded
     *
     * @return Selectionlistupload
     */
    public function setDateUploaded($dateUploaded)
    {
        $this->dateUploaded = $dateUploaded;

        return $this;
    }

    /**
     * Get dateUploaded
     *
     * @return \DateTime
     */
    public function getDateUploaded()
    {
        return $this->dateUploaded;
    }

    /**
     * Set fileContents
     *
     * @param string $fileContents
     *
     * @return Selectionlistupload
     */
    public function setFileContents($fileContents)
    {
        $this->fileContents = $fileContents;

        return $this;
    }

    /**
     * Get fileContents
     *
     * @return string
     */
    public function getFileContents()
    {
        return $this->fileContents;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return Selectionlistupload
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set regnumberAssigned
     *
     * @param string $regnumberAssigned
     *
     * @return Selectionlistupload
     */
    public function setRegnumberAssigned($regnumberAssigned)
    {
        $this->regnumberAssigned = $regnumberAssigned;

        return $this;
    }

    /**
     * Get regnumberAssigned
     *
     * @return string
     */
    public function getRegnumberAssigned()
    {
        return $this->regnumberAssigned;
    }

    /**
     * Set saved
     *
     * @param string $saved
     *
     * @return Selectionlistupload
     */
    public function setSaved($saved)
    {
        $this->saved = $saved;

        return $this;
    }

    /**
     * Get saved
     *
     * @return string
     */
    public function getSaved()
    {
        return $this->saved;
    }

    /**
     * Set fkAcademicperiodid
     *
     * @param \Application\Entity\Academicyear $fkAcademicperiodid
     *
     * @return Selectionlistupload
     */
    public function setFkAcademicperiodid(\Application\Entity\Academicyear $fkAcademicperiodid = null)
    {
        $this->fkAcademicperiodid = $fkAcademicperiodid;

        return $this;
    }

    /**
     * Get fkAcademicperiodid
     *
     * @return \Application\Entity\Academicyear
     */
    public function getFkAcademicperiodid()
    {
        return $this->fkAcademicperiodid;
    }

    /**
     * Set uploadedBy
     *
     * @param \Application\Entity\User $uploadedBy
     *
     * @return Selectionlistupload
     */
    public function setUploadedBy(\Application\Entity\User $uploadedBy = null)
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
    }

    /**
     * Get uploadedBy
     *
     * @return \Application\Entity\User
     */
    public function getUploadedBy()
    {
        return $this->uploadedBy;
    }
}
