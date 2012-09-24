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
 * @package    Feeligo_Se3_Mapper_Languagevar
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
require_once(str_replace('//','/',dirname(__FILE__).'/').'db/row.php');

/*
 * Mapper for the SE3 language var
 */
 
class Feeligo_Se3_Mapper_Languagevar extends Feeligo_Se3_Mapper_Db_Row {
  
  static $_last_given_id = null;
  
  public function __construct($fields) {
    parent::__construct(Feeligo_Se3_Mapper_Db_Table::_('se_languagevars'), $fields);
  }
  
  public static function create_or_update($language_id, $key, $body) {

    // find existing language variables
    $lvars = self::find_all_by_key_and_language_id($key, $language_id, 1);

    $fields = array(
      'languagevar_default' => $key,
      'languagevar_language_id' => $language_id,
      'languagevar_value' => $body
    );

    if(sizeof($lvars) == 0) {
      $fields['languagevar_id'] = self::next_available_id();
      $lvar = new self($fields);
    }else{
      $lvar = $lvars[0];
      $lvar->update($fields);
    }
    $lvar->save();
    
    return $lvar;
  }
  
  public static function find_all_by_key($key, $limit=null) {
    return self::_rows_to_instances(Feeligo_Se3_Mapper_Db_Table::_('se_languagevars')->fetch_all_rows_where(array('languagevar_default = ?', $key), $limit));
  }
  
  public static function find_all_by_key_and_language_id($key, $language_id, $limit=null) {
    return self::_rows_to_instances(Feeligo_Se3_Mapper_Db_Table::_('se_languagevars')->fetch_all_rows_where(array('languagevar_default = ? AND languagevar_language_id = ?', $key, $language_id), $limit));
  }
  
  private static function _rows_to_instances($rows) {
    $instances = array();
    if(sizeof($rows) > 0) {
      foreach ($rows as $row) { $instances[] = new self($row); }
    }
    return $instances;
  }
  
  public static function next_available_id() {
    // look up 1 language var whose id is higher than $start and higher than the last returned id,
    // highest ID's first  :  the first (if any) is the highest languagevar ID currently in use 
    $where = "languagevar_id >= $start";
    if (self::$_last_given_id !== null) $where .= " AND languagevar_id > ".self::$_last_given_id;
    $lv_rows = Feeligo_Se3_Mapper_Db_Table::_('se_languagevars')->fetch_all_rows_where($where, 1, 0, 'ORDER BY languagevar_id DESC');

    if (sizeof($lv_rows) > 0) {
      // the first row has the highest ID in use : return it + 1
      //return $_last_given_lv_id = ($lv_rows[0]['languagevar_id'] + 1);
      return self::_set_last_given_id($lv_rows[0]['languagevar_id'] + 1);
    }
    // $lv_rows is empty
    return self::_incr_last_given_id();
  }
  
  private static function _incr_last_given_id() {
    if (self::$_last_given_id === null)
      return self::_set_last_given_id(10060000);
    else
      return self::_set_last_given_id(self::$_last_given_id + 1);
  }
  
  private static function _set_last_given_id($id) {
    return self::$_last_given_id = $id; 
  }
  
}