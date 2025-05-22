# Foundation Layout Module

## Purpose

The Foundation Layout module handles configuration related to the layout variants used in the application. It provides functionality to switch between different layout styles and configurations.

## Components

- **Console Commands**
  - `app:configure-layouts`: A command to configure and switch between available layouts

- **Service**: Contains the core functionality for managing layouts
- **Variants**: Contains different layout variants that can be configured
- **Data**: Contains data structures used by the module
- **Providers**: Contains service providers for the module

## Usage

### Configuration

The module is automatically registered through its service provider `Modules\FoundationLayout\Providers\FoundationLayoutServiceProvider`.

### Commands

To configure and switch between available layouts, run:

```bash
php artisan app:configure-layouts
```

This command allows you to select from the available layout variants provided by the Laravel starter kits.

## Integration

The module integrates with the Laravel application to provide layout configuration capabilities. It works with the starter kits' available layouts as mentioned in the Laravel documentation.
