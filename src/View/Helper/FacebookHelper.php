<?php
namespace AkkaFacebook\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\GraphSessionInfo;

/**
 * Graph helper
 */
class FacebookHelper extends Helper {

    public $helpers = ['Html'];
    public $appId = null;
    public $redirectUrl = null;
    public $appScope = null;
    protected $_configs = null;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
	'app_id' => '',
	'redirect_url' => '',
	'app_scope' => ''
    ];

    public function __construct(View $view, $config = [])
    {
	parent::__construct($view, $config);
	$this->_configs = $this->config();
	$this->appId = $this->_configs['app_id'];
	$this->redirectUrl = $this->_configs['redirect_url'];
	$this->appScope = $this->_configs['app_scope'];
    }

    /**
     * Create a Facebook Login Link
     * 
     * @param type $options
     * @return type
     */
    public function loginLink($options = [])
    {
	$id = (isset($options['id']) ? $options['id'] : 'FB-login-button');
	$class = (isset($options['class']) ? $options['class'] : 'FB-login-button');
	$title = (isset($options['title']) ? $options['title'] : 'Login with Facebook');
	$style = (isset($options['style']) ? $options['style'] : '');
        $label = (isset($options['label']) ? $options['label'] : 'Facebook Login');
	
	return '<a id="' . $id . '" class="' . $class . '" href="' . Configure::read('fb_login_url') . '" title="' . $title . '" style="' . $style . '">'.$label.'</a>';
    }
    
    /**
     * Creates Facebook native button
     * 
     * @param type $options
     * @return type
     */
    public function loginButton($options = []){	
	$options = array_merge([
	    'auto-logout-link' => false,
	    'max-rows' => 1,
	    'onlogin' => null,
	    'scope' => $this->appScope,
	    'size' => 'small',
	    'show-faces' => false,
	    'default-audience' => 'friends'
	], $options);
	
	return <<<EOT
	<div class="fb-login-button" 
	    data-auto-logout-link="{$options['auto-logout-link']}" 
		data-max-rows="{$options['max-rows']}" 
		    onlogin="{$options['onlogin']}" 
			data-scope="{$options['scope']}" 
			    data-size="{$options['size']}" 
				data-show-faces="{$options['show-faces']}" 
				    data-default-audience="{$options['default-audience']}"></div>
EOT;
    }

    public function initJsSDK()
    {
	return <<<EOT
	<div id="fb-root"></div>
        <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '$this->appId',
          xfbml      : true,
          version    : 'v2.1'
        });
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>
EOT;
    }

    public function htmlTag()
    {
	
    }

    public function registrationLink()
    {
	
    }

    /**
     * Create facebook share button
     * 
     * Options
     *	    href	-> not set
     *	    action	-> [button_count]/box_count/button/icon_link/icon/link
     * 
     * @param type $options
     * @return type
     */
    public function shareButton($options = [])
    {
	$options = array_merge([
	    'layout' => 'button_count' // button_count/box_count/button/icon_link/icon/link
	], $options);
	
	return <<<EOT
	<div class="fb-share-button" 
	    data-href="{$this->__href($options)}" 
		data-layout="{$options['layout']}">
		    </div>
EOT;
    }    
    
    
    /**
     * Create Facebook FollowButton
     * 
     * options
     *	    href		-> not set
     *	    height		-> 100
     *	    width		-> 300
     *	    colorscheme		-> light
     *	    layout		-> standard/button_count/box_count
     *	    show-faces		-> false
     *	    kid-directed-site	-> false
     * 
     * @param type $options
     * @return type
     */
    public function followButton($options = []){
	$options = array_merge([
	    'width' => 300,
	    'height' => 100,
	    'colorscheme' => 'light',
	    'layout' => 'standard',
	    'show-faces' => false,
	    'kid-directed-site' => false
	], $options);
	
	return <<<EOT
	<div class="fb-follow" 
	    data-href="{$this->__href($options)}" 
		data-width="{$options['width']}" 
		    data-height="{$options['height']}" 
			data-colorscheme="{$options['colorscheme']}" 
			    data-layout="{$options['layout']}" 
				data-show-faces="{$options['show-faces']}" 
				    data-kid-directed-site="{$options['kid-directed-site']}">
					</div>
EOT;
    }
    
    public function activityFeed(){
	
    }
    
    public function recommendedContent(){
	
    }
    
    public function likeBox(){
	
    }
    
    public function facepile(){
	
    }

    public function userProfilePicture()
    {
	
    }

    /**
     * Create facebook send button
     * 
     * options
     *	    href		-> not set
     *	    width		-> 50
     *	    height		-> 30
     *	    colorscheme	-> dark
     * 
     * @param type $options
     * @return type
     */
    public function sendButton($options = [])
    {
	$options = [
	    'width' => 50,
	    'height' => 30,
	    'colorscheme' => dark
	];
	
	return <<<EOT
	<div class="fb-send" 
	    data-href="{$this->__href($options)}" 
		data-width="{$options['width']}" 
		    data-height="{$options['height']}" 
			data-colorscheme="{$options['colorscheme']}">
			    </div>
EOT;
    }

    /**
     * Create facebook like button
     * 
     * Options
     *	    href	    -> not set
     *	    action	    -> like
     *	    share	    -> true
     *	    width	    -> 450
     *	    show-faces -> true
     *	    layout	    -> [standard]/box_count/button_count/button
     * 
     * @param type $options
     * @return type
     */
    public function likeButton($options = [])
    {	
	$options = array_merge([
	    'action' => 'like', // like, recommend
	    'share' => true,
	    'width' => 450,
	    'show-faces' => true,
	    'layout' => 'standard' // standard, box_count, button_count, button
	], $options);
	
	return <<<EOT
	<div class="fb-like" 
	    data-href="{$this->__href($options)}" 
		data-share="{$options['share']}" 
		    data-width="{$options['width']}" 
			data-show-faces="{$options['show-faces']}" 
			    data-layout="{$options['layout']}" 
				data-action="{$options['action']}">
				    </div>
EOT;
    }
    
    /**
     * Create facebook comments
     * 
     *	options
     *	    colorscheme	-> [light]/dark
     *	    mobile	-> Auto-detected
     *	    num-posts	-> 10
     *	    order-by	-> [social]/reverse_time/time
     *	    width	-> 550
     * 
     * @param type $options
     * @return type
     */
    public function comments($options = []){
	$options = array_merge([
	    'colorscheme' => 'light', // light/dark
	    'mobile' => 'Auto-detected',
	    'num-posts' => 10,
	    'order-by' => 'social', // social/reverse_time/time
	    'width' => 550
	], $options);
	
	return <<<EOT
	<div class="fb-comments" 
	    data-href="{$this->here}" 
		data-numposts="{$options['num_posts']}" 
		    data-colorscheme="{$options['colorscheme']}" 
			data-mobile="{$options['mobile']}" 
			    data-order-by="{$options['order-by']}" 
				data-width="{$options['width']}">
				    </div>
EOT;
    }
    
    /**
     * Created facebook embedded posts
     * 
     * options
     *	    href	-> not set
     *	    width	-> 500
     * @param type $options
     * @return type
     */
    public function embeddedPosts($options = []){
	$options = array_merge([
	    'width' => 500
	], $_);
	
	return <<<EOT
	<div class="fb-post" 
	    data-href="{$this->__href($options)}" 
		data-width="{$options['width']}">
		    </div>
EOT;
    }
    
    /**
     * Created facebook embedded videos
     * 
     * options
     *	    href	-> not set
     *	    width	-> 500
     * @see https://developers.facebook.com/docs/plugins/embedded-video-player
     * @param type $options
     * @return type
     */
    public function embeddedVideo($options = []){
	$options = array_merge([
	    'href' => '',
	    'width' => 500
	], $_);
	
	return <<<EOT
	<div class="fb-video" 
	    data-href="{$options['href']}" 
		data-width="{$options['width']}">
		    </div>
EOT;
    }
    
    /**
     * Created facebook page plugin
     * 
     * options
     *	    href	-> https://www.facebook.com/facebook
     *	    width	-> 500
     *	    height	-> 300
     *	    hide-cover	-> false
     *	    show-facepile -> true
     *	    show-posts	-> false
     * 
     * @see https://developers.facebook.com/docs/plugins/embedded-video-player
     * @param type $options
     * @return type
     */
    public function page($options = []){
	$options = array_merge([
	    'href' => 'https://www.facebook.com/facebook',
	    'height' => 300,
	    'hide-cover' => false,
	    'show-facepile' => true,
	    'show-posts' => false,
	    'width' => 500
	], $_);
	
	return <<<EOT
	<div class="fb-video" 
	    data-height="{$options['height']}"  
		data-hide-cover="{$options['hide-cover']}"  
		    data-show-facepile="{$options['show-facepile']}"  
			data-show-posts="{$options['show-posts']}"
			    data-href="{$options['href']}" 
				data-width="{$options['width']}">
				    </div>
EOT;
    }
    
    /**
     * Return HREF
     * 
     * @param type $options
     * @return type
     */
    private function __href($options){
	return (isset($options['href']) && $options['href'] != '') ? $options['href'] : Router::url(null, true);
    }

}
