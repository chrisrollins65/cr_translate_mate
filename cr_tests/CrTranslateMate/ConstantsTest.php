<?php

namespace CrTranslateMate;

use PHPUnit\Framework\TestCase;

class ConstantsTest extends TestCase
{
    /** @var Constants */
    private $constants;

    protected function setUp()
    {
        $this->constants = new Constants();
    }

    public function testThatConstantIsReturned()
    {
        if (!defined('CONSTANTS_TEST_CONSTANT')) {
            define('CONSTANTS_TEST_CONSTANT', 'constantsTestConstant');
        }

        $this->assertEquals(CONSTANTS_TEST_CONSTANT, $this->constants->get('CONSTANTS_TEST_CONSTANT'));
    }

    public function testThatEmptyStringIsReturnedIfConstantDoesntExist()
    {
        $this->assertEquals('', $this->constants->get('CONSTANTS_TEST_CONSTANT_THAT_DOESNT_EXIST'));
    }
}