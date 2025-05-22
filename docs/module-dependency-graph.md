# Module Dependency Graph

This graph shows the dependencies between modules in the Laravel Starter Kit.

```mermaid
graph TD;
    foundationLayout["FoundationLayout"];
    notification["Notification"];
    workspace["Workspace"];
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

- Workspace

### Workspace

This module supports a workspace concept to form smaller tribes to collaborate in.

**Dependencies:**

- Notification

**Not used by other modules**

