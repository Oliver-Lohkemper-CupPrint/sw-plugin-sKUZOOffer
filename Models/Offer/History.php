<?php

namespace Shopware\CustomModels\Offer;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="s_offer_history")
 */
class History extends ModelEntity
{
    /**
     * Unique identifier field for the history model.
     * This is the primary key field. (strategy="IDENTITY")
     *
     * @var integer $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var integer $offerId
     * @ORM\Column(name="offerId", type="integer", nullable=false)
     */
    private $offerId;

    /**
     *
     * @var integer $userId
     * @ORM\Column(name="userId", type="integer", nullable=true, unique=false)
     */
    private $userId = null;

    /**
     *
     * @var integer
     * @ORM\Column(name="previousStatusId", type="integer", nullable=false)
     */
    private $previousStatusId = null;

    /**
     *
     * @var integer $statusId
     * @ORM\Column(name="statusId", type="integer", nullable=false)
     */
    private $statusId = null;

    /**
     *
     * @var string $comment
     * @ORM\Column(name="comment", type="text", nullable=false)
     */
    private $comment = '';

    /**
     *
     * @ORM\ManyToOne(targetEntity="\Shopware\CustomModels\Offer\Offer", inversedBy="history")
     * @ORM\JoinColumn(name="offerId", referencedColumnName="id")
     * @var \Shopware\CustomModels\Offer\Offer $offer
     */
    private $offer;

    /**
     *
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\User\User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     * @var \Shopware\Models\User\User $user
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="\Shopware\CustomModels\Offer\States")
     * @ORM\JoinColumn(name="previousStatusId", referencedColumnName="id")
     * @var \Shopware\CustomModels\Offer\States $previousStatus
     */
    private $previousStatus;

    /**
     *
     * @ORM\ManyToOne(targetEntity="\Shopware\CustomModels\Offer\States")
     * @ORM\JoinColumn(name="statusId", referencedColumnName="id")
     * @var \Shopware\CustomModels\Offer\States $status
     */
    private $status;

    /**
     * @ORM\Column(name="changeDate", type="datetime", nullable=false)
     * @var \DateTime
     */
    private $changeDate;

    /**
     * Getter function for the user property.
     * Unique identifier field for the history model.
     * This is the primary key field. (strategy="IDENTITY")
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param \Shopware\Models\User\User $user
     * @return \Shopware\CustomModels\Offer\History
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     *
     * @return \Shopware\Models\User\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     *
     * @param string $comment
     * @return \Shopware\CustomModels\Offer\History
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     *
     * @param \Shopware\CustomModels\Offer\Offer $offer
     * @return \Shopware\CustomModels\Offer\History
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
        return $this;
    }

    /**
     *
     * @return \Shopware\CustomModels\Offer\Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     *
     * @param int $offerId
     * @return \Shopware\CustomModels\Offer\History
     */
    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     *
     * @param \Shopware\CustomModels\Offer\States $status
     * @return \Shopware\CustomModels\Offer\History
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     *
     * @return \Shopware\CustomModels\Offer\States
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param int $statusId
     * @return \Shopware\CustomModels\Offer\History
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     *
     * @param \Shopware\CustomModels\Offer\States $previousStatus
     * @return \Shopware\CustomModels\Offer\History
     */
    public function setPreviousStatus($previousStatus)
    {
        $this->previousStatus = $previousStatus;
        return $this;
    }

    /**
     *
     * @return \Shopware\CustomModels\Offer\States
     */
    public function getPreviousStatus()
    {
        return $this->previousStatus;
    }

    /**
     *
     * @param int $previousStatusId
     * @return \Shopware\CustomModels\Offer\History
     */
    public function setPreviousStatusId($previousStatusId)
    {
        $this->previousStatusId = $previousStatusId;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getPreviousStatusId()
    {
        return $this->previousStatusId;
    }

    /**
     *
     * @return \DateTime
     */
    public function getChangeDate()
    {
        return $this->changeDate;
    }

    /**
     *
     * @param \DateTime $changeDate
     */
    public function setChangeDate($changeDate)
    {
        $this->changeDate = $changeDate;
    }
}
