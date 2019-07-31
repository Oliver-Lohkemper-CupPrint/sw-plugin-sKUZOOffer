<?php


namespace Shopware\CustomModels\Offer;
use       Shopware\Components\Model\ModelEntity,
          Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Table(name="s_offer_details")
 * @ORM\Entity
  */
class Detail extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $offerId
     *
     * @ORM\Column(name="offerID", type="integer", nullable=false)
     */
    private $offerId;

    /**
     * @var integer $articleDetailsId
     *
     * @ORM\Column(name="articleDetailsID", type="integer", nullable=false)
     */
    private $articleDetailsId;

    /**
     * @var integer $taxId
     *
     * @ORM\Column(name="taxID", type="integer", nullable=true)
     */
    private $taxId;

    /**
     * @var float $taxRate
     *
      @ORM\Column(name="tax_rate", type="float", nullable=false)
     */
    private $taxRate;


    /**
     * @var string $number
     *
     * @ORM\Column(name="offerNumber", type="string", length=40, nullable=true)
     */
    private $number;

    /**
     * @var string $articleNumber
     *
     * @ORM\Column(name="articleoffernumber", type="string", length=30, nullable=false)
     */
    private $articleNumber;

    /**
     * @var float $originalPrice
     *
     * @ORM\Column(name="originalPrice", type="float", nullable=false)
     */
    private $originalPrice;

    /**
     * @var float $price
     *
     * @ORM\Column(name="price", type="float", nullable=false)
     */
    private $price;

    /**
     * @var integer $quantity
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var string $articleName
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $articleName;

    /**
     * @ORM\ManyToOne(targetEntity="\Shopware\CustomModels\Offer\Offer", inversedBy="details")
     * @ORM\JoinColumn(name="offerID", referencedColumnName="id")
     * @var \Shopware\CustomModels\Offer\Offer
     */
    protected $offer;

    /**
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Tax\Tax")
     * @ORM\JoinColumn(name="taxID", referencedColumnName="id")
     * @var \Shopware\Models\Tax\Tax
     */
    protected $tax;

    /**
     * @var integer $mode
     *
     * @ORM\Column(name="modus", type="integer", nullable=false)
     */
    private $mode = 0;

    /**
     * @var integer $quantityId
     *
     * @ORM\Column(name="quantityID", type="integer", nullable=true)
     */
    private $quantityId;

    /**
     * @var
     *
     * @ORM\Column(name="isConvertedToNet", type="integer", nullable=true)
     */
    private $isConvertedToNet;

    /**
     * @var
     *
     * @ORM\Column(name="swagCustomProductsMode", type="integer", nullable=true)
     */
    private $swagCustomProductsMode;

    /**
     * @var
     *
     * @ORM\Column(name="swagCustomProductsConfigurationHash", type="string", length=255, nullable=true)
     */
    private $swagCustomProductsConfigurationHash;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return Detail
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set offerId
     *
     * @param integer $offerId
     * @return Detail
     */
    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;
        return $this;
    }

    /**
     * Get offerId
     *
     * @return integer
     */
    public function getOfferId()
    {
        return $this->offerId;
    }


    /**
     * Set taxId
     *
     * @param integer $taxId
     * @return Detail
     */
    public function setTaxId($taxId)
    {
        $this->taxId = $taxId;
        return $this;
    }

    /**
     * Get taxId
     *
     * @return integer
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * Set articleDetailsId
     *
     * @param integer $articleDetailsId
     * @return Detail
     */
    public function setArticleDetailsId($articleDetailsId)
    {
        $this->articleDetailsId = $articleDetailsId;
        return $this;
    }

    /**
     * Get articleDetailsId
     *
     * @return integer
     */
    public function getArticleDetailsId()
    {
        return $this->articleDetailsId;
    }


    /**
     * Set originalPrice
     *
     * @param float $originalPrice
     * @return Detail
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;
        return $this;
    }

    /**
     * Get originalPrice
     *
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Detail
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return Detail
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }


    /**
     * Set articleName
     *
     * @param string $articleNumber
     * @return Detail
     */
    public function setArticleNumber($articleNumber)
    {
        $this->articleNumber = $articleNumber;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getArticleNumber()
    {
        return $this->articleNumber;
    }



    /**
     * Set articleName
     *
     * @param string $articleName
     * @return Detail
     */
    public function setArticleName($articleName)
    {
        $this->articleName = $articleName;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getArticleName()
    {
        return $this->articleName;
    }





    /**
     * @return Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param Offer $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * @param string $taxRate
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }

    /**
     * @return string
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }
     /**
     * @return \Shopware\Models\Tax\Tax
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param \Shopware\Models\Tax\Tax $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    /**
     * Internal helper function which check if the associated order exist
     * and recalculate the order amount by using the
     * Shopware\CustomModels\Offer\Offer::calculateInvoiceAmount function.
     */
    private function calculateOrderAmount()
    {
        if ($this->getOffer() instanceof Offer) {
            //recalculates the new amount
            $this->getOffer()->calculateInvoiceAmount();
            Shopware()->Models()->persist($this->getOffer());
        }
    }

    /**
     * This function conver data object to array
     *
     * @param $data
     * @return array
     */
    public function toArray($data) {
        if (is_array($data) || is_object($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->toArray($value);
            }
            return $result;
        }
        return $data;
    }


    /**
     * Set mode
     *
     * @param integer $mode
     * @return Detail
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Get mode
     *
     * @return integer
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set quantityId
     *
     * @param $quantityId
     */
    public function setQuantityId($quantityId)
    {
        $this->quantityId = $quantityId;
    }

    /**
     * Get quantityId
     *
     * @return integer
     */
    public function getQuantityId()
    {
        return $this->quantityId;
    }

    /**
     * get isConvertedToNet
     * @return int
     */
    public function getIsConvertedToNet()
    {
        return $this->isConvertedToNet;
    }

    /**
     * set isConvertedToNet
     * @param $isConvertedToNet
     * @return $this
     */
    public function setIsConvertedToNet($isConvertedToNet)
    {
        $this->isConvertedToNet = $isConvertedToNet;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSwagCustomProductsMode() {
        return $this->swagCustomProductsMode;
    }

    /**
     * @param mixed $swagCustomProductsMode
     */
    public function setSwagCustomProductsMode($swagCustomProductsMode) {
        $this->swagCustomProductsMode = $swagCustomProductsMode;
    }

    /**
     * @return mixed
     */
    public function getSwagCustomProductsConfigurationHash() {
        return $this->swagCustomProductsConfigurationHash;
    }

    /**
     * @param mixed $swagCustomProductsConfigurationHash
     */
    public function setSwagCustomProductsConfigurationHash($swagCustomProductsConfigurationHash) {
        $this->swagCustomProductsConfigurationHash = $swagCustomProductsConfigurationHash;
    }

}
