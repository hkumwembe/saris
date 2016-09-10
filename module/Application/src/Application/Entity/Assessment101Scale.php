<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Assessment101Scale
 *
 * @ORM\Table(name="assessment_101_scale")
 * @ORM\Entity
 */
class Assessment101Scale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_ASID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkAsid;

    /**
     * @var string
     *
     * @ORM\Column(name="CODE", type="string", length=30, nullable=false)
     */
    private $code = '';

    /**
     * @var string
     *
     * @ORM\Column(name="REMARK", type="string", length=50, nullable=true)
     */
    private $remark;

    /**
     * @var integer
     *
     * @ORM\Column(name="MARK_FROM", type="integer", nullable=true)
     */
    private $markFrom;

    /**
     * @var integer
     *
     * @ORM\Column(name="MARK_TO", type="integer", nullable=true)
     */
    private $markTo;



    /**
     * Get pkAsid
     *
     * @return integer
     */
    public function getPkAsid()
    {
        return $this->pkAsid;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Assessment101Scale
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set remark
     *
     * @param string $remark
     *
     * @return Assessment101Scale
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set markFrom
     *
     * @param integer $markFrom
     *
     * @return Assessment101Scale
     */
    public function setMarkFrom($markFrom)
    {
        $this->markFrom = $markFrom;

        return $this;
    }

    /**
     * Get markFrom
     *
     * @return integer
     */
    public function getMarkFrom()
    {
        return $this->markFrom;
    }

    /**
     * Set markTo
     *
     * @param integer $markTo
     *
     * @return Assessment101Scale
     */
    public function setMarkTo($markTo)
    {
        $this->markTo = $markTo;

        return $this;
    }

    /**
     * Get markTo
     *
     * @return integer
     */
    public function getMarkTo()
    {
        return $this->markTo;
    }
}
