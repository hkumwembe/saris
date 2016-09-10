<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Regulation
 *
 * @ORM\Table(name="regulation", indexes={@ORM\Index(name="useregulation", columns={"CAPTURED_BY"})})
 * @ORM\Entity
 */
class Regulation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_REGULATIONID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkRegulationid;

    /**
     * @var string
     *
     * @ORM\Column(name="TITLE", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="BODY", type="blob", length=65535, nullable=false)
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="IS_CURRENT", type="text", nullable=true)
     */
    private $isCurrent = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="MODULE", type="text", nullable=true)
     */
    private $module;

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
     * Get pkRegulationid
     *
     * @return integer
     */
    public function getPkRegulationid()
    {
        return $this->pkRegulationid;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Regulation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Regulation
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
     * Set isCurrent
     *
     * @param string $isCurrent
     *
     * @return Regulation
     */
    public function setIsCurrent($isCurrent)
    {
        $this->isCurrent = $isCurrent;

        return $this;
    }

    /**
     * Get isCurrent
     *
     * @return string
     */
    public function getIsCurrent()
    {
        return $this->isCurrent;
    }

    /**
     * Set module
     *
     * @param string $module
     *
     * @return Regulation
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set capturedBy
     *
     * @param \Application\Entity\User $capturedBy
     *
     * @return Regulation
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
