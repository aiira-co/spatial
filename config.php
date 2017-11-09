<?php

class AdConfig
{
	// General
    public $offline = '0';
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
