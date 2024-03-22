## Get Started

This guide will walk you through the steps needed to get this project up and running on your local machine.

### Prerequisites

Before you begin, ensure you have the following installed:

- Docker
- Docker Compose

### Building the Docker Environment

Build and start the containers:

```
docker-compose up -d --build
```

### Installing Dependencies

```
docker-compose exec app sh
composer install
```

### Database Setup

Set up the database:

```
bin/cake migrations migrate
```

```
bin/cake migrations Seed
```

### Accessing the Application

The application should now be accessible at http://localhost:34251

## How to check

### Authentication

1. Account
```
username:author@author.com
password:author
```
NOTE: Can only be used by authenticated users.

### Article Management
1. Create an Article (POST)
   URL:  http://localhost:34251/articles.json
   Method: POST
   Body:
```
{
    "title": "title_1",
    "body": "body_1"
}
```
NOTE: Can only be used by authenticated users.
2. Update an Article (PUT)
URL:  http://localhost:34251/articles/{id}.json
Method: PUT
Body:
```
{
    "title": "title_1",
    "body": "body_1"
}
```

NOTE:
- {id} is id of Article
- Can only be used by authenticated article writer users.

3. Retrieve All Articles (GET)
URL:  http://localhost:34251/articles.json
Method: GET

NOTE: Can only be used by all users.

4. Retrieve All Articles (GET)
URL:  http://localhost:34251/articles/{id}.json
Method: GET

NOTE: 
- {id} is id of Article
- Can only be used by all users.

### Like Feature

1. Like Feature.
URL:  http://localhost:34251/articles/{id}/like.json
Method: Post
NOTE: 
- {id} is id of Article