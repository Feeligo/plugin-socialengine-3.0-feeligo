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
 * @package    Feeligo_Se3_Mapper_Action_Type
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
require_once(str_replace('//','/',dirname(__FILE__).'/').'db/row.php');

/*
 * Mapper for SE3 actions
 */
 
class Feeligo_Se3_Mapper_Action_Type extends Feeligo_Se3_Mapper_Db_Row {
  
  public function __construct($fields = array()) {
    parent::__construct(Feeligo_Se3_Mapper_Db_Table::_('se_actiontypes'), $fields);
  }
    
  /*
   * returns Feeligo_Se3_Mapper_Action_Type instances for all action types matching actiontype_name = $name
   * (there should be one or none)
   */
  public static function find_all_by_name($name) {
    $all = array();
    if(sizeof($rows = Feeligo_Se3_Mapper_Db_Table::_('se_actiontypes')->fetch_all_rows_where(array('actiontype_name = ?', $name))) > 0) {
      foreach ($rows as $row) { $all[] = new self($row); }
    }
    return $all;
  }
  
}