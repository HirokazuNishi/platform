<?php
namespace Oro\Bundle\FlexibleEntityBundle\Tests\Unit\Entity;

use Oro\Bundle\FlexibleEntityBundle\Entity\Attribute;
use Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOption;
use Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOptionValue;

/**
 * Test related class
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 *
 */
class AttributeOptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @staticvar string
     */
    protected static $locale = 'en';

    /**
     * @staticvar string
     */
    protected static $localeFr = 'fr';

    /**
     * @staticvar integer
     */
    protected static $id = 3;

    /**
     * @staticvar integer
     */
    protected static $sortOrder = 5;

    /**
     * @staticvar string
     */
    protected static $attCode = 'testAtt';

    /**
     * @staticvar string
     */
    protected static $attOptValueEn = 'testAttOptValueEn';

    /**
     * @staticvar string
     */
    protected static $attOptValueFr = 'testAttOptValueFr';

    /**
     * @staticvar string
     */
    protected static $defaultValue = 'testDefValue';

    /**
     * @staticvar string
     */
    protected static $attClass = 'Oro\Bundle\FlexibleEntityBundle\Entity\Attribute';

    /**
     * @staticvar string
     */
    protected static $attOptClass = 'Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOption';

    /**
     * @staticvar string
     */
    protected static $attOptValueClass = 'Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOptionValue';

    /**
     * Test related getter/setter method
     */
    public function testId()
    {
        $attOpt = new AttributeOption();

        // assert default value is null
        $this->assertNull($attOpt->getId());

        // assert get/set
        $obj = $attOpt->setId(self::$id);
        $this->assertInstanceOf(self::$attOptClass, $obj);
        $this->assertEquals(self::$id, $attOpt->getId());
    }

    /**
     * Test related getter/setter method
     */
    public function testTranslatable()
    {
        $attOpt = new AttributeOption();

        // assert default value
        $this->assertTrue($attOpt->getTranslatable());

        // assert false value
        $obj = $attOpt->setTranslatable(false);
        $this->assertInstanceOf(self::$attOptClass, $obj);
        $this->assertFalse($attOpt->getTranslatable());

        // assert true value
        $obj = $attOpt->setTranslatable(true);
        $this->assertInstanceOf(self::$attOptClass, $obj);
        $this->assertTrue($attOpt->getTranslatable());
    }

    /**
     * Test related getter/setter method
     */
    public function testSortOrder()
    {
        $attOpt = new AttributeOption();

        // assert default value
        $this->assertEquals(1, $attOpt->getSortOrder());

        // assert get/set
        $obj = $attOpt->setSortOrder(self::$sortOrder);
        $this->assertInstanceOf(self::$attOptClass, $obj);
        $this->assertEquals(self::$sortOrder, $attOpt->getSortOrder());
    }

    /**
     * Test related getter/setter method
     */
    public function testgetLocale()
    {
        $attOpt = new AttributeOption();

        // assert default value is null
        $this->assertNull($attOpt->getLocale());

        // assert get/set
        $obj = $attOpt->setLocale(self::$locale);
        $this->assertInstanceOf(self::$attOptClass, $obj);
        $this->assertEquals(self::$locale, $attOpt->getLocale());
    }

    /**
     * Test related getter/setter method
     */
    public function testAttribute()
    {
        $attOpt = new AttributeOption();

        // assert default value
        $this->assertNull($attOpt->getAttribute());

        // assert get/set
        $att = new Attribute();
        $att->setCode(self::$attCode);
        $obj = $attOpt->setAttribute($att);

        $this->assertInstanceOf(self::$attOptClass, $obj);
        $this->assertInstanceOf(self::$attClass, $attOpt->getAttribute());
        $this->assertEquals($att, $attOpt->getAttribute());
        $this->assertEquals(self::$attCode, $attOpt->getAttribute()->getCode());
    }

    /**
     * Test related getter/setter method
     */
    public function testAttributeOptionValue()
    {
        $attOpt = new AttributeOption();

        // assert default values
        $this->assertCount(0, $attOpt->getOptionValues());

        // assert adding option
        $attOptValueEn = new AttributeOptionValue();
        $attOptValueEn->setValue(self::$attOptValueEn);
        $attOptValueEn->setLocale(self::$locale);
        $attOpt->addOptionValue($attOptValueEn);

        // assert result
        $attOpt->setTranslatable(false);
        $attOptValue = $attOpt->getOptionValue();
        $this->assertInstanceOf(self::$attOptValueClass, $attOptValue);
        $this->assertEquals(self::$locale, $attOptValue->getLocale());
        $this->assertEquals(self::$attOptValueEn, $attOptValue->getValue());

        // add a second value and define option as translatable
        $attOpt->setTranslatable(true);
        $attOptValueFr = new AttributeOptionValue();
        $attOptValueFr->setValue(self::$attOptValueFr);
        $attOptValueFr->setLocale(self::$localeFr);
        $attOpt->setLocale(self::$localeFr);
        $obj = $attOpt->addOptionValue($attOptValueFr);

        // assertions getter
        $this->assertInstanceOf(self::$attOptClass, $obj);
        $this->assertCount(2, $attOpt->getOptionValues());

        // assert option value fr
        $attOptValue = $attOpt->getOptionValue();
        $this->assertInstanceOf(self::$attOptValueClass, $attOptValue);
        $this->assertEquals(self::$localeFr, $attOptValue->getLocale());
        $this->assertEquals(self::$attOptValueFr, $attOptValue->getValue());

        // assert option value en
        $attOpt->setLocale(self::$locale);
        $attOptValue = $attOpt->getOptionValue();
        $this->assertInstanceOf(self::$attOptValueClass, $attOptValue);
        $this->assertEquals(self::$locale, $attOptValue->getLocale());
        $this->assertEquals(self::$attOptValueEn, $attOptValue->getValue());
        $this->assertEquals(self::$attOptValueEn, $attOpt->__toString());

        // assert remove option value
        $obj = $attOpt->removeOptionValue($attOptValueEn);
        $this->assertInstanceOf(self::$attOptClass, $obj);
        $this->assertCount(1, $attOpt->getOptionValues());
        $this->assertFalse($attOpt->getOptionValue());

        $attOpt->setLocale(self::$localeFr);
        $attOptValue = $attOpt->getOptionValue();
        $this->assertInstanceOf(self::$attOptValueClass, $attOptValue);
        $this->assertEquals(self::$localeFr, $attOptValue->getLocale());
        $this->assertEquals(self::$attOptValueFr, $attOptValue->getValue());
        $this->assertEquals(self::$attOptValueFr, $attOpt->__toString());
    }

    /**
     * Test related getter/setter method
     */
    public function testDefaultValue()
    {
        $attOpt = new AttributeOption();

        // assert default value is null
        $this->assertNull($attOpt->getDefaultValue());

        // assert get/set
        $obj = $attOpt->setDefaultValue(self::$defaultValue);
        $this->assertInstanceOf(self::$attOptClass, $obj);
        $this->assertEquals(self::$defaultValue, $attOpt->getDefaultValue());
    }
}
