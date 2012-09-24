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
 * @package    Feeligo_Se3_Mapper_Action
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
require_once(str_replace('//','/',dirname(__FILE__).'/').'db/row.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'db/table.php');

/*
 * Mapper for SE3 actions
 */
 
class Feeligo_Se3_Mapper_Action extends Feeligo_Se3_Mapper_Db_Row {
  
  public function __construct($fields = array()) {
    parent::__construct(Feeligo_Se3_Mapper_Db_Table::_('se_actions'), $fields);
  }
 
  private function _load() {
    $b = parent::load();
    // unserialize the action_text attribute
    if ($this->_fields['action_text'])
      $this->_fields['action_text'] = unserialize($this->_fields['action_text']);
    return $b;
  }
  
}