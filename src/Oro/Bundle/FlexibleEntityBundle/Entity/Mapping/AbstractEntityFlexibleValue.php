<?php
namespace Oro\Bundle\FlexibleEntityBundle\Entity\Mapping;

use Symfony\Component\HttpFoundation\File\File;
use Oro\Bundle\FlexibleEntityBundle\Model\AbstractFlexible;
use Oro\Bundle\FlexibleEntityBundle\Model\AbstractAttribute;
use Oro\Bundle\FlexibleEntityBundle\Model\AbstractFlexibleValue;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Base Doctrine ORM entity attribute value
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT  MIT
 *
 */
abstract class AbstractEntityFlexibleValue extends AbstractFlexibleValue
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Attribute $attribute
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\FlexibleEntityBundle\Entity\Attribute")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute;

    /**
     * @var Entity $entity
     *
     * This field must by overrided in concret value class
     *
     * @ORM\ManyToOne(targetEntity="AbstractEntityFlexible", inversedBy="values")
     */
    protected $entity;

    /**
     * Locale code
     * @var string $locale
     *
     * @ORM\Column(name="locale_code", type="string", length=5, nullable=true)
     */
    protected $locale;

    /**
     * Locale code
     * @var string $scope
     *
     * @ORM\Column(name="scope_code", type="string", length=20, nullable=true)
     */
    protected $scope;

    /**
     * Store varchar value
     * @var string $varchar
     *
     * @ORM\Column(name="value_string", type="string", length=255, nullable=true)
     */
    protected $varchar;

    /**
     * Store int value
     * @var integer $integer
     *
     * @ORM\Column(name="value_integer", type="integer", nullable=true)
     */
    protected $integer;

    /**
     * Store decimal value
     * @var double $decimal
     *
     * @ORM\Column(name="value_decimal", type="decimal", nullable=true)
     */
    protected $decimal;

    /**
     * Store text value
     * @var string $text
     *
     * @ORM\Column(name="value_text", type="text", nullable=true)
     */
    protected $text;

    /**
     * Store date value
     * @var date $date
     *
     * @ORM\Column(name="value_date", type="date", nullable=true)
     */
    protected $date;

    /**
     * Store datetime value
     * @var date $datetime
     *
     * @ORM\Column(name="value_datetime", type="datetime", nullable=true)
     */
    protected $datetime;

    /**
     * Store many options values
     *
     * This field must by overrided in concret value class
     *
     * @var options ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOption")
     * @ORM\JoinTable(name="oro_flexibleentity_values_options",
     *      joinColumns={@ORM\JoinColumn(name="value_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="option_id", referencedColumnName="id")}
     * )
     */
    protected $options;

    /**
     * Store simple option value
     *
     * @var Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOption $option
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOption", cascade="persist")
     * @ORM\JoinColumn(name="option_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $option;

    /**
     * To implement collection attribute storage, this field must be overridden in concret value class
     *
     * @var ArrayCollection
     */
    protected $collection;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->collection = new ArrayCollection();
    }

    /**
     * Set entity
     *
     * @param AbstractFlexible $entity
     *
     * @return EntityAttributeValue
     */
    public function setEntity(AbstractFlexible $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Set data
     *
     * @param mixed $data
     *
     * @return EntityAttributeValue
     */
    public function setData($data)
    {
        $name = 'set'.ucfirst($this->attribute->getBackendType());

        return $this->$name($data);
    }

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData()
    {
        $name = 'get'.ucfirst($this->attribute->getBackendType());

        return $this->$name();
    }

    /**
     * Get varchar data
     *
     * @return string
     */
    public function getVarchar()
    {
        return $this->varchar;
    }

    /**
     * Set varchar data
     *
     * @param string $varchar
     *
     * @return EntityAttributeValue
     */
    public function setVarchar($varchar)
    {
        $this->varchar = $varchar;

        return $this;
    }

    /**
     * Get integer data
     *
     * @return integer
     */
    public function getInteger()
    {
        return $this->integer;
    }

    /**
     * Set integer data
     *
     * @param integer $integer
     *
     * @return EntityAttributeValue
     */
    public function setInteger($integer)
    {
        $this->integer = $integer;

        return $this;
    }

    /**
     * Get decimal data
     *
     * @return double
     */
    public function getDecimal()
    {
        return $this->decimal;
    }

    /**
     * Set decimal data
     *
     * @param double $decimal
     *
     * @return EntityAttributeValue
     */
    public function setDecimal($decimal)
    {
        $this->decimal = $decimal;

        return $this;
    }

    /**
     * Get text data
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text data
     *
     * @param string $text
     *
     * @return EntityAttributeValue
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get date data
     *
     * @return date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date data
     *
     * @param date $date
     *
     * @return EntityAttributeValue
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get datetime data
     *
     * @return datetime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set datetime data
     *
     * @param datetime $datetime
     *
     * @return EntityAttributeValue
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Set option, used for simple select to set single option
     *
     * @param AbstractEntityAttributeOption $option
     *
     * @return AbstractEntityFlexibleValue
     */
    public function setOption(AbstractEntityAttributeOption $option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get related option, used for simple select to set single option
     *
     * @return AbstractEntityAttributeOption
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Get options, used for multi select to retrieve many options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options, used for multi select to retrieve many options
     *
     * @param ArrayCollection $options
     *
     * @return AbstractEntityFlexibleValue
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Add option, used for multi select to add many options
     *
     * @param AbstractEntityAttributeOption $option
     *
     * @return AbstractEntityFlexible
     */
    public function addOption(AbstractEntityAttributeOption $option)
    {
        $this->options[] = $option;

        return $this;
    }

    /**
     * Get Collection attribute values
     *
     * @return Collection[]
     */
    public function getCollections()
    {
        return $this->collection;
    }

    /**
     * Get Collection attribute values
     *
     * @return Collection[]
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set collections data from value object
     *
     * @param AbstractEntityFlexibleValue $value
     * @return $this
     */
    public function setCollections(AbstractEntityFlexibleValue $value)
    {
        $this->collection = $value->getCollections();

        return $this;
    }

    /**
     * Set collection attribute values
     *
     * @param Collection[] $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $data = $this->getData();

        if ($data instanceof \DateTime) {
            $data = $data->format(\DateTime::ISO8601);
        }

        if ($data instanceof \Doctrine\Common\Collections\Collection) {
            $items = array();
            foreach ($data as $item) {
                $items[]= $item->__toString();
            }

            return implode(', ', $items);

        } else if (is_object($data)) {

            return $data->__toString();
        }

        return $data;
    }
}
