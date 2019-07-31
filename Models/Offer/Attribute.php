<?php

namespace Shopware\CustomModels\Offer;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="s_offer_attributes")
 */
class Attribute extends ModelEntity
{
    /**
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
     protected $id;

    /**
     * @var integer $offerId
     *
     * @ORM\Column(name="offerId", type="integer", nullable=true)
     */
     protected $offerId;

    /**
     * @ORM\OneToOne(targetEntity="\Shopware\CustomModels\Offer\Offer", inversedBy="attribute")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     * @var \Shopware\CustomModels\Offer\Offer $offer
     */
    protected $offer;

    /**
     * @var integer $isSendRememberMail1
     *
     * @ORM\Column(name="$isSendRememberMail1", type="integer", nullable=true)
     */
    protected $isSendRememberMail1;

    /**
     * @var integer $isSendRememberMail2
     *
     * @ORM\Column(name="$isSendRememberMail2", type="integer", nullable=true)
     */
    protected $isSendRememberMail2;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    

    public function getOfferlId()
    {
        return $this->offerId;
    }

    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;
        return $this;
    }
    

    public function getOffer()
    {
        return $this->offer;
    }

    public function setOffer($offer)
    {
        $this->offer = $offer;
        return $this;
    }

    public function getIsSendRememberMail1()
    {
        return $this->isSendRememberMail1;
    }

    public function setIsSendRememberMail1($isSendRememberMail1)
    {
        $this->isSendRememberMail1 = $isSendRememberMail1;
        return $this;
    }

    public function getIsSendRememberMail2()
    {
        return $this->isSendRememberMail2;
    }

    public function setIsSendRememberMail2($isSendRememberMail2)
    {
        $this->isSendRememberMail2 = $isSendRememberMail2;
        return $this;
    }
}