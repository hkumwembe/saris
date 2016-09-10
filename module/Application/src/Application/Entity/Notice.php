<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notice
 *
 * @ORM\Table(name="notice", indexes={@ORM\Index(name="noticerole", columns={"ACCOUNTTYPE"}), @ORM\Index(name="usernotice", columns={"CAPTURED_BY"})})
 * @ORM\Entity
 */
class Notice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_NOTICEID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkNoticeid;

    /**
     * @var string
     *
     * @ORM\Column(name="HEADER", type="string", length=128, nullable=false)
     */
    private $header;

    /**
     * @var string
     *
     * @ORM\Column(name="BODY", type="text", length=65535, nullable=false)
     */
    private $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_PUBLISHED", type="datetime", nullable=true)
     */
    private $datePublished;

    /**
     * @var string
     *
     * @ORM\Column(name="IS_ACTIVE", type="text", nullable=true)
     */
    private $isActive = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ACCOUNTTYPE", type="text", nullable=true)
     */
    private $accounttype = 'STUDENT';

    /**
     * @var \Application\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAPTURED_BY", referencedColumnName="PK_USERID")
     * })
     */
    private $capturedBy;



    /**
     * Get pkNoticeid
     *
     * @return integer
     */
    public function getPkNoticeid()
    {
        return $this->pkNoticeid;
    }

    /**
     * Set header
     *
     * @param string $header
     *
     * @return Notice
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Notice
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set datePublished
     *
     * @param \DateTime $datePublished
     *
     * @return Notice
     */
    public function setDatePublished($datePublished)
    {
        $this->datePublished = $datePublished;

        return $this;
    }

    /**
     * Get datePublished
     *
     * @return \DateTime
     */
    public function getDatePublished()
    {
        return $this->datePublished;
    }

    /**
     * Set isActive
     *
     * @param string $isActive
     *
     * @return Notice
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set accounttype
     *
     * @param string $accounttype
     *
     * @return Notice
     */
    public function setAccounttype($accounttype)
    {
        $this->accounttype = $accounttype;

        return $this;
    }

    /**
     * Get accounttype
     *
     * @return string
     */
    public function getAccounttype()
    {
        return $this->accounttype;
    }

    /**
     * Set capturedBy
     *
     * @param \Application\Entity\User $capturedBy
     *
     * @return Notice
     */
    public function setCapturedBy(\Application\Entity\User $capturedBy = null)
    {
        $this->capturedBy = $capturedBy;

        return $this;
    }

    /**
     * Get capturedBy
     *
     * @return \Application\Entity\User
     */
    public function getCapturedBy()
    {
        return $this->capturedBy;
    }
}
