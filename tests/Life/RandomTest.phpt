<?php declare(strict_types=1);

namespace Life;

use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class RandomTest extends TestCase
{
    public function testGettingRandomArrayValue(): void
    {
        $random = new Random();
        $values = [1, 3, 5, 10];

        $randomValue = $random->getRandomArrayValue($values);

        Assert::contains($randomValue, $values);
    }
}

(new RandomTest())->run();
