<?php

/* cbs-constants.php
 * 
 * Plugin constants
 * 
 * PHP version 5
 * 
 * @author      Rane Wallin
 * @copyright   SwiftWP
 * @license     GPLv2
 * @version     0.6.0
 * @link        http://swiftwp.com/swift-callouts-swiftwp-wordpress-callout-plugin/
 * 
 */

define("ECBS_VALUE_TYPE", 0);
define("ECBS_NICE_NAME", 1);

define("ECBS_TEMPLATE_NAME", 0);
define("ECBS_RADIO_OPTIONS", 2);

define("ECBS_PX_FAIL", "%s: Needs to be a number.\n" );
define("ECBS_HEX_FAIL", "%s: Needs to be a hexidecimal value.\n" );
define("ECBS_ID_FAIL", "The id can only contain letters and numbers, no spaces, dashes, underscores, or special characters.\n");
define("ECBS_NAME_FAIL", "Template name can only contain letters, numbers and spaces. No dashes, underscores, or special characters.\n");

define("ECBS_TEMPLATES", "ecbs_callout_templates");
define("ECBS_ADMIN_STYLESHEET", "ecbs-admin-stylesheet");
define("ECBS_PATHS", dirname(__FILE__) . '/../paths.php');


