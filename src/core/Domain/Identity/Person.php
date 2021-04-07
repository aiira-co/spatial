<?php

namespace Core\Domain\Identity;


// src/core/domain/identity/User.php
/**
 * @Entity @Table(name="person")
 */
class Person
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @Column(name="person_id", type="string",nullable=true)
     */
    protected $personId;

    /**
     * @var string
     *
     * @Column(name="image", type="text", length=65535,nullable=true)
     */
    protected $image;
    /**
     * @var string
     *
     * @Column(name="cover", type="text", length=65535,nullable=true)
     */
    protected $cover;

    /**
     * @var string
     *
     * @Column(name="username", type="string", length=15, nullable=false)
     */
    protected $username;

    /**
     * @var string
     *
     * @Column(name="tagline", type="string", length=20, nullable=false)
     */
    protected $tagline;

    /**
     * @var string
     *
     * @Column(name="bio", type="string", length=256, nullable=true)
     */
    protected $bio;

    /**
     * @var string
     *
     * @Column(name="surname", type="string", length=32, nullable=true )
     */
    protected $surname;

    /**
     * @var string
     *
     * @Column(name="othername", type="string", length=32,nullable=true )
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
     * @Column(name="birth_date", type="date",nullable=true)
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
     * @Column(name="phone_one", type="string", length=15,nullable=true)
     */
    protected $phoneOne;

    /**
     * @var string
     *
     * @Column(name="phone_two", type="string", length=15,nullable=true)
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
     * @Column(name="lockout_end", type="datetime", nullable=true)
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
     * @Column(name="hashed", type="text", length=65535, nullable=false)
     */
    protected $hashed;

    /**
     * @var \DateTime
     *
     * @Column(name="created", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $created = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @Column(type="string", length=32)
     */
    protected $city;

    // /**
    //  * @ManyToOne(targetEntity="Country")
    //  * @JoinColumn(name="country", referencedColumnName="code")
    //  * @var Country
    //  */

    /**
     * @var string
     *
     * @Column(type="string", length=32)
     */
    protected $country;

    /**
     * @var string
     *
     * @Column(type="string", length=32)
     */
    protected $timezone;

    /**
     * @var string
     *
     * @Column(type="string", length=32)
     */
    protected $language;

    /**
     * @ManyToOne(targetEntity="Groups")
     * @JoinColumn(name="account_type_id", referencedColumnName="id")
     * @var Groups
     */
    protected $accountType;



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
     * Get the value of othername
     *
     * @return  string
     */
    public function getName(): string
    {
        return  $this->othername . ' ' . $this->surname;
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
     * Get the value of hashed
     *
     * @return  string
     */
    public function getHashed()
    {
        return $this->hashed;
    }

    /**
     * Set the value of hashed
     *
     * @param  string  $hashed
     *
     * @return  self
     */
    public function setHashed(string $hashed)
    {
        $this->hashed = $hashed;

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

    public function authenticate(string $password): bool
    {
        return password_verify($password, $this->hashed);
        // && !$this->hasActiveBans();
    }

    public function changePassword(string $password): void
    {
        $this->hashed = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Get the value of tagline
     *
     * @return  string
     */
    public function getTagline()
    {
        return $this->tagline;
    }

    /**
     * Set the value of tagline
     *
     * @param  string  $tagline
     *
     * @return  self
     */
    public function setTagline(string $tagline)
    {
        $this->tagline = $tagline;

        return $this;
    }

    /**
     * Get the value of location
     *
     * @return  string
     */
    public function getLocation()
    {
        return [
            'city' => $this->city,
            'country' => $this->country,
        ];
    }

    /**
     * Set the value of tagline
     *
     * @param string $city
     * @param string $country
     * @return void
     */
    public function setLocation(string $city, string $country)
    {
        $this->city = $city;
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of language
     *
     * @return  string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the value of language
     *
     * @param  string  $language
     *
     * @return  self
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get the value of accountType
     *
     * @return  Groups
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * Set the value of accountType
     *
     * @param  Groups  $accountType
     *
     * @return  self
     */
    public function setAccountType(Groups $accountType)
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * Get the value of bio
     *
     * @return  string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set the value of bio
     *
     * @param  string  $bio
     *
     * @return  self
     */
    public function setBio(string $bio)
    {
        $this->bio = $bio;

        return $this;
    }



    /**
     * Get the value of timezone
     *
     * @return  string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set the value of timezone
     *
     * @param  string  $timezone
     *
     * @return  self
     */
    public function setTimezone(string $timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get the value of cover
     *
     * @return  string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set the value of cover
     *
     * @param  string  $cover
     *
     * @return  self
     */
    public function setCover(string $cover)
    {
        $this->cover = $cover;

        return $this;
    }
}
