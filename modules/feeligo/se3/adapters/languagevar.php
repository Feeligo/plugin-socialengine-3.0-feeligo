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
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../mappers/language.php');

 
class Feeligo_Se3_Adapter_Languagevar {
 
  public function __construct($key, $i18n) {
    $this->_id = null;
    $this->_key = $key;
    $this->_i18n = $i18n;
    $this->_mappers = array();
    
    // lookup id in database
    $mappers = Feeligo_Se3_Mapper_Languagevar::find_all_by_key($this->_key);
    if (sizeof($mappers) > 0) {
      $this->_id = $mappers[0]->id();
    }else{
      $this->_id = Feeligo_Se3_Mapper_Languagevar::next_available_id();
    }
    
    // create language vars for each translation
    foreach($this->_i18n as $locale => $message_body) {
      // get the language specified by the $locale
      $language = Feeligo_Se3_Mapper_Language::get_by_locale($locale);
      // if the language is in use, create the language var
      if ($language !== null) {
        $fields = array(
          'languagevar_id' => $this->id(),
          'languagevar_language_id' => $language->id(),
          'languagevar_value' => $message_body,
          'languagevar_default' => $this->key()
        );
        
        // update the already fetched mappers or create new ones
        $found = false;
        if (sizeof($mappers) > 0) {
          foreach($mappers as $mapper) {
            if ($mapper->get('languagevar_language_id') == $fields['languagevar_language_id']) {
              $found = true;
              $mapper->update($fields);
              break;
            }
          }
        }
        if (!$found) {
          $mapper = new Feeligo_Se3_Mapper_Languagevar($fields);
        }
        
        $mapper->save();
        $this->_mappers[$locale] = $mapper;
          
        //$this->_mappers[$locale] = Feeligo_Se3_Mapper_Languagevar::create_or_update($this->_id, $language->id(), $message_body, $key.'_body'); // NOT USED ANYMORE
      }
    }
  
  }
  
  public function id() {
    return $this->_id;
  }
  
  public function key() {
    return $this->_key;
  }
  
}