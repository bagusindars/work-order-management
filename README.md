# Application Setup Guide

## Prerequisites

### General Requirements

- PHP Version 8.2 or higher

- Laravel Version: 11

- Node.js: 18 or higher

- Composer: 2.x
 
- Postgress 16 or higher

### Docker Requirements (Opsional)

- Docker: 27.x or higher

- Docker Compose: 2.x or higher


## 1. Running without Docker
- Clone the repository: <br>
<code>git clone git@github.com:bagusindars/work-order-management.git</code>
- Install PHP and Composer:
Ensure PHP (version 8.3 or higher) and Composer are installed on your system.
- Install Node js:
Ensure Node.js (version 18 or higher) is installed on your system.
- In the root project, Copy **.env** file <br>
<code>cp .env.example .env</code>
- Create your database (ex: work-order-management)
- Open .env file and setup the database<br>
<code> DB_CONNECTION=pgsql
    DB_HOST=localhost
    DB_PORT=5432
    DB_DATABASE=work-order-management # (your db name)
    DB_USERNAME=postgres # (your postgress username)
    DB_PASSWORD=password # (your postgress password)
</code>
- Install dependencies:
<code>
composer install
npm install
</code>
- Set application key: <br>
<code>php artisan key:generate</code>
- Run migrations & Seeder: <br>
<code>php artisan migrate --seed</code>
- Build the asset: <br>
<code>npm run build</code>
- Start the development server: <br>
<code>php artisan serve</code>
- Access the application:
The application will be available at http://localhost:8000



## 2. Running with docker
- Clone the repository: <br>
<code>git clone git@github.com:bagusindars/work-order-management.git</code>
- In the root project, Copy **.env** file <br>
<code>cp .env.example .env</code>
- Open .env file and setup the database based on docker compose<br>
<code> DB_CONNECTION=pgsql
    DB_HOST=db # docker compose container
    DB_PORT=5432
    DB_DATABASE=work-order-management # docker compose db name
    DB_USERNAME=postgres # docker compose username
    DB_PASSWORD=password # docker compose password name
</code>
- Build the container<br>
<code>docker-compose build</code>
- Start the container<br>
<code>docker-compose up -d</code>
- Access the application container<br>
<code>docker exec -it work-order-management-app bash</code>
- Install dependencies:
<code>
composer install
npm install
</code>
- Set application key: <br>
<code>php artisan key:generate</code>
- Run migrations & Seeder: <br>
<code>php artisan migrate --seed</code>
- Build the asset: <br>
<code>npm run build</code>
- Start the development server: <br>
<code>php artisan serve</code>
- Access the application:
The application will be available at http://localhost:8081
### You don't have to access application container first (bash)
you can run the artisan command with:<br>
<code>docker exec -it work-order-management-app {your command}</code> <br>
ex <code>docker exec -it work-order-management-app php artisan serve</code>

## Access
to enter the dashboard we are already running the seeder. Based from the user seeder the defaul user information are
#### Production Manager
email : pm@gmail.com <br>
password : 12345678
#### Operator 1
email : operator1@gmail.com <br>
password : 12345678
#### Operator 2
email : operator2@gmail.com <br>
password : 12345678

