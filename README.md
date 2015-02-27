# CakePHP 3 Facebook Plugin

A CakePHP 3.x Plugin to allow Facebook Login into an application.

## Requirements
- CakePHP 3.x
- PHP 5.4.6
- Facebook PHP SDK 4.0

## Included
- GraphComponent
- GraphHelper

NOTE: Facebook PHP SDK is a requirement. Composer will automatically install Facebook for your if not already installed. If manual download is chosen, you must download the Facebook Source into `vendor/facebook/php-sdk-v4`

This plugin is currently in development and should be tested before use.

## Installation

### Git Close (plugins/AkkaFacebook)
git clone git@github.com:akkaweb/AKKA-CakePHP-Facebook-Plugin.git

### Download
https://github.com/akkaweb/AKKA-CakePHP-Facebook-Plugin/archive/master.zip

### Composer

Add the following to your `composer.json` in the `require` section

```
"require": {
	"akkaweb/cakephp-facebook": "dev-master"
}
```

## Configuration

1. Load the plugin in your application's `bootstrap.php` file:

```php
Plugin::load('AkkaFacebook', ['bootstrap' => false, 'routes' => true]);
```

2. Load the plugin's component in `AppController.php`
```php
$this->loadComponent('AkkaFacebook.Graph', [
    'app_id' => 'your-fb-app-id',
    'app_secret' => 'your-fb-app-secret',
    'app_scope' => '', //ie. email
    'redirect_url' => '', //ie. Router::url(['controller' => 'Users', 'action' => 'login'], TRUE),
    'post_login_redirect' => '' //ie. Router::url(['controller' => 'Users', 'action' => 'account'], TRUE)
]);
```

## Usage

Note: GraphHelper is automatically loaded by the composer. If that is not desired, add `'enable_graph_helper' => true,` to `$this->loadComponent()` above.

### Helper Template File Setup

- Add `<?php echo $this->FacebookGraph->initJsSDK(); ?>` immediately after the opening `<body>` tag

### Login Button

`<?php echo $this->FacebookGraph->loginLink(); ?>`



DOCUMENTANTION IN DEVELOPMENT. MORE COMING SOON...


## Disclaimer
Use at your own risk. Please provide any fixes or enhancements via issue or pull request.
