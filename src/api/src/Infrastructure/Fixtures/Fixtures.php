<?php

declare(strict_types=1);

namespace App\Infrastructure\Fixtures;

use Faker\Factory;
use Faker\Generator;

abstract class Fixtures
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }
}
