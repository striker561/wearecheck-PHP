## Overview

This project implements a custom REST API using core PHP that:

- Imports data from JSONPlaceholder API endpoints (users, posts, comments, todos, albums, photos)
- Establishes proper relationships between data entities
- Stores data in a relational database with ULID primary keys
- Provides REST endpoints to interact with the stored data
- Implements pagination and filtering

## Data Relationships

The API maintains these relationships between entities:

1. User → Todos
2. User → Posts → Comments
3. User → Albums → Photos

## Technical Specifications

- PHP 8.2+
- MySQL/MariaDB database
- Custom routing system
- Environment-based configuration
- ULID generation for unique identifiers

## Installation

1. Clone the repository:

   ```
   git clone https://github.com/striker561/wearecheck-PHP.git
   ```

2. Install dependencies:

   ```
   composer install
   ```

3. Configure environment:

   - Copy `.env.example` to `.env`
   - Update the values to match your environment

4. Set up the database:
   - Import the schema from the `database` folder to your database
   - Run the initialization endpoint to populate data

## Configuration

The `.env` file controls the application behavior:

```
# ENVIRONMENT
PRODUCTION=0  # Set to 1 for production mode (error logging)

# DATABASE
DATABASE_HOST=
DATABASE_NAME=
DATABASE_USER=
DATABASE_PASSWORD=

# OTHER
BASE_PATH='/your/installation/path'  # Set to match your server configuration
```

### Important Notes:

- **PRODUCTION**: When set to 1, errors are logged to `src/logs/app.log` instead of being displayed
- **BASE_PATH**: Must be set correctly for the routing system to work properly, you can ignore if your app is running on the root directory

## API Usage

Initialize the database with JSONPlaceholder data by accessing:

```
GET /initialize
```

Then use the following endpoints:

```
GET /users
GET /todos
GET /posts
GET /comments
GET /albums
GET /photos
```

A complete Postman collection is available in the `api` folder with examples of all endpoints and supported parameters.

## Features

- **Data Initialization**: One-time import of all JSONPlaceholder data
- **Pagination**: Automatic paging of large datasets
- **Filtering**: Query parameters for filtering results
- **Error Handling**: Proper HTTP status codes and error messages

## Design Decisions

- **No Framework**: Built with core PHP to demonstrate understanding of fundamental concepts
- **Custom Router**: Implements a simple but effective routing system
- **ULID Keys**: Uses ULIDs instead of sequential IDs for better scalability
- **Bulk Imports**: Optimized data importing to handle large datasets efficiently
- **Memory Management**: Careful handling of large datasets with cleanup of lookup maps

## Testing

Test the API using the included Postman collection in the `api` folder. This collection contains requests for all endpoints with appropriate parameters and examples. Ensure that the endpoint in post matches the one in your environment.
