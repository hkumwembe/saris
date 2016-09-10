<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eventtype
 *
 * @ORM\Table(name="eventtype")
 * @ORM\Entity
 */
class Eventtype
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_EVENTTYPEID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkEventtypeid;

    /**
     * @var string
     *
     * @ORM\Column(name="CODE", type="string", length=10, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=120, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="MODULE", type="string", length=15, nullable=true)
     */
    private $module;



    /**
     * Get pkEventtypeid
     *
     * @return integer
     */
    public function getPkEventtypeid()
    {
        return $this->pkEventtypeid;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Eventtype
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
     * Set description
     *
     * @param string $description
     *
     * @return Eventtype
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
     * Set module
     *
     * @param string $module
     *
     * @return Eventtype
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
}
