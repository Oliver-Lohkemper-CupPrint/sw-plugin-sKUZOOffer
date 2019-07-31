<?php

namespace Shopware\CustomModels\Offer;

use Shopware\Components\Model\ModelEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_offer")
 */
class Offer extends ModelEntity
{
    /**
     * Unique identifier field.
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Contains the alphanumeric offer number. If the
     * @var string $number
     * @ORM\Column(name="offerNumber", type="string", length=30, nullable=true)
     */
    private $number;

    /**
     * @var \DateTime $offerTime
     *
     * @ORM\Column(name="offertime", type="datetime", nullable=true)
     */
    private $offerTime = null;

    /**
     * @var float $invoiceAmount
     *
     * @ORM\Column(name="invoice_amount", type="float", nullable=false)
     */
    private $invoiceAmount;


    /**
     * @var float $discountAmount
     *
     * @ORM\Column(name="discount_amount", type="float", nullable=false)
     */
    private $discountAmount;

    /**
     * @var float $discountAmountNet
     *
     * @ORM\Column(name="discount_amount_net", type="float", nullable=false)
     */
    private $discountAmountNet;


    /**
     * @var float $invoiceAmountNet
     *
     * @ORM\Column(name="invoice_amount_net", type="float", nullable=false)
     */
    private $invoiceAmountNet;

    /**
     * @var float $invoiceShipping
     *
     * @ORM\Column(name="invoice_shipping", type="float", nullable=false)
     */
    private $invoiceShipping;

    /**
     * @var float $invoiceShippingNet
     *
     * @ORM\Column(name="invoice_shipping_net", type="float", nullable=false)
     */
    private $invoiceShippingNet;

    /**
     * @var integer $customerId
     *
     * @ORM\Column(name="userID", type="integer", nullable=true)
     */
    private $customerId;

    /**
     * @var integer $orderId
     *
     * @ORM\Column(name="orderID", type="integer", nullable=true)
     */
    private $orderId;


    /**
     * @var integer $paymentId
     *
     * @ORM\Column(name="paymentID", type="integer", nullable=true)
     */
    private $paymentId;


    /**
     * @var string $dispatchId
     *
     * @ORM\Column(name="dispatchID", type="integer", nullable=true)
     */
    private $dispatchId;

    /**
     * @var \Shopware\Models\Payment\Payment
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Payment\Payment")
     * @ORM\JoinColumn(name="paymentID", referencedColumnName="id")
     */
    protected $payment;

    /**
     * @var integer $shopId
     *
     * @ORM\Column(name="subshopID", type="integer", nullable=false)
     */
    private $shopId;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @var integer $status
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var string $currency
     *
     * @ORM\Column(name="currency", type="string", length=5, nullable=false)
     */
    private $currency;

    /**
     * The shop property is the owning side of the association between order and shop.
     * The association is joined over the order userID field and the id field of the shop.
     *
     * @var \Shopware\Models\Shop\Shop
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="subshopID", referencedColumnName="id")
     */
    protected $shop;

    /**
     * @var \Shopware\Models\Customer\Address
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Customer\Address", inversedBy="customer")
     * @ORM\JoinColumn(name="default_billing_address_id", referencedColumnName="id")
     */
    protected $billing;

    /**
     * INVERSE SIDE
     * The billing property is the inverse side of the association between offer and billing.
     * The association is joined over the billing orderID field and the id field of the order
     *
     * @ORM\OneToOne(targetEntity="Shopware\CustomModels\Offer\Billing", mappedBy="offer", orphanRemoval=true, cascade={"persist"})
     * @var \Shopware\CustomModels\Offer\Billing
     */
    protected $offerBilling;

    /**
     * INVERSE SIDE
     * The shipping property is the inverse side of the association between offer and shipping.
     * The association is joined over the shipping orderID field and the id field of the order
     *
     * @ORM\OneToOne(targetEntity="Shopware\CustomModels\Offer\Shipping", mappedBy="offer", orphanRemoval=true, cascade={"persist"})
     * @var \Shopware\CustomModels\Offer\Shipping
     */
    protected $offerShipping;

