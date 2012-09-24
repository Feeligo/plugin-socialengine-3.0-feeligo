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
 * @package    Feeligo_Se3_Mapper_Db_Row
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
/**
 * SE3 does not provide  a proper object model for manipulating data.
 * For instance, the SE3User class allows to access and modify users,
 * but there is no equivalent for Actions, ActionTypes etc..
 *
 * The aim of this class is to provide a basis for Mappers which allow
 * to programmatically interact with all kinds of SE3 objects.
 *
 * To simplify things and because all SE3 tables have one single ID column
 * as primary key, this Mapper uses only one ID attribute to identify objects.
 */ 
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'table.php');
 
class Feeligo_Se3_Mapper_Db_Row {
  
  public function __construct($table, $fields = array()) {
    $this->_table = $table;
    $this->_fields = $fields;
    $this->_is_new = true;
    // attempt initializing from DB if id is provided
    if ($this->id() !== null) {
      $this->_is_new = !$this->_load();
    }
  }
  
  public function table() {
    return $this->_table;
  }
  
  public function id() {
    return isset($this->_fields[$this->table()->primary_key()]) ? $this->_fields[$this->table()->primary_key()] : null;
  }
 
  public function save() {
    return $this->_save();
  }
  
  public function is_new() {
    return $this->_is_new;
  }
  
  /*
   * returns the database row where $id_key == $id
   * as an associative array
   */
  private function _fetch() {
    // query
    $results = $this->table()->fetch_all_rows_where(array($this->table()->primary_key().' = ?', $this->id()), 1);
    return sizeof($results) > 0 ? $results[0] : null;
    
    $query = "SELECT * FROM `".$this->table()->name()."` WHERE ".self::$id_key."=".self::sql_quote_value($this->id())." LIMIT 1";
    $result = self::query($query);
    // return first row as associative array, or null
    while ($row = mysql_fetch_assoc($result)) { return $row; }
    return null;
  }

  /*
   * must return the ID of the adaptee in the database
   *
   * override this in implementation
   */
  private function _save() {
    $set = array();
    foreach($this->_fields as $k => $v) {
      if ($k != $this->table()->primary_key() || !$this->table()->primary_key_is_set_automatically()) {
        $set[] = "$k=".$this->table()->sql_quote_value($v);
      }
    }
    $set_fragment = 'SET '.implode(',', $set);
    
    if ($this->is_new()) {
      $query = "INSERT INTO `".$this->table()->name()."` ".$set_fragment;
    }else{
      $query = "UPDATE `".$this->table()->name()."` ".$set_fragment." WHERE ".$this->table()->primary_key()."=".$this->table()->sql_quote_value($this->id())."";
    }
    $result = $this->table()->query($query);
    $this->_is_new = false;
    return true; // TODO : check query success|failure
  }
  
  

  /*
   * must load values from the database
   *
   * override this in implementation
   */
  private function _load() {
    if (($row = $this->_fetch()) !== null) {
       $this->_fields = $row;
       return true;
     }
     return false;
  }

  /*
   * must initialize and save a new instance of this object
   *
   * override this in implementation
   * returns an instance of Feeligo_Se3_Mapper_Base
   */
  public static function create($fields) {
    $new = new self($fields);
    if ($new->save()) {
      return $new;
    }
    // TODO: error
    return null;
  }
  
  /*
   * updater
   */
  public function update($fields) {
    if (is_array($fields) && sizeof($fields) > 0) {
      foreach ($fields as $key => $value) {
        $this->set($key, $value);
      }
    }
    return $this;
  }
  
  /*
   * setter
   */
  public function set($key, $value) {
    $this->_fields[$key] = $value;
    return $this;
  }
  
  /*
   * getter
   */
  public function get($key) {
    return isset($this->_fields[$key]) ? $this->_fields[$key] : null;
  } 
}