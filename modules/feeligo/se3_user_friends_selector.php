<?php
/**
 * Feeligo_Se3_User_Friends_Selector
 *
 * this class implements methods to find users in the
 * database and pass them as Adapters to the Feeligo API.
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'sdk/interfaces/users_selector.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3_user_adapter.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'se3_abstract_selector.php');

/**
 * this file provides a skeleton implementation, modify to your needs.
 */
 
class Feeligo_Se3_User_Friends_Selector extends Feeligo_Se3_Abstract_Selector implements FeeligoUsersSelector {
  
  function __construct($user_adapter) {
    parent::__construct('se_users', 'user');
    $this->_user_adapter = $user_adapter;
  }
    
  /**
   * returns an array containing all the Users
   *
   * @param int $limit argument for the SQL LIMIT clause
   * @param int $offset argument for the SQL OFFSET clause
   * @return FeeligoUserAdapter[] array
   */
  public function all($limit = null, $offset = 0) {
    return $this->_all_where("", $limit, $offset);
  }
 
  protected function user() {
    return $this->_user_adapter->user();
  }
 
  /**
   * finds a specific User by its id
   *
   * @param mixed $id argument for the SQL id='$id' condition
   * @return FeeligoUserAdapter
   */
  public function find($id, $throw = true) {
    $se_users = $this->user()->user_friend_list(0, 1, 0, 1, "se_users.user_id ASC", "se_users.user_id=$id", 0, 0);
    if (count($se_users) == 1) {
      return new Feeligo_Se3_User_Adapter($se_users[0], null);
    }
    if ($throw) throw new FeeligoNotFoundException('type', 'could not find friend with id='.$id);
    return null;
  }
 
  /**
   * finds a list of Users by their id's
   *
   * @param mixed array $ids
   * @return FeeligoUserAdapter[] array
   */
  public function find_all($ids) {
    return $this->_all_where("se_users.user_id IN (".join(',',$ids).")", $limit, $offset);
  }
    
  /**
   * returns an array containing all the Users whose name matches the query
   *
   * @param string $query the search query, argument to a SQL LIKE '%$query%' clause
   * @param int $limit argument for the SQL LIMIT clause
   * @param int $offset argument for the SQL OFFSET clause
   * @return FeeligoUserAdapter[] array
   */  
  public function search($query, $limit = null, $offset = 0) {
    $where = '`'.$this->_table_name.'`.`'.$this->_table_column_prefix.'_displayname` LIKE "%'.$query.'%"';
    return $this->_all_where($where, $limit, $offset);
  }
  
  protected function _all_where($where, $limit = null, $offset = 0) {
    $limit === null ? 10000 : $limit;
    return $this->_adapt_users($this->user()->user_friend_list($offset, $limit, 0, 1, "se_users.user_id ASC", $where, 0, 0));
  }
  
  protected function _adapt_users($users) {
    for ($i=0; $i<sizeof($users); $i++) {
      $users[$i] = new Feeligo_Se3_User_Adapter($users[$i]);
    }
    return $users;
  }
  
  protected function _row_to_se_user($row) {
    $user_id = isset($row['user_id']) ? $row['user_id'] : '';
    $user_username = isset($row['user_username']) ? $row['user_username'] : '';
    return new SEUser(Array($user_id, $user_username));
  }
  
  protected function _fetch_users($query) {
    $users = array();
    $rows = $this->_fetch_rows($query);
    foreach ($rows as $row) {
      $users[] = $this->_row_to_se_user($row);
    }
    return $users;
  }
 
}