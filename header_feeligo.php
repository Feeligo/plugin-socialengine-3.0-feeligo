<?php
/*
 * Feeligo Plug-in for Social Engine 3
 *
 * Author:  Davide Bonapersona <tech@feeligo.com>
 * Version: 2.0
 */
 
defined('SE_PAGE') or exit();
require_once(str_replace('//','/',dirname(__FILE__).'/').'modules/feeligo/se3_api.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'modules/feeligo/sdk/apps/giftbar.php');


/**
 * instantiate App object
 */
$flg_app = new FeeligoGiftbarApp(Feeligo_Se3_Api::_());

/**
 * assign template variables
 */

// parameter to enable/disable rendering
$smarty->assign('flg_is_enabled', $flg_app->is_enabled());
// URLs to load CSS (base and ie7)
$smarty->assign('flg_css_url', $flg_app->css_url());
$smarty->assign('flg_css_ie7_url', $flg_app->css_url('ie7'));
// URL to load loader.js
$smarty->assign('flg_loader_js_url', $flg_app->loader_js_urL());
// javascript code to be injected in the <body> (see footer_feeligo.tpl)
$smarty->assign('flg_initialization_js', $flg_app->initialization_js());

?>