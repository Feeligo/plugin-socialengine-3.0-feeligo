<?php
/**
 * Feeligo_Se3_Actions_Selector
 *
 * this class implements methods to create actions in the
 * database and return Adapters wrapping them
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'sdk/interfaces/actions_selector.php');

require_once(str_replace('//','/',dirname(__FILE__).'/').'se3/mappers/action.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3/adapters/action_type.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3/adapters/action_message.php');

require_once(str_replace('//','/',dirname(__FILE__).'/').'se3_api.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3_action_adapter.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3_abstract_selector.php');

/**
 * this file provides a skeleton implementation, modify to your needs.
 */
 
class Feeligo_Se3_Actions_Selector implements FeeligoActionsSelector {
  
  /**
   * creates an Action from the data passed by the API
   *
   * @return Feeligo_Se3_Action_Adapter
   */
  public function create($data) {
    
    if ($data['type'] !== 'action') return null;
    
    $api = Feeligo_Se3_Api::_();
    
    // find adapters for subject and owner
    $adapter_user = null;
    $adapter_owner = null;
    if (isset($data['arguments']) && is_array($data['arguments']) && sizeof($data['arguments']) > 0) {
      foreach($data['arguments'] as $arg) {
        if ($arg['type'] == 'user' && $arg['domain'] == 'community' && isset($arg['properties']) && 
          isset($arg['properties']['function']) && isset($arg['properties']['id'])) {
          if (($function = $arg['properties']['function']) == 'subject') {
            $adapter_user = $api->users()->find($arg['properties']['id'], false);
          } elseif ($function == 'direct_object' || $function == 'indirect_object') {
            $adapter_owner = $api->users()->find($arg['properties']['id'], false);
          }
        }
      }
    }
    if ($adapter_user == null) {
      // FAIL : a user is required
      return null;
    }
    
    // action message
    $action_message = Feeligo_Se3_Adapter_Action_Message::create_or_update_from_action_data($data);
    if ($action_message === null) {
      // FAIL : action message could not be created
      return null;
    }
    
    // ensure Action Type exists
    $actiontype = Feeligo_Se3_Adapter_Action_Type::create_or_update_from_action_data($data, $action_message);
    if ($actiontype === null) {
      // FAIL : action type could not be created
      return null;
    }
  	
  	// Action media
  	$action_media = array();
  	if (isset($data['media']) && is_array($data['media']) && sizeof($data['media']) > 0) {
    	foreach($data['media'] as $medium) {
    	  if (isset($medium['content-type']) && strpos($type = $medium['content-type'], 'image')!==false) {
    	    if (isset($medium['sizes']) && is_array($medium['sizes']) && sizeof($medium['sizes']) > 0) {
    	      $target_medium_area = 120*144;
    	      $best_medium = null;
    	      $best_medium_diff = null;
    	      
    	      foreach($medium['sizes'] as $size => $url) {
    	        $size = explode('x', $size);
              if (sizeof($size) != 2) break;
              $w = $size[0] + 0;
              $h = $size[1] + 0;
              if (is_string($w) || is_string($h) || $w<=0 || $h<=0) break;

              $diff = $target_medium_area - $w*$h;
              $diff = sqrt($diff*$diff);
              if ($best_medium_diff === null || $diff < $best_medium_diff) {
                $best_medium_diff = $diff;
                $best_medium = array(
                  'media_path' => $url,
                  'media_link' => null,
                  'media_height' => $h,
                  'media_width' => $w
                );
              }

              /*// copy media file locally
              if (($media_path = flg_path_to_local_file_for_medium($media)) !== null) {
                $se3_action_media[] = array(
                  'media_path' => $media_path,
                  'media_link' => $media['link_to'],
                  'media_height' => $media['height_px'],
                  'media_width' => $media['width_px']
                );

              }*/
              
    	      }
    	      
    	      // add the best medium to the action's media
    	      if ($best_medium !== null) {
    	        $action_media[] = $best_medium;
    	      }
    	    }
    	  }
    	} // end foreach $medium
    }
    
	
	  //  $user REPRESENTING THE USER OBJECT OF THE USER WHO COMMITTED THE ACTION
  	//	$actiontype_name REPRESENTING THE TYPE OF ACTION COMMITTED
  	//	$replace (OPTIONAL) REPRESENTING AN ARRAY OF VALUES FOR THE ACTION TEXT STRING (MUST CORRESPOND TO ACTIONTYPE_VARS)
  	//	$action_media (OPTIONAL) REPRESENTING AN ARRAY OF VALUES FOR ACTION MEDIA
  	//	$timeframe (OPTIONAL) REPRESENTING THE TIME (IN SEC) AFTER WHICH TO INSERT A NEW ROW - SET TO 0 TO ALWAYS INSERT A NEW ROW
  	//	$replace_media (OPTIONAL) REPRESENTING WHETHER TO REPLACE MEDIA FOR AN OLD ACTION OR SIMPLY ADD ADDITIONAL MEDIA
  	//	$action_object_owner (OPTIONAL) REPRESENTING THE OWNER OF THE OBJECT (ex: 'user')
  	//	$action_object_owner_id (OPTIONAL) REPRESENTING THE ID OF THE OWNER
  	//	$action_object_privacy (OPTIONAL) REPRESENTING THE PRIVACY OF THE OBJECT
  	
  	$user = $adapter_user->user();
  	$actiontype_name = $actiontype->name(); // string
  	$replace = $action_message->arguments(); // array
  	$timeframe = 0;
  	$replace_media = true; // always replace media in this operation
  	$action_object_owner = 'user'; // owner is always a user (at least here)
  	$action_object_owner_id = $adapter_owner ? $adapter_owner->id() : null;
  	$action_object_privacy = 63; // TODO!
    
    // create Action
    $actions = new se_actions();
    $actions->actions_add($user, $actiontype_name, $replace, $action_media, $timeframe, $replace_media, $action_object_owner, $action_object_owner_id, $action_object_privacy); 
    
    // retrieve id and return action adapter
    $action_id = mysql_insert_id();
    
    return new Feeligo_Se3_Action_Adapter(new Feeligo_Se3_Mapper_Action(array('action_id' => $action_id)));
  }
}