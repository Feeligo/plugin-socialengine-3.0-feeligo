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
 * @package    Feeligo_Se3_Mapper_Language
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
require_once(str_replace('//','/',dirname(__FILE__).'/').'db/row.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'languagevar.php');

/*
 * Mapper for SE3 languages
 */
 
class Feeligo_Se3_Mapper_Language extends Feeligo_Se3_Mapper_Db_Row {
 
  public function __construct($fields) {
    parent::__construct(Feeligo_Se3_Mapper_Db_Table::_('se_languages'), $fields);
  }
  
  public static function get_current() {
    return new self(array('language_id' => SELanguage::info("language_id")));
  }
  
  public static function get_by_locale($locale) {
    $rows = Feeligo_Se3_Mapper_Db_Table::_('se_languages')->fetch_all_rows_where("language_code IN ('".$locale."','".substr($locale, 0, 2)."')", null, 0, 'ORDER BY language_code DESC');
    if (sizeof($rows) == 0) return null;
    return new self($rows[0]);
  }
  
  
  
}