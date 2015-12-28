<?php
/**
 * AkkaFacebook Graph Component
 * 
 * @author Andre Santiago
 * @copyright (c) 2015 akkaweb.com
 * @license MIT
 */
namespace AkkaFacebook\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\GraphUser;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;

/**
 * Graph component
 */
class GraphComponent extends Component {

    /**
     * 	Facebook Redirect Login Helper
     * 
     * @var type Object
     */
    public $FacebookHelper = null;

    /**
     * Facebook Access Token
     * 
     * @var type String
     */
    public $FacebookAccessToken = null;

    /**
     * Assigned Redirect Url 
     * 
     * @var type String
     */
    public $FacebookRedirectUrl = null;

    /**
     * Facebook Request
     * 
     * @var type Object
     */
    public $FacebookRequest = null;

    /**
     * Facebook Response
     * 
     * @var type Object
     */
    public $FacebookResponse = null;

    /**
     * Facebook Graph Object
     * 
     * @var type Object
     */
    public $FacebookGraphObject = null;

    /**
     * Facebook Graph User
     * 
     * @var type Object
     */
    public $FacebookGraphUser = null;

    /**
     * Facebook Session
     * 
     * @var type Object
     */
    public $FacebookSession = null;

    /**
     * Facebook User Full Name
     * 
     * @var type String
     */
    public $FacebookName = null;

    /**
     * Facebook User First Name
     * 
     * @var type String
     */
    public $FacebookFirstName = null;

    /**
     * Facebook User Last Name
     * 
     * @var type String
     */
    public $FacebookLastName = null;

    /**
     * Facebook User Id
     * 
     * @var type String
     */
    public $FacebookId = null;

    /**
     * Facebook User Email
     * 
     * @var type String
     */
    public $FacebookEmail = null;

    /**
     * Component Configuration
     * 
     * @var type Array
     */
    protected $_configs = null;

    /**
     * Application Users Model Object
     * 
     * @var type Object
     */
    protected $Users = null;

    /**
     * Components Controller
     * 
     * @var type Object
     */
    protected $Controller = null;

