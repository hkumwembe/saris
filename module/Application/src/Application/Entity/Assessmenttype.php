<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Assessmenttype
 *
 * @ORM\Table(name="assessmenttype")
 * @ORM\Entity
 */
class Assessmenttype
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_ATID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkAtid;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPE_CODE", type="string", length=10, nullable=false)
     */
    private $typeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPE_NAME", type="string", length=45, nullable=false)
     */
    private $typeName;

    /**
     * @var string
     *
     * @ORM\Column(name="SYSTEM_GENERATED", type="text", nullable=false)
     */
    private $systemGenerated;

    /**
     * @var string
     *
     * @ORM\Column(name="EXIST_ONCE", type="text", nullable=false)
     */
    private $existOnce = '0';



    /**
     * Get pkAtid
     *
     * @return integer
     */
    public function getPkAtid()
    {
        return $this->pkAtid;
    }

    /**
     * Set typeCode
     *
     * @param string $typeCode
     *
     * @return Assessmenttype
     */
    public function setTypeCode($typeCode)
    {
        $this->typeCode = $typeCode;

        return $this;
    }

    /**
     * Get typeCode
     *
     * @return string
     */
    public function getTypeCode()
    {
        return $this->typeCode;
    }

    /**
     * Set typeName
     *
     * @param string $typeName
     *
     * @return Assessmenttype
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * Get typeName
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * Set systemGenerated
     *
     * @param string $systemGenerated
     *
     * @return Assessmenttype
     */
    public function setSystemGenerated($systemGenerated)
    {
        $this->systemGenerated = $systemGenerated;

        return $this;
    }

    /**
     * Get systemGenerated
     *
     * @return string
     */
    public function getSystemGenerated()
    {
        return $this->systemGenerated;
    }

    /**
     * Set existOnce
     *
     * @param string $existOnce
     *
     * @return Assessmenttype
     */
    public function setExistOnce($existOnce)
    {
        $this->existOnce = $existOnce;

        return $this;
    }

    /**
     * Get existOnce
     *
     * @return string
     */
    public function getExistOnce()
    {
        return $this->existOnce;
    }
}
