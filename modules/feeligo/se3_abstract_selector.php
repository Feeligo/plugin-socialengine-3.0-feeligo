<?php
/**
 * Feeligo_Se3_Abstract_Selector
 *
 */
 
class Feeligo_Se3_Abstract_Selector {
  
  function __construct($table_name, $table_column_prefix) {
    $this->_table_name = $table_name;
    $this->_table_column_prefix = $table_column_prefix;
  }
  
  protected function _select($fields = '*', $query='') {
    if (is_array($fields)) {
      if (count($fields) > 0) {
        $fields = '`'.join('`,`', $fields).'`';
      }else{
        $fields = '*';
      }
    }elseif ($fields !== '*') {
      $fields = '`'.$fields.'`';
    }
    if (strlen($query) == 0) $query = 'WHERE 1';
    $query = "SELECT $fields FROM `".$this->_table_name."` ".$query;
    return $query;
  }
  
  protected function _where($where, $query='') {
    return "WHERE $where $query";
  }
  
  protected function _by_id($id, $query='') {
    return $this->_where($this->_table_column_prefix.'_id = '.$id, $query);
  }
  
  protected function _by_id_multi($ids, $query='') {
    return $this->_where($this->_table_column_prefix.'_id IN ('.join(',', $ids).')', $query);
  }
  
  protected function _limit($limit=null, $offset=null, $query='') {
    $q = '';
    if ($limit !== null) { $q .= "LIMIT $limit"; }
    if ($offset !== null && $offset > 0) { $q .= " OFFSET $offset"; }
    return $q." ".$query;
  }
  
  protected function _fetch_rows($query) {
    $rows = array();
    $result = mysql_query($query);
    while($row = mysql_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }
  
}