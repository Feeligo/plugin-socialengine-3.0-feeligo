<?php
/**
 * Feeligo_Se3_Api
 *
 * This class is the main access point for all Feeligo methods.
 * It implements the Singleton pattern, and must be instantiated
 * by calling Feeligo_Se3_Api::()
 */
 
/**
 * enable development mode
 * WARNING: remove this for production use!
 */
// define('FLG_ENV', 'development');

/** 
 * Include SocialEngine3's header.php file
 * (provides session data and access to SE3 classes)
 */
include_once(str_replace('//','/',dirname(__FILE__).'/').'../../header.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3_user_adapter.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3_users_selector.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3_actions_selector.php');
 
/**
 * Feeligo_Se3_Api
 *
 * this class is the main access point for all Feeligo methods
 */
require_once(str_replace('//','/',dirname(__FILE__).'/').'sdk/lib/api.php');

class Feeligo_Se3_Api extends FeeligoApi {
  
  // enter your Feeligo API credentials here:
  const __community_api_key = FLG__community_api_key;
  const __community_secret  = FLG__community_secret;
  
  // URL of the Feeligo API endpoint (do not change)
  const __remote_server_url = FLG__server_url;
  
  /**
   * tells whether a viewer is available in the current context
   * the viewer is the User which is currently logged in, when applicable
   *
   * @return bool
   */
  public function has_viewer() {
    global $user;
    return $this->_se3_user_exists($user);
  }

  /**
   * Accessor for the viewer
   *
   * @return bool
   */    
  public function viewer() {
    global $user;
    return new Feeligo_Se3_User_Adapter($user);
  }
  
  /**
   * tells whether a subject is available in the current context
   * the subject is the user which is currently being viewed, when applicable
   *
   * @return bool
   */
  public function has_subject() {
    global $user, $owner;
    return $this->_se3_user_exists($owner) && $this->_se3_user_exists($user) && $owner->user_info['user_id'] != $user->user_info['user_id'];
  }

  /**
   * Accessor for the subject
   *
   * @return FeeligoUserAdapter
   */
  public function subject() {
    global $owner;
    return new Feeligo_Se3_User_Adapter($owner);
  }

  /**
   * Accessor for the website users
   *
   * @return FeeligoUsersSelector
   */
  public function users() {
    return new Feeligo_Se3_Users_Selector();
  }

  /**
   * Accessor for user Actions
   *
   * @return FeeligoActionsSelector
   */
  public function actions() {
    return new Feeligo_Se3_Actions_Selector();
  }
  
  /**
   * Singleton pattern: gets or creates a single instance of this class
   * 
   * @return Feeligo_Se3_Api
   */
  public static function getInstance() {
    if( is_null(self::$_instance) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Shorthand for getInstance, allows to call Feeligo_Se3_Api::_()
   *
   * @return Feeligo_Se3_Api
   */
  public static function _() {
    return self::getInstance();
  }
  
  /**
   * checks if the $user is an existing SocialEngine 3 user
   *
   * @return bool
   */
  protected function _se3_user_exists($user) {
    return null !== $user && method_exists($user, 'user_friend_list') && isset($user->user_info) && isset($user->user_exists) && $user->user_exists;
  }
  
}