<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Selectionlist
 *
 * @ORM\Table(name="selectionlist", indexes={@ORM\Index(name="recordclass_idx", columns={"FK_CLASSID"}), @ORM\Index(name="recordupload_idx", columns={"FK_UPLOADID"}), @ORM\Index(name="recordacademicperiod_idx", columns={"FK_ACADEMICPERIODID"}), @ORM\Index(name="campus_idx", columns={"FK_CAMPUSID"}), @ORM\Index(name="entrymanner_idx", columns={"FK_ENTRYMANNERID"}), @ORM\Index(name="nationality_idx", columns={"FK_COUNTRYID"}), @ORM\Index(name="recorddropout_idx", columns={"FK_DROPOUTSTATUS"})})
 * @ORM\Entity
 */
class Selectionlist
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_SLID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkSlid;

    /**
     * @var string
     *
     * @ORM\Column(name="FIRSTNAME", type="string", length=50, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="SURNAME", type="string", length=50, nullable=true)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="MIDDLENAME", type="string", length=255, nullable=true)
     */
    private $middlename;

    /**
     * @var string
     *
     * @ORM\Column(name="GENDER", type="string", length=1, nullable=true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="CENTER_NUMBER", type="string", length=50, nullable=true)
     */
    private $centerNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="GENERATED_USERNAME", type="string", length=50, nullable=true)
     */
    private $generatedUsername;

    /**
     * @var string
     *
     * @ORM\Column(name="GENERATED_PASSWORD", type="string", length=50, nullable=true)
     */
    private $generatedPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="GENERATED_REGNUMBER", type="string", length=50, nullable=true)
     */
    private $generatedRegnumber = '';

    /**
     * @var string
     *
     * @ORM\Column(name="GENERATED_FIN_ACCOUNTNO", type="string", length=50, nullable=true)
     */
    private $generatedFinAccountno;

    /**
     * @var integer
     *
     * @ORM\Column(name="STUDENT_NUMBER", type="integer", nullable=true)
     */
    private $studentNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="ACCOUNTSTATUS", type="text", nullable=true)
     */
    private $accountstatus = '1';

    /**
     * @var \Application\Entity\Campus
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Campus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_CAMPUSID", referencedColumnName="PK_CAMPUSID")
     * })
     */
    private $fkCampusid;

    /**
     * @var \Application\Entity\Entrymanner
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Entrymanner")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_ENTRYMANNERID", referencedColumnName="PK_ENTRYMANNERID")
     * })
     */
    private $fkEntrymannerid;

    /**
     * @var \Application\Entity\Country
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_COUNTRYID", referencedColumnName="PK_COUNTRYID")
     * })
     */
    private $fkCountryid;

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
     * @var \Application\Entity\Classes
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Classes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_CLASSID", referencedColumnName="PK_CLASSID")
     * })
     */
    private $fkClassid;

    /**
     * @var \Application\Entity\Dropoutstudent
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Dropoutstudent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_DROPOUTSTATUS", referencedColumnName="PK_DSID")
     * })
     */
    private $fkDropoutstatus;

    /**
     * @var \Application\Entity\Selectionlistupload
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Selectionlistupload")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_UPLOADID", referencedColumnName="PK_UPLOADID")
     * })
     */
    private $fkUploadid;



    /**
     * Get pkSlid
     *
     * @return integer
     */
    public function getPkSlid()
    {
        return $this->pkSlid;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Selectionlist
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return Selectionlist
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     *
     * @return Selectionlist
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Selectionlist
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set centerNumber
     *
     * @param string $centerNumber
     *
     * @return Selectionlist
     */
    public function setCenterNumber($centerNumber)
    {
        $this->centerNumber = $centerNumber;

        return $this;
    }

    /**
     * Get centerNumber
     *
     * @return string
     */
    public function getCenterNumber()
    {
        return $this->centerNumber;
    }

    /**
     * Set generatedUsername
     *
     * @param string $generatedUsername
     *
     * @return Selectionlist
     */
    public function setGeneratedUsername($generatedUsername)
    {
        $this->generatedUsername = $generatedUsername;

        return $this;
    }

    /**
     * Get generatedUsername
     *
     * @return string
     */
    public function getGeneratedUsername()
    {
        return $this->generatedUsername;
    }

    /**
     * Set generatedPassword
     *
     * @param string $generatedPassword
     *
     * @return Selectionlist
     */
    public function setGeneratedPassword($generatedPassword)
    {
        $this->generatedPassword = $generatedPassword;

        return $this;
    }

    /**
     * Get generatedPassword
     *
     * @return string
     */
    public function getGeneratedPassword()
    {
        return $this->generatedPassword;
    }

    /**
     * Set generatedRegnumber
     *
     * @param string $generatedRegnumber
     *
     * @return Selectionlist
     */
    public function setGeneratedRegnumber($generatedRegnumber)
    {
        $this->generatedRegnumber = $generatedRegnumber;

        return $this;
    }

    /**
     * Get generatedRegnumber
     *
     * @return string
     */
    public function getGeneratedRegnumber()
    {
        return $this->generatedRegnumber;
    }

    /**
     * Set generatedFinAccountno
     *
     * @param string $generatedFinAccountno
     *
     * @return Selectionlist
     */
    public function setGeneratedFinAccountno($generatedFinAccountno)
    {
        $this->generatedFinAccountno = $generatedFinAccountno;

        return $this;
    }

    /**
     * Get generatedFinAccountno
     *
     * @return string
     */
    public function getGeneratedFinAccountno()
    {
        return $this->generatedFinAccountno;
    }

    /**
     * Set studentNumber
     *
     * @param integer $studentNumber
     *
     * @return Selectionlist
     */
    public function setStudentNumber($studentNumber)
    {
        $this->studentNumber = $studentNumber;

        return $this;
    }

    /**
     * Get studentNumber
     *
     * @return integer
     */
    public function getStudentNumber()
    {
        return $this->studentNumber;
    }

    /**
     * Set accountstatus
     *
     * @param string $accountstatus
     *
     * @return Selectionlist
     */
    public function setAccountstatus($accountstatus)
    {
        $this->accountstatus = $accountstatus;

        return $this;
    }

    /**
     * Get accountstatus
     *
     * @return string
     */
    public function getAccountstatus()
    {
        return $this->accountstatus;
    }

    /**
     * Set fkCampusid
     *
     * @param \Application\Entity\Campus $fkCampusid
     *
     * @return Selectionlist
     */
    public function setFkCampusid(\Application\Entity\Campus $fkCampusid = null)
    {
        $this->fkCampusid = $fkCampusid;

        return $this;
    }

    /**
     * Get fkCampusid
     *
     * @return \Application\Entity\Campus
     */
    public function getFkCampusid()
    {
        return $this->fkCampusid;
    }

    /**
     * Set fkEntrymannerid
     *
     * @param \Application\Entity\Entrymanner $fkEntrymannerid
     *
     * @return Selectionlist
     */
    public function setFkEntrymannerid(\Application\Entity\Entrymanner $fkEntrymannerid = null)
    {
        $this->fkEntrymannerid = $fkEntrymannerid;

        return $this;
    }

    /**
     * Get fkEntrymannerid
     *
     * @return \Application\Entity\Entrymanner
     */
    public function getFkEntrymannerid()
    {
        return $this->fkEntrymannerid;
    }

    /**
     * Set fkCountryid
     *
     * @param \Application\Entity\Country $fkCountryid
     *
     * @return Selectionlist
     */
    public function setFkCountryid(\Application\Entity\Country $fkCountryid = null)
    {
        $this->fkCountryid = $fkCountryid;

        return $this;
    }

    /**
     * Get fkCountryid
     *
     * @return \Application\Entity\Country
     */
    public function getFkCountryid()
    {
        return $this->fkCountryid;
    }

    /**
     * Set fkAcademicperiodid
     *
     * @param \Application\Entity\Academicyear $fkAcademicperiodid
     *
     * @return Selectionlist
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
     * Set fkClassid
     *
     * @param \Application\Entity\Classes $fkClassid
     *
     * @return Selectionlist
     */
    public function setFkClassid(\Application\Entity\Classes $fkClassid = null)
    {
        $this->fkClassid = $fkClassid;

        return $this;
    }

    /**
     * Get fkClassid
     *
     * @return \Application\Entity\Classes
     */
    public function getFkClassid()
    {
        return $this->fkClassid;
    }

    /**
     * Set fkDropoutstatus
     *
     * @param \Application\Entity\Dropoutstudent $fkDropoutstatus
     *
     * @return Selectionlist
     */
    public function setFkDropoutstatus(\Application\Entity\Dropoutstudent $fkDropoutstatus = null)
    {
        $this->fkDropoutstatus = $fkDropoutstatus;

        return $this;
    }

    /**
     * Get fkDropoutstatus
     *
     * @return \Application\Entity\Dropoutstudent
     */
    public function getFkDropoutstatus()
    {
        return $this->fkDropoutstatus;
    }

    /**
     * Set fkUploadid
     *
     * @param \Application\Entity\Selectionlistupload $fkUploadid
     *
     * @return Selectionlist
     */
    public function setFkUploadid(\Application\Entity\Selectionlistupload $fkUploadid = null)
    {
        $this->fkUploadid = $fkUploadid;

        return $this;
    }

    /**
     * Get fkUploadid
     *
     * @return \Application\Entity\Selectionlistupload
     */
    public function getFkUploadid()
    {
        return $this->fkUploadid;
    }
}
