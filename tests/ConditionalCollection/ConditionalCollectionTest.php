<?php

namespace MX\Tests\ConditionalCollection;

use Carbon\CarbonInterval;
use Exception;
use InvalidArgumentException;
use MX\ConditionalCollection;
use MX\ConditionalPeriod;
use MX\ConditionalType;
use PHPUnit\Framework\TestCase;
use TypeError;

class ConditionalPeriodByCategoryTest extends TestCase
{
    public function testNewing()
    {
        $this->assertInstanceOf(ConditionalCollection::class, new ConditionalCollection);
    }


    public function testCreatingWithInvalidValues()
    {
        foreach ([-1, 0, 1, null] as $val) {
            try {
                $collection = ConditionalCollection::create($val);

                $this->assertNull($collection, 'Constructed with '.strval($val));
            } catch (Exception $e) {
                $this->assertInstanceOf(InvalidArgumentException::class, $e);
                $this->assertStringStartsWith('Only MX\ConditionalPeriod (as object or string form) can be stored', $e->getMessage());
            }
        }
    }

    public function testCreatingWithConditionalPeriodByCategory()
    {
        $cp = new ConditionalPeriod(ConditionalType::CATEGORY, 1, 2, new CarbonInterval('P1D'));
        $c = ConditionalCollection::create($cp);

        $this->assertInstanceOf(ConditionalCollection::class, $c);
        $this->assertCount(1, $c);
        $this->assertEquals($cp, $c[0]);
    }

    public function testCreatingWithStringConditionalPeriodByCategory()
    {
        $cpStr = 'C1-2P1D';
        $c = ConditionalCollection::create($cpStr);

        $this->assertInstanceOf(ConditionalCollection::class, $c);
        $this->assertCount(1, $c);
        $this->assertEquals($cpStr, $c[0]->toString());
    }

    public function testCreatingWithConditionalPeriodByDuration()
    {
        $cp = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P1D'),
            new CarbonInterval('P2D'),
            new CarbonInterval('P3D')
        );
        $c = ConditionalCollection::create($cp);

