<?php



namespace   Shopware\CustomModels\Offer;
use         Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * Shopware offer shipping model represents a single shipping address of an offer.
 *
 * The Shopware offer shipping model represents a row of the s_offer_shippingaddress table.
 * The shipping model data set from the Shopware\CustomModels\Offer\Repository.
 * One shipping address has the follows associations:
 * <code>
 *   - Offer    =>  Shopware\CustomModels\Offer\Offer [1:1] [s_offer]
 *   - Customer =>  Shopware\Models\Customer\Customer [1:1] [s_user]
 * </code>
 * The s_offer_shippingaddress table has the follows indices:
 * <code>
 *   - PRIMARY KEY (`id`)
 *   - UNIQUE KEY `FOREIGN` (`userID`)
 *   - UNIQUE KEY `FOREIGN` (`offerID`)
 * </code>
 *
 * @ORM\Entity
 * @ORM\Table(name="s_offer_shippingaddress")
 * @ORM\HasLifecycleCallbacks
 */
class Shipping extends ModelEntity
{
    /**
     * The id property is an identifier property which means
     * doctrine associations can be defined over this field
     *
     * @var integer $id
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * If of the associated offer. Used as foreign key for the
     * offer - shipping association.
     *
     * @var integer $offerId
     * @ORM\Column(name="offerID", type="integer", nullable=false)
     */
    private $offerId;

    /**
     * If of the associated customer. Used as foreign key for the
     * customer - shipping association.
     *
     * @var integer $customerId
     * @ORM\Column(name="userID", type="integer", nullable=true)
     */
    private $customerId = null;


    /**
     * Contains the id of the country. Used for the shipping - country association.
     * @var integer $country
     * @ORM\Column(name="countryID", type="integer", nullable=false)
     */
    private $countryId = 0;


    /**
     * Contains the id of the country. Used for the shipping - country association.
     * @var integer $stateId
     * @ORM\Column(name="stateID", type="integer", nullable=false)
     */
    private $stateId;

    /**
     * Contains the name of the shipping address company
     * @var string $company
     * @ORM\Column(name="company", type="string", length=255, nullable=false)
     */
    private $company = '';

    /**
     * Contains the department name of the shipping address company
     * @var string $department
     * @ORM\Column(name="department", type="string", length=35, nullable=false)
     */
    private $department = '';

    /**
     * Contains the customer salutation (Mr, Ms, Company)
     * @var string $salutation
     * @ORM\Column(name="salutation", type="string", length=30, nullable=false)
     */
    private $salutation = '';

    /**
     * Contains the first name of the shipping address
     * @var string $firstName
     * @ORM\Column(name="firstname", type="string", length=50, nullable=false)
     */
    private $firstName = '';

    /**
     * Contains the last name of the shipping address
     * @var string $lastName
     * @ORM\Column(name="lastname", type="string", length=60, nullable=false)
     */
    private $lastName = '';

    /**
     * Contains the street name of the shipping address
     * @var string $street
     * @ORM\Column(name="street", type="string", length=100, nullable=false)
     */
    private $street = '';



    /**
     * Contains the zip code of the shipping address
     * @var string $zipCode
     * @ORM\Column(name="zipcode", type="string", length=50, nullable=false)
     */
    private $zipCode = '';

    /**
     * Contains the city name of the shipping address
     * @var string $city
     * @ORM\Column(name="city", type="string", length=70, nullable=false)
     */
    private $city = '';


    /**
     * The customer property is the owning side of the association between customer and shipping.
     * The association is joined over the shipping userID and the customer id
     *
     * @var \Shopware\Models\Customer\Customer $customer
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Customer\Customer")
     * @ORM\JoinColumn(name="userID", referencedColumnName="id")
     */
    private $customer;

    /**
     * The offer property is the owning side of the association between offer and shipping.
     * The association is joined over the shipping offerID and the offer id
     *
     * @var \Shopware\CustomModels\Offer\Offer $offer
     * @ORM\OneToOne(targetEntity="\Shopware\CustomModels\Offer\Offer", inversedBy="offerShipping")
     * @ORM\JoinColumn(name="offerID", referencedColumnName="id")
     */
    private $offer;

    /**
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Country\Country")
     * @ORM\JoinColumn(name="countryID", referencedColumnName="id")
     * @var \Shopware\Models\Country\Country
     */
    private $country;



    /**
     * Getter function for the unique id identifier property
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter function for the company column property
     *
     * @param string $company
     * @return Shipping
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Getter function for the company column property.
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Setter function for the department column property.
     *
     * @param string $department
     * @return Shipping
     */
    public function setDepartment($department)
    {
        $this->department = $department;
        return $this;
    }

    /**
     * Getter function for the department column property.
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Setter function for the salutation column property.
     *
     * @param string $salutation
     * @return Shipping
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;
        return $this;
    }

    /**
     * Getter function for the salutation column property.
     *
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * Setter function for the firstName column property.
     *
     * @param string $firstName
     * @return Shipping
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Getter function for the firstName column property.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Setter function for the lastName column property.
     *
     * @param string $lastName
     * @return Shipping
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Getter function for the lastName column property.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Setter function for the street column property.
     *
     * @param string $street
     * @return Shipping
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }

    /**
     * Getter function for the street column property.
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Setter function for the zipCode column property.
     *
     * @param string $zipCode
     * @return Shipping
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * Getter function for the zipCode column property.
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Setter function for the city column property.
     *
     * @param string $city
     * @return Shipping
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Getter function for the city column property.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Returns the instance of the Shopware\Models\Customer\Customer model which
     * contains all data about the customer. The association is defined over
     * the Customer.shipping property (INVERSE SIDE) and the Shipping.customer (OWNING SIDE) property.
     * The customer data is joined over the s_user.id field.
     *
     * @return \Shopware\Models\Customer\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Setter function for the customer association property which contains an instance of the Shopware\Models\Customer\Customer model which
     * contains all data about the customer. The association is defined over
     * the Customer.shipping property (INVERSE SIDE) and the Shipping.customer (OWNING SIDE) property.
     * The customer data is joined over the s_user.id field.
     *
     * @param \Shopware\Models\Customer\Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * get offer model
     *
     * @return \Shopware\CustomModels\Offer\Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * set offer model
     *
     * @param \Shopware\CustomModels\Offer\Offer $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * @return int
     */
    public function getCountryId() {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     */
    public function setCountryId($countryId) {
        $this->countryId = $countryId;
    }

    /**
     * get country
     *
     * get Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * set country
     *
     * @param  $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    

}
