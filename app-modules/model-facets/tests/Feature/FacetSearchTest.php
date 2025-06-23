<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\ModelFacets\Tests\Models\Product;

beforeEach(function (): void {
    Schema::create('test__products', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('brand');
        $table->string('color')->nullable();
        $table->integer('price');
        $table->boolean('is_available');
        $table->text('description');
        $table->timestamps();
    });

    Product::create(['name' => 'iPhone 15', 'brand' => 'Apple', 'color' => 'Red', 'price' => 1200, 'is_available' => true, 'description' => 'Latest Apple phone']);
    Product::create(['name' => 'Galaxy S23', 'brand' => 'Samsung', 'color' => 'Blue', 'price' => 1000, 'is_available' => true, 'description' => 'Latest Samsung phone']);
    Product::create(['name' => 'Pixel 8', 'brand' => 'Google', 'color' => 'Black', 'price' => 900, 'is_available' => false, 'description' => 'Pure Android phone']);
    Product::create(['name' => 'iPhone SE', 'brand' => 'Apple', 'color' => 'Black', 'price' => 600, 'is_available' => true, 'description' => 'Affordable Apple phone']);
    Product::create(['name' => 'Galaxy Z Fold', 'brand' => 'Samsung', 'color' => null, 'price' => 1800, 'is_available' => true, 'description' => 'Foldable Samsung device']);
});

afterEach(function (): void {
    Schema::dropIfExists('test__products');
});

it('fetches all results without filters and with correct facet counts', function (): void {
    $response = Product::query()->withFacetSearch([]);

    // Ergebnisse
    expect($response->results->total())->toBe(5);

    // Facetten-Counts
    $facets = collect($response->facets['brand']['options']);
    expect($facets->firstWhere('value', 'Apple')['count'])->toBe(2);
    expect($facets->firstWhere('value', 'Samsung')['count'])->toBe(2);
    expect($facets->firstWhere('value', 'Google')['count'])->toBe(1);
});

it('filters by a single value', function (): void {
    $response = Product::query()->withFacetSearch(['filters' => ['brand' => 'Apple']]);

    // Ergebnisse
    expect($response->results->total())->toBe(2);
    expect($response->results->items()[0]->name)->toBe('iPhone 15');
    expect($response->results->items()[1]->name)->toBe('iPhone SE');
});

it('filters by multiple values (multi-select)', function (): void {
    $response = Product::query()->withFacetSearch(['filters' => ['brand' => ['Apple', 'Google']]]);

    expect($response->results->total())->toBe(3);
});

it('filters by a NULL value', function (): void {
    $response = Product::query()->withFacetSearch(['filters' => ['color' => '_null_']]);

    expect($response->results->total())->toBe(1);
    expect($response->results->items()[0]->name)->toBe('Galaxy Z Fold');
});

it('combines a regular filter and a NULL filter', function (): void {
    $response = Product::query()->withFacetSearch(['filters' => ['color' => ['Black', '_null_']]]);

    expect($response->results->total())->toBe(3); // Pixel 8 (Black), iPhone SE (Black), Galaxy Z Fold (null)
});

it('performs a fuzzy search', function (): void {
    $response = Product::query()->withFacetSearch(['q' => 'phone']);

    expect($response->results->total())->toBe(4); // Alle außer dem Foldable
});

it('sorts results by price descending', function (): void {
    $response = Product::query()->withFacetSearch(['sort_by' => '-price']);

    expect($response->results->items()[0]->name)->toBe('Galaxy Z Fold'); // teuerstes
    expect($response->results->items()[4]->name)->toBe('iPhone SE'); // günstigstes
});

it('correctly calculates self-excluding facet counts', function (): void {
    // Filtern nach Marke "Apple"
    $response = Product::query()->withFacetSearch(['filters' => ['brand' => 'Apple']]);

    // Ergebnisse sind nur von Apple
    expect($response->results->total())->toBe(2);

    // 1. Die "brand"-Facette (die gefilterte) sollte immer noch alle Marken anzeigen,
    // um die Auswahl zu erweitern oder zu ändern.
    $brandFacets = collect($response->facets['brand']['options']);
    expect($brandFacets->firstWhere('value', 'Apple')['count'])->toBe(2);
    expect($brandFacets->firstWhere('value', 'Samsung')['count'])->toBe(2); // Wichtig!
    expect($brandFacets->firstWhere('value', 'Google')['count'])->toBe(1);  // Wichtig!

    // 2. Die "color"-Facette sollte nur die Farben der gefilterten Apple-Produkte anzeigen.
    $colorFacets = collect($response->facets['color']['options']);
    expect($colorFacets->count())->toBe(2);
    expect($colorFacets->firstWhere('value', 'Red')['count'])->toBe(1);
    expect($colorFacets->firstWhere('value', 'Black')['count'])->toBe(1);
    expect($colorFacets->firstWhere('value', 'Blue'))->toBeNull(); // Keine blauen iPhones
});

it('combines a filter and a fuzzy search', function (): void {
    // Suche nach "phone" nur bei der Marke "Samsung"
    $response = Product::query()->withFacetSearch([
        'q' => 'phone',
        'filters' => ['brand' => 'Samsung'],
    ]);

    expect($response->results->total())->toBe(1);
    expect($response->results->items()[0]->name)->toBe('Galaxy S23');
});

it('uses the php label resolver correctly', function (): void {
    $response = Product::query()->withFacetSearch([]);

    $availableFacets = collect($response->facets['available']['options']);
    // Teste, ob `is_available = 1` zu "Ja" wird und die Counts stimmen
    $yesOption = $availableFacets->firstWhere('value', 1);
    expect($yesOption['label'])->toBe('Ja');
    expect($yesOption['count'])->toBe(4);

    // Teste, ob `is_available = 0` zu "Nein" wird
    $noOption = $availableFacets->firstWhere('value', 0);
    expect($noOption['label'])->toBe('Nein');
    expect($noOption['count'])->toBe(1);
});

it('resolves the correct range facet options', function () {
    $response = Product::query()->withFacetSearch([]);

    $priceOptions = $response->facets['price']['options'];
    expect($priceOptions['min'])->toBe(600);
    expect($priceOptions['max'])->toBe(1800);
});

it('finds products by price range', function () {
    $response = Product::query()->withFacetSearch([
        'filters' => [
            'price' => [
                'min' => 300,
                'max' => 650,
            ]
        ]
    ]);

    expect($response->results->total())->toBe(1);
    expect($response->results->items()[0]->name)->toBe('iPhone SE');
});

it('resolves the correct boolean facet options', function () {
    $response = Product::query()->withFacetSearch([]);

    $availableFacets = collect($response->facets['is_available_as_boolean']['options']);
    $yesOption = $availableFacets->firstWhere('value', 1);
    expect($yesOption['label'])->toBe('Auf Lager');
    expect($yesOption['count'])->toBe(4);

    // Teste, ob `is_available = 0` zu "Nein" wird
    $noOption = $availableFacets->firstWhere('value', 0);
    expect($noOption['label'])->toBe('Ausverkauft');
    expect($noOption['count'])->toBe(1);
});
