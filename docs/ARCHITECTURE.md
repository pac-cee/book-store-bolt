# System Architecture

## Overview

BookHaven is a modern online bookstore built with a focus on user experience, performance, and scalability. This document outlines the technical architecture and design decisions.

## Architecture Overview

```mermaid
graph TB
    subgraph Frontend
        UI[User Interface]
        Components[React Components]
        State[State Management]
    end
    
    subgraph Backend
        API[API Layer]
        Services[Business Logic]
        Auth[Authentication]
    end
    
    subgraph Data
        MySQL[(MySQL Database)]
        Cache[Redis Cache]
        Search[Search Engine]
    end
    
    UI --> Components
    Components --> State
    State --> API
    API --> Services
    Services --> Auth
    Services --> MySQL
    Services --> Cache
    Services --> Search
```

## Component Architecture

```mermaid
graph TD
    App[App Container]
    Auth[Auth Module]
    Catalog[Catalog Module]
    Cart[Cart Module]
    Orders[Orders Module]
    Admin[Admin Module]
    
    App --> Auth
    App --> Catalog
    App --> Cart
    App --> Orders
    App --> Admin
    
    subgraph Auth
        Login[Login]
        Register[Register]
        Profile[Profile]
    end
    
    subgraph Catalog
        Browse[Browse]
        Search[Search]
        Filter[Filter]
        Details[Details]
    end
```

## Data Flow

```mermaid
sequenceDiagram
    participant User
    participant UI
    participant API
    participant Cache
    participant DB
    
    User->>UI: Interaction
    UI->>Cache: Check Cache
    alt Cache Hit
        Cache-->>UI: Return Data
    else Cache Miss
        UI->>API: Request
        API->>DB: Query
        DB-->>API: Data
        API-->>Cache: Update Cache
        API-->>UI: Response
    end
    UI-->>User: Update View
```

## Security Architecture

```mermaid
graph LR
    subgraph Security
        Auth[Authentication]
        JWT[JWT Tokens]
        RBAC[Role-Based Access]
        Encrypt[Encryption]
    end
    
    Auth --> JWT
    JWT --> RBAC
    RBAC --> Resources[Protected Resources]
    Encrypt --> Data[Sensitive Data]
```

## Deployment Architecture

```mermaid
graph TB
    subgraph Production
        LB[Load Balancer]
        Web1[Web Server 1]
        Web2[Web Server 2]
        Cache[(Redis Cache)]
        DB[(Primary DB)]
        DBReplica[(DB Replica)]
    end
    
    Client-->LB
    LB-->Web1
    LB-->Web2
    Web1-->Cache
    Web2-->Cache
    Web1-->DB
    Web2-->DB
    DB-->DBReplica
```