        $this->assertInstanceOf(ConditionalCollection::class, $c);
        $this->assertCount(1, $c);
        $this->assertEquals($cp, $c[0]);
    }

    public function testCreatingWithStringConditionalPeriodByDuration()
    {
        $cpStr = 'DP1DP2DP3D';
        $c = ConditionalCollection::create($cpStr);

        $this->assertInstanceOf(ConditionalCollection::class, $c);
        $this->assertCount(1, $c);
        $this->assertEquals($cpStr, $c[0]->toString());
    }

    public function testParseWithInvalidValues()
    {
        foreach ([-1, 0, 1, null, new CarbonInterval, ConditionalPeriod::parse('DP1DP2DP3D')] as $val) {
            try {
                $collection = ConditionalCollection::parse($val);

                $this->assertNull($collection, 'Constructed with '.strval($val));
            } catch (Exception $e) {
                $this->assertInstanceOf(InvalidArgumentException::class, $e);
                $this->assertStringStartsWith('First argument of parse() must be a string', $e->getMessage());
            }
        }
    }

    public function testParseWithValidStringByCategory()
    {
        $cStr = 'C1-3P3D,C4-6P6D,C7-10P9D';
        $c0 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            4,
            6,
            new CarbonInterval('P6D')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            new CarbonInterval('P9D')
        );

        $c = ConditionalCollection::parse($cStr);

        $this->assertInstanceOf(ConditionalCollection::class, $c);
        $this->assertCount(3, $c);
        $this->assertEquals($c0, $c[0]);
        $this->assertEquals($c1, $c[1]);
        $this->assertEquals($c2, $c[2]);
    }

    public function testParseWithValidStringByDuration()
    {
        $cStr = 'DP2DP4DP3M,DP5DP7DP6M,DP8DP10DP9M';
        $c0 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P2D'),
            new CarbonInterval('P4D'),
            new CarbonInterval('P3M')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P5D'),
            new CarbonInterval('P7D'),
            new CarbonInterval('P6M')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P8D'),
            new CarbonInterval('P10D'),
            new CarbonInterval('P9M')
        );

        $c = ConditionalCollection::parse($cStr);

        $this->assertInstanceOf(ConditionalCollection::class, $c);
        $this->assertCount(3, $c);
        $this->assertEquals($c0, $c[0]);
        $this->assertEquals($c1, $c[1]);
        $this->assertEquals($c2, $c[2]);
    }

    public function testFromArrayWithArrayElements()
    {
        foreach ([[-1], [0], [1], [null], [new CarbonInterval]] as $val) {
            try {
                $collection = ConditionalCollection::fromArray($val);

                $this->assertNull($collection, 'Constructed with '.strval($val));
            } catch (Exception $e) {
                $this->assertInstanceOf(InvalidArgumentException::class, $e);
                $this->assertStringStartsWith('First argument of fromArray() must only contain only MX\ConditionalPeriod or its string form elements', $e->getMessage());
            }
        }
    }

    public function testFromArrayWithValidArrayElements()
    {
        $c0 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c1 = 'C4-6P6D';
        $c2 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            'P9D'
        );
        $arr = [$c0, $c1, $c2];

        $c = ConditionalCollection::fromArray($arr);

        $this->assertInstanceOf(ConditionalCollection::class, $c);
        $this->assertCount(3, $c);
        $this->assertEquals($c0, $c[0]);
        $this->assertEquals(ConditionalPeriod::parse($c1), $c[1]);
        $this->assertEquals($c2, $c[2]);
    }

    public function testFromJsonWithArrayElements()
    {
        foreach ([-1, 0, 1, null, new CarbonInterval] as $val) {
            try {
                $collection = ConditionalCollection::fromJson($val);

                $this->assertNull($collection, 'Constructed with '.strval($val));
            } catch (TypeError $e) {
                $this->assertInstanceOf(TypeError::class, $e);
            }
        }
    }

    public function testFromJsonWithValidArrayElements()
    {
        $json = '["C1-3P3D","C4-6P6D","C7-10P9D"]';
        $jsonC = ConditionalCollection::fromArray([
            new ConditionalPeriod(
                ConditionalType::CATEGORY,
                1,
                3,
                new CarbonInterval('P3D')
            ),
            new ConditionalPeriod(
                ConditionalType::CATEGORY,
                4,
                6,
                new CarbonInterval('P6D')
            ),
            new ConditionalPeriod(
                ConditionalType::CATEGORY,
                7,
                10,
                new CarbonInterval('P9D')
            ),
        ]);

        $c = ConditionalCollection::fromJson($json);

        $this->assertInstanceOf(ConditionalCollection::class, $c);
        $this->assertCount(3, $c);
        $this->assertEquals($jsonC, $c);
    }

    public function testPushInvalidValues()
    {
        $exceptionMessage = 'Only MX\ConditionalPeriod (as object or string form) can be stored';
        $c = new ConditionalCollection;

        try {
            $c->push(null);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertCount(0, $c);

        try {
            $c->push(0);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertCount(0, $c);

        try {
            $c->push(1);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertCount(0, $c);
    }

    public function testPushValidCategoryValues()
    {
        $arr = [
            new ConditionalPeriod(
                ConditionalType::CATEGORY,
                1,
                3,
                new CarbonInterval('P3D')
            ),
            'C4-6P6D',
            new ConditionalPeriod(
                ConditionalType::CATEGORY,
                7,
                10,
                'P9D'
            ),
        ];

        $c = new ConditionalCollection;

        $this->assertEquals($c, $c->push($arr[2]));
        $this->assertCount(1, $c);
        $this->assertEquals($arr[2], $c[0]);

        $this->assertEquals($arr[0], $c->push($arr[0], 0)[0]);
        $this->assertCount(2, $c);

        $this->assertEquals($arr[1], $c->push($arr[1], 1)[1]);
        $this->assertCount(3, $c);
    }

    public function testPushValidDurationValues()
    {
        $arr = [
            new ConditionalPeriod(
                ConditionalType::DURATION,
                CarbonInterval::make('P1D'),
                CarbonInterval::make('P3D'),
                new CarbonInterval('P3D')
            ),
            'DP4DP6DP6D',
            new ConditionalPeriod(
                ConditionalType::DURATION,
                'P7D',
                'P10D',
                'P9D'
            ),
        ];

        $c = new ConditionalCollection;

        $this->assertEquals($c, $c->push($arr[2]));
        $this->assertCount(1, $c);
        $this->assertEquals($arr[2], $c[0]);

        $this->assertEquals($arr[0], $c->push($arr[0], 0)[0]);
        $this->assertCount(2, $c);

        $this->assertEquals($arr[1], $c->push($arr[1], 1)[1]);
        $this->assertCount(3, $c);
    }

    public function testFindWithInvalidValues()
    {
        $c = new ConditionalCollection;
        $c[] = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c[] = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            4,
            6,
            new CarbonInterval('P6D')
        );
        $c[] = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            new CarbonInterval('P9D')
        );

        foreach ([null, new ConditionalCollection] as $val) {
            try {
                $found = $c->find($val);
            } catch (Exception $e) {
                $this->assertInstanceOf(InvalidArgumentException::class, $e);
                $this->assertStringStartsWith('Only Carbon\CarbonInterval (as object or string form) or integers can be found', $e->getMessage());
            }
            $this->assertFalse(isset($found));
        }
    }

    public function testFindInCategoryCollection()
    {
        $cp1 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            2,
            4,
            new CarbonInterval('P3D')
        );
        $cp2 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            5,
            7,
            new CarbonInterval('P6D')
        );
        $cp3 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            8,
            10,
            new CarbonInterval('P9D')
        );

        $c = new ConditionalCollection;
        $c[] = $cp1;
        $c[] = $cp2;
        $c[] = $cp3;

        $this->assertNull($c->find(1));
        $this->assertEquals($cp1, $c->find(2));
        $this->assertEquals($cp1, $c->find(3));
        $this->assertEquals($cp1, $c->find(4));

        $this->assertEquals($cp2, $c->find(5));
        $this->assertEquals($cp2, $c->find(6));
        $this->assertEquals($cp2, $c->find(7));

        $this->assertEquals($cp3, $c->find(8));
        $this->assertEquals($cp3, $c->find(9));
        $this->assertEquals($cp3, $c->find(10));
        $this->assertNull($c->find(11));
    }

    public function testFindInDurationCollection()
    {
        $cp1 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P2D'),
            new CarbonInterval('P4D'),
            new CarbonInterval('P3M')
        );
        $cp2 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P5D'),
            new CarbonInterval('P7D'),
            new CarbonInterval('P6M')
        );
        $cp3 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P8D'),
            new CarbonInterval('P10D'),
            new CarbonInterval('P9M')
        );

        $c = new ConditionalCollection;
        $c[] = $cp1;
        $c[] = $cp2;
        $c[] = $cp3;

        $this->assertNull($c->find(new CarbonInterval('P1D')));
        $this->assertEquals($cp1, $c->find(new CarbonInterval('P2D')));
        $this->assertEquals($cp1, $c->find(new CarbonInterval('P3D')));
        $this->assertEquals($cp1, $c->find(new CarbonInterval('P4D')));

        $this->assertEquals($cp2, $c->find(new CarbonInterval('P5D')));
        $this->assertEquals($cp2, $c->find(new CarbonInterval('P6D')));
        $this->assertEquals($cp2, $c->find(new CarbonInterval('P7D')));

        $this->assertEquals($cp3, $c->find(new CarbonInterval('P8D')));
        $this->assertEquals($cp3, $c->find(new CarbonInterval('P9D')));
        $this->assertEquals($cp3, $c->find(new CarbonInterval('P10D')));
        $this->assertNull($c->find(new CarbonInterval('P11D')));

        $this->assertNull($c->find('P1D'));
        $this->assertEquals($cp1, $c->find('P2D'));
        $this->assertEquals($cp1, $c->find('P3D'));
        $this->assertEquals($cp1, $c->find('P4D'));

        $this->assertEquals($cp2, $c->find('P5D'));
        $this->assertEquals($cp2, $c->find('P6D'));
        $this->assertEquals($cp2, $c->find('P7D'));

        $this->assertEquals($cp3, $c->find('P8D'));
        $this->assertEquals($cp3, $c->find('P9D'));
        $this->assertEquals($cp3, $c->find('P10D'));
        $this->assertNull($c->find('P11D'));
    }

    public function testToArrayByCategory()
    {
        $c0 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            4,
            6,
            new CarbonInterval('P6D')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            new CarbonInterval('P9D')
        );
        $cArray = [
            $c0,
            $c1,
            $c2,
        ];

        $c = new ConditionalCollection;
        $c[] = $c0;
        $c[] = $c1;
        $c[] = $c2;

        $this->assertEquals($cArray, $c->toArray());
    }

    public function testToArrayByDuration()
    {
        $c0 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P2D'),
            new CarbonInterval('P4D'),
            new CarbonInterval('P3M')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P5D'),
            new CarbonInterval('P7D'),
            new CarbonInterval('P6M')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P8D'),
            new CarbonInterval('P10D'),
            new CarbonInterval('P9M')
        );
        $cArray = [
            $c0,
            $c1,
            $c2
        ];

        $c = new ConditionalCollection;
        $c[] = $c0;
        $c[] = $c1;
        $c[] = $c2;

        $this->assertEquals([$c0, $c1, $c2], $c->toArray());
    }

    public function testToJsonByCategory()
    {
        $c0 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            4,
            6,
            new CarbonInterval('P6D')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            new CarbonInterval('P9D')
        );
        $cJson = '["C1-3P3D","C4-6P6D","C7-10P9D"]';

        $c = new ConditionalCollection;
        $c[] = $c0;
        $c[] = $c1;
        $c[] = $c2;

        $this->assertEquals($cJson, $c->toJson());
    }

    public function testToJsonByDuration()
    {
        $c0 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P2D'),
            new CarbonInterval('P4D'),
            new CarbonInterval('P3M')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P5D'),
            new CarbonInterval('P7D'),
            new CarbonInterval('P6M')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P8D'),
            new CarbonInterval('P10D'),
            new CarbonInterval('P9M')
        );
        $cJson = '["DP2DP4DP3M","DP5DP7DP6M","DP8DP10DP9M"]';

        $c = new ConditionalCollection;
        $c[] = $c0;
        $c[] = $c1;
        $c[] = $c2;

        $this->assertEquals($cJson, $c->toJson());
    }

    public function testToStringByCategory()
    {
        $c0 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            4,
            6,
            new CarbonInterval('P6D')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            new CarbonInterval('P9D')
        );
        $cStr = 'C1-3P3D,C4-6P6D,C7-10P9D';

        $c = new ConditionalCollection;
        $c[] = $c0;
        $c[] = $c1;
        $c[] = $c2;

        $this->assertEquals($cStr, $c->toString());
    }

    public function testToStringByDuration()
    {
        $c0 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P2D'),
            new CarbonInterval('P4D'),
            new CarbonInterval('P3M')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P5D'),
            new CarbonInterval('P7D'),
            new CarbonInterval('P6M')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::DURATION,
            new CarbonInterval('P8D'),
            new CarbonInterval('P10D'),
            new CarbonInterval('P9M')
        );
        $cStr = 'DP2DP4DP3M,DP5DP7DP6M,DP8DP10DP9M';

        $c = new ConditionalCollection;
        $c[] = $c0;
        $c[] = $c1;
        $c[] = $c2;

        $this->assertEquals($cStr, $c->toString());
    }

    public function testArrayAccess()
    {
        $c0 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            4,
            6,
            new CarbonInterval('P6D')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            new CarbonInterval('P9D')
        );

        $c = new ConditionalCollection;

        // offsetExists()
        $this->assertFalse(isset($c[0]));
        $c[] = $c0;
        $c[] = $c0;
        $this->assertTrue(isset($c[0]));

        // offsetGet()
        $this->assertEquals($c0, $c[0]);
        $this->assertEquals($c0, $c[1]);

        // offsetSet()
        $c[1] = $c1;
        $this->assertEquals($c1, $c[1]);

        // offsetUnset()
        unset($c[1]);
        $this->assertFalse(isset($c[1]));
    }

    public function testCountable()
    {
        $c0 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            4,
            6,
            new CarbonInterval('P6D')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            new CarbonInterval('P9D')
        );

        $c = new ConditionalCollection;
        $c[] = $c0;
        $c[] = $c1;
        $c[] = $c2;

        $this->assertCount(3, $c);
    }

    public function testSerialize()
    {
        $c = new ConditionalCollection;
        $c[] = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c[] = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            4,
            6,
            new CarbonInterval('P6D')
        );
        $c[] = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            new CarbonInterval('P9D')
        );
        $cStr = 'O:24:"MX\ConditionalCollection":3:{i:0;s:7:"C1-3P3D";i:1;s:7:"C4-6P6D";i:2;s:8:"C7-10P9D";}';

        $this->assertEquals($cStr, serialize($c));
    }

    public function testUnserialize()
    {
        $cStr = 'O:24:"MX\ConditionalCollection":3:{i:0;s:7:"C1-3P3D";i:1;s:7:"C4-6P6D";i:2;s:8:"C7-10P9D";}';
        $c0 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            1,
            3,
            new CarbonInterval('P3D')
        );
        $c1 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            4,
            6,
            new CarbonInterval('P6D')
        );
        $c2 = new ConditionalPeriod(
            ConditionalType::CATEGORY,
            7,
            10,
            new CarbonInterval('P9D')
        );

        $c = unserialize($cStr);

        $this->assertInstanceOf(ConditionalCollection::class, $c);
        $this->assertCount(3, $c);
        $this->assertEquals($c0, $c[0]);
        $this->assertEquals($c1, $c[1]);
        $this->assertEquals($c2, $c[2]);
    }
}
