# Notification Module

## Purpose

The Notification module improves the user notification system by providing functionality for managing user notification preferences and handling notification delivery. It allows users to choose between in-app notifications and email notifications based on their preferences.

## Components

- **Console Commands**
  - `notifications:purge`: A command to purge old notifications

- **Contracts**: Contains interfaces defining the module's functionality
- **DataTransferObjects**: Contains DTOs for transferring data between components
- **Http**: Contains controllers and requests for handling HTTP interactions
- **Notifications**: Contains notification classes
- **Providers**: Contains service providers for the module
- **Repositories**: Contains repository classes for data access

## Usage

### Configuration

The module is automatically registered through its service provider `Modules\Notification\Providers\NotificationServiceProvider`.

### User Notification Preferences

Users can set their preferred notification delivery method (in-app or email) through the application interface.

### Managing Notifications via UI

Users can manage their notifications through the application interface:

- **View Notifications**: Users can view all their notifications in a list.
- **Mark as Read/Unread**: Users can mark notifications as read or unread.
- **Delete Notifications**: Users can delete individual notifications.

### Purging Old Notifications

To purge old notifications from the system, run:

```bash
php artisan notifications:purge [--age=60] [--include-unread]
```

Options:
- `--age=60`: Specify the age in days of notifications to purge (default: 60 days)
- `--include-unread`: Include unread notifications in the purge (by default, only read notifications are purged)

## Integration

The module integrates with Laravel's notification system and provides additional functionality for managing user notification preferences and purging old notifications.
