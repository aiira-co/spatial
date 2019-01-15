# CQured MULTI-WebAPI Framework

Still under-development.
Onion Architecture for multi-api framework
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