<?php

namespace Laravel\Nova\Tests\Feature;

use Laravel\Nova\Metrics\PartitionResult;
use Laravel\Nova\Tests\IntegrationTest;

class PartitionTest extends IntegrationTest
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function test_partition_keys_and_values_are_formatted_correctly_when_serialized()
    {
        $result = new PartitionResult(['Monthly' => 60, 'Yearly' => 90]);

        $this->assertEquals([
            'value' => [
                ['label' => 'Monthly', 'value' => 60],
                ['label' => 'Yearly', 'value' => 90],
            ],
        ], $result->jsonSerialize());
    }
}
