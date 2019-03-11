# CQured MULTI-WebAPI Framework - Clean Architecture

Still under-development.

* To Access the appAPI's ValuesController, go to localhost/cqured/appApi/values   -> this just returns the array in the controller's httpGet() method.
* The real magic is to enter localhost/cqured/appApi/test to access the the controller's httpGet() method.
This now calls the GetPersonQuery from the your 'app' application in the core/applications/app/person/queries folder.
  * The Query Class automatically calls its Handler class (done with the cqured\mediatr class).

  
Clean/Onion Architecture for multi-api framework
* CQured web-api for presentation(Api)
* Doctrine for DB
* GuzzlePSR7 for http
* lcobucci/jwt for Auth

# Quick Intro
The Framework is divided into 4 sections.
* Presentation
* Common
* Core
* Infrastructure

The Presentation layer/folder holds your individual Api(s) for each app being created.
presentation api is registered at the config.php file for access.

The Common layer/folder holds your constants, general functions, exceptions, libraries etc. which will be used by the entire workspace. Therefore these are independent of any app or api.

The Core layer/folder holds your Application & Their Domain(Entity)
* The Application folder collects your logics, interfaces and models
* The Domain folder collects your entities generated with Doctrine ORM

The Infrastructure layer/folder holds services and database connections.
Hences, services like, SMS, Generating Tokens, Database Connections Goes here.