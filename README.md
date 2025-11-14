## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/ph-hitachi/laravel-api-jwt-starter.git
    ```

2. Navigate to the project directory:
    ```bash
    cd laravel-api-jwt-starter
    ```
3. Install the required dependencies using Composer::

   ```bash
   composer install
    ```
4. Set up your environment variables by copying the `.env.example` file:
   ```bash
   cp .env.example .env
    ```

5. Generate a new application key:
    ```bash
    php artisan key:generate
    ```
6. Configure your database connection in the `.env` file.
7. Run the migrations:
    ```bash
    php artisan migrate
    ```
8. Generate a JWT secret key:

   ```bash
   php artisan jwt:secret
    ```
9. Check connect DB:

   ```bash
    mysql -h 192.168.1.10 -u root -p
    ```

    