    /**
     * Application Components
     * 
     * @var type Component
     */
    public $components = ['Flash', 'Auth'];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
	'app_id' => '',
	'app_secret' => '',
	'app_scope' => [],
	'redirect_url' => '/users/login',
	'post_login_redirect' => '/',
	'enable_graph_helper' => true,
	'user_model' => 'Users',
	'user_columns' => [
	    'first_name' => 'first_name',
	    'last_name' => 'last_name',
	    'password' => 'password',
	    'username' => 'username'
	]
    ];

    /**
     * Initialize Controllers, User Model and Session
     * 
     * @param array $config
     */
    public function initialize(array $config)
    {
	parent::initialize($config);
	/**
	 * Assigned merge configuration
	 */
	$this->_configs = $this->config();

	/**
	 * Get current controller
	 */
	$this->Controller = $this->_registry->getController();
	//debug($this->Controller->request);
	/**
	 * Start session if not already started
	 */
	if ($this->isSessionStarted() === FALSE)
	{
	    $this->Controller->request->session()->start();
	}

	/**
	 * Attach Facebook Graph Helper
	 */
	if ($this->_configs['enable_graph_helper'])
	{
	    $this->Controller->helpers = [
		'AkkaFacebook.Facebook' => [
		    'redirect_url' => $this->_configs['redirect_url'],
		    'app_id' => $this->_configs['app_id'],
		    'app_scope' => $this->_configs['app_scope']
		]
	    ];
	}

	/**
	 * Initialize the Users Model class
	 */
	$this->Users = TableRegistry::get($this->_configs['user_model']);
	$this->Users->recursive = -1;
    }

    /**
     * Initialize Facebook, create Session, fire Request and get User Object
     * 
     * @param \Cake\Event\Event $event
     */
    public function beforeFilter(Event $event)
    {
	FacebookSession::setDefaultApplication($this->_configs['app_id'], $this->_configs['app_secret']);

	$this->FacebookRedirectUrl = $this->_configs['redirect_url'];

	$this->FacebookHelper = new FacebookRedirectLoginHelper($this->FacebookRedirectUrl);

	try {
	    $this->FacebookSession = $this->FacebookHelper->getSessionFromRedirect();
	} catch (FacebookRequestException $ex) {
	    $this->log($ex->getMessage());
	} catch (Exception $ex) {
	    $this->log($ex->getMessage());
	}

	if ($this->FacebookSession)
	{
	    try {
		$this->FacebookRequest = new FacebookRequest($this->FacebookSession, 'GET', '/me');
		$this->FacebookResponse = $this->FacebookRequest->execute();
		$this->FacebookGraphObject = $this->FacebookResponse->getGraphObject();
		$this->FacebookGraphUser = $this->FacebookGraphObject->cast(GraphUser::className());

		$this->FacebookName = $this->FacebookGraphUser->getName();
		$this->FacebookFirstName = $this->FacebookGraphUser->getFirstName();
		$this->FacebookLastName = $this->FacebookGraphUser->getLastName();
		$this->FacebookEmail = $this->FacebookGraphUser->getEmail();
		$this->FacebookId = $this->FacebookGraphUser->getId();
	    } catch (FacebookRequestException $ex) {
		$this->log($ex->getMessage());
	    } catch (Exception $ex) {
		$this->log($ex->getMessage());
	    }
	}
    }

    /**
     *  Component Startup
     * 
     * @param \Cake\Event\Event $event
     */
    public function startup(Event $event)
    {
	/**
	 * Checks if user is trying to authenticate by watching for what Facebook returns
	 */
 	//debug($this->Controller->request->query('code'));
	if ($this->Controller->request->query('code'))
	{
		//debug($this->Controller->request);die;
	    /**
	     * Queries database for existing Facebook Id
	     */
	    $queryFacebookId = $this->Users->find('all')->where(['facebook_id' => $this->FacebookId])->first();

	    /**
	     * Authenticates existing user into application
	     */
	    if ($queryFacebookId)
	    {

		$existing_user = $queryFacebookId->toArray();
		if ($this->Auth->user() && $this->Auth->user('facebook_id') != $existing_user['facebook_id'])
		{
		    $this->Flash->warning("This Facebook account is already connected with another user. You can only have one account with Facebook");
		    $this->Controller->redirect($this->_configs['post_login_redirect']);
		}
		else
		{
		    $this->Auth->setUser($existing_user);
		    $this->Controller->redirect($this->_configs['post_login_redirect']);
		}
	    }
	    else
	    {
		/**
		 * Queries database for existing user based on Email
		 */
		$queryFacebookEmail = $this->Users->find('all')->where(['email' => $this->FacebookEmail])->first();


		/**
		 * Updates user account by adding FacebookId to it and authenticates user
		 */
		if ($queryFacebookEmail)
		{
		    if ($this->Auth->user() && $this->Auth->user('email') != $queryFacebookEmail['email'])
		    {
			$this->Flash->warning("This Facebook account is already connected with another user. You can only have one account with Facebook");
		    }
		    else
		    {
			$this->__updateAccount($queryFacebookEmail);
		    }
		}
		else
		{
		    /**
		     * If user is already logged in... add to their logged in account
		     */
		    if ($this->Auth->user())
		    {
			$user = $this->Users->get($this->Auth->user('id'));
			$this->__updateAccount($user);
		    }
		    else
		    {
			/**
			 * If FacebookUserId and FacebookUserEmail is not in database, create new account
			 */
			$this->__newAccount();
		    }
		}
	    }
	}
    }

    /**
     *  Component Before Render 
     * 
     * @param \Cake\Event\Event $event
     */
    public function beforeRender(Event $event)
    {
	/**
	 * Sets/Configures fb_login_url to be assigned in Facebook Login Button
	 */
	$loginUrl = $this->FacebookHelper->getLoginUrl([$this->_configs['app_scope']]);

	$this->Controller->set('fb_login_url', $loginUrl);
	Configure::write('fb_login_url', $loginUrl);
    }

    /**
     * Add facebook_id to existing user based on their email
     * @param type $user
     */
    protected function __updateAccount($user)
    {
	$this->Users->patchEntity($user, ['facebook_id' => $this->FacebookId]);
	if ($result = $this->Users->save($user))
	{
	    $this->__autoLogin($result);
	}
    }

    /**
     * Create a new user using Facebook Credentials
     */
    protected function __newAccount()
    {
	$data = [
	    $this->_configs['user_columns']['username'] => $this->__generateUsername(),
	    $this->_configs['user_columns']['first_name'] => $this->FacebookFirstName,
	    $this->_configs['user_columns']['last_name'] => $this->FacebookLastName,
	    $this->_configs['user_columns']['password'] => $this->__randomPassword(),
	    'facebook_id' => $this->FacebookId,
	    'email' => $this->FacebookEmail
	];

	$user = $this->Users->newEntity($data);

	if ($result = $this->Users->save($user))
	{
	    $this->__autoLogin($result);
	}
    }

    /**
     * Logs user in application after successful save
     * 
     * @param type $result
     */
    protected function __autoLogin($result)
    {
	$authUser = $this->Users->get($result->id)->toArray();

	$this->Auth->setUser($authUser);
	$this->Controller->redirect($this->_configs['post_login_redirect']);
    }

    /**
     * Creates a new username
     * 
     * @return type String
     */
    protected function __generateUsername()
    {
	$username = strtolower($this->FacebookFirstName . $this->FacebookLastName);

	while ($this->Users->find()->where([$this->_configs['user_columns']['username'] => $username])->first()) {
	    $username = $username . rand(0, 900);
	}

	return $username;
    }

    /**
     * Generate a random password
     * 
     * @return type String
     */
    protected function __randomPassword()
    {
	$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 8; $i++)
	{
	    $n = rand(0, $alphaLength);
	    $pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string        
    }

    /**
     * @return bool
     */
    public function isSessionStarted()
    {
	if (php_sapi_name() !== 'cli')
	{
	    if (version_compare(phpversion(), '5.4.0', '>='))
	    {
		return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
	    }
	    else
	    {
		return session_id() === '' ? FALSE : TRUE;
	    }
	}
	return FALSE;
    }

}
