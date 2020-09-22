### AirtimeFlip API

#### Introduction
This API can be used for Electricity Bill and Cable TV Subscription.
Follow this [link](https://documenter.getpostman.com/view/5683762/SWE6bybW) to read the API documentation <br/>
This API is also available for testing through this link [http://localhost:8080/ v1](http://localhost:8080/v1 )


#### Quick Start
To load in all php dependencies

````
$ cd baxi-test
$ composer install
````

Copy the .env.example file to .env and update the following accordingly
 
````
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=XXX
DB_USERNAME=XXX
DB_PASSWORD=XXX
````

---
Replace the Xs with your actual value in the .env file.

Setup queue connection in .env file as follow

````
QUEUE_CONNECTION=database
````

Still on the .env file, Update your Baxi API Credentials
````
BAXI_ENV=XXXXXXXX
BAXI_API_URL=XXXXXXXX
BAXI_USERNAME=XXXXXXXX
BAXI_USER_SECRET=XXXXXXXX
BAXI_API_KEY=XXXXXXXX
````

Configure Email Credentials in .env file
````
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=XXXXXXXX
MAIL_PASSWORD=XXXXXXXX
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=XXXXXXXX
MAIL_FROM_NAME="${APP_NAME}"
````

Proceed to generating you unique application key with the following command

````
$ php artisan key:generate
````

When all this has been done, you then proceed to running your migration and database seed command
````
$ php artisan migrate --seed
````

After running your migration and seed, proceed to installing passport with the following command

````
$ php artisan passport:install
````

#### Synchronizing with front-end

Update the app frontend url in the ````.env```` file
````
MAIN_APP_URL=https:/airtimeflip.com
````

#### Running Queue process

This API uses laravel queues to send mails. Supervisor can be installed to manage the queue worker.

- **Running queue process using supervisor** : 

###### Step 1: Install supervisor
````
sudo apt-get install supervisor

````
###### Step 2: Create ````airtimeflip-queue```` .conf file
````
sudo nano /etc/supervisor/conf.d/airtimeflip-worker.conf

````
>Paste the below configuration and save file
````
[program:airtimeflip-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/<project_path>/artisan queue:work
autostart=true
autorestart=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=<project_path>/storage/logs/airtimeflip-worker.log
````
>**NOTE:** Replace ````<project_path>```` with your own project path.


- **Running queue process using artisan command** : 
````
php artisan queue:work
````