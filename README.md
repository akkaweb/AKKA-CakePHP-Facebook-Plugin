> IMPORTANT:
> Support for Facebook SDKv5

# CakePHP 3 Facebook Plugin

A CakePHP 3.x Plugin to allow Facebook Login into an application.

[![Total Downloads](https://poser.pugx.org/akkaweb/cakephp-facebook/downloads.svg)](https://packagist.org/packages/akkaweb/cakephp-facebook)
[![License](https://poser.pugx.org/akkaweb/cakephp-facebook/license.svg)](https://packagist.org/packages/akkaweb/cakephp-facebook)
[![Gitter](https://badges.gitter.im/akkaweb/AKKA-CakePHP-Facebook-Plugin.svg)](https://gitter.im/akkaweb/AKKA-CakePHP-Facebook-Plugin?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## Requirements 

- CakePHP 3.x with Auth component correctly setup. Refer to http://book.cakephp.org/3.0/en/controllers/components/authentication.html for setup
- PHP 5.6+
- Facebook PHP SDK 5.0+
- Users Table with at least the following columns
    * `facebook_id varchar(20)`
    * `first_name` // can be set in config
    * `last_name` // can be set in config
    * `username` // can be set in config
    * `password` // can be set in config

For existing applications that already have a `first_name`, `last_name`, `username` and `password` already created with a different column name, you can dynamically set it in configuration options when loading the `Component` to your `AppController.php` file. See section 2 of Configuration below

NOTE: Facebook PHP SDK is a requirement. Composer will automatically install Facebook for you if not already installed. If manual download is chosen, you must download the Facebook Source into `vendor/facebook/php-sdk-v4` and make sure it can be loaded by the composer. Else you need to manually include FAcebook's `autoload.php` file in your application's `bootstrap.php`.

## Included 

- Facebook Graph Component
- Facebook Graph Helper
	- $this->Facebook->loginLink()
	- $this->Facebook->likeButton()
	- $this->Facebook->shareButton()
	- $this->Facebook->sendButton()
	- $this->Facebook->followButton()
	- $this->Facebook->comments()
	- $this->Facebook->embeddedPosts()
	- $this->Facebook->embeddedVideo()
	- $this->Facebook->page()
	
***** See below for more details on how to use each

## Installation 

##### Composer (Best Choice)

1. Run the following command to install the latest tagged version of AKKAweb's CakePHP Facebook Plugin

```php
composer require akkaweb/cakephp-facebook
```


## Configuration 

1. Load the plugin in your application's `bootstrap.php` file:

```php
Plugin::load('Akkaweb/Facebook', ['bootstrap' => false, 'routes' => true]);
```

2. Load the plugin's component in `AppController.php` 

Note: You need to obtain your facebook `App Id` and `App Secret` from http://developers.facebook.com. Once you are on that page and have an App created, you can retrieve your `App Id` and `App Secret`. Make sure you add your domain to `App Domains` in the `Settings` page.

`user_columns` is optional and should only be used if your `Users` database columns defer from the defaults as specified in the Requirement section, above.

```php
public function initialize(){
    parent::initialize();
    
    $this->loadComponent('Akkaweb\Facebook.Graph', [
	    'app_id' => 'your-fb-app-id',
	    'app_secret' => 'your-fb-app-secret',
	    'app_scope' => 'email,public_profile', // https://developers.facebook.com/docs/facebook-login/permissions/v2.4
	    'redirect_url' => Router::url(['controller' => 'Users', 'action' => 'login'], TRUE), // This should be enabled by default
	    'post_login_redirect' => '' //ie. Router::url(['controller' => 'Users', 'action' => 'account'], TRUE)
	    // 'user_columns' => ['first_name' => 'fname', 'last_name' => 'lname', 'username' => 'uname', 'password' => 'pass'] //not required
    ]);
}
```

## Usage 

Note: FacebookHelper is automatically loaded by the `Graph Component`. If that is not desired, add `'enable_graph_helper' => false,` to `$this->loadComponent()` above.

##### Login Link (Customizable Facebook link <a href)

Reference - https://developers.facebook.com/docs/plugins/login-button

````
Optional Settings Array
* id 	-> Link id
* class -> Link class
* title -> Link title
* style -> Any html style
* label -> Hyperlink text
	
`<?php echo $this->Facebook->loginLink($options = []); ?>`
````

##### Like Button (Facebook Like)

Reference - https://developers.facebook.com/docs/plugins/like-button

````
Default Options
* 'action' => 'like', // like, recommend
* 'share' => true,
* 'width' => 450,
* 'show-faces' => true,
* 'layout' => 'standard' // standard, box_count, button_count, button

`<?php echo $this->Facebook->likeButton($options = []); ?>`
````

##### Share Button (Facebook Share)

Reference - https://developers.facebook.com/docs/plugins/share-button

````
Default Options
* 'layout' => 'button_count' // button_count/box_count/button/icon_link/icon/link

`<?php echo $this->Facebook->shareButton($options = []); ?>`
````

##### Send Button (Facebook Send)

Reference - https://developers.facebook.com/docs/plugins/send-button

````
Default Options
* 'width' => 50,
* 'height' => 30,
* 'colorscheme' => dark
	
`<?php echo $this->Facebook->sendButton($options = []); ?>`
````

##### Follow Button (Facebook Follow)

Reference - https://developers.facebook.com/docs/plugins/follow-button

````
Default Options
* 'width' => 300,
* 'height' => 100,
* 'colorscheme' => 'light',
* 'layout' => 'standard',
* 'show-faces' => false,
* 'kid-directed-site' => false
	
`<?php echo $this->Facebook->followButton($options = []); ?>`
````

##### Comments (Facebook Comments)

Reference - https://developers.facebook.com/docs/plugins/comments

````
Default Options
* 'colorscheme' => light, // light/dark
* 'mobile' => 'Auto-detected',
* 'num-posts' => 10,
* 'order-by' => 'social', // social/reverse_time/time
* 'width' => 550
	
`<?php echo $this->Facebook->comments($options = []); ?>`
````

##### Embedded Posts (Facebook Embedded Posts)

Reference - https://developers.facebook.com/docs/plugins/embedded-posts

````
Default Options
* 'width' => 500
	
`<?php echo $this->Facebook->embeddedPosts($options = []); ?>`
````

##### Embedded Video (Facebook Embedded Video)

Reference - https://developers.facebook.com/docs/plugins/embedded-video-player

````
Default Options
* 'href' => ''
* 'width' => 500
	
`<?php echo $this->Facebook->embeddedVideo($options = []); ?>`
````

##### Page Plugin (Facebook Page Plugin)

Reference - https://developers.facebook.com/docs/plugins/page-plugin

````
Default Options
* 'href' => 'https://www.facebook.com/facebook',
* 'height' => 300,
* 'hide-cover' => false,
* 'show-facepile' => true,
* 'show-posts' => false,
* 'width' => 500
	
`<?php echo $this->Facebook->page($options = []); ?>`
````

## Events 

To allow for better integration with this plugin the following 3 events are available:

##### `Model.Users.afterFacebookCreate`

This event is fired as soon as a new user is created thru Facebook Login

##### `Model.Users.afterFacebookUpdate`

This event is fired as soon as Facebook Login is added to an existing user

##### `Model.Users.afterFacebookLogin`

This event is fired after the request is sent to have the newly created/updated user autoLogged in.

## Disclaimer
Although we have done many tests to ensure this plugin works as intended, we advise you to use it at your own risk. As with anything else, you should first test any addition to your application in a test environment. Please provide any fixes or enhancements via issue or pull request.

