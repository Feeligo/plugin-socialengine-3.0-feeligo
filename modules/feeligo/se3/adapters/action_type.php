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
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../mappers/action_type.php');

 
class Feeligo_Se3_Adapter_Action_Type {
  
  public function __construct($actiontype_mapper) {
    $this->_adaptee = $actiontype_mapper;
  }
  
  public function name() {
    return $this->_adaptee->get('actiontype_name');
  }
  
  /*
   * builds an instance of Feeligo_Se3_Adapter_Action_Type from action data
   * also needs an action_message instance, representing the message for the action
   * which can be created from the same data (see Feeligo_Se3_Adapter_Action_Message)
   */
  public static function create_or_update_from_action_data($data, $action_message) {
    
    $at_name = substr($at_name = strtolower($data['name']), 0, strlen($pre = 'flg_action_')) == $pre ? $at_name : $pre.$at_name;
    $at_icon = 'action_addfriend.gif';
    $at_is_setting = 0;
    $at_is_enabled = 1;
    $at_desc = $action_message->languagevar('desc')->id();
    $at_text = $action_message->languagevar('body')->id();
    $at_vars = $action_message->vars_string();//TEMP flg_make_actiontype_vars_string($action);
    $at_has_media = isset($data['media']) && is_array($data['media']) && sizeof($data['media']) > 0;//TEMP is_nonempty_array($action['media']); //temp
    
    $fields = array(
      'actiontype_name' => $at_name,
      'actiontype_icon' => $at_icon,
      'actiontype_setting' => $at_is_setting,
      'actiontype_enabled' => $at_is_enabled,
      'actiontype_desc' => $at_desc,
      'actiontype_text' => $at_text,
      'actiontype_vars' => $at_vars,
      'actiontype_media' => $at_has_media
    );

    if (sizeof($mappers = Feeligo_Se3_Mapper_Action_Type::find_all_by_name($at_name)) == 0) {
      // actiontype does not exist, create it 
      $mapper = new Feeligo_Se3_Mapper_Action_Type($fields);
    }else{
      // actiontype exists, update it
      $mapper = $mappers[0];
      $mapper->update($fields);
    }
    $mapper->save();
    return new self($mapper);
  }
  
  
  
}