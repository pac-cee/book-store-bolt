# System Architecture

## Overview

BookHaven is designed using a modular architecture that separates concerns between frontend, backend, and database layers. This document outlines the system's architecture, components, and their interactions.

## Architecture Diagram

```mermaid
graph TD
    A[Client Browser] -->|HTTP/HTTPS| B[Web Server]
    B -->|PHP| C[Application Layer]
    C -->|PDO| D[MySQL Database]
    C -->|File System| E[Asset Storage]
    F[Admin Dashboard] -->|PHP| C
```

## Components

### Frontend Layer
- HTML5 templates with PHP integration
- CSS3 with responsive design
- JavaScript for dynamic interactions
- AJAX for asynchronous operations

### Application Layer
- PHP controllers for request handling
- Business logic implementation
- Authentication and authorization
- Session management
- API endpoints

### Data Layer
- MySQL database
- PDO for database operations
- Prepared statements for security
- Transaction management

### Security Layer
- Password hashing
- CSRF protection
- XSS prevention
- Input validation
- Session security

## Data Flow

1. User Request Flow
```mermaid
sequenceDiagram
    participant User
    participant Frontend
    participant Backend
    participant Database
    
    User->>Frontend: Initiates Action
    Frontend->>Backend: HTTP Request
    Backend->>Database: Query Data
    Database-->>Backend: Return Results
    Backend-->>Frontend: Process & Format
    Frontend-->>User: Display Results
```

2. Order Processing Flow
```mermaid
sequenceDiagram
    participant User
    participant Cart
    participant Order
    participant Payment
    participant Database
    
    User->>Cart: Add Items
    Cart->>Order: Checkout
    Order->>Payment: Process Payment
    Payment->>Database: Store Transaction
    Database-->>User: Confirmation
```

## Security Measures

1. Authentication
   - Session-based authentication
   - Password hashing with bcrypt
   - Remember-me functionality
   - Password reset capability

2. Authorization
   - Role-based access control
   - Resource-level permissions
   - API authentication

3. Data Protection
   - HTTPS enforcement
   - SQL injection prevention
   - XSS protection
   - CSRF tokens

## Performance Optimization

1. Database
   - Indexed queries
   - Prepared statements
   - Connection pooling
   - Query optimization

2. Frontend
   - Asset minification
   - Image optimization
   - Lazy loading
   - Browser caching

3. Backend
   - Response caching
   - Query caching
   - Optimized algorithms
   - Resource pooling




   # System Architecture

## Overview

This document outlines the technical architecture and system design of BookHaven.

## System Components

```mermaid
graph TB
    Client[Web Browser]
    WebServer[PHP Web Server]
    Database[(MySQL Database)]
    FileSystem[File System]
    Cache[Cache Layer]
    
    Client -->|HTTP/HTTPS| WebServer
    WebServer -->|SQL| Database
    WebServer -->|Read/Write| FileSystem
    WebServer -->|Cache Operations| Cache
    Cache -->|Cache Miss| Database
```

## User Flow Diagram

```mermaid
stateDiagram-v2
    [*] --> BrowseCatalog
    BrowseCatalog --> ViewBook
    ViewBook --> AddToCart
    AddToCart --> ViewCart
    ViewCart --> Checkout
    Checkout --> Login: If not logged in
    Login --> EnterShipping
    EnterShipping --> EnterPayment
    EnterPayment --> PlaceOrder
    PlaceOrder --> OrderConfirmation
    OrderConfirmation --> [*]
```

## Component Interaction

```mermaid
sequenceDiagram
    participant U as User
    participant F as Frontend
    participant B as Backend
    participant D as Database
    
    U->>F: Browse Books
    F->>B: GET /api/books
    B->>D: Query Books
    D-->>B: Return Results
    B-->>F: JSON Response
    F-->>U: Display Books
```

## Deployment Architecture

```mermaid
graph TB
    subgraph Production
        LB[Load Balancer]
        WS1[Web Server 1]
        WS2[Web Server 2]
        DB[(Primary DB)]
        DBS[(Replica DB)]
        FS[File Storage]
        
        LB --> WS1
        LB --> WS2
        WS1 --> DB
        WS2 --> DB
        DB --> DBS
        WS1 --> FS
        WS2 --> FS
    end
```

## Cart System Flow

```mermaid
stateDiagram-v2
    [*] --> EmptyCart
    EmptyCart --> CartWithItems: Add Item
    CartWithItems --> CartWithItems: Update Quantity
    CartWithItems --> EmptyCart: Remove All Items
    CartWithItems --> Checkout: Proceed to Checkout
    Checkout --> OrderPlaced: Payment Success
    Checkout --> CartWithItems: Payment Failed
    OrderPlaced --> [*]
```

## Authentication Flow

```mermaid
sequenceDiagram
    participant U as User
    participant F as Frontend
    participant B as Backend
    participant D as Database
    
    U->>F: Enter Credentials
    F->>B: POST /api/login
    B->>D: Verify Credentials
    D-->>B: User Data
    B->>B: Generate Session
    B-->>F: Session Token
    F-->>U: Redirect to Dashboard
```

## Order Processing

```mermaid
graph TD
    A[Order Placed] -->|Validate| B{Stock Check}
    B -->|In Stock| C[Process Payment]
    B -->|Out of Stock| D[Notify User]
    C -->|Success| E[Create Order]
    C -->|Failure| F[Payment Failed]
    E --> G[Update Inventory]
    E --> H[Send Confirmation]
    H --> I[Order Complete]
```

## Admin Dashboard Flow

```mermaid
stateDiagram-v2
    [*] --> Dashboard
    Dashboard --> OrderManagement
    Dashboard --> BookManagement
    Dashboard --> UserManagement
    Dashboard --> Reports
    
    OrderManagement --> ViewOrder
    OrderManagement --> UpdateStatus
    
    BookManagement --> AddBook
    BookManagement --> EditBook
    BookManagement --> ManageInventory
    
    Reports --> SalesReport
    Reports --> InventoryReport
    Reports --> UserReport
```