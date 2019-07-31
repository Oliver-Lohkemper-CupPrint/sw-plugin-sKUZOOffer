<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\CustomModels\Offer\Document;
use       Shopware\Components\Model\ModelEntity,
          Doctrine\ORM\Mapping AS ORM;

/**
 *
 * Shopware offer detail model represents a single detail data of an offer .
 * <br>
 * The Shopware offer detail model represents a row of the offer_details table.
 * The s_offer_details table has the follows indices:
 * <code>
 *   - PRIMARY KEY (`ID`),
 *   - KEY `offerID` (`offerID`),
 *   - KEY `userID` (`userID`)
 * </code>
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_offer_documents")
 */
class Document extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime $date
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var integer $typeId
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $typeId;

    /**
     * @var integer $customerId
     *
     * @ORM\Column(name="userID", type="integer", nullable=false)
     */
    private $customerId;

    /**
     * @var integer $offerId
     *
     * @ORM\Column(name="offerID", type="integer", nullable=false)
     */
    private $offerId;

    /**
     * @var float $amount
     *
     * @ORM\Column(name="amount", type="float", nullable=false)
     */
    private $amount;

    /**
     * @var integer $documentId
     *
     * @ORM\Column(name="docID", type="integer", nullable=false)
     */
    private $documentId;

    /**
     * @var string $hash
     *
     * @ORM\Column(name="hash", type="string", length=255, nullable=false)
     */
    private $hash;

    /**
     * @var integer $mediaId
     *
     * @ORM\Column(name="mediaID", type="integer", nullable=true)
     */
    private $mediaId;

    /**
     * @ORM\ManyToOne(targetEntity="\Shopware\CustomModels\Offer\Offer", inversedBy="documents")
     * @ORM\JoinColumn(name="offerID", referencedColumnName="id")
     * @var \Shopware\CustomModels\Offer\Offer
     */
    private $offer;

    /**
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Document\Document")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     * @var
     */
    private $type;

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
     * Set date
     *
     * @param \DateTime $date
     * @return Document
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     * @return Document
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set offerId
     *
     * @param integer $offerId
     * @return Document
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
     * Set amount
     *
     * @param float $amount
     * @return Document
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set documentId
     *
     * @param integer $documentId
     * @return Document
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;
        return $this;
    }

    /**
     * Get documentId
     *
     * @return integer
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Document
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return \Shopware\CustomModels\Offer\Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param \Shopware\CustomModels\Offer\Offer $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getMediaId() {
        return $this->mediaId;
    }

    /**
     * @param mixed $mediaId
     */
    public function setMediaId($mediaId) {
        $this->mediaId = $mediaId;
    }

   /**
    * @param  $type
    */
    /*
    public function setType($type)
    {
        $this->type = $type;
    }*/


}
