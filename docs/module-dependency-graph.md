# Module Dependency Graph

This graph shows the dependencies between modules in the Laravel Starter Kit.

```mermaid
graph TD;
    foundationLayout["FoundationLayout"];
    notification["Notification"];
    passkey["Passkey"];
    todo["Todo"];
    workspace["Workspace"];
    todo --> workspace;
    todo --> notification;
    workspace --> notification;
```

## Module Details

### FoundationLayout

The foundation layout domain handles configuration stuff regarding the used layout variant.

**No dependencies**

**Not used by other modules**

### Notification

Notification module to improve the user notification system.

**No dependencies**

**Used by:**

- Todo
- Workspace

### Passkey

This module supports the passkey login option.

**No dependencies**

**Not used by other modules**

### Todo

This module provides todo functionality for workspaces.

**Dependencies:**

- Workspace
- Notification

**Not used by other modules**

### Workspace

This module supports a workspace concept to form smaller tribes to collaborate in.

**Dependencies:**

- Notification

**Used by:**

- Todo

