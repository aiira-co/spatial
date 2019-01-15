<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Userlog
 *
 * @ORM\Table(name="UserLog")
 * @ORM\Entity
 */
class Userlog
{
    /**
     * @var string
     *
     * @ORM\Column(name="remoteAddr", type="string", length=255, nullable=false)
     */
    private $remoteaddr;

    /**
     * @var string
     *
     * @ORM\Column(name="requestUri", type="string", length=255, nullable=false)
     */
    private $requesturi;

    /**
     * @var string
     *
     * @ORM\Column(name="requestMethod", type="string", length=10, nullable=false)
     */
    private $requestmethod;

    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer", nullable=false)
     */
    private $userid;

    /**
     * @var int
     *
     * @ORM\Column(name="activityId", type="integer", nullable=false)
     */
    private $activityid;

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
     * Set remoteaddr.
     *
     * @param string $remoteaddr
     *
     * @return Userlog
     */
    public function setRemoteaddr($remoteaddr)
    {
        $this->remoteaddr = $remoteaddr;

        return $this;
    }

    /**
     * Get remoteaddr.
     *
     * @return string
     */
    public function getRemoteaddr()
    {
        return $this->remoteaddr;
    }

    /**
     * Set requesturi.
     *
     * @param string $requesturi
     *
     * @return Userlog
     */
    public function setRequesturi($requesturi)
    {
        $this->requesturi = $requesturi;

        return $this;
    }

    /**
     * Get requesturi.
     *
     * @return string
     */
    public function getRequesturi()
    {
        return $this->requesturi;
    }

    /**
     * Set requestmethod.
     *
     * @param string $requestmethod
     *
     * @return Userlog
     */
    public function setRequestmethod($requestmethod)
    {
        $this->requestmethod = $requestmethod;

        return $this;
    }

    /**
     * Get requestmethod.
     *
     * @return string
     */
    public function getRequestmethod()
    {
        return $this->requestmethod;
    }

    /**
     * Set userid.
     *
     * @param int $userid
     *
     * @return Userlog
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
     * Set activityid.
     *
     * @param int $activityid
     *
     * @return Userlog
     */
    public function setActivityid($activityid)
    {
        $this->activityid = $activityid;

        return $this;
    }

    /**
     * Get activityid.
     *
     * @return int
     */
    public function getActivityid()
    {
        return $this->activityid;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Userlog
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
