<?php
/**
 * Feeligo_Se3_Action_Adapter
 *
 * this class implements the Adapter pattern to adapt the interface
 * of the local Action model to the specifications of the Feeligo API
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'sdk/interfaces/action_adapter.php');


class Feeligo_Se3_Action_Adapter implements FeeligoActionAdapter {
 
  public function __construct($se_action_mapper = null) {
    $this->_mapper = $se_action_mapper;
    //parent::__construct($se_action_mapper, ($se_action_mapper !== null ? $se_action_mapper->id() : null));
  }
  
  public function id() {
    return $this->_mapper->id();
  }
  
}