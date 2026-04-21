<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Users\Models\User;
use LaravelEnso\Migrator\Exceptions\EnsoStructure;
use LaravelEnso\Migrator\Services\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MigratorValidatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs(User::first());
    }

    #[Test]
    public function accepts_valid_attribute_payloads(): void
    {
        Validator::run(['name', 'description'], [
            'name' => 'example.permission',
            'description' => 'Example',
        ], 'permissions');

        $this->assertTrue(true);
    }

    #[Test]
    public function throws_when_element_is_not_an_array(): void
    {
        $this->expectException(EnsoStructure::class);
        $this->expectExceptionMessage('Invalid structure element "permissions"');

        Validator::run(['name'], 'invalid', 'permissions');
    }

    #[Test]
    public function throws_when_required_attributes_are_missing(): void
    {
        $this->expectException(EnsoStructure::class);
        $this->expectExceptionMessage(
            'Mandatory attribute(s) "description,is_default" missing from the current element "permissions"'
        );

        Validator::run(['name', 'description', 'is_default'], [
            'name' => 'example.permission',
        ], 'permissions');
    }
}
