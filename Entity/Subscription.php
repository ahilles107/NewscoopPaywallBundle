<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\PaywallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\PaywallBundle\Validator\Constraints as PaywallValidators;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Subscriptions entity.
 *
 * @ORM\Entity(repositoryClass="Newscoop\PaywallBundle\Entity\Repository\SubscriptionRepository")
 * @ORM\Table(name="plugin_paywall_subscriptions")
 * @Gedmo\TranslationEntity(class="SubscriptionTranslation")
 */
class Subscription implements Translatable, PriceableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     *
     * @var int
     */
    protected $id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", name="name")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="SubscriptionSpecification", mappedBy="subscription", cascade={"persist", "remove"})
     *
     * @var array
     */
    protected $specification;

    /**
     * @ORM\Column(type="text", name="type")
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="Duration", mappedBy="subscription", cascade={"persist", "remove"})
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    protected $ranges;

    /**
     * @PaywallValidators\ContainsDecimal(entity="Subscription", property="price")
     * @ORM\Column(type="decimal", name="price", precision=10, scale=2)
     *
     * @var decimal
     */
    protected $price;

    /**
     * @ORM\Column(type="string", name="currency")
     *
     * @var string
     */
    protected $currency;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", name="description", nullable=true)
     *
     * @var text
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var string
     */
    protected $created_at;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     *
     * @var bool
     */
    protected $is_active;

    /**
     * @ORM\Column(type="boolean", name="is_default", nullable=true)
     *
     * @var bool
     */
    protected $is_default;

    /**
     * @ORM\OneToMany(
     *   targetEntity="SubscriptionTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    public $locale;

    public function __construct($name = null, $type = null, $price = null, $currency = null, $duration = null)
    {
        $this->specification = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
        $this->setIsActive(true);
        $this->ranges = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->name = $name;
        $this->type = $type;
        $this->price = $price;
        $this->currency = $currency;
        if ($duration) {
            $this->addRange($duration);
        }
    }

    /**
     * Get subscription id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get subscription name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set subscription name.
     *
     * @param string $name
     *
     * @return string
     */
    public function setName($name)
    {
        $this->name = $name;

        return $name;
    }

    /**
     * Get specification.
     *
     * @return array
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * Get subscription type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set subscription type.
     *
     * @param int $type
     *
     * @return int
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get subscription price.
     *
     * @return decimal
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set subscription price.
     *
     * @param decimal $price
     *
     * @return decimal
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get subscription currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set subscription currency.
     *
     * @param string $currency
     *
     * @return string
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get subscription description.
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set subscription description.
     *
     * @param text $description
     *
     * @return text
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get subscription status.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set subscription status.
     *
     * @param bool $is_active
     *
     * @return bool
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * Get subscription create date.
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set subscription create date.
     *
     * @param datetime $created_at
     *
     * @return datetime
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get default.
     *
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->is_default;
    }

    /**
     * Set is_default.
     *
     * @param bool $is_default
     *
     * @return bool
     */
    public function setIsDefault($is_default)
    {
        $this->is_default = $is_default;

        return $this;
    }

    /**
     * Gets the value of ranges.
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * Sets the value of ranges.
     *
     * @param Doctrine\Common\Collections\ArrayCollection $ranges the ranges
     *
     * @return self
     */
    public function setRanges(\Doctrine\Common\Collections\ArrayCollection $ranges)
    {
        $this->ranges = $ranges;

        return $this;
    }

    /**
     * Adds Subscription duration.
     *
     * @param Duration $duration Duration to add
     *
     * @return Duration
     */
    public function addRange(Duration $duration)
    {
        $this->ranges->add($duration);

        return $this;
    }

    public function getObject()
    {
        return clone $this;
    }

    public function getContextCurrency()
    {
        return clone $this;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(SubscriptionTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setObject($this);
        }
    }

    /**
     * Adds Subscription specification.
     *
     * @param SubscriptionSpecification $spec SubscriptionSpecification to add
     *
     * @return SubscriptionSpecification
     */
    public function addSpecification(SubscriptionSpecification $spec)
    {
        if (!$this->specification->contains($spec)) {
            $this->specification->add($spec);
        }
    }
}
