# Workspace Module

## Purpose

The Workspace module supports a workspace concept to form smaller teams or projects for collaboration. It provides functionality for creating and managing workspaces, inviting users to workspaces, and managing workspace memberships.

## Components

- **Actions**: Contains action classes for business logic
- **Console Commands**
  - `workspace:purge-orphaned-invitations`: A command to purge old workspace invitations
- **Contracts**: Contains interfaces defining the module's functionality
- **DataTransferObjects**: Contains DTOs for transferring data between components
- **Events**: Contains event classes for workspace-related events
- **Http**: Contains controllers and requests for handling HTTP interactions
- **Listeners**: Contains event listeners for workspace-related events
- **Mail**: Contains mail classes for sending workspace-related emails
- **Models**: Contains model classes for workspace-related data
- **Notifications**: Contains notification classes for workspace-related notifications
- **Policies**: Contains authorization policies for workspace-related actions
- **Providers**: Contains service providers for the module
- **Repositories**: Contains repository classes for data access
- **Rules**: Contains validation rules for workspace-related data

## Usage

### Configuration

The module is automatically registered through its service provider `Modules\Workspace\Providers\WorkspaceServiceProvider`.

### Creating Workspaces

Users can create workspaces through the application interface. A workspace serves as a context for collaboration, similar to a team or project.

### Inviting Users

Workspace owners can invite other users to join their workspace. Invitations are sent to users, who can accept or decline them.

### Managing Memberships

Workspace owners can manage the memberships of users in their workspace, including removing users from the workspace.

### Purging Orphaned Invitations

To purge old workspace invitations that have not been accepted, run:

```bash
php artisan workspace:purge-orphaned-invitations [--age=60]
```

Options:
- `--age=60`: Specify the age in days of invitations to purge (default: 60 days)

## Integration

The module integrates with the Laravel application to provide workspace functionality. It includes models, controllers, and views for managing workspaces, as well as events and listeners for handling workspace-related actions.
