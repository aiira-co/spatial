# cQured webapi
This is a PHP 7 Web API to partner client-side applications like Angular, Ionic, etc.
*   Simple and Easy to use.
*   Extensible with composer
*   Use JWT the easy way.
*   CoreModel available in the box for Database queries.

## Getting Started

*   Download the the API from this repo, unzip it and place it at the
directory of your PHP Server. eg. For XAMPP, place it at the 'htdocs' folder.
You can rename the folder if you desire.

* Setting Up: Setting this framework up is very easy. Simply open the 'config.php' file; this file
contains the configuration for the framework.

```php
<?php

class AdConfig
{
	// General
    public $offline = 0;
    public $offline_message = 'This site is down for maintenance.<br />Please check back again soon.';
    public $display_offline_message = '1';
    public $offline_image = '';
    public $sitename = 'airCore';
    public $captcha = '0';
    public $list_limit = '20';
    public $access = '1';

    // Database Connection
    public $dbtype = 'mysqli';
    public $host = 'localhost';
    public $user = 'root';
    public $password = '';
    public $db = 'airDB';
    public $dbprefix = '';

    // Header & Cross Origin Setting
    public $allow_origin = "http://localhost:4200";
    public $allow_methods ='GET, POST, PUT, DELETE, OPTIONS';
    public $max_age='86400'; //cache for 1 day

    // Routing
    public $secret = 'Pi1gS3vrtWvNq3O0';
    public $routerPath="./app.router.php";
}

```

