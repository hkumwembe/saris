<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sponsor
 *
 * @ORM\Table(name="sponsor")
 * @ORM\Entity
 */
class Sponsor
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_SPONSORID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkSponsorid;

    /**
     * @var string
     *
     * @ORM\Column(name="SPONSOR_NAME", type="string", length=45, nullable=false)
     */
    private $sponsorName;

    /**
     * @var string
     *
     * @ORM\Column(name="POSTAL_ADDRESS", type="text", length=65535, nullable=true)
     */
    private $postalAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="CONTACT__PERSON", type="string", length=45, nullable=true)
     */
    private $contactPerson;

    /**
     * @var string
     *
     * @ORM\Column(name="PHONE_NUMBER", type="string", length=45, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="DOCUMENT_URL", type="string", length=45, nullable=true)
     */
    private $documentUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="CURRENT_STATUS", type="text", nullable=true)
     */
    private $currentStatus = '1';



    /**
     * Get pkSponsorid
     *
     * @return integer
     */
    public function getPkSponsorid()
    {
        return $this->pkSponsorid;
    }

    /**
     * Set sponsorName
     *
     * @param string $sponsorName
     *
     * @return Sponsor
     */
    public function setSponsorName($sponsorName)
    {
        $this->sponsorName = $sponsorName;

        return $this;
    }

    /**
     * Get sponsorName
     *
     * @return string
     */
    public function getSponsorName()
    {
        return $this->sponsorName;
    }

    /**
     * Set postalAddress
     *
     * @param string $postalAddress
     *
     * @return Sponsor
     */
    public function setPostalAddress($postalAddress)
    {
        $this->postalAddress = $postalAddress;

        return $this;
    }

    /**
     * Get postalAddress
     *
     * @return string
     */
    public function getPostalAddress()
    {
        return $this->postalAddress;
    }

    /**
     * Set contactPerson
     *
     * @param string $contactPerson
     *
     * @return Sponsor
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    /**
     * Get contactPerson
     *
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Sponsor
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
     * Set description
     *
     * @param string $description
     *
     * @return Sponsor
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
     * Set documentUrl
     *
     * @param string $documentUrl
     *
     * @return Sponsor
     */
    public function setDocumentUrl($documentUrl)
    {
        $this->documentUrl = $documentUrl;

        return $this;
    }

    /**
     * Get documentUrl
     *
     * @return string
     */
    public function getDocumentUrl()
    {
        return $this->documentUrl;
    }

    /**
     * Set currentStatus
     *
     * @param string $currentStatus
     *
     * @return Sponsor
     */
    public function setCurrentStatus($currentStatus)
    {
        $this->currentStatus = $currentStatus;

        return $this;
    }

    /**
     * Get currentStatus
     *
     * @return string
     */
    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }
}
