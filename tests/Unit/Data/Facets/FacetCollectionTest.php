<?php

declare(strict_types=1);

use App\Data\Facets\AbstractFacet;
use App\Data\Facets\FacetCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Mockery as m;

// Create a mock implementation of AbstractFacet for testing
class CollectionTestFacet extends AbstractFacet
{
    public function __construct(string $label, string $key, ?string $column = null)
    {
        parent::__construct($label, $key, $column);
    }
}

test('it can be instantiated using make method', function (): void {
    $collection = FacetCollection::make();

    expect($collection)->toBeInstanceOf(FacetCollection::class);
});

test('it can add and retrieve facets', function (): void {
    $facet1 = new CollectionTestFacet('Facet 1', 'facet1');
    $facet2 = new CollectionTestFacet('Facet 2', 'facet2');

    $collection = FacetCollection::make()
        ->add($facet1)
        ->add($facet2);

    $facets = $collection->get();

    expect($facets)->toBeInstanceOf(Collection::class)
        ->and($facets)->toHaveCount(2)
        ->and($facets->get('facet1'))->toBe($facet1)
        ->and($facets->get('facet2'))->toBe($facet2);
});

test('it can filter a query using facets', function (): void {
    // Create concrete facets
    $facet1 = new CollectionTestFacet('Facet 1', 'facet1');
    $facet2 = new CollectionTestFacet('Facet 2', 'facet2');

    // Create mock query builder
    $query = m::mock(Builder::class);

    // Set up expectations for query methods
    $query->shouldReceive('when')->twice()->andReturnSelf();
    $query->shouldReceive('unless')->twice()->andReturnSelf();

    // Create filters collection
    $filters = collect([
        'facet1' => ['value1', 'value2'],
        'facet2' => ['value3'],
        'facet3' => ['value4'], // This facet doesn't exist in the collection
    ]);

    // Create and populate facet collection
    $collection = FacetCollection::make()
        ->add($facet1)
        ->add($facet2);

    // Filter the query
    $collection->filterQuery($filters, $query);

    // Mockery will verify that the expected methods were called
    expect(true)->toBeTrue();
});

test('it skips filtering for facets that do not exist', function (): void {
    // Create concrete facet
    $facet = new CollectionTestFacet('Facet 1', 'facet1');

    // Create mock query builder
    $query = m::mock(Builder::class);

    // Set up expectations for query methods
    $query->shouldReceive('when')->once()->andReturnSelf();
    $query->shouldReceive('unless')->once()->andReturnSelf();

    // Create filters collection with a non-existent facet
    $filters = collect([
        'facet1' => ['value1'],
        'nonexistent' => ['value2'],
    ]);

    // Create and populate facet collection
    $collection = FacetCollection::make()->add($facet);

    // Filter the query
    $collection->filterQuery($filters, $query);

    // Mockery will verify that the expected methods were called
    expect(true)->toBeTrue();
});

// Clean up Mockery after each test
afterEach(function (): void {
    m::close();
});
