# Analytics Module

The Analytics Module provides tracking and visualization of user interactions with elements in the application. It allows you to monitor impressions, hovers, and clicks, and calculate conversion rates between these metrics.

## Features

- Track user interactions (impressions, hovers, clicks)
- Calculate conversion rates
- Sort and filter analytics data
- Configure analytics flows (funnels without direct user correlation)

## Installation

The module is included in the Laravel Starter Kit by default. No additional installation steps are required.

## Configuration

The module can be configured in the `config/analytics.php` file:

```php
return [
    /**
     * Configure pan analytics
     */
    'pan' => [
        'route_prefix' => 'pan',
        'max_analytics' => PHP_INT_MAX,
        'allowed_analytics' => [],
    ],

    /**
     * Configure flows. They are like funnels without the direct user correlation.
     *
     * A flow just works with clicks.
     *
     * Each flow has a description and a list of steps. Each step can have one or more pan-tags.
     */
    'flows' => [
        'Turn prospects into users' => [
            'register',
            ['login', 'login-with-passkey'],
        ],
        'Do not scale on your own' => [
            'create-workspace',
            'invite-workspace-member',
        ],
    ],
];
```

### Configuration Options

- **pan.route_prefix**: The prefix for analytics routes
- **pan.max_analytics**: The maximum number of analytics items to track
- **pan.allowed_analytics**: A list of allowed analytics items
- **flows**: Configuration for analytics flows, which are like funnels without direct user correlation

## Database Structure

The module uses a single table `pan_analytics` with the following structure:

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Name of the analytics item |
| impressions | unsignedBigInteger | Number of impressions |
| hovers | unsignedBigInteger | Number of hovers |
| clicks | unsignedBigInteger | Number of clicks |

## Usage

### Viewing Analytics

Navigate to the analytics dashboard to view the analytics data. The dashboard displays a table with the following columns:

- Name: The name of the analytics item
- Impressions: The number of impressions
- Hovers: The number of hovers (with conversion rate from impressions)
- Clicks: The number of clicks (with conversion rate from impressions)

You can sort the data by any column in ascending or descending order.

### Tracking Analytics

The module uses the Pan analytics library to track user interactions. You can track impressions, hovers, and clicks by adding the appropriate attributes to your HTML elements.

## Dependencies

The module depends on the Pan analytics library, which is included in the Laravel Starter Kit.

## License

This module is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
