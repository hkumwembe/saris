<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dropoutstatus
 *
 * @ORM\Table(name="dropoutstatus")
 * @ORM\Entity
 */
class Dropoutstatus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_DSTATUSID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkDstatusid;

    /**
     * @var string
     *
     * @ORM\Column(name="TITLE", type="string", length=200, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="CANBEPROGRESSED", type="text", nullable=true)
     */
    private $canbeprogressed;

    /**
     * @var string
     *
     * @ORM\Column(name="ABBREVIATION", type="string", length=7, nullable=true)
     */
    private $abbreviation;



    /**
     * Get pkDstatusid
     *
     * @return integer
     */
    public function getPkDstatusid()
    {
        return $this->pkDstatusid;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Dropoutstatus
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
     * Set canbeprogressed
     *
     * @param string $canbeprogressed
     *
     * @return Dropoutstatus
     */
    public function setCanbeprogressed($canbeprogressed)
    {
        $this->canbeprogressed = $canbeprogressed;

        return $this;
    }

    /**
     * Get canbeprogressed
     *
     * @return string
     */
    public function getCanbeprogressed()
    {
        return $this->canbeprogressed;
    }

    /**
     * Set abbreviation
     *
     * @param string $abbreviation
     *
     * @return Dropoutstatus
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }
}
