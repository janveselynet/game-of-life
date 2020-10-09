<?php declare(strict_types=1);

namespace Life\Environment;

use Life\TestCase;
use Life\Utils\Random;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

final class RandomTest extends TestCase
{

    /** @var Random */
    private $random;

    protected function setUp()
    {
        parent::setUp();
        $this->random = new Random();
    }

    public function testGettingRandomArrayValue()
    {
        $array = [1, 5, 10];
        for ($i = 0; $i < 10; $i++) {
            $randomValue = $this->random->getRandomArrayValue($array);
            Assert::true(in_array($randomValue, $array, true), "Given value should be between original values");
        }
    }

}

(new RandomTest())->run();