    /**
     * INVERSE SIDE
     * @ORM\OneToMany(targetEntity="Shopware\CustomModels\Offer\Detail", mappedBy="offer", orphanRemoval=true, cascade={"persist"})
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $details;

    /**
     * INVERSE SIDE
     * @ORM\OneToMany(targetEntity="Shopware\CustomModels\Offer\Document\Document", mappedBy="offer", orphanRemoval=true, cascade={"persist"})
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $documents;


    /**
     * @var
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Customer\Customer", inversedBy="offer")
     * @ORM\JoinColumn(name="userID", referencedColumnName="id")
     */
    protected $customer;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="\Shopware\CustomModels\Offer\States", inversedBy="offer")
     * @ORM\JoinColumn(name="status", referencedColumnName="id")
     */
    protected $states;

    /**
     * @var \Shopware\Models\Dispatch\Dispatch
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Dispatch\Dispatch")
     * @ORM\JoinColumn(name="dispatchID", referencedColumnName="id")
     */
    protected $dispatch;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Order\Order", inversedBy="offer")
     * @ORM\JoinColumn(name="orderID", referencedColumnName="id")
     */
    protected $offerOrder;

    /**
     * @var
     *
     * @ORM\Column(name="isSendRememberMail1", type="integer", nullable=true)
     */
    protected $isSendRememberMail1;

    /**
     * @var
     *
     * @ORM\Column(name="isSendRememberMail2", type="integer", nullable=true)
     */
    protected $isSendRememberMail2;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string $customerComment
     *
     * @ORM\Column(name="customercomment", type="text", nullable=true)
     */
    private $customerComment;

    /**
     * @var string $internalComment
     *
     * @ORM\Column(name="internalcomment", type="text", nullable=true)
     */
    private $internalComment;

    /**
     * INVERSE SIDE
     * @ORM\OneToMany(targetEntity="Shopware\CustomModels\Offer\History", mappedBy="offer", orphanRemoval=true, cascade={"persist"})
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $history;

    /**
     * @var string $languageIso
     * @ORM\Column(name="language", type="string", length=10, nullable=false)
     */
    private $languageIso;




    public function __construct()
    {
        $this->details = new ArrayCollection();
        $this->paymentInstances = new ArrayCollection();
    }

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
     * Set offerTime
     *
     * @param \DateTime $offerTime
     * @return Offer
     */
    public function setOfferTime($offerTime)
    {
        if (!$offerTime instanceof \DateTime && is_string($offerTime)) {
            $offerTime = new \DateTime($offerTime);
        }
        $this->offerTime = $offerTime;
        return $this;
    }

    /**
     * Get offerTime
     *
     * @return \DateTime
     */
    public function getOfferTime()
    {
        return $this->offerTime;
    }

    /**
     * Get payment
     *
     * @return \Shopware\Models\Payment\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * set payment
     *
     * @param  $payment
     */

    public function setPayment($payment)
    {
        $this->payment = $payment;
    }


    /**
     * Set number
     *
     * @param string $number
     * @return Offer
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
     * Set invoiceAmount
     *
     * @param float $invoiceAmount
     * @return Order
     */
    public function setInvoiceAmount($invoiceAmount)
    {
        $this->invoiceAmount = $invoiceAmount;
        return $this;
    }

    /**
     * Get invoiceAmount
     *
     * @return float
     */
    public function getInvoiceAmount()
    {
        return $this->invoiceAmount;
    }

    /**
     * Set discountAmount
     *
     * @param float $discountAmount
     * @return Order
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;
        return $this;
    }
     /**
     * Get discountAmount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set discountAmountNet
     *
     * @param float $discountAmountNet
     * @return Order
     */
    public function setDiscountAmountNet($discountAmountNet)
    {
        $this->discountAmountNet = $discountAmountNet;
        return $this;
    }
    /**
     * Get discountAmountNet
     *
     * @return float
     */
    public function getDiscountAmountNet()
    {
        return $this->discountAmountNet;
    }


    /**
     * Set invoiceAmountNet
     *
     * @param float $invoiceAmountNet
     * @return Order
     */
    public function setInvoiceAmountNet($invoiceAmountNet)
    {
        $this->invoiceAmountNet = $invoiceAmountNet;
        return $this;
    }

    /**
     * Get invoiceAmountNet
     *
     * @return float
     */
    public function getInvoiceAmountNet()
    {
        return $this->invoiceAmountNet;
    }

    /**
     * Set invoiceShipping
     *
     * @param float $invoiceShipping
     * @return Order
     */
    public function setInvoiceShipping($invoiceShipping)
    {
        $this->invoiceShipping = $invoiceShipping;
        return $this;
    }

    /**
     * Get invoiceShipping
     *
     * @return float
     */
    public function getInvoiceShipping()
    {
        return $this->invoiceShipping;
    }

    /**
     * Set invoiceShippingNet
     *
     * @param float $invoiceShippingNet
     * @return Offer
     */
    public function setInvoiceShippingNet($invoiceShippingNet)
    {
        $this->invoiceShippingNet = $invoiceShippingNet;
        return $this;
    }

    /**
     * Get invoiceShippingNet
     *
     * @return float
     */
    public function getInvoiceShippingNet()
    {
        return $this->invoiceShippingNet;
    }


    /**
     * get customerId
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * set customerId
     *
     * @param $customerId
     * @return void
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }


    /**
     * get orderId
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->Id;
    }

    /**
     * setorderId
     *
     * @param $orderId
     * @return void
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * get shopId
     *
     * @return integer
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * set shopId
     *
     * @param $shopId
     * @return void
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * get paymentId
     *
     * @return integer
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * set paymentId
     *
     * @param $paymentId
     * @return void
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }


    /**
     * get dispatchId
     *
     * @return integer
     */
    public function getDispatchId()
    {
        return $this->dispatchId;
    }

    /**
     * set dispatchId
     *
     * @param $dispatchId
     * @return void
     */
    public function setDispatchId($dispatchId)
    {
        $this->dispatchId = $dispatchId;
    }

    /**
     * get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * set active
     *
     * @param $active
     * @return void
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * set status
     *
     * @param $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * Set currency
     *
     * @param string $currency
     * @return Offer
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }



    /**
     * get shop model
     *
     * @return \Shopware\Models\Shop\Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * set shop model
     *
     * @param \Shopware\Models\Shop\Shop $shop
     */
    public function setShop($shop)
    {
        $this->shop = $shop;
    }



    /**
     * get billing model
     *
     * @return \Shopware\Models\Customer\Address
     */
    public function getBilling()
    {
        return $this->billing;
    }
