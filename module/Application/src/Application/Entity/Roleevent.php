<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Roleevent
 *
 * @ORM\Table(name="roleevent", indexes={@ORM\Index(name="eventrole", columns={"FK_ROLEID"}), @ORM\Index(name="eventtyperole", columns={"FK_EVENTID"})})
 * @ORM\Entity
 */
class Roleevent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_EVENTROLEID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkEventroleid;

    /**
     * @var \Application\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_ROLEID", referencedColumnName="PK_ROLEID")
     * })
     */
    private $fkRoleid;

    /**
     * @var \Application\Entity\Calendarevent
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Calendarevent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="FK_EVENTID", referencedColumnName="PK_EVENTID")
     * })
     */
    private $fkEventid;



    /**
     * Get pkEventroleid
     *
     * @return integer
     */
    public function getPkEventroleid()
    {
        return $this->pkEventroleid;
    }

    /**
     * Set fkRoleid
     *
     * @param \Application\Entity\Role $fkRoleid
     *
     * @return Roleevent
     */
    public function setFkRoleid(\Application\Entity\Role $fkRoleid = null)
    {
        $this->fkRoleid = $fkRoleid;

        return $this;
    }

    /**
     * Get fkRoleid
     *
     * @return \Application\Entity\Role
     */
    public function getFkRoleid()
    {
        return $this->fkRoleid;
    }

    /**
     * Set fkEventid
     *
     * @param \Application\Entity\Calendarevent $fkEventid
     *
     * @return Roleevent
     */
    public function setFkEventid(\Application\Entity\Calendarevent $fkEventid = null)
    {
        $this->fkEventid = $fkEventid;

        return $this;
    }

    /**
     * Get fkEventid
     *
     * @return \Application\Entity\Calendarevent
     */
    public function getFkEventid()
    {
        return $this->fkEventid;
    }
}
