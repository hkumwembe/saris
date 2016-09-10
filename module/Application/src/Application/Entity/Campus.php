<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Campus
 *
 * @ORM\Table(name="campus")
 * @ORM\Entity
 */
class Campus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_CAMPUSID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkCampusid;

    /**
     * @var string
     *
     * @ORM\Column(name="CAMPUS_NAME", type="string", length=45, nullable=false)
     */
    private $campusName;

    /**
     * @var string
     *
     * @ORM\Column(name="POSTAL_ADDRESS", type="string", length=255, nullable=true)
     */
    private $postalAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=60, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="PHONE", type="string", length=22, nullable=true)
     */
    private $phone;



    /**
     * Get pkCampusid
     *
     * @return integer
     */
    public function getPkCampusid()
    {
        return $this->pkCampusid;
    }

    /**
     * Set campusName
     *
     * @param string $campusName
     *
     * @return Campus
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;

        return $this;
    }

    /**
     * Get campusName
     *
     * @return string
     */
    public function getCampusName()
    {
        return $this->campusName;
    }

    /**
     * Set postalAddress
     *
     * @param string $postalAddress
     *
     * @return Campus
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
     * Set email
     *
     * @param string $email
     *
     * @return Campus
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Campus
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