*   The General Section are for handling the name and the offline mode of the api
(similar to coreFramework's config file );
*   Database Connection Section: is for configuring the your database.
    *   $dbtype : means the database server type,
        *   For MySQL, MySQLi or MariaDB, use 'mysqli'
        *   For Oracle , use 'oracle'
        *   For MSSQL , use 'mssql'
    * The rest are self explanatory,
    *   $host: where the database is hosted
    *   $user: the username to access the database
    *   $password: the user password to access the database
    *   $db: the database name
    *   $dbprefix: the prefix for the tables in the database.

*   Headers & Cross Origin Settings: This is to set the client
    apps to allow, the REQUEST_METHODS to accepts and the
    expire of the connection.

*   Routing Section: this is to point the routing file for the
navigation of the api to get to the  controls.



## Controllers
Controllers represents the routes that are called when a request is made.
for example, if i get users from my client side app, the uri to the
api would probably be http://api.com/user.

```typescript
getUsers():Observable<IUsers>{
    return this.http.get('http://api.com/user')
    .map(response => response.json());
}
```

*   For the above example, the request is sent to the api via
http://api.com
*   The '/user' here is used in web-api for routing to the a controller
which will then return a response.
*  Routing of the url to a controller is set at the 'app.router.php' file (which can be changed at the config.php)

```php
<?php

$apiRoutes = [
    ['path'=>'user', 'controller'=>'user'],
    ['path'=>'client', 'controller'=>'person']
    ];


 $apiRouterModule = CORE::getInstance('Router');
 $apiRouterModule->setRouter($apiRoutes);

```

*   The 'path' represents the url path
*   'Controller' is the controller to point to when the path mathes the url.
    *   For  example, the 'person.controller.php' will be called if path is 'client; i.e. http://api.com/client

## Default

By default, we only have a values.controller in the controllers folder,
and in  the app.router.php file, we have mapped the path values to the controller.

*   To view this in action, run your php server and open your browser.
*   enter the api path and the router path:
    *   http://localhost/cqured/values

You should see
```json
 ["value1","value2"]

```

That value is returned from the 'httpGet()' method in the controller.

## Controller Structure

```php
<?php
use CoreModel as DB;

class ValuesController
{



    function __construct()
    {
    }


    // method called to handle a GET request

    function httpGet(int ...$id): ?array
    {

        return ['value1','value2'];
    }


    // method called to handle a POST request
    function httpPost(array $form)
    {
        $postId=null;

      // code here
        return ['success'=>true,'alert'=>'We have it at post','id'=>$postId];
    }


    // method called to handle a PUT request
    function httpPut(array $form, int $id)
    {


      // code here
        return ['success'=>true,'alert'=>'We have it at put'];
    }


    // method called to handle a DELETE request
    function httpDelete(int $id)
    {
      // code here
        return ['id'=>2];
    }
}

```


*   Each time a request is sent to a controller, the api checks if the request method is allowed in the api (see config.php; Headers & Cross Origin Setting).
*   Then it check the app.router.php  to map the url path to the controller.
*   When its found, the controller checks if it has a method to handle that Requested Method, else, an error is returned.


## CoreModel
You might find in the controller file a
```php
<?php

use CoreModel as DB;

```

This is a library written to easily query database.
* Let us consider a database table called 'user'.

```php
<?php

class interface IUser{
    private id;
    private name;
    private gender;
    private email;

}

class class User implements IUser{
    public id=0;
    public name='';
    public gender='';
    public email='';

}
```

* Remember the database settings are already made in the config.php file.
*   To query the table
**  These returns a row of objects

*   SELECT All Users :
```php
DB::table('user')->get();
```

*   SELECT All Users DISTINCT:
```php
DB::table('user')->distinct();
```


*   Count All Users :
 ```php
 DB::table('user')->count();
 ```

*   SELECT All Users with only id and name Fields:
```php
DB::table('user')->fields('id, name')
								 ->get();
```

*   SELECT All Users Limit to 10 :
 ```php
 DB::table('user')->limit(10)
								  ->get();
```


*   SELECT All Users Limit to 10 Offset 100:
 ```php
 DB::table('user')->limit(10)
								  ->offset(100);
```


*   SELECT First User in Users row :
 ```php
 DB::table('user')->first();
```

*   SELECT Last User in Users row :
 ```php
 DB::table('user')->last();
```

*   SELECT All Users Order by name DESCENDING :
 ```php
 DB::table('user')->orderBy('name')
                    ->get();
```

*   SELECT All Users Order by ASCENDING :
```php
DB::table('user')->orderBy('name',2)
                 ->get();
```

*   SELECT User with id == 3 :
```php
DB::table('user')->where('id',3)
              	 ->get();
```

*   SELECT User with id != 3 :
```php
DB::table('user')->where('id','!=',3)
                ->get();
```

*   SELECT User with id < 3 :
```php
DB::table('user')->where('id','<',3)
                ->get();
```
*   SELECT User with id > 3 :
```php
DB::table('user')->where('id','>',3)
                ->get();
```    

*   SELECT User with id <= 3 :
```php
DB::table('user')->where('id','<=',3)
                ->get();
```

*   SELECT User with name LIKE 'kel' :
```php
DB::table('user')->where('id','LIKE','%kel%')
                ->get();
```

**  These returns a single object
*   SELECT A Single User with id == 3 :
```php
DB::table('user')->where('id',3)
                ->single();
```

*   SELECT Users with id == 3 or name = 'kelvin' :
```php
DB::table('user')->where('id',3)
                ->orWhere('name','kelvin')
                ->get();
```
*   SELECT Users with id == 3 and name = 'kelvin' :
```php
DB::table('user')->where('id',3)
                ->andWhere('name','kelvin')
                ->get();
```    
**  These returns a true or false if its sucessfull or failed
*   INSERT New User:
```php
DB::table('user')->add(['name'=>'kelvin','email'=>'kelvin@air.com','gender'=>'male']);
```    
*   UPDATE User with id = 3 :
```php
DB::table('user')->where('id',3)
                ->update(['name'=>'kelvin','email'=>'kelvin@air.com','gender'=>'male']);
```   
*   DELETE User with id = 3 :
```php
DB::table('user')->where('id',3)
                ->del();
```
* JOIN queries
    *   SELECT Users and JOIN comments ON user.id == comment.userId
```php
DB::table('user u')
        ->join('comment c')
        ->on('u.id','c.userId')
        ->get();
```
*   The letter 'u' in the table method after the table name 'user' is the alias of the table.
*   There also is the rightJoin, leftJoin, innerJoin, fullJoin

* GROUP BY

* MULTI DATABASE            


## Authentication and Authorization.
You should see the 'vendor' folder at the root directory of the api. This is in relation to 'composer.json'.
Hence you can add other libraries and packages to the api for both security and manipulations of data.
