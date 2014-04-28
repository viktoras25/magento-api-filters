<?php

namespace MagentoFilters\Tests;

use MagentoFilters\Builder;

/**
 * @covers MagentoFilters\Builder
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testNewlyCreated()
    {
        $builder = new Builder();
        $this->assertTrue($builder->isSimple());
        $this->assertFalse($builder->isComplex());
        $this->assertEquals($builder->toArray(), array());
    }

    public function testComplexFilter()
    {
        $builder = new Builder();
        $builder->
            from('created_at', '2014-01-01 00:00:00')->
            in('status', 'complete,pending')->
            greaterThanOrEquals('price', '100')->
            toArray();

        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'created_at',
                    'value' => array('key' => 'from', 'value' => '2014-01-01 00:00:00')
                ),
                array(
                    'key' => 'status',
                    'value' => array('key' => 'in', 'value' => 'complete,pending')
                ),
                array(
                    'key' => 'price',
                    'value' => array('key' => 'gteq', 'value' => '100')
                ),
            )
        ));
    }

    public function testTwoFiltersOnAField()
    {
        $builder = new Builder();
        $builder->
            from('created_at', '2014-01-01 00:00:00')->
            to('created_at', '2013-01-01 00:00:00')->
            toArray();

        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'created_at',
                    'value' => array('key' => 'to', 'value' => '2013-01-01 00:00:00')
                )
            )
        ));
    }

    public function testValidate()
    {
        $builder = new Builder();
        $array = $builder->
            to('test', 'value')->
            eq('something', 'else')->
            notIn('dont forget', 'this also')->
            gt('what what', 'in the')->
            toArray();

        $this->assertTrue($builder->validate($array));
    }

    public function testEq()
    {
        $builder = new Builder();
        $builder->eq('some_id', 100);
        $this->assertTrue($builder->isSimple());
        $this->assertFalse($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => 100
                )
            )
        ));
        $builder->eq('other_param', 5);
        $this->assertTrue($builder->isSimple());
        $this->assertFalse($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => 100
                ),
                array(
                    'key' => 'other_param',
                    'value' => 5
                )
            )
        ));
    }

    public function testEquals()
    {
        $builder = new Builder();
        $builder->equals('some_id', 100);
        $this->assertTrue($builder->isSimple());
        $this->assertEquals($builder->toArray(), array(
            'filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => 100
                )
            )
        ));
        $builder->equals('other_param', 5);
        $this->assertTrue($builder->isSimple());
        $this->assertEquals($builder->toArray(), array(
            'filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => 100
                ),
                array(
                    'key' => 'other_param',
                    'value' => 5
                )
            )
        ));
    }

    public function testNeq()
    {
        $builder = new Builder();
        $builder->neq('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'neq', 'value' => 100)
                )
            )
        ));
    }

    public function testNotEquals()
    {
        $builder = new Builder();
        $builder->notEquals('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'neq', 'value' => 100)
                )
            )
        ));
    }

    public function testLike()
    {
        $builder = new Builder();
        $builder->like('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'like', 'value' => 100)
                )
            )
        ));
    }

    public function testNlike()
    {
        $builder = new Builder();
        $builder->nlike('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'nlike', 'value' => 100)
                )
            )
        ));
    }

    public function testNotLike()
    {
        $builder = new Builder();
        $builder->notLike('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'nlike', 'value' => 100)
                )
            )
        ));
    }

    public function testIn()
    {
        $builder = new Builder();
        $builder->in('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'in', 'value' => 100)
                )
            )
        ));
    }

    public function testNis()
    {
        $builder = new Builder();
        $builder->nin('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'nin', 'value' => 100)
                )
            )
        ));
    }

    public function testNotIn()
    {
        $builder = new Builder();
        $builder->notIn('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'nin', 'value' => 100)
                )
            )
        ));
    }

    public function testIs()
    {
        $builder = new Builder();
        $builder->is('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'is', 'value' => 100)
                )
            )
        ));
    }

    public function testNotNull()
    {
        $builder = new Builder();
        $builder->notNull('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'notnull', 'value' => 100)
                )
            )
        ));
    }

    public function testIsNull()
    {
        $builder = new Builder();
        $builder->isNull('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'null', 'value' => 100)
                )
            )
        ));
    }

    public function testGt()
    {
        $builder = new Builder();
        $builder->gt('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'gt', 'value' => 100)
                )
            )
        ));
    }

    public function testGreaterThan()
    {
        $builder = new Builder();
        $builder->greaterThan('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'gt', 'value' => 100)
                )
            )
        ));
    }

    public function testLt()
    {
        $builder = new Builder();
        $builder->lt('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'lt', 'value' => 100)
                )
            )
        ));
    }

    public function testLessThan()
    {
        $builder = new Builder();
        $builder->lessThan('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'lt', 'value' => 100)
                )
            )
        ));
    }

    public function testGteq()
    {
        $builder = new Builder();
        $builder->gteq('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'gteq', 'value' => 100)
                )
            )
        ));
    }

    public function testGreaterThanOrEquals()
    {
        $builder = new Builder();
        $builder->greaterThanOrEquals('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'gteq', 'value' => 100)
                )
            )
        ));
    }

    public function testLteq() {

        $builder = new Builder();
        $builder->lteq('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'lteq', 'value' => 100)
                )
            )
        ));
    }

    public function testLessThanOrEquals()
    {
        $builder = new Builder();
        $builder->lessThanOrEquals('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'lteq', 'value' => 100)
                )
            )
        ));
    }

    public function testFindInSet()
    {
        $builder = new Builder();
        $builder->findInSet('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'finset', 'value' => 100)
                )
            )
        ));
    }

    public function testRegexp()
    {
        $builder = new Builder();
        $builder->regexp('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'regexp', 'value' => 100)
                )
            )
        ));
    }

    public function testFrom()
    {
        $builder = new Builder();
        $builder->from('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'from', 'value' => 100)
                )
            )
        ));
    }

    public function testTo()
    {
        $builder = new Builder();
        $builder->to('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'to', 'value' => 100)
                )
            )
        ));
    }

    public function testSeq()
    {
        $builder = new Builder();
        $builder->seq('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'seq', 'value' => 100)
                )
            )
        ));
    }

    public function testSneq()
    {
        $builder = new Builder();
        $builder->sneq('some_id', 100);
        $this->assertTrue($builder->isComplex());
        $this->assertEquals($builder->toArray(), array(
            'complex_filter' => array(
                array(
                    'key' => 'some_id',
                    'value' => array('key' => 'sneq', 'value' => 100)
                )
            )
        ));
    }
}