# Spatial (CQured) MULTI-WebAPI Framework - Clean Architecture

- To Access the appAPI's ValuesController, go to localhost/appApi/values -> this just returns the array in the controller's httpGet() method.
- The real magic is to enter localhost/cqured/appApi/test to access the the controller's httpGet() method.
  This now calls the GetPersonQuery from the your 'app' application in the /src/core/applications/logics/app/person/queries folder.
    - The Query Class automatically calls its Handler class (done with the Spatial\Mediatr class).

Clean/Onion Architecture for multi-api framework

- Spatial\Route for presentation(Api)
- Spatial\Mediator as middleware between \Presentation\\ and \Core\\ (PSR)
- Doctrine for DB
- GuzzlePSR7 for http
- lcobucci/jwt for Auth

## Server Requirements
The Spatial framework has a few system requirements. All of these requirements are satisfied by the Spatial Homestead Docker, so it's highly recommended that you use Homestead as your local Spatial development environment.

However, if you are not using Homestead, you will need to make sure your server meets the following requirements:

- PHP >= 7.4.2
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Installing Spatial
Spatial utilizes Composer to manage its dependencies. So, before using Spatial, make sure you have Composer installed on your machine.
Install Spatial by issuing the Composer create-project command in your terminal:
```
composer create-project spatial/spatial webapi
```

# Configuration
### Public Directory
After installing Spatial, you should configure your web server's document / web root to be the public directory. The index.php in this directory serves as the front controller for all HTTP requests entering your API.

### Configuration Files
All of the configuration files for the Spatial framework are stored in the config directory. Each option is documented, so feel free to look through the files and get familiar with the options available to you.

### Directory Permissions
After installing Spatial, you may need to configure some permissions. Directories within the assets, common/domain and the var/cache directories should be writable by your web server or Spatial will not run. If you are using the Homestead virtual machine, these permissions should already be set.

### Use Secret for Sensitive Information¶
When your application has sensitive configuration - like an API key - you should store those securely via secrets(config/secrets/).
Assuming you're coding locally in the dev environment, this will create:
```
config/secrets/dev/dev.encrypt.public.php
```
Used to encrypt/add secrets to the vault. Can be safely committed.
```
config/secrets/dev/dev.decrypt.private.php
```
Used to decrypt/read secrets from the vault. The dev decryption key can be committed (assuming no highly-sensitive secrets are stored in the dev vault) but the prod decryption key should never be committed.

### Additional Configuration
Spatial needs almost no other configuration out of the box. You are free to get started developing! However, you may wish to review the config/services.yaml file and its documentation. It contains several options such as timezone and locale that you may wish to change according to your application.

You may also want to configure a few additional components of Spatial, such as:

- Cache
- Database
- Session

# Web Server Configuration

### Directory Configuration
Spatial should always be served out of the root of the "web directory" configured for your web server. You should not attempt to serve a Spatial application out of a subdirectory of the "web directory". Attempting to do so could expose sensitive files present within your application.


## Pretty URLs
### Apache
Spatial includes a public/.htaccess file that is used to provide URLs without the index.php front controller in the path. Before serving Spatial with Apache, be sure to enable the mod_rewrite module so the .htaccess file will be honored by the server.

If the .htaccess file that ships with Spatial does not work with your Apache installation, try this alternative:
```apacheconfig
Options +FollowSymLinks -Indexes
RewriteEngine On

RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```
### Nginx
If you are using Nginx, the following directive in your site configuration will direct all requests to the index.php front controller:
```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

# Use the Default Directory Structure¶
Unless your project follows a development practice that imposes a certain directory structure, follow the default Spatial directory structure. It's flat, self-explanatory and not coupled to Spatial:
```$xslt
your_project/
├─ assets/
├─ bin/
│  └─ console
├─ config/
│  ├─ packages/
│  └─ services.yaml
└─ public/
│  ├─ build/
│  └─ index.php
├─ src/
│  ├─ common/
│  ├─ core/
│  ├─ presentaion/
│  ├─ infrastructure/
├─ templates/
├─ tests/
├─ var/
│  ├─ cache/
│  └─ log/
└─ vendor/
```

### The Src Directory
The app directory contains the core code of your application. We'll explore this directory in more detail soon; however, almost all of the classes in your application will be in this directory.

### The Config Directory
The config directory, as the name implies, contains all of your application's configuration files. It's a great idea to read through all of these files and familiarize yourself with all of the options available to you.

### The Database Directory
The database directory contains your database migrations, model factories, and seeds. If you wish, you may also use this directory to hold an SQLite database.

### The Public Directory
The public directory contains the index.php file, which is the entry point for all requests entering your application and configures autoloading. This directory also houses your assets such as images, JavaScript, and CSS.

### The Tests Directory
The tests directory contains your automated tests. An example PHPUnit test is provided out of the box. Each test class should be suffixed with the word Test. You may run your tests using the phpunit or php vendor/bin/phpunit commands.

### The Vendor Directory
The vendor directory contains your Composer dependencies.

# The Src Directory
### The Presentation Directory
The Presentation layer/folder holds your individual Api(s) for each app being created.
presentation api is registered at the config.php file for access.
### The Common Directory
The Common layer/folder holds your 
- constants, 
- general functions, 
- exceptions, 
- libraries etc. 
which will be used by the entire workspace: therefore these are independent of any app or api.
### The Core Directory
The Core layer/folder holds your Application (logics, interface, traits) and their Domain(Entity)

- The Application folder collects your logics, interfaces and models
- The Domain folder collects your entities generated with Doctrine ORM
### The Infrastructure Directory
The Infrastructure layer/folder holds services and database connections.
Hences, services like, SMS, Generating Tokens, Database Connections Goes here.
