<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Usersubscription
 *
 * @ORM\Table(name="UserSubscription")
 * @ORM\Entity
 */
class Usersubscription
{
    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer", nullable=false)
     */
    private $userid;

    /**
     * @var int
     *
     * @ORM\Column(name="subscriptionId", type="integer", nullable=false)
     */
    private $subscriptionid;

    /**
     * @var bool
     *
     * @ORM\Column(name="activated", type="boolean", nullable=false)
     */
    private $activated;

    /**
     * @var int
     *
     * @ORM\Column(name="lastActiveTime", type="integer", nullable=false)
     */
    private $lastactivetime;

    /**
     * @var int
     *
     * @ORM\Column(name="date", type="integer", nullable=false)
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set userid.
     *
     * @param int $userid
     *
     * @return Usersubscription
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid.
     *
     * @return int
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set subscriptionid.
     *
     * @param int $subscriptionid
     *
     * @return Usersubscription
     */
    public function setSubscriptionid($subscriptionid)
    {
        $this->subscriptionid = $subscriptionid;

        return $this;
    }

    /**
     * Get subscriptionid.
     *
     * @return int
     */
    public function getSubscriptionid()
    {
        return $this->subscriptionid;
    }

    /**
     * Set activated.
     *
     * @param bool $activated
     *
     * @return Usersubscription
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated.
     *
     * @return bool
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set lastactivetime.
     *
     * @param int $lastactivetime
     *
     * @return Usersubscription
     */
    public function setLastactivetime($lastactivetime)
    {
        $this->lastactivetime = $lastactivetime;

        return $this;
    }

    /**
     * Get lastactivetime.
     *
     * @return int
     */
    public function getLastactivetime()
    {
        return $this->lastactivetime;
    }

    /**
     * Set date.
     *
     * @param int $date
     *
     * @return Usersubscription
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return int
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
