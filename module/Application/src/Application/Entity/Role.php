<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="role", uniqueConstraints={@ORM\UniqueConstraint(name="UserGroup_ID_UNIQUE", columns={"ROLE_NAME"})})
 * @ORM\Entity
 */
class Role
{
    /**
     * @var integer
     *
     * @ORM\Column(name="PK_ROLEID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pkRoleid;

    /**
     * @var string
     *
     * @ORM\Column(name="ROLE_NAME", type="string", length=50, nullable=false)
     */
    private $roleName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="IS_FACULTY_ROLE", type="text", nullable=true)
     */
    private $isFacultyRole = '0';



    /**
     * Get pkRoleid
     *
     * @return integer
     */
    public function getPkRoleid()
    {
        return $this->pkRoleid;
    }

    /**
     * Set roleName
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;

        return $this;
    }

    /**
     * Get roleName
     *
     * @return string
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Role
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
     * Set isFacultyRole
     *
     * @param string $isFacultyRole
     *
     * @return Role
     */
    public function setIsFacultyRole($isFacultyRole)
    {
        $this->isFacultyRole = $isFacultyRole;

        return $this;
    }

    /**
     * Get isFacultyRole
     *
     * @return string
     */
    public function getIsFacultyRole()
    {
        return $this->isFacultyRole;
    }
}
