# Database Entity Relationship Diagram

This document provides a visual representation of the database schema for the Laravel Starter Kit project.

## Entity Relationship Diagram

```mermaid
erDiagram
    users {
        id bigint PK
        name string
        email string UK
        email_verified_at timestamp NULL
        password string
        remember_token string NULL
        locale string "default en"
        preferred_notification_channels longtext NULL
        workspace_id bigint FK NULL "current active workspace"
        created_at timestamp
        updated_at timestamp
    }

    password_reset_tokens {
        email string PK
        token string
        created_at timestamp NULL
    }

    sessions {
        id string PK
        user_id bigint FK NULL "index"
        ip_address string NULL
        user_agent text NULL
        payload longtext
        last_activity integer "index"
    }

    cache {
        key string PK
        value mediumtext
        expiration integer
    }

    cache_locks {
        key string PK
        owner string
        expiration integer
    }

    jobs {
        id bigint PK
        queue string "index"
        payload longtext
        attempts tinyint
        reserved_at integer NULL
        available_at integer
        created_at integer
    }

    job_batches {
        id string PK
        name string
        total_jobs integer
        pending_jobs integer
        failed_jobs integer
        failed_job_ids longtext
        options mediumtext NULL
        cancelled_at integer NULL
        created_at integer
        finished_at integer NULL
    }

    failed_jobs {
        id bigint PK
        uuid string UK
        connection text
        queue text
        payload longtext
        exception longtext
        failed_at timestamp "default current timestamp"
    }

    notifications {
        id uuid PK
        type string
        notifiable_type string "polymorphic"
        notifiable_id bigint "polymorphic"
        data text
        read_at timestamp NULL
        created_at timestamp
        updated_at timestamp
    }

    workspaces {
        id bigint PK
        user_id bigint FK "index, owner of the workspace"
        name string
        created_at timestamp
        updated_at timestamp
    }

    workspace_member_invitations {
        id bigint PK
        workspace_id bigint FK "cascade on delete"
        email string
        role string NULL "default null"
        created_at timestamp
        updated_at timestamp
    }

    workspace_members {
        id bigint PK
        workspace_id bigint FK
        user_id bigint FK
        role string NULL "default null"
        created_at timestamp
        updated_at timestamp
    }

    users ||--o{ sessions : "has many"
    users ||--o{ notifications : "has many"
    users ||--o{ workspaces : "owns many"
    users ||--o{ workspace_members : "belongs to many workspaces"
    users }|--o| workspaces : "has current active workspace"

    workspaces ||--o{ workspace_members : "has many members"
    workspaces ||--o{ workspace_member_invitations : "has many invitations"
```

## Table Descriptions

### Core Tables
- **users**: Stores user account information
- **password_reset_tokens**: Manages password reset functionality
- **sessions**: Tracks user sessions

### Cache and Queue Tables
- **cache**: Stores cached data
- **cache_locks**: Manages cache locks
- **jobs**: Stores queued jobs
- **job_batches**: Manages batches of jobs
- **failed_jobs**: Tracks failed jobs

### Notification Tables
- **notifications**: Stores user notifications

### Workspace Tables
- **workspaces**: Stores workspace information
- **workspace_members**: Manages workspace membership
- **workspace_member_invitations**: Manages invitations to workspaces

## Relationships

- A user can own many workspaces
- A user can belong to many workspaces through workspace_members
- A user can have a current active workspace
- A workspace can have many members through workspace_members
- A workspace can have many invitations through workspace_member_invitations
- A user can have many sessions
- A user can have many notifications
