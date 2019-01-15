<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Groups
 *
 * @ORM\Table(name="Groups")
 * @ORM\Entity
 */
class Groups
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=25, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=25, nullable=false)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="groupTypeId", type="integer", nullable=false)
     */
    private $grouptypeid;

    /**
     * @var int
     *
     * @ORM\Column(name="supergroupId", type="integer", nullable=false)
     */
    private $supergroupid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Groups
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return Groups
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Groups
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set grouptypeid.
     *
     * @param int $grouptypeid
     *
     * @return Groups
     */
    public function setGrouptypeid($grouptypeid)
    {
        $this->grouptypeid = $grouptypeid;

        return $this;
    }

    /**
     * Get grouptypeid.
     *
     * @return int
     */
    public function getGrouptypeid()
    {
        return $this->grouptypeid;
    }

    /**
     * Set supergroupid.
     *
     * @param int $supergroupid
     *
     * @return Groups
     */
    public function setSupergroupid($supergroupid)
    {
        $this->supergroupid = $supergroupid;

        return $this;
    }

    /**
     * Get supergroupid.
     *
     * @return int
     */
    public function getSupergroupid()
    {
        return $this->supergroupid;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Groups
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
