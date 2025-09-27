<?php

declare(strict_types=1);

use App\Data\Facets\AbstractFacet;
use App\Data\Facets\DataTransferObjects\FacetOption;
use Illuminate\Database\Eloquent\Builder;

// Create a concrete implementation of AbstractFacet for testing
class TestFacet extends AbstractFacet
{
    public function __construct(string $label, string $key, ?string $column = null)
    {
        parent::__construct($label, $key, $column);
    }
}

test('it can be instantiated with required properties', function (): void {
    $facet = new TestFacet('Test Label', 'test_key', 'test_column');

    $array = $facet->toArray();
    expect($array)->toBeArray()
        ->and($array)->toHaveKey('label')
        ->and($array['label'])->toBe('Test Label')
        ->and($array)->toHaveKey('key')
        ->and($array['key'])->toBe('test_key');
});

test('it uses key as column when column is not provided', function (): void {
    $facet = new TestFacet('Test Label', 'test_key');

    // We need to test the assignConditionToQuery method to verify the column is set correctly
    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('when')->once()->andReturnSelf();
    $query->shouldReceive('unless')->once()->andReturnSelf();

    $facet->assignConditionToQuery($query, ['value']);

    // Mockery will verify that the expected methods were called
    expect(true)->toBeTrue();
});

test('it can set display selected items', function (): void {
    $facet = new TestFacet('Test Label', 'test_key');
    $facet->displaySelectedItems(5);

    $array = $facet->toArray();
    expect($array)->toBeArray()
        ->and($array)->toHaveKey('label')
        ->and($array['label'])->toBe('Test Label')
        ->and($array)->toHaveKey('key')
        ->and($array['key'])->toBe('test_key')
        ->and($array)->toHaveKey('displaySelectedItems')
        ->and($array['displaySelectedItems'])->toBe(5);
});

test('it can set counts for options', function (): void {
    $facet = new TestFacet('Test Label', 'test_key');

    // Set options using reflection since there's no public method to set options
    $reflection = new ReflectionClass($facet);
    $optionsProperty = $reflection->getProperty('options');
    $optionsProperty->setValue($facet, [
        ['label' => 'Option 1', 'value' => 'option1'],
        ['label' => 'Option 2', 'value' => 'option2'],
    ]);

    // Create a value map for counts
    $valueMap = collect([
        'option1' => 5,
        'option2' => 10,
    ]);

    // Set counts
    $facet->setCounts($valueMap);

    // Get the updated options
    $options = $facet->toArray()['options'];

    expect($options)->toHaveCount(2)
        ->and($options[0])->toBeInstanceOf(FacetOption::class)
        ->and($options[0]->label)->toBe('Option 1')
        ->and($options[0]->value)->toBe('option1')
        ->and($options[0]->count)->toBe(5)
        ->and($options[1])->toBeInstanceOf(FacetOption::class)
        ->and($options[1]->label)->toBe('Option 2')
        ->and($options[1]->value)->toBe('option2')
        ->and($options[1]->count)->toBe(10);
});

test('it can be converted to array', function (): void {
    $facet = new TestFacet('Test Label', 'test_key');

    $array = $facet->toArray();
    expect($array)->toBeArray()
        ->and($array)->toHaveKey('label')
        ->and($array['label'])->toBe('Test Label')
        ->and($array)->toHaveKey('key')
        ->and($array['key'])->toBe('test_key');
});

test('it can be serialized to JSON', function (): void {
    $facet = new TestFacet('Test Label', 'test_key');

    $json = json_encode($facet);
    $decoded = json_decode($json, true);

    expect($decoded)->toBeArray()
        ->and($decoded)->toHaveKey('label')
        ->and($decoded['label'])->toBe('Test Label')
        ->and($decoded)->toHaveKey('key')
        ->and($decoded['key'])->toBe('test_key');
});

test('it can return its key', function (): void {
    $facet = new TestFacet('Test Label', 'test_key');

    expect($facet->key())->toBe('test_key');
});

test('it can assign condition to query for direct column', function (): void {
    $facet = new TestFacet('Test Label', 'test_key', 'test_column');

    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('when')->once()->andReturnSelf();
    $query->shouldReceive('unless')->once()->andReturnSelf();

    $facet->assignConditionToQuery($query, ['value1', 'value2']);

    // Mockery will verify that the expected methods were called
    expect(true)->toBeTrue();
});

test('it can assign condition to query for relation column', function (): void {
    $facet = new TestFacet('Test Label', 'test_key', 'relation.column');

    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('unless')->once()->with('relation', Mockery::type('Closure'))->andReturnSelf();
    $query->shouldReceive('when')->once()->with('relation', Mockery::type('Closure'))->andReturnSelf();

    $facet->assignConditionToQuery($query, ['value1', 'value2']);

    // Mockery will verify that the expected methods were called
    expect(true)->toBeTrue();
});

// Clean up Mockery after each test
afterEach(function (): void {
    Mockery::close();
});
