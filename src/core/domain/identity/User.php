<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="User")
 * @ORM\Entity
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="personId", type="integer", nullable=false)
     */
    private $personid;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="text", length=65535, nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="provider", type="string", length=10, nullable=false)
     */
    private $provider;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=10, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=32, nullable=false)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="othername", type="string", length=32, nullable=false)
     */
    private $othername;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=7, nullable=false)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDate", type="date", nullable=false)
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=32, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneOne", type="string", length=15, nullable=false)
     */
    private $phoneone;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneTwo", type="string", length=15, nullable=false)
     */
    private $phonetwo;

    /**
     * @var bool
     *
     * @ORM\Column(name="emailVerified", type="boolean", nullable=false)
     */
    private $emailverified;

    /**
     * @var bool
     *
     * @ORM\Column(name="activated", type="boolean", nullable=false)
     */
    private $activated;

    /**
     * @var bool
     *
     * @ORM\Column(name="lockoutEnabled", type="boolean", nullable=false)
     */
    private $lockoutenabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lockoutEnd", type="datetime", nullable=false)
     */
    private $lockoutend;

    /**
     * @var int
     *
     * @ORM\Column(name="accessFailedCount", type="integer", nullable=false)
     */
    private $accessfailedcount;

    /**
     * @var bool
     *
     * @ORM\Column(name="phoneVerified", type="boolean", nullable=false)
     */
    private $phoneverified;

    /**
     * @var string
     *
     * @ORM\Column(name="hashed", type="text", length=65535, nullable=false)
     */
    private $hashed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registeredDate", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $registereddate = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set personid.
     *
     * @param int $personid
     *
     * @return User
     */
    public function setPersonid($personid)
    {
        $this->personid = $personid;

        return $this;
    }

    /**
     * Get personid.
     *
     * @return int
     */
    public function getPersonid()
    {
        return $this->personid;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return User
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set provider.
     *
     * @param string $provider
     *
     * @return User
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider.
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set surname.
     *
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname.
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set othername.
     *
     * @param string $othername
     *
     * @return User
     */
    public function setOthername($othername)
    {
        $this->othername = $othername;

        return $this;
    }

    /**
     * Get othername.
     *
     * @return string
     */
    public function getOthername()
    {
        return $this->othername;
    }

    /**
     * Set gender.
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthdate.
     *
     * @param \DateTime $birthdate
     *
     * @return User
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate.
     *
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phoneone.
     *
     * @param string $phoneone
     *
     * @return User
     */
    public function setPhoneone($phoneone)
    {
        $this->phoneone = $phoneone;

        return $this;
    }

    /**
     * Get phoneone.
     *
     * @return string
     */
    public function getPhoneone()
    {
        return $this->phoneone;
    }

    /**
     * Set phonetwo.
     *
     * @param string $phonetwo
     *
     * @return User
     */
    public function setPhonetwo($phonetwo)
    {
        $this->phonetwo = $phonetwo;

        return $this;
    }

    /**
     * Get phonetwo.
     *
     * @return string
     */
    public function getPhonetwo()
    {
        return $this->phonetwo;
    }

    /**
     * Set emailverified.
     *
     * @param bool $emailverified
     *
     * @return User
     */
    public function setEmailverified($emailverified)
    {
        $this->emailverified = $emailverified;

        return $this;
    }

    /**
     * Get emailverified.
     *
     * @return bool
     */
    public function getEmailverified()
    {
        return $this->emailverified;
    }

    /**
     * Set activated.
     *
     * @param bool $activated
     *
     * @return User
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
     * Set lockoutenabled.
     *
     * @param bool $lockoutenabled
     *
     * @return User
     */
    public function setLockoutenabled($lockoutenabled)
    {
        $this->lockoutenabled = $lockoutenabled;

        return $this;
    }

    /**
     * Get lockoutenabled.
     *
     * @return bool
     */
    public function getLockoutenabled()
    {
        return $this->lockoutenabled;
    }

    /**
     * Set lockoutend.
     *
     * @param \DateTime $lockoutend
     *
     * @return User
     */
    public function setLockoutend($lockoutend)
    {
        $this->lockoutend = $lockoutend;

        return $this;
    }

    /**
     * Get lockoutend.
     *
     * @return \DateTime
     */
    public function getLockoutend()
    {
        return $this->lockoutend;
    }

    /**
     * Set accessfailedcount.
     *
     * @param int $accessfailedcount
     *
     * @return User
     */
    public function setAccessfailedcount($accessfailedcount)
    {
        $this->accessfailedcount = $accessfailedcount;

        return $this;
    }

    /**
     * Get accessfailedcount.
     *
     * @return int
     */
    public function getAccessfailedcount()
    {
        return $this->accessfailedcount;
    }

    /**
     * Set phoneverified.
     *
     * @param bool $phoneverified
     *
     * @return User
     */
    public function setPhoneverified($phoneverified)
    {
        $this->phoneverified = $phoneverified;

        return $this;
    }

    /**
     * Get phoneverified.
     *
     * @return bool
     */
    public function getPhoneverified()
    {
        return $this->phoneverified;
    }

    /**
     * Set hashed.
     *
     * @param string $hashed
     *
     * @return User
     */
    public function setHashed($hashed)
    {
        $this->hashed = $hashed;

        return $this;
    }

    /**
     * Get hashed.
     *
     * @return string
     */
    public function getHashed()
    {
        return $this->hashed;
    }

    /**
     * Set registereddate.
     *
     * @param \DateTime $registereddate
     *
     * @return User
     */
    public function setRegistereddate($registereddate)
    {
        $this->registereddate = $registereddate;

        return $this;
    }

    /**
     * Get registereddate.
     *
     * @return \DateTime
     */
    public function getRegistereddate()
    {
        return $this->registereddate;
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
