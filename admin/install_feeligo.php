<?php
/*
 * Installer script for the Feeligo GiftBar plugin
 *
 * to run this script, go to Admin -> View Plugins
 * and click "Install Plugin" under Feeligo GiftBar 2.0
 *
 * then delete this script for security.
 *
 * version: 2.0
 */

$_lk_PluginName = 690690029;
$lang = array(
  $_lk_PluginName => 'Feeligo GiftBar'
);

foreach ($lang as $key => $value) {
  $sql = "
  INSERT IGNORE INTO 
    `se_languagevars` (`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`)
  VALUES
    ($key, 1, '$value', '')
  ";
  $database->database_query($sql);
}



$plugin_name = $lang[$_lk_PluginName];
$plugin_version = "2.0";
$req_core_version = "3.01";
$plugin_type = "feeligo";
$plugin_desc = "Enables people on your network to send virtual gifts to each other.";
$plugin_icon = "feeligo.png";
$plugin_menu_title = $_lk_PluginName;
$plugin_pages_main = "690690029<!>feeligo.png<!>admin_feeligo.php<~!~>";
$plugin_pages_level = "";
$plugin_url_htaccess = "http://www.feeligo.com";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';


if($install == "feeligo"){
    
  
	//check if plugin was already installed
	$sql = "SELECT * FROM se_plugins WHERE plugin_type='$plugin_type' LIMIT 1";
	$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
	
	$plugin_info = array();
	if( $database->database_num_rows($resource) )
		$plugin_info = $database->database_fetch_assoc($resource);
	
	//install plugin
	if( !$plugin_info ) {
		$database->database_query("INSERT INTO se_plugins (plugin_name,
			plugin_version,
			plugin_type,
			plugin_desc,
			plugin_icon,
			plugin_menu_title,
			plugin_pages_main,
			plugin_pages_level,
			plugin_url_htaccess
			) VALUES (
			'$plugin_name',
			'$plugin_version',
			'$plugin_type',
			'".str_replace("'", "\'", $plugin_desc)."',
			'$plugin_icon',
			$plugin_menu_title,
			'$plugin_pages_main',
			'',
			'$plugin_url_htaccess')");
	
	
	//update plugin
	} else {
		$database->database_query("UPDATE se_plugins SET plugin_name='$plugin_name',
			plugin_version='$plugin_version',
			plugin_desc='".str_replace("'", "\'", $plugin_desc)."',
			plugin_icon='$plugin_icon',
			plugin_menu_title=$plugin_menu_title,
			plugin_pages_main='$plugin_pages_main',
			plugin_pages_level='',
			plugin_url_htaccess='$plugin_url_htaccess' WHERE plugin_type='$plugin_type'");
	}	
}

?>