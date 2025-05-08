# Data Flow Diagrams

## Context Level (Level 0)

```mermaid
graph TD
    Customer((Customer))
    Admin((Admin))
    System[BookHaven System]
    
    Customer -->|Browse/Order Books| System
    Admin -->|Manage Inventory/Orders| System
    System -->|Order Confirmation| Customer
    System -->|Reports/Analytics| Admin
```

## Level 1: Main System Processes

```mermaid
graph TD
    subgraph Customer Processes
        BP[Book Processing]
        OP[Order Processing]
        UP[User Processing]
    end
    
    subgraph Data Stores
        Books[(Books DB)]
        Orders[(Orders DB)]
        Users[(Users DB)]
    end
    
    Customer((Customer)) -->|Search/Browse| BP
    BP -->|Book Data| Customer
    Customer -->|Place Order| OP
    OP -->|Order Status| Customer
    Customer -->|Register/Login| UP
    UP -->|User Data| Customer
    
    BP ---|Query/Update| Books
    OP ---|Create/Read| Orders
    UP ---|CRUD| Users
```

## Level 2: Book Management Flow

```mermaid
graph TD
    Search[Search Books]
    Filter[Filter Books]
    View[View Details]
    Cart[Shopping Cart]
    Books[(Books DB)]
    
    Search -->|Query| Books
    Filter -->|Filter Criteria| Books
    Books -->|Book Data| View
    View -->|Add Book| Cart
```

## Level 3: Order Processing Flow

```mermaid
graph TD
    Cart[Shopping Cart]
    Checkout[Checkout Process]
    Payment[Payment Processing]
    Inventory[Inventory Management]
    Orders[(Orders DB)]
    Books[(Books DB)]
    
    Cart -->|Items| Checkout
    Checkout -->|Validate| Inventory
    Inventory -->|Check Stock| Books
    Checkout -->|Process| Payment
    Payment -->|Success| Orders
    Orders -->|Update| Inventory
```

## Level 4: User Management Flow

```mermaid
graph TD
    Register[Registration]
    Login[Login]
    Profile[Profile Management]
    Orders[Order History]
    Users[(Users DB)]
    OrderDB[(Orders DB)]
    
    Register -->|Create| Users
    Login -->|Verify| Users
    Profile -->|Update| Users
    Users -->|User Data| Orders
    Orders -->|Fetch History| OrderDB
```

## Level 5: Admin Flow

```mermaid
graph TD
    Inventory[Inventory Management]
    Orders[Order Management]
    Reports[Generate Reports]
    Users[User Management]
    
    Books[(Books DB)]
    OrderDB[(Orders DB)]
    UserDB[(Users DB)]
    
    Inventory -->|CRUD Operations| Books
    Orders -->|Update Status| OrderDB
    Reports -->|Analytics| Books
    Reports -->|Sales Data| OrderDB
    Users -->|Manage| UserDB
```

## Data Store Details

### Books Database
- Book ID
- Title
- Author
- Price
- Stock
- Category
- Description
- Cover Image

### Orders Database
- Order ID
- User ID
- Items
- Total Amount
- Status
- Payment Info
- Shipping Details

### Users Database
- User ID
- Name
- Email
- Password Hash
- Role
- Address
- Order History

## Data Flow Rules

1. **Authentication**
   - All user operations require valid session
   - Admin operations require admin privileges
   - Session timeout after inactivity

2. **Data Validation**
   - Input sanitization at all entry points
   - Business rule validation before storage
   - Referential integrity in database

3. **Error Handling**
   - Graceful error recovery
   - User-friendly error messages
   - Error logging for debugging

4. **Performance**
   - Caching for frequent queries
   - Pagination for large datasets
   - Optimized database queries