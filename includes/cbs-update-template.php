<?php

/* cbs-update-template.php
 * 
 * Adds new teplates and deletes existing templates
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

// After all the form values have been validated, it's all sent here to be
// formatted and put into the database using update_option()
function ecbs_update_template_options() {

    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    $ecbs_post_data = $_POST;

    if ( !wp_verify_nonce( $ecbs_post_data['_wpnonce'],
                    'ecbs-edit-templates' ) ) {
        wp_die( __( 'You do not have permission to update this page.' ) );
    }

    $ecbs_template_id = $ecbs_post_data['ecbs-template-id'];

    $ecbs_style_class = new ecbs_style_data();
    $ecbs_style_data = $ecbs_style_class->ecbs_get_style_data();
    
    // remove slashes in case magic quotes is enabled and then use
    // htmlspecialchars to encode the HTML using entities
    if ($ecbs_post_data['default-content'] !== '' )
        $ecbs_post_data['default-content'] = htmlspecialchars(stripslashes($ecbs_post_data['default-content']), ENT_QUOTES);

    // find the values from the post data that are needed to create
    // the template and put them into a template values array
    foreach ( $ecbs_post_data as $ecbs_style => $ecbs_value ) {
        if ( array_key_exists( $ecbs_style,
                        $ecbs_style_data ) )
            $ecbs_template_data[$ecbs_style] = $ecbs_value;
    }
    
    // add the template values to the template array in the format
    // template_id => array( **template values ** )
    $ecbs_format_template = array( $ecbs_template_id => $ecbs_template_data );

    $ecbs_all_options = get_option( ECBS_TEMPLATES );

    // User is updating an existing template
    if ( array_key_exists( $ecbs_template_id,
                    $ecbs_all_options ) ) {
        // remove the old template from the array so we can replace it
        // with the new one
        unset( $ecbs_all_options[$ecbs_template_id] );
    }

    // add the new array of style values to the end of the options
    $ecbs_all_options += $ecbs_format_template;

    // sort the array by keys before updating the options to keep the order
    // consistent on the user's side
    ksort( $ecbs_all_options );


    // Put the options into the database
    update_option( ECBS_TEMPLATES,
            $ecbs_all_options );

    // Send the user back to the callouts manager after template
    // update is complete.
    wp_redirect( admin_url( '/plugins.php?page=cbs-admin-menu&m=1' ) );
}

function ecbs_delete_template() {
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    if ( !isset( $_POST ) )
        wp_die( __( 'No data received!' ) );

    $ecbs_post_data = $_POST['data'];

    if ( $ecbs_post_data['template'] === 'default' )
        wp_die( __( 'You can\'t delete the default template!' ) );

    if ( $ecbs_post_data['template'] === 'dummy' )
        wp_die( __( 'Please select a template to delete.' ) );

    $ecbs_templates = get_option( ECBS_TEMPLATES );

    if ( array_key_exists( $ecbs_post_data['template'], $ecbs_templates ) ) {
        unset( $ecbs_templates[$ecbs_post_data['template']] );
        
        ksort( $ecbs_templates );

        update_option( ECBS_TEMPLATES, $ecbs_templates );

        die( 'Template deleted!' );
    } else
        echo wp_die( __( 'Unable to delete template!' ) );
}
