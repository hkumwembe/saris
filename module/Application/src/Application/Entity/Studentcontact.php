<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Studentcontact
 *
 * @ORM\Table(name="studentcontact", indexes={@ORM\Index(name="fk_studentcontact_idx", columns={"FK_STUDENTID"}), @ORM\Index(name="nokrelationship", columns={"NOKRELATIONSHIP"})})
 * @ORM\Entity
 */
class Studentcontact
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_CONTACTID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkContactid;

    /**
     * @var string
     *
     * @ORM\Column(name="MOBILE", type="string", length=30, nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="LANDLINE", type="string", length=10, nullable=true)
     */
    private $landline;

    /**
     * @var string
     *
     * @ORM\Column(name="POSTALADDRESS", type="text", length=65535, nullable=true)
     */
    private $postaladdress;

    /**
     * @var string
     *
     * @ORM\Column(name="NOKTITLE", type="string", length=45, nullable=true)
     */
    private $noktitle;

    /**
     * @var string
     *
     * @ORM\Column(name="NOKFIRSTNAME", type="string", length=45, nullable=true)
     */
    private $nokfirstname;

    /**
     * @var string
     *
     * @ORM\Column(name="NOKSURNAME", type="string", length=45, nullable=true)
     */
    private $noksurname;

    /**
     * @var string
     *
     * @ORM\Column(name="NOKPOSTALADDRESS", type="text", length=65535, nullable=true)
     */
    private $nokpostaladdress;

    /**
     * @var string
     *
     * @ORM\Column(name="NOKMOBILE", type="string", length=30, nullable=true)
     */
    private $nokmobile;

    /**
     * @var string
     *
     * @ORM\Column(name="NOKEMAIL", type="string", length=45, nullable=true)
     */
    private $nokemail;

    /**
     * @var string
     *
     * @ORM\Column(name="EMGTITLE", type="string", length=15, nullable=true)
     */
    private $emgtitle;

    /**
     * @var string
     *
     * @ORM\Column(name="EMGFIRSTNAME", type="string", length=45, nullable=true)
     */
    private $emgfirstname;

    /**
     * @var string
     *
     * @ORM\Column(name="EMGSURNAME", type="string", length=45, nullable=true)
     */
    private $emgsurname;

    /**
     * @var string
     *
     * @ORM\Column(name="EMGPOSTALADDRESS", type="string", length=255, nullable=true)
     */
    private $emgpostaladdress;

    /**
     * @var string
     *
     * @ORM\Column(name="EMGPHONE", type="string", length=25, nullable=true)
     */
    private $emgphone;

    /**
     * @var string
     *
     * @ORM\Column(name="EMGEMAILADDRESS", type="string", length=30, nullable=true)
     */
    private $emgemailaddress;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAILADDRESS", type="string", length=180, nullable=true)
     */
    private $emailaddress;

    /**
     * @var \Application\Entity\Student
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Student")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_STUDENTID", referencedColumnName="PK_STUDENTID")
     * })
     */
    private $fkStudentid;

    /**
     * @var \Application\Entity\Relation
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Relation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="NOKRELATIONSHIP", referencedColumnName="PK_RELATIONID")
     * })
     */
    private $nokrelationship;



    /**
     * Get pkContactid
     *
     * @return integer
     */
    public function getPkContactid()
    {
        return $this->pkContactid;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Studentcontact
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set landline
     *
     * @param string $landline
     *
     * @return Studentcontact
     */
    public function setLandline($landline)
    {
        $this->landline = $landline;

        return $this;
    }

    /**
     * Get landline
     *
     * @return string
     */
    public function getLandline()
    {
        return $this->landline;
    }

    /**
     * Set postaladdress
     *
     * @param string $postaladdress
     *
     * @return Studentcontact
     */
    public function setPostaladdress($postaladdress)
    {
        $this->postaladdress = $postaladdress;

        return $this;
    }

    /**
     * Get postaladdress
     *
     * @return string
     */
    public function getPostaladdress()
    {
        return $this->postaladdress;
    }

    /**
     * Set noktitle
     *
     * @param string $noktitle
     *
     * @return Studentcontact
     */
    public function setNoktitle($noktitle)
    {
        $this->noktitle = $noktitle;

        return $this;
    }

    /**
     * Get noktitle
     *
     * @return string
     */
    public function getNoktitle()
    {
        return $this->noktitle;
    }

    /**
     * Set nokfirstname
     *
     * @param string $nokfirstname
     *
     * @return Studentcontact
     */
    public function setNokfirstname($nokfirstname)
    {
        $this->nokfirstname = $nokfirstname;

        return $this;
    }

    /**
     * Get nokfirstname
     *
     * @return string
     */
    public function getNokfirstname()
    {
        return $this->nokfirstname;
    }

    /**
     * Set noksurname
     *
     * @param string $noksurname
     *
     * @return Studentcontact
     */
    public function setNoksurname($noksurname)
    {
        $this->noksurname = $noksurname;

        return $this;
    }

    /**
     * Get noksurname
     *
     * @return string
     */
    public function getNoksurname()
    {
        return $this->noksurname;
    }

    /**
     * Set nokpostaladdress
     *
     * @param string $nokpostaladdress
     *
     * @return Studentcontact
     */
    public function setNokpostaladdress($nokpostaladdress)
    {
        $this->nokpostaladdress = $nokpostaladdress;

        return $this;
    }

    /**
     * Get nokpostaladdress
     *
     * @return string
     */
    public function getNokpostaladdress()
    {
        return $this->nokpostaladdress;
    }

    /**
     * Set nokmobile
     *
     * @param string $nokmobile
     *
     * @return Studentcontact
     */
    public function setNokmobile($nokmobile)
    {
        $this->nokmobile = $nokmobile;

        return $this;
    }

    /**
     * Get nokmobile
     *
     * @return string
     */
    public function getNokmobile()
    {
        return $this->nokmobile;
    }

    /**
     * Set nokemail
     *
     * @param string $nokemail
     *
     * @return Studentcontact
     */
    public function setNokemail($nokemail)
    {
        $this->nokemail = $nokemail;

        return $this;
    }

    /**
     * Get nokemail
     *
     * @return string
     */
    public function getNokemail()
    {
        return $this->nokemail;
    }

    /**
     * Set emgtitle
     *
     * @param string $emgtitle
     *
     * @return Studentcontact
     */
    public function setEmgtitle($emgtitle)
    {
        $this->emgtitle = $emgtitle;

        return $this;
    }

    /**
     * Get emgtitle
     *
     * @return string
     */
    public function getEmgtitle()
    {
        return $this->emgtitle;
    }

    /**
     * Set emgfirstname
     *
     * @param string $emgfirstname
     *
     * @return Studentcontact
     */
    public function setEmgfirstname($emgfirstname)
    {
        $this->emgfirstname = $emgfirstname;

        return $this;
    }

    /**
     * Get emgfirstname
     *
     * @return string
     */
    public function getEmgfirstname()
    {
        return $this->emgfirstname;
    }

    /**
     * Set emgsurname
     *
     * @param string $emgsurname
     *
     * @return Studentcontact
     */
    public function setEmgsurname($emgsurname)
    {
        $this->emgsurname = $emgsurname;

        return $this;
    }

    /**
     * Get emgsurname
     *
     * @return string
     */
    public function getEmgsurname()
    {
        return $this->emgsurname;
    }

    /**
     * Set emgpostaladdress
     *
     * @param string $emgpostaladdress
     *
     * @return Studentcontact
     */
    public function setEmgpostaladdress($emgpostaladdress)
    {
        $this->emgpostaladdress = $emgpostaladdress;

        return $this;
    }

    /**
     * Get emgpostaladdress
     *
     * @return string
     */
    public function getEmgpostaladdress()
    {
        return $this->emgpostaladdress;
    }

    /**
     * Set emgphone
     *
     * @param string $emgphone
     *
     * @return Studentcontact
     */
    public function setEmgphone($emgphone)
    {
        $this->emgphone = $emgphone;

        return $this;
    }

    /**
     * Get emgphone
     *
     * @return string
     */
    public function getEmgphone()
    {
        return $this->emgphone;
    }

    /**
     * Set emgemailaddress
     *
     * @param string $emgemailaddress
     *
     * @return Studentcontact
     */
    public function setEmgemailaddress($emgemailaddress)
    {
        $this->emgemailaddress = $emgemailaddress;

        return $this;
    }

    /**
     * Get emgemailaddress
     *
     * @return string
     */
    public function getEmgemailaddress()
    {
        return $this->emgemailaddress;
    }

    /**
     * Set emailaddress
     *
     * @param string $emailaddress
     *
     * @return Studentcontact
     */
    public function setEmailaddress($emailaddress)
    {
        $this->emailaddress = $emailaddress;

        return $this;
    }

    /**
     * Get emailaddress
     *
     * @return string
     */
    public function getEmailaddress()
    {
        return $this->emailaddress;
    }

    /**
     * Set fkStudentid
     *
     * @param \Application\Entity\Student $fkStudentid
     *
     * @return Studentcontact
     */
    public function setFkStudentid(\Application\Entity\Student $fkStudentid = null)
    {
        $this->fkStudentid = $fkStudentid;

        return $this;
    }

    /**
     * Get fkStudentid
     *
     * @return \Application\Entity\Student
     */
    public function getFkStudentid()
    {
        return $this->fkStudentid;
    }

    /**
     * Set nokrelationship
     *
     * @param \Application\Entity\Relation $nokrelationship
     *
     * @return Studentcontact
     */
    public function setNokrelationship(\Application\Entity\Relation $nokrelationship = null)
    {
        $this->nokrelationship = $nokrelationship;

        return $this;
    }

    /**
     * Get nokrelationship
     *
     * @return \Application\Entity\Relation
     */
    public function getNokrelationship()
    {
        return $this->nokrelationship;
    }
}
