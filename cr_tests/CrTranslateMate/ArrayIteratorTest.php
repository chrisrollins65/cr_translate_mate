<?php

namespace CrTranslateMate;

use PHPUnit\Framework\TestCase;

class ArrayIteratorTest extends TestCase
{
    private $array = ['element1', 'element2', 'element3'];
    /** @var ArrayIterator */
    private $iterator;

    protected function setUp()
    {
        $this->iterator = new ArrayIterator($this->array);
    }

    public function testAdvancingAndRewindingArray()
    {
        $this->assertEquals($this->array[1], $this->iterator->next());
        $this->iterator->rewind();
        $this->assertEquals($this->array[0], $this->iterator->current());
    }

    public function testGettingArrayKey()
    {
        $this->assertEquals(key($this->array), $this->iterator->key());
    }

    public function testGettingInvalidArrayKey()
    {
        $this->iterator->next();
        $this->iterator->next();
        $this->iterator->next();
        $this->assertFalse($this->iterator->valid());
    }
}