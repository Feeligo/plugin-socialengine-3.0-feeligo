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
 * @package    Feeligo_Se3_Adapter_Base
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
/**
 * Abstraction of a database table
 */ 
 
class Feeligo_Se3_Mapper_Db_Table {
  
  public function __construct($name, $primary_key, $primary_key_autoset = true) {
    $this->_name = $name;
    $this->_primary_key = $primary_key;
    $this->_primary_key_autoset = $primary_key_autoset;
  }
  
  public function name() {
    return $this->_name;
  }
  
  public function primary_key() {
    return $this->_primary_key;
  }
  
  public function fetch_all_rows_where($where, $limit=null, $offset=0, $post='') {
    if (is_array($where) && sizeof($where) > 0 && is_string($where[0])) {
      // process conditions passed as an array, e.g. ["x=? AND y=?", 1, 3]
      $k = 0;
      while(($i = strpos($where[0], '?')) !== false && ($k += 1) < sizeof($where)) {
        $where[0] = substr_replace($where[0], $this->sql_quote_value($where[$k]), $i, 1);
      }
      $where = $where[0];
    }
    // issue query
    $query = "SELECT * FROM `".$this->name()."` WHERE ".$where;
    if ($limit !== null) $query .= " LIMIT ".$limit;
    if ($offset > 0) $query .= " OFFSET ".$offset;
    if (strlen($post) > 0) $query .= ' '.$post;
    
    $result = $this->query($query);
    // return results
    $rows = array();
    if ($result !== false) {
      while ($row = mysql_fetch_assoc($result)) { $rows[] = $row; }
    }
    return $rows;
  }
  
  /*
   * sql query
   */
  public function query($sql) {
    //var_dump("querying: $sql"); //DEBUG
    return mysql_query($sql);
  }
    
  /*
   * quote a value for insertion in a SQL query
   */
  public function sql_quote_value($value) {
    if ($value === null) return "NULL";
    if ($value === false || $value === true) return $value ? '1' : '0';
    if (is_string($value)) return "'".mysql_real_escape_string($value)."'";
    return $value;
  }
  
  /*
   * get tables
   */
  public static function _($table_name) {
    if ($table_name == 'se_actions') return new Feeligo_Se3_Mapper_Db_Table($table_name, 'action_id');
    if ($table_name == 'se_actiontypes') return new Feeligo_Se3_Mapper_Db_Table($table_name, 'actiontype_id');
    if ($table_name == 'se_languagevars') return new Feeligo_Se3_Mapper_Db_Table($table_name, 'languagevar_id', false);
    if ($table_name == 'se_languages') return new Feeligo_Se3_Mapper_Db_Table($table_name, 'language_id');
  }
  
  /*
   * tells whether the primary key is set automatically by the DB
   */
  public function primary_key_is_set_automatically () {
    return $this->_primary_key_autoset;
  }
  
  
}