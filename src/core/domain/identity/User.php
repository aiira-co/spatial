<?php
namespace Core\Domain\Identity;

// src/core/domain/identity/User.php
/**
 * User
 *
 * @Entity @Table(name="user")
 */
class User
{
    /**
     * @var int
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @Column(name="person_id", type="string", nullable=false)
     */
    protected $personId;

    /**
     * @var string
     *
     * @Column(name="image", type="text", length=65535, nullable=false)
     */
    protected $image;

    /**
     * @var string
     *
     * @Column(name="username", type="string", length=15, nullable=false)
     */
    protected $username;

    /**
     * @var string
     *
     * @Column(name="surname", type="string", length=32, nullable=false)
     */
    protected $surname;

    /**
     * @var string
     *
     * @Column(name="othername", type="string", length=32, nullable=false)
     */
    protected $othername;

    /**
     * @ManyToOne(targetEntity="Groups")
     * @JoinColumn(name="gender_id", referencedColumnName="id")
     * @var Groups
     */
    protected $gender;

    /**
     * @ManyToOne(targetEntity="Groups")
     * @JoinColumn(name="provider_id", referencedColumnName="id")
     * @var Groups
     */
    protected $provider;

    /**
     * @var \DateTime
     *
     * @Column(name="birth_date", type="date", nullable=false)
     */
    protected $birthdate;

    /**
     * @var string
     *
     * @Column(name="email", type="string", length=32, nullable=false)
     */
    protected $email;

    /**
     * @var string
     *
     * @Column(name="phone_one", type="string", length=15, nullable=false)
     */
    protected $phoneOne;

    /**
     * @var string
     *
     * @Column(name="phone_two", type="string", length=15, nullable=false)
     */
    protected $phoneTwo;

    /**
     * @var bool
     *
     * @Column(name="email_verified", type="boolean", nullable=false)
     */
    protected $emailVerified;

    /**
     * @var bool
     *
     * @Column(name="activated", type="boolean", nullable=false)
     */
    protected $activated;

    /**
     * @var bool
     *
     * @Column(name="lockout_enabled", type="boolean", nullable=false)
     */
    protected $lockoutEnabled;

    /**
     * @var \DateTime
     *
     * @Column(name="lockout_end", type="datetime", nullable=false)
     */
    protected $lockoutEnd;

    /**
     * @var int
     *
     * @Column(name="access_failed_count", type="integer", nullable=false)
     */
    protected $accessFailedCount;

    /**
     * @var bool
     *
     * @Column(name="phone_verified", type="boolean", nullable=false)
     */
    protected $phoneVerified;

    /**
     * @var string
     *
     * @Column(name="passHashed", type="text", length=65535, nullable=false)
     */
    protected $passHashed;

    /**
     * @var \DateTime
     *
     * @Column(name="created", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $created = 'CURRENT_TIMESTAMP';

    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of personId
     *
     * @return  string
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set the value of personId
     *
     * @param  string  $personId
     *
     * @return  self
     */
    public function setPersonId(string $personId)
    {
        $this->personId = $personId;

        return $this;
    }

