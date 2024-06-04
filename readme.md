# Tasks API Robot
This is an application written in PHP's Laravel to process simulate requests to the Tasks API.

## About
This application runs on Docker. It uses a docker image of PHP 8.1 paired with Apache to run a Laravel 9 application and also a MySQL container.

This application also uses Composer to handle dependencies.

## Installation
Once you have Docker running on your environment, it's time to set up this application. For this, start by cloning this repository on a folder of your choosing, and then move into the newly created folder and performe the following instructions.

1. Set up the container with
```
docker-compose build
```
Once it has finished building the container, run
```
docker-compose up -d
```
To get it up and running. Both commands must be executed on the project's root folder

2. Then `cd` into the `app` folder and create a file copy named `.env` from the `.env.local` file by running the following:
```
cp .env.example .env
cp .env.testing.example .env.testing
```
Both files are pre-configured for local and testing enviroment

3. Now you'll need to install all the project's dependencies, execute the following on the project root folder:
```
docker-compose exec web composer install
```

4. Next, you need to run migrations to create the application tables. Execute the following on the project root folder:
```
docker-compose exec web php artisan migrate
```

5. And you're done! You may need to run a ```docker-compose exec web chmod -R 777 storage/``` command for enabling the app to write on log files.

## Usage
### By direct command
There's two ways to use this application to generate requests. One way is using the command:
```
docker-compose exec tar_web php artisan app:api-command {action}
```
It will create a single random request. You can also send this command with an argument to issue specific requests.

Use `create_task` to create a new task, `update_task` to update a task and `close_task` to close a task

### By scheduling
By running the following command
```
docker-compose exec tar_web php artisan schedule:work
```
you will set up Laravel to run a random request every 30 seconds.

### Logs
On the matter of failures or any other problems, check the log files under `app\storage\logs\`. The `requests.log` logs all of the requests steps and procedures.
