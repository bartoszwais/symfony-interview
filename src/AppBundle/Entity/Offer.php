<?php // src/AppBundle/Entity/Product.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @UniqueEntity("application_id")
 * @ORM\Table(name="offer")
 */

class Offer
{
    /** @ORM\Column(type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $application_id;
    /** @ORM\Column(type="string")
    * @Assert\Country() */
    private $country;
    /** @ORM\Column(type="decimal", scale=2) */
    private $payout;
    /** @ORM\Column(type="string") */
    private $name;
    /** @ORM\Column(name="platform", type="string", nullable=false, columnDefinition="enum('Android', 'iOS')")
     * * @Assert\NotNull()
     */
    private $platform;

    /**
     * Set applicationId
     *
     * @param integer $applicationId
     *
     * @return Offer
     */
    public function setApplicationId($applicationId)
    {
        $this->application_id = $applicationId;

        return $this;
    }

    /**
     * Get applicationId
     *
     * @return integer
     */
    public function getApplicationId()
    {
        return $this->application_id;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Offer
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set payout
     *
     * @param string $payout
     *
     * @return Offer
     */
    public function setPayout($payout)
    {
        $this->payout = $payout;

        return $this;
    }

    /**
     * Get payout
     *
     * @return string
     */
    public function getPayout()
    {
        return $this->payout;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Offer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set platform
     *
     * @param string $platform
     *
     * @return Offer
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }
}
