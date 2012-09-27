<?php
/**
 * Feeligo
 *
 * @category   Feeligo
 * @package    Feeligo_Api
 * @copyright  Copyright 2012 Feeligo
 * @license    
 * @author     Davide Bonapersona <tech@feeligo.com>
 */

/**
 * @category   Feeligo
 * @package    Feeligo_Se3_Adapter_Action_Type
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
require_once(str_replace('//','/',dirname(__FILE__).'/').'../adapters/languagevar.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'../../se3_api.php');

 
class Feeligo_Se3_Adapter_Action_Message {
  
  public function __construct() {
    $this->_languagevars = array(
      'body' => null,
      /**
       * the languagevar for 'desc' will appear in the 'Preferences' drop-down menu on the user's "What's new" page
       * as a label for the checkbox which the user needs to check to enable our notifications.
       *
       * At the moment we set it to "Gifts" but something else will be needed to support additional notification types
       */
      'desc' => new Feeligo_Se3_Adapter_Languagevar('flg_actiontype_user_sent_gift_to_user_desc', array('en' => 'Gifts', 'fr' => 'Cadeaux', 'es' => 'Regalos'))
    );
    $this->_arguments = array();
    $this->_argument_vars = array();
    $this->_argument_index = array();
  }
  
  public function vars_string() {
    return implode(',', $this->_argument_vars);
  }
  
  public function arguments() {
    return $this->_arguments;
  }
  
  /*
   * returns the language vars
   */
  public function set_body_var($languagevar) {
    $this->_languagevars['body'] = $languagevar;
  }
   
  public function languagevar($key) {
    return isset($this->_languagevars[$key]) ? $this->_languagevars[$key] : null;
  }
    
  /*
   * performs replacements within the message body string
   * <a href=\"profile.php?user=%3$s\">%4$s</a> sent <b>%1$s</b> to <a href=\"profile.php?user=%5$s\">%6$s</a>.<div class=\"recentaction_div_media flg_action_media clearfix\">[media]<div class=\"flg_action_message\" style=\"display:inline; padding-left:10px\">&raquo;%2$s&laquo;</div></div>
   */
  public function process_message_body($body, $locale, $data) {
    $media_inner = '';
    
    // process arguments
    if (isset($data['arguments']) && is_array($data['arguments']) && sizeof($data['arguments']) > 0) {
      foreach($data['arguments'] as $arg) {
        $function = $arg['properties']['function'];
        $key = '${'.$function.'}';
        
        $value = '';
        // default : use generic
        if (isset($arg['generic']) && isset($arg['generic']['i18n']) && isset($arg['generic']['i18n'][$locale])) {
          $value = $arg['generic']['i18n'][$locale];
        }
        
        // domain: community
        if ($arg['domain'] == 'community') {
          if ($arg['type'] == 'user') {
            // find the user by ID within the community
            if (isset($arg['properties']['id'])) {
              $user = Feeligo_Se3_Api::_()->users()->find($arg['properties']['id']);
            }
            // if user was found, link to their page. Else use generic
            if ($user !== null) {
              $value = '<a class="flg_action_user" data-flg-role="link" data-flg-type="user" data-flg-id="'.$user->id().'" href="%'.$this->_push_argument($function.'_id', $user->link()).'$s">%'.$this->_push_argument($function.'_name', $user->name()).'$s</a>';
            }
          }
        }
        
        // domain: feeligo
        if ($arg['domain'] == 'feeligo') {
          if ($arg['type'] == 'gift') {
            if (isset($arg['properties']['id']) && isset($arg['properties']['name'])) {
              $value = '<b data-flg-role="link" data-flg-type="gift" data-flg-id="'.$arg['properties']['id'].'">%'
                . $this->_push_argument($function.'_name', $arg['properties']['name'])
                . '$s</b>';
            }
            $gift_message = null;
            if (isset($arg['properties']['message']) && $arg['properties']['message']!==null && strlen($arg['properties']['message'].'')>0) {
              $gift_message = $arg['properties']['message'];
            }
            $media_inner .= '<div class="flg_action_message" style="display:inline; padding-left:10px">%'.$this->_push_argument($function.'_message', $gift_message).'$s</div>';
            
          } 
        } 
        
        // perform replacement
        $body = str_replace($key, $value, $body);
      }
    }
    
    // add media
    if (isset($data['media']) && is_array($data['media']) && sizeof($data['media']) > 0) {
      $body .= '<div class="recentaction_div_media flg_action_media clearfix">[media]'.$media_inner.'</div>';
    }
    
    
    return $body; 
  }
  
  /*
   * stores an argument and returns its index
   */
  private function _push_argument($key, $arg) {
    if (!isset($this->_argument_index[$key])) {
      $this->_arguments[] = $arg;
      $this->_argument_vars[] = '['.$key.']';
      $this->_argument_index[$key] = sizeof($this->_arguments); //indexes for SE3 start at 1
    }
    return $this->_argument_index[$key];
  }
  
  /*
   * create an action message from the action's data
   */
  public static function create_or_update_from_action_data($data) {
    $key = (substr($at_name = strtolower($data['name']), 0, strlen($pre = 'flg_action_')) == $pre ? $at_name : $pre.$at_name).'_message';
    
    $message = new self();
    
    //$lang = Feeligo_Se3_Mapper_Language::get_current();
    
    if (isset($data['message']) && isset($data['message']['i18n']) && sizeof($data['message']['i18n']) > 0) {
      // process the message body
      foreach ($data['message']['i18n'] as $locale => $message_body) {
        // process the message body
        $data['message']['i18n'][$locale] = $message->process_message_body($message_body, $locale, $data);
      }
      // create/update and store the languagevar
      $message->set_body_var(new Feeligo_Se3_Adapter_Languagevar($key.'_body', $data['message']['i18n']));
    }
    
    return $message;
  }
  
}