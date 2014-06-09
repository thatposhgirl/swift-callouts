<?php

/*
  Plugin Name: Swift Callouts (Beta)
  Plugin URI: http://swiftwp.com/swift-callouts-swiftwp-wordpress-callout-plugin/
  Description: Swift Callouts by SwiftWP allows you to add callouts into posts and pages using simple shortcode.
  Author: Rane Wallin @ SwiftWP
  Version: 0.6.0
  Author URI: http://swiftwp.com
 */

// includes
include 'includes/cbs-admin-menu.php';
include 'includes/cbs-create-shortcode.php';
include_once 'includes/cbs-constants.php';
include ECBS_PATHS;

// Hook to link the callouts stylesheet
add_action( 'wp_head', 'ecbs_link_stylesheet' );

// Hook to create default options
register_activation_hook( __FILE__, 'ecbs_set_default_options' );

register_uninstall_hook( __FILE__, 'ecbs_on_uninstall' );

// Handle AJAX
add_action( 'wp_ajax_ecbs_add_admin_scripts', 'ecbs_add_admin_scripts' );
add_action( 'wp_ajax_ecbs_populate_admin_template', 'ecbs_populate_admin_template' );
add_action( 'wp_ajax_ecbs_validate_data', 'ecbs_validate_data' );
add_action( 'wp_ajax_ecbs_delete_template', 'ecbs_delete_template' );

// Add callouts stylesheet to the head
//
function ecbs_link_stylesheet() {
    echo "<link rel=\"stylesheet\" id=\"cbs-stylesheet\" type=\"text/css\" href=\""
    . ECBS_CSS_URL . "cbs-callout-style.css\" >";
}

// Sets up the default options used if no template is selected or if the
// selected template does not exist.
//
// The template values are stored in the database using add_option or
// update_option. The structure is:
//
//              'template_id' => array( 'Template Name', array( **options** ) ),
//
// There are 17 customizable options. If the callout has a shadow, there are 
// four additional options for formatting the shadow.
//
// All options are stored in a single row on the table to improve speed
function ecbs_set_default_options() {

    if ( !current_user_can( 'activate_plugins' ) )
        return;

    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
    
    check_admin_referer( "activate-plugin_{$plugin}" );
    
// Sets up the pre-installed default values
    $ecbs_default_options = array(
        'default' => array( 
            'ecbs-nice-name' => 'Default',
            'float'=>'right', 
            'background-color' => '#997766', 
            'border-color' => '#000000',
            'border-style' => 'dashed',
             'border-width' => '5',
            'margin-bottom' => '5',
            'margin-top' => '5',
            'margin-right' => '5',
            'margin-left' => '5',
            'padding-top' => '10',
            'padding-bottom' => '10',
            'padding-right' => '10',
            'padding-left' => '10', 
            'width' => '200',
            'height' => '',
            'color' => '#220011',
            'shadow' => '10px 10px 10px #000',
            'default-content' => ''
            ),
        'sunset' => array( 
            'ecbs-nice-name' => 'Sunset',
            'float'=>'right', 
            'background-color' => '#F8D28B', 
            'border-color' => '#FC3903',
            'border-style' => 'dashed',
             'border-width' => '5',
            'margin-bottom' => '5',
            'margin-top' => '5',
            'margin-right' => '5',
            'margin-left' => '5',
            'padding-top' => '10',
            'padding-bottom' => '10',
            'padding-right' => '10',
            'padding-left' => '10', 
            'width' => '200',
            'height' => '',
            'color' => '#A6026C',
            'shadow' => '10px 10px 10px #A78D9E',
            'default-content' => ''
            ),
        'desert' => array( 
            'ecbs-nice-name' => 'Painted Desert',
            'float'=>'none', 
            'background-color' => '#FFDDAB', 
            'border-color' => '#641A00',
            'border-style' => 'solid',
             'border-width' => '5',
            'margin-bottom' => '5',
            'margin-top' => '5',
            'margin-right' => '5',
            'margin-left' => '5',
            'padding-top' => '10',
            'padding-bottom' => '10',
            'padding-right' => '10',
            'padding-left' => '10', 
            'width' => '',
            'height' => '',
            'color' => '#0F1F2B',
            'shadow' => '10px 10px 10px #DE410C',
            'default-content' => ''
            ),
        'twilight' => array( 
            'ecbs-nice-name' => 'Twilight Hour',
            'float'=>'left', 
            'background-color' => '#89A185', 
            'border-color' => '#07072B',
            'border-style' => 'dotted',
             'border-width' => '5',
            'margin-bottom' => '5',
            'margin-top' => '5',
            'margin-right' => '5',
            'margin-left' => '5',
            'padding-top' => '10',
            'padding-bottom' => '10',
            'padding-right' => '10',
            'padding-left' => '10', 
            'width' => '200',
            'height' => '',
            'color' => '#070012',
            'shadow' => '10px 10px 10px #044559',
            'default-content' => ''
            ) );



    //delete_option( ECBS_TEMPLATES ); // for testing purposes only
    add_option( ECBS_TEMPLATES, $ecbs_default_options );
}

function ecbs_on_uninstall() {
    
    if ( !current_user_can( 'activate_plugins' ) )
        return;
    
    check_admin_referer( 'bulk-plugins' );


    delete_option( ECBS_TEMPLATES );
}