    /**
     * Get the value of image
     *
     * @return  string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @param  string  $image
     *
     * @return  self
     */
    public function setImage(string $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of username
     *
     * @return  string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @param  string  $username
     *
     * @return  self
     */
    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of surname
     *
     * @return  string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set the value of surname
     *
     * @param  string  $surname
     *
     * @return  self
     */
    public function setSurname(string $surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get the value of othername
     *
     * @return  string
     */
    public function getOthername()
    {
        return $this->othername;
    }

    /**
     * Set the value of othername
     *
     * @param  string  $othername
     *
     * @return  self
     */
    public function setOthername(string $othername)
    {
        $this->othername = $othername;

        return $this;
    }

    /**
     * Get the value of gender
     *
     * @return  Groups
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set the value of gender
     *
     * @param  Groups  $gender
     *
     * @return  self
     */
    public function setGender(Groups $gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get the value of provider
     *
     * @return  Groups
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set the value of provider
     *
     * @param  Groups  $provider
     *
     * @return  self
     */
    public function setProvider(Groups $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get the value of birthdate
     *
     * @return  \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set the value of birthdate
     *
     * @param  \DateTime  $birthdate
     *
     * @return  self
     */
    public function setBirthdate(\DateTime $birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get the value of email
     *
     * @return  string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string  $email
     *
     * @return  self
     */
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of phoneOne
     *
     * @return  string
     */
    public function getPhoneOne()
    {
        return $this->phoneOne;
    }

    /**
     * Set the value of phoneOne
     *
     * @param  string  $phoneOne
     *
     * @return  self
     */
    public function setPhoneOne(string $phoneOne)
    {
        $this->phoneOne = $phoneOne;

        return $this;
    }

    /**
     * Get the value of phoneTwo
     *
     * @return  string
     */
    public function getPhoneTwo()
    {
        return $this->phoneTwo;
    }

    /**
     * Set the value of phoneTwo
     *
     * @param  string  $phoneTwo
     *
     * @return  self
     */
    public function setPhoneTwo(string $phoneTwo)
    {
        $this->phoneTwo = $phoneTwo;

        return $this;
    }

    /**
     * Get the value of emailVerified
     *
     * @return  bool
     */
    public function getEmailVerified()
    {
        return $this->emailVerified;
    }

    /**
     * Set the value of emailVerified
     *
     * @param  bool  $emailVerified
     *
     * @return  self
     */
    public function setEmailVerified(bool $emailVerified)
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    /**
     * Get the value of activated
     *
     * @return  bool
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set the value of activated
     *
     * @param  bool  $activated
     *
     * @return  self
     */
    public function setActivated(bool $activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get the value of lockoutEnabled
     *
     * @return  bool
     */
    public function getLockoutEnabled()
    {
        return $this->lockoutEnabled;
    }

    /**
     * Set the value of lockoutEnabled
     *
     * @param  bool  $lockoutEnabled
     *
     * @return  self
     */
    public function setLockoutEnabled(bool $lockoutEnabled)
    {
        $this->lockoutEnabled = $lockoutEnabled;

        return $this;
    }

    /**
     * Get the value of lockoutEnd
     *
     * @return  \DateTime
     */
    public function getLockoutEnd()
    {
        return $this->lockoutEnd;
    }

    /**
     * Set the value of lockoutEnd
     *
     * @param  \DateTime  $lockoutEnd
     *
     * @return  self
     */
    public function setLockoutEnd(\DateTime $lockoutEnd)
    {
        $this->lockoutEnd = $lockoutEnd;

        return $this;
    }

    /**
     * Get the value of accessFailedCount
     *
     * @return  int
     */
    public function getAccessFailedCount()
    {
        return $this->accessFailedCount;
    }

    /**
     * Set the value of accessFailedCount
     *
     * @param  int  $accessFailedCount
     *
     * @return  self
     */
    public function setAccessFailedCount(int $accessFailedCount)
    {
        $this->accessFailedCount = $accessFailedCount;

        return $this;
    }

    /**
     * Get the value of phoneVerified
     *
     * @return  bool
     */
    public function getPhoneVerified()
    {
        return $this->phoneVerified;
    }

    /**
     * Set the value of phoneVerified
     *
     * @param  bool  $phoneVerified
     *
     * @return  self
     */
    public function setPhoneVerified(bool $phoneVerified)
    {
        $this->phoneVerified = $phoneVerified;

        return $this;
    }

    /**
     * Get the value of passHashed
     *
     * @return  string
     */
    public function getpassHashed()
    {
        return $this->passHashed;
    }

    /**
     * Set the value of passHashed
     *
     * @param  string  $passHashed
     *
     * @return  self
     */
    public function setpassHashed(string $passHashed)
    {
        $this->passHashed = $passHashed;

        return $this;
    }

    /**
     * Get the value of created
     *
     * @return  \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set the value of created
     *
     * @param  \DateTime  $created
     *
     * @return  self
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    public function authenticate(string $password, callable $checkHash): bool
    {
        return $checkHash($password, $this->passHashed) && !$this->hasActiveBans();
    }

    public function changePassword(string $password, callable $hash): void
    {
        $this->passHashed = $hash($password);
    }
}
