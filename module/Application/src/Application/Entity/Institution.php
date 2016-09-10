<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Institution
 *
 * @ORM\Table(name="institution")
 * @ORM\Entity
 */
class Institution
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_IID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkIid;

    /**
     * @var string
     *
     * @ORM\Column(name="LONG_NAME", type="string", length=255, nullable=true)
     */
    private $longName;

    /**
     * @var string
     *
     * @ORM\Column(name="SHORT_NAME", type="string", length=200, nullable=true)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="ADDRESS_LINE1", type="string", length=100, nullable=true)
     */
    private $addressLine1;

    /**
     * @var string
     *
     * @ORM\Column(name="ADDRESS_LINE2", type="string", length=100, nullable=true)
     */
    private $addressLine2;

    /**
     * @var string
     *
     * @ORM\Column(name="ADDRESS_LINE3", type="string", length=100, nullable=true)
     */
    private $addressLine3;

    /**
     * @var string
     *
     * @ORM\Column(name="ADDRESS_LINE4", type="string", length=100, nullable=true)
     */
    private $addressLine4;

    /**
     * @var string
     *
     * @ORM\Column(name="PHONE_NUMBER", type="string", length=30, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL_ADDRESS", type="string", length=45, nullable=true)
     */
    private $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="PRINCIPAL_NAME", type="string", length=100, nullable=true)
     */
    private $principalName;

    /**
     * @var string
     *
     * @ORM\Column(name="QUALIFICATIONS", type="string", length=200, nullable=true)
     */
    private $qualifications;



    /**
     * Get pkIid
     *
     * @return integer
     */
    public function getPkIid()
    {
        return $this->pkIid;
    }

    /**
     * Set longName
     *
     * @param string $longName
     *
     * @return Institution
     */
    public function setLongName($longName)
    {
        $this->longName = $longName;

        return $this;
    }

    /**
     * Get longName
     *
     * @return string
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Institution
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
     * Set addressLine1
     *
     * @param string $addressLine1
     *
     * @return Institution
     */
    public function setAddressLine1($addressLine1)
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    /**
     * Get addressLine1
     *
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * Set addressLine2
     *
     * @param string $addressLine2
     *
     * @return Institution
     */
    public function setAddressLine2($addressLine2)
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    /**
     * Get addressLine2
     *
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * Set addressLine3
     *
     * @param string $addressLine3
     *
     * @return Institution
     */
    public function setAddressLine3($addressLine3)
    {
        $this->addressLine3 = $addressLine3;

        return $this;
    }

    /**
     * Get addressLine3
     *
     * @return string
     */
    public function getAddressLine3()
    {
        return $this->addressLine3;
    }

    /**
     * Set addressLine4
     *
     * @param string $addressLine4
     *
     * @return Institution
     */
    public function setAddressLine4($addressLine4)
    {
        $this->addressLine4 = $addressLine4;

        return $this;
    }

    /**
     * Get addressLine4
     *
     * @return string
     */
    public function getAddressLine4()
    {
        return $this->addressLine4;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Institution
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set emailAddress
     *
     * @param string $emailAddress
     *
     * @return Institution
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set principalName
     *
     * @param string $principalName
     *
     * @return Institution
     */
    public function setPrincipalName($principalName)
    {
        $this->principalName = $principalName;

        return $this;
    }

    /**
     * Get principalName
     *
     * @return string
     */
    public function getPrincipalName()
    {
        return $this->principalName;
    }

    /**
     * Set qualifications
     *
     * @param string $qualifications
     *
     * @return Institution
     */
    public function setQualifications($qualifications)
    {
        $this->qualifications = $qualifications;

        return $this;
    }

    /**
     * Get qualifications
     *
     * @return string
     */
    public function getQualifications()
    {
        return $this->qualifications;
    }
}
