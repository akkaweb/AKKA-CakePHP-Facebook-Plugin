# CakePHP 3 Facebook Plugin

A CakePHP 3.x Plugin to allow Facebook Login into an application.

[![Latest Stable Version](https://poser.pugx.org/akkaweb/cakephp-facebook/version.svg)](https://packagist.org/packages/akkaweb/cakephp-facebook) 
[![Total Downloads](https://poser.pugx.org/akkaweb/cakephp-facebook/downloads.svg)](https://packagist.org/packages/akkaweb/cakephp-facebook)
[![License](https://poser.pugx.org/akkaweb/cakephp-facebook/license.svg)](https://packagist.org/packages/akkaweb/cakephp-facebook)

## Requirements
- CakePHP 3.x with Auth component correctly setup. Refer to http://book.cakephp.org/3.0/en/controllers/components/authentication.html for setup
- PHP 5.4.6+
- Facebook PHP SDK 4.0+
- Users Table with at least the following columns
    * `facebook_id varchar(20)`
    * `first_name` // can be set in config
    * `last_name` // can be set in config
    * `username` // can be set in config
    * `password` // can be set in config

For existing applications tha already have a `first_name`, `last_name`, `username` and `password` already created with a different column name, you can dynamically set it in configuration options when loading the `Component` to your `AppController.php` file. See section 2 of Configuration below

NOTE: Facebook PHP SDK is a requirement. Composer will automatically install Facebook for you if not already installed. If manual download is chosen, you must download the Facebook Source into `vendor/facebook/php-sdk-v4` and make sure it can be loaded by the composer. Else you need to manually include FAcebook's `autoload.php` file in your application's `bootstrap.php`.

## Included
- Facebook Graph Component
- Facebook Graph Helper

## Installation

#### Composer (Best Choice)

1. Add the following to your `composer.json` located in the root of your application, in the `require` section . ie. `/var/www/domain.com/composer.json`

```php
"require": {
	"akkaweb/cakephp-facebook": "dev-master"
}
```

2. Run the following at the root of your application

```
sudo php composer.phar update
```
Note: if `composer.phar` is not found, you need to install it. Follow CakePHP's documentation here -> http://book.cakephp.org/3.0/en/installation.html. Refer to Installing Cakephp section

#### Git Clone (plugins/AkkaFacebook)
`git clone git@github.com:akkaweb/AKKA-CakePHP-Facebook-Plugin.git`

#### Download
`https://github.com/akkaweb/AKKA-CakePHP-Facebook-Plugin/archive/master.zip`

Note: When installing with either manual download or Git Clone and CakePHP complains it cannot find the plugin, you need to add the plugin to `vendor/cakephp-plugins.php` in the `plugins` array [] --> `'AkkaFacebook' => $baseDir . '/plugins/AkkaFacebook/'`. If you are using composer, running `php composer.phar dumpautoload` could be sufficient. If it does not work add the following to the `"autoload"` section in the root application's `composer.json` file in the `"psr-4":` section: `"AkkaFacebook\\": "./plugins/AkkaFacebook/src"`


## Configuration

1. Load the plugin in your application's `bootstrap.php` file:

```php
Plugin::load('AkkaFacebook', ['bootstrap' => false, 'routes' => true]);
```

2. Load the plugin's component in `AppController.php` 

Note: You need to obtain your facebook `App Id` and `App Secret` from `http://developers.facebook.com`. Once you are on that page and have an App created, you can retrieve your `App Id` and `App Secret`. Make sure you add your domain to `App Domains` in the `Settings` page.

`user_columns` is optional and should only be used if your `Users` database columns defer from the defaults as specified in the Requirement section, above.

```php
public function initialize(){
    parent::initialize();
    
    $this->loadComponent('AkkaFacebook.Graph', [
	'app_id' => 'your-fb-app-id',
	'app_secret' => 'your-fb-app-secret',
	'app_scope' => '', //ie. email
	'redirect_url' => '', //ie. Router::url(['controller' => 'Users', 'action' => 'login'], TRUE),
	'post_login_redirect' => '' //ie. Router::url(['controller' => 'Users', 'action' => 'account'], TRUE)
	// 'user_columns' => ['first_name' => 'fname', 'last_name' => 'lname', 'username' => 'uname', 'password' => 'pass'] //not required
    ]);
}
```

## Usage

Note: FacebookHelper is automatically loaded by the `Graph Component`. If that is not desired, add `'enable_graph_helper' => false,` to `$this->loadComponent()` above.

#### Helper Template File Setup

- Add `<?php echo $this->Facebook->initJsSDK(); ?>` immediately after the opening `<body>` tag

#### Login Button

`<?php echo $this->Facebook->loginLink(); ?>`



THIS IS THE BASIC TO HAVE THIS PLUGIN ALLOW YOUR APPLICATION TO ACCEPT FACEBOOK LOGIN/REGISTRATION. MORE HELPER DOCUMENTATION WILL BE COMING SOON.


## Disclaimer
Although we have done many tests to ensure this plugin works as intended, we advise you to use it at your own risk. As with anything else, you should first test any addition to your application in a test environment. Please provide any fixes or enhancements via issue or pull request.
