<?php
/**
 * Feeligo_Se3_User_Adapter
 *
 * this class implements the Adapter pattern to adapt the interface
 * of the local User model as expected by the Feeligo API
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'sdk/interfaces/user_adapter.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3_user_friends_selector.php');

/**
 * this file provides a skeleton implementation, modify to your needs.
 */
 
class Feeligo_Se3_User_Adapter implements FeeligoUserAdapter {
  
  /**
   * constructor
   * expects an instance of se3_User
   *
   * @param mixed $adaptee
   */
  public function __construct($adaptee) {
    // we assume the $adaptee is a SE3 user
    $this->_adaptee = $adaptee;
  }
  
  public function user() {
    return $this->_adaptee;
  }
    
  /**
   * returns the unique identifier of the user
   *
   * @return string
   */
  public function id() {
    return isset($this->_adaptee->user_info['user_id']) ? $this->_adaptee->user_info['user_id'] : null;
  }
  /**
   * the user's display name
   *
   * human-readable name which is shown to other users
   *
   * @return string
   */
  public function name() {
    return isset($this->_adaptee->user_info['user_displayname']) ? $this->_adaptee->user_info['user_displayname'] : null;
  }
    
  /**
   * the URL of the user's profile page (full URL, not only the path)
   *
   * @return string
   */
  public function link() {
    return isset($this->_adaptee->user_info['user_username']) ? "profile.php?user=".$this->_adaptee->user_info['user_username'] : null;
  }
  
  /**
   * the URL of the user's profile picture
   *
   * @return string
   */
  public function picture_url() {
    return $this->_adaptee->user_photo("", true);
  }

  /**
   * returns a FeeligoUsersSelector to select friends of this user
   *
   * @return FeeligoUsersSelector
   */
  public function friends_selector() {
    return new Feeligo_Se3_User_Friends_Selector($this);
  }

}