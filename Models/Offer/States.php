<?php


namespace Shopware\CustomModels\Offer;
use       Shopware\Components\Model\ModelEntity,
          Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Table(name="s_offer_states")
 * @ORM\Entity
  */
class States extends ModelEntity
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
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;


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
     * Set description
     *
     * @param string $description
     * @return Status
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


}
