<?php
/**
 * Plugin Name:     Search by Net Locomotion
 * Plugin URI:      https://netlocomotion.com
 * Description:     An alternative WordPress search engine
 * Author:          Net Locomotion
 * Author URI:      https://netlocomotion.com
 * Text Domain:     net-locomotion-search
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Net_Locomotion_Search
 */

// Your code starts here.

/**
* Autoload all classes
* @param string $className  The class name
*/
if (!function_exists('Net_Locomotion_Search_Autoloader')) {
  function Net_Locomotion_Search_Autoloader($className) {
    $className = str_replace('Net_Locomotion_Search_', '', $className);
    include(__DIR__ . '/classes/' . $className . '.php');
  }
  spl_autoload_register('Net_Locomotion_Search_Autoloader');
}

// $options = new Net_Locomotion_Search_Options();
$shortcodes = new Net_Locomotion_Search_Shortcodes();