/**
     * set billing model
     *
     * @param \Shopware\Models\Customer\Address|array|null $billing
     * @return \Shopware\Models\Customer\Address
     */

    /*
    public function setBilling($billing)
    {
        return $this->setOneToOne($billing, '\Shopware\Models\Customer\Address', 'billing', 'offer');
    }*/

    /**
     * get offer details
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * set offer details
     *
     * @param \Doctrine\Common\Collections\ArrayCollection|array|null $details
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function setDetails($details)
    {
        return $this->setOneToMany($details, '\Shopware\CustomModels\Offer\Detail', 'details', 'offer');
    }

    /**
     * get offer document
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * set offer document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection|array|null $documents
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function setDocuments($documents)
    {
        return $this->setOneToMany($documents, '\Shopware\CustomModels\Offer\Document\Document', 'documents', 'offer');
    }

    /**
     * get offer shipping model
     *
     * @return \Shopware\CustomModels\Offer\Shipping
     */
    public function getOfferShipping()
    {
        return $this->offerShipping;
    }

    /**
     * set offer shipping model
     *
     * @param \Shopware\CustomModels\Offer\Shipping|array|null $offerShipping
     * @return \Shopware\CustomModels\Offer\Shipping
     */
    public function setOfferShipping($offerShipping)
    {
        return $this->setOneToOne($offerShipping, '\Shopware\CustomModels\Offer\Shipping', 'offerShipping', 'offer');
    }


    /**
     * get offer billing model
     *
     * @return \Shopware\CustomModels\Offer\Billing
     */
    public function getOfferBilling()
    {
        return $this->offerBilling;
    }

    /**
     * set offer billing model
     *
     * @param \Shopware\CustomModels\Offer\Billing|array|null $offerBilling
     * @return \Shopware\CustomModels\Offer\Billing
     */
    public function setOfferBilling($offerBilling)
    {
        return $this->setOneToOne($offerBilling, '\Shopware\CustomModels\Offer\Billing', 'offerBilling', 'offer');
    }


    /**
     * get customer
     *
     * @return \Shopware\Models\Customer\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }


    /**
     * set customer
     *
     * @return \Shopware\CustomModels\Offer\States
     */
    public function getStates()
    {
        return $this->states;
    }


    /**
     * get dispatch
     *
     * @return \Shopware\Models\Dispatch\Dispatch
     */
    public function getDispatch()
    {
        return $this->dispatch;
    }

    /**
     * set dispatch
     *
     * @param  $dispatch
     */
    public function setDispatch($dispatch)
    {
        $this->dispatch = $dispatch;
    }

    /**
     * get isSendRememberMail1
     * @return int
     */
    public function getIsSendRememberMail1()
    {
        return $this->isSendRememberMail1;
    }

    /**
     * set isSendRememberMail1
     * @param $isSendRememberMail1
     * @return $this
     */
    public function setIsSendRememberMail1($isSendRememberMail1)
    {
        $this->isSendRememberMail1 = $isSendRememberMail1;
        return $this;
    }

    /**
     * get isSendRememberMail2
     * @return int
     */
    public function getIsSendRememberMail2()
    {
        return $this->isSendRememberMail2;
    }

    /**
     * set isSendRememberMail2
     * @param $isSendRememberMail2
     * @return $this
     */
    public function setIsSendRememberMail2($isSendRememberMail2)
    {
        $this->isSendRememberMail2 = $isSendRememberMail2;
        return $this;
    }

    /**
     * The calculateInvoiceAmount function recalculated the net and gross amount based on the
     * order positions.
     */
    public function calculateInvoiceAmount()
    {
        $invoiceAmount = 0;
        $discountAmount = 0;
        $invoiceAmountNet = 0;
        $discountAmountNet = 0;

        //iterate order details to recalculate the amount.
        /**@var $detail Detail*/
        foreach ($this->getDetails() as $detail) {

            $tax = $detail->getTax();

            $taxValue = 0;

            // additional tax checks required for sw-2238, sw-2903 and sw-3164
            if ($tax && $tax->getId() !== 0 && $tax->getId() !== null && $tax->getTax() !== null) {
                $taxValue = $tax->getTax();
            } elseif($detail->getTaxRate()) {
                $taxValue = $detail->getTaxRate();
            } else {
                $taxValue = 19;
            }


            $originalPriceWithTax = $detail->getOriginalPrice() * ($taxValue + 100) / 100;
            $priceWithTax = $detail->getPrice() * ($taxValue + 100) / 100;
            $invoiceAmount += $originalPriceWithTax * $detail->getQuantity();
            $discountAmount += $priceWithTax * $detail->getQuantity();

            $invoiceAmountNet += $detail->getOriginalPrice() * $detail->getQuantity();
            $discountAmountNet += $detail->getPrice() * $detail->getQuantity();
        }


        $this->invoiceAmount = $invoiceAmount ;
        $this->discountAmount = $discountAmount ;
        $this->invoiceAmountNet = $invoiceAmountNet;
        $this->discountAmountNet = $discountAmountNet;
    }


    /**
     * Set comment
     *
     * @param string $comment
     * @return Order
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set customerComment
     *
     * @param string $customerComment
     * @return Order
     */
    public function setCustomerComment($customerComment)
    {
        $this->customerComment = $customerComment;
        return $this;
    }

    /**
     * Get customerComment
     *
     * @return string
     */
    public function getCustomerComment()
    {
        return $this->customerComment;
    }

    /**
     * Set internalComment
     *
     * @param string $internalComment
     * @return Order
     */
    public function setInternalComment($internalComment)
    {
        $this->internalComment = $internalComment;
        return $this;
    }

    /**
     * Get internalComment
     *
     * @return string
     */
    public function getInternalComment()
    {
        return $this->internalComment;
    }

    /**
     * get offer history
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * set offer history
     *
     * @param \Doctrine\Common\Collections\ArrayCollection|array|null $history
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function setHistory($history)
    {
        return $this->setOneToMany($history, '\Shopware\CustomModels\Offer\History', 'history', 'offer');
    }

    /**
     * Set languageIso
     *
     * @param string $languageIso
     * @return Order
     */
    public function setLanguageIso($languageIso)
    {
        $this->languageIso = $languageIso;
        return $this;
    }

    /**
     * Get languageIso
     *
     * @return string
     */
    public function getLanguageIso()
    {
        return $this->languageIso;
    }

    /**
     * This function converts data object to array
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

}
