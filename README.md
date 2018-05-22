# cQured webapi
This is a PHP 7 Web API to partner client-side applications like Angular, Ionic, etc.
![alt text](https://raw.githubusercontent.com/air-Design/airUI-Design-and-Media-.Tutorial-Example/media/apis.JPG)
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

## CONTROLLERS FOLDER
The 'controllers' folder contains all the controllers for the API. These controllers represent the individual uri(s) for data of the API.

Controllers are created in the controllers folder in as a singlw file.

## Creating a Controller
* Open the controllers folder and create a new php file. The file name should match the name of the controller you want to create. (Let us consider creating a controller for users.) Hence the controller file should be named users.controller.php.

* The user.controller.php file here handles all the php logics and variable which are made available for the client app as JSON. It could be considered as the controller of the MVC framework.

This is how  the users.controller.php will look like.
* The class name must match the name of the component.

```php
<?php

class UsersController
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

## Routing
Though the users controller is created, we have not created any route to get to that controller. To do so, we open the app.router.php file at the root directory of the framework.

* The app.router.php file registers a url to a controller, so that whenever the url matches the one registered, the binding controller is rendered.

* Create a variable called $users. The variable must be an array with members
  * path: the url to bind the component to. In this case, input 'users' as the value for this array member.
  * controller: this should correspond to the controller name in the controllers folder. In this case, the controller name is 'users'

  * The $users variable should look like:
    * $users = ['path'=>'users', 'controller'=>'users'];

* The above variable is not yet registered as a route. To register the variable, add it as a member to the '$appRoutes' array . Like so:
  * $appRoutes=[..., $users];

```PHP
 $users = [
            'path'=>'users', // http://127.0.0.1/users the router looks if the url matches the path, hence users. i.e http://api.com/{{path}}
            'controller'=>'users' // Controller to go when the path matches the url given    
            ];

$appRouter = [
  ['path'=>'values', 'controller'=>'values'],
 $users

];

$appRouterModule = CORE::getInstance('Router'); //creates an instance of the router class
$appRouterModule->setRouter($appRouter); //registers the routers

```
* Now the users controller is available to the router to view in the browser. Enter the server name to the App and add the path of the controller ('users'). I.e
  * http://localhost/api/users.
  * This should display the users controller data as JSON from the httpGet() method.

### AUTHGUARD
* Authentication and Authorization is key in Application development and cQured Web API already has this feature implemented for you.
* 'authguard' is also a member of the routes and takes arrays of models name as strings which are used to authorize clients to access the controller.
* example:

```PHP

$appRouter = [
  //Values Controller does not contain any Authorization
  ['path'=>'values', 'controller'=>'values'],

  //Users Controller contains Authorization
  [
  'path'=>'users',
  'controller'=>'users',
  'authguard'=>['authenticate'] // authguard checks a method 'canActivate():bool' in the model authenticate.model
  ];

];

$appRouterModule = CORE::getInstance('Router'); //creates an instance of the router class
$appRouterModule->setRouter($appRouter); //registers the routers

```
* The authguard member checks the method 'canActivate():bool' in the model authenticate.model which expects a boolean return.
  - If the canActivate method returns true, then the controller loads
  - if false, controller does not loads and the developer has the freedom to redirect the user to a differenct controller or show a 404 error.
* Example of the authenticate model is as follows:


```php

<?php

class AuthenticateModel{

  // This method is used by the routing class to allow or disabllow a route to the component
  function canActivate(string $url):bool{
    if (CoreSession::IsLoggedIn()) {
      return true;
    }else{
        Core::redirect(BaseUrl.'account/login',$url);
        return false;
    }
  }


}

```

* The $url parameter of the method is automatically passed in by cQured API: That is, the path you are trying to access.

* CoreSession::IsLoggedIn() is a static method in cQured used to check if a users is LoggedIn.
* This method specifically checks if the $_SESSION['id'] isset.


## MODELS FOLDER
This folder contains model files for database queries.
For legibility and separation of concerns, use the models for database related scripts and the controller file for logics.

* Every model must contain a [name].model.php (where [name] represent the name of the model or controller the model is related to).

* Example of user.model.php.

```php
<?php

use CoreModel as DB;

class UserModel
{

    private $table='users';

    function getUsers()
    {
        return DB::table($this->table)
                    ->get();
    }

    function getUser(int $id)
    {
        return DB::table($this->table)
                    ->where('id', $id)
                    ->get();
    }
}

```


* To get to an instance of the model in the component,
  * Create a private varible in the component called '$dataModel'
  * In the 'constructor()' method, get the model with 'CORE::getModel('user');

```php

class UsersComponent
{

    public $title="Users component works!";
    private $dataModel;

    function constructor()
    {
      $this->dataModel = CORE::getModel('user');
    }
}


```

* CORE:: makes reference the coreFrameworks 'core class' which contains static methods like
  * getModel('modelName') --> for instantiating a model
  * getInstance('className') --> for instantiating a core class 'params' and components.
  * component('componentName') --> for rendering a component within a component.

In  this case, ``CORE::getModel('user')`` looks into the models folder for the model file user.model.php, then it now instantiate it with 'UserModel'.
* It takes the 'model name' paramenter, and also a 'path' parameter to point to a custom folder the model exists.


* Now that we have the model in the component, we can easily get the data of all users or a single user from the model in the component.

```php

class UsersComponent
{

    public $title="Users component works!";
    private $dataModel;

    //create a variable to hold data
    public $data;

    function constructor()
    {
      // instantiate the model
      $this->dataModel = CORE::getModel('user');

      if(isset($_GET['id'])){
        $this->getSingleData($_GET['id']);
      }else{

        // get data
        $this->getData();
      }
    }

    function getData()
    {
      $this->data = $this->dataModel->getUsers();

    }

    function getSingleUser($id)
    {
      $this->data = $this->dataModel->getUser($id);

    }

}


```


* Rather than using the '$_GET' global variable, coreFramework has a class called params, which stores any POST or GET in it.
* A more pleasing way would be to use the params class.
  * Create private variable called $params, then instantiate it at the 'onInit()' method
  with CORE::getInstance('params');


```php

class UsersController
{

    public $title="Users component works!";
    private $dataModel;
    private $params;
    //create a variable to hold data
    public $data;

    function httpGet(): array
    {
      // instantiate the model
      $this->dataModel = CORE::getModel('user');

      // instantiate params
      $this->params = CORE::getInstance('params');

      if(isset($this->params->id)){

        return $this->getSingleData($this->params->id);

      }else{

        // get data
        return $this->getData();
      }
    }

    function getData()
    {
      $result = $this->dataModel->getUsers();
      return ['data'=>$result];
    }

    function getSingleUser($id)
    {
      $result = $this->dataModel->getUser($id);;
      return ['data'=>$result];

    }

}


```

* We expect '$data' to be a row of objects and will probably have a 'name' field, hence...

* In the model or controller file, you realise that we make use of the 'CoreModel' namespace with an alias 'DB',
```php
  <?php

use CoreModel as DB;

class UserModel
{
  ...
}

```

## CoreModel
You might find in a 'controller or model file', 'component file(not the best for coreFramework)' or 'controller file(cQured web-api)' a
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

* 'DB' here is the alias or represents the 'CoreModel' class

*   Query any SQL statement :
```php
DB::sql('SELECT * FROM users t WHERE u.age > 45 LIMIT 10 ORDER BY u.name')
        ->get();
```

*   SELECT All Users :
```php
DB::table('user')
          ->get();
```

*   SELECT All Users DISTINCT:
```php
DB::table('user')
          ->distinct();
```


*   Count All Users :
 ```php
 DB::table('user')
          ->count();
 ```

*   SELECT All Users with only id and name Fields:
```php
DB::table('user')
          ->fields('id, name')
          ->get();
```


*   SELECT All Users with only id and name Fields 'AS' username:
```php
DB::table('user')
          ->fields('id, name AS username')
          ->get();
```


*   SELECT All Users Limit to 10 :
```php
 DB::table('user')
          ->limit(10)
          ->get();
```


*   SELECT All Users Limit to 10 Offset 100:
 ```php
 DB::table('user')
          ->limit(10)
          ->offset(100)
          ->get();
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
 DB::table('user')
        ->orderBy('name')
        ->get();
```

*   SELECT All Users Order by ASCENDING :
```php
DB::table('user')
          ->orderBy('name',2)
           ->get();
```

*   SELECT User with id == 3 :
```php
DB::table('user')
        ->where('id',3)
    	  ->get();
```

*   SELECT User with id != 3 :
```php
DB::table('user')
        ->where('id','!=',3)
        ->get();
```

*   SELECT User with id < 3 :
```php
DB::table('user')
        ->where('id','<',3)
        ->get();
```
*   SELECT User with id > 3 :
```php
DB::table('user')
          ->where('id','>',3)
          ->get();
```    

*   SELECT User with id <= 3 :
```php
DB::table('user')
          ->where('id','<=',3)
          ->get();
```

*   SELECT User with name LIKE 'kel' :
```php
DB::table('user')
          ->where('id','LIKE','%kel%')
          ->get();
```

**  These returns a single object
*   SELECT A Single User with id == 3 :
```php
DB::table('user')
          ->where('id',3)
          ->single();
```

*   SELECT Users with id == 3 or name = 'kelvin' :
```php
DB::table('user')
          ->where('id',3)
          ->orWhere('name','kelvin')
          ->get();
```
*   SELECT Users with id == 3 and name = 'kelvin' :
```php
DB::table('user')
          ->where('id',3)
          ->andWhere('name','kelvin')
          ->get();
```    
**  These returns a true or false if its sucessfull or failed
*   INSERT New User:
```php
$data = [
  'name'=>'kelvin',
  'email'=>'kelvin@air.com',
  'gender'=>'male'
  ];
DB::table('user')
          ->add($data);
```    
*   UPDATE User with id = 3 :
```php
$data = [
  'name'=>'kelvin',
  'email'=>'kelvin@air.com',
  'gender'=>'male'
  ];
DB::table('user')
          ->where('id',3)
          ->update($data);
```   
*   DELETE User with id = 3 :
```php
DB::table('user')
          ->where('id',3)
          ->delete();
```
* JOIN queries
    *   SELECT * FROM Users u and INNER JOIN comments c ON u.id == c.userId
```php
DB::table('user u')
        ->join('comment','c')
        ->on('u.id','c.userId')
        ->get();
```

*   SELECT * FROM Users u and LEFT JOIN comments c ON u.id == c.userId
```php
DB::table('user u')
    ->leftJoin('comment','c')
    ->on('u.id','c.userId')
    ->get();
```

*   SELECT * FROM Users u and RIGHT JOIN comments c ON u.id == c.userId
```php
DB::table('user u')
    ->rightJoin('comment','c')
    ->on('u.id','c.userId')
    ->get();
```

*   The letter 'u' in the table method after the table name 'user' is the alias of the table.
*   There also is the rightJoin, leftJoin, innerJoin, fullJoin

* GROUP BY
*   SELECT Users u and RIGHT JOIN comments c ON u.id == c.userId GROUP BY u.id
```php
DB::table('user u')
    ->rightJoin('comment','c')
    ->on('u.id','c.userId')
    ->groupBy('t.id')
    ->get();
```

* MULTI DATABASE            
*   SELECT identityDB.Users u and INNER JOIN blogDB.comments c ON u.id
```php
DB::table('identityDB.user u')
    ->join('blogDB.comment','c')
    ->on('u.id','c.userId')
    ->groupBy('t.id')
    ->get();
```

## Authentication and Authorization.
You should see the 'vendor' folder at the root directory of the api. This is in relation to 'composer.json'.
Hence you can add other libraries and packages to the api for both security and manipulations of data.
