<?php

/* cbs-validate-data.php
 * 
 * Validates data when user submits form to update templates
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

include_once 'cbs-style-class.php';

function ecbs_validate_data() {
    if ( !isset( $_POST ) ) {
        die ( 'No data received.' );
    }

    // Take the POST data and parse the form_inputs data sent via AJAX
    // in a key => value array
    $ecbs_params = $_POST['data'];
    $ecbs_params = $ecbs_params['form_inputs'];
    parse_str( $ecbs_params, $ecbs_values );

    // Verify the request is coming from the right place
    if ( !wp_verify_nonce( $ecbs_values['_wpnonce'], 'ecbs-edit-templates' ) ) {
        die( 'You do not have permission to update this page.' );
    }

    // Get the style data from cbs-style-class.php
    $ecbs_the_values = new ecbs_style_data();
    $ecbs_style_data = $ecbs_the_values->ecbs_get_style_data();

    die ( ecbs_find_validation_problems( $ecbs_values, $ecbs_style_data ) );

}

function ecbs_find_validation_problems( $ecbs_values, $ecbs_style_data ){

    // Verify template id has no spaces, underscores or dashes
    if ( !ecbs_validate_id( $ecbs_values['ecbs-template-id'] ) ) {
        $ecbs_what_is_wrong .= ECBS_ID_FAIL;
    }

    // Verify the template name contains no underscores or dashes
    if ( !ecbs_validate_name( $ecbs_values['ecbs-nice-name'] ) ) {
        $ecbs_what_is_wrong .= ECBS_NAME_FAIL;
    }

    // Loop through the form_inputs and validate the input
    foreach ( $ecbs_values as $ecbs_style => $ecbs_value ) {
        if ( array_key_exists( $ecbs_style, $ecbs_style_data ) && 
                $ecbs_style_data[$ecbs_style][ECBS_VALUE_TYPE] !==
                'radio' && !ecbs_validate_style_data( $ecbs_value,
                        $ecbs_style_data[$ecbs_style][ECBS_VALUE_TYPE] ) ) {

            $ecbs_what_is_wrong .=
                    ecbs_get_what_is_wrong( $ecbs_style_data[$ecbs_style][ECBS_NICE_NAME],
                    $ecbs_style_data[$ecbs_style][ECBS_VALUE_TYPE] );
        }
    }

    if ( !isset( $ecbs_what_is_wrong ) )
        return ( 'success' );
    else
        return ( $ecbs_what_is_wrong );
}

function ecbs_validate_style_data( $ecbs_value, $ecbs_type ) {
    $ecbs_is_valid = TRUE;

    switch ( $ecbs_type ) {
        case 'px': $ecbs_is_valid = ecbs_validate_px( $ecbs_value );
            break;
        case 'hex': $ecbs_is_valid = ecbs_validate_hex( $ecbs_value );
            break;
        case 'shadow': $ecbs_is_valid = ecbs_validate_shadow( $ecbs_value );
        // Type is radio, which doesn't require validation
        default: $ecbs_is_valid = TRUE;
    }

    return $ecbs_is_valid;
}

function ecbs_validate_px( $ecbs_value ) {
    return preg_match( '/^-?[0-9]*$/', $ecbs_value ) ||
            preg_match( '/^-?[0-9]*\s*px$/i', $ecbs_value );
}

function ecbs_validate_hex( $ecbs_value ) {
    return ecbs_valid_hex( $ecbs_value ) ||
            ecbs_valid_hex( '#' . $ecbs_value ) ||
            $ecbs_value === '';
}

function ecbs_valid_hex( $ecbs_value ) {
    return preg_match( '/^#[a-f0-9]{6}$/i', $ecbs_value ) ||
            preg_match( '/^#[a-f0-9]{3}$/i', $ecbs_value );
}

function ecbs_validate_id( $ecbs_value ) {
    return preg_match( '/^[a-zA-Z0-9]+$/', $ecbs_value );
}

function ecbs_validate_name( $ecbs_value ) {
    return preg_match( '/^[a-z0-9 ]*$/i', $ecbs_value ) &&
            ($ecbs_value !== '');
}

function ecbs_validate_shadow( $ecbs_value ) {
    return preg_match( '/^[0-9]*px [0-9]*px$/',
                    $ecbs_value ) ||
            preg_match( '/^[0-9]*px [0-9]*px [0-9]*px$/',
                    $ecbs_value ) ||
            preg_match( '/^[0-9]*px [0-9]*px [0-9]*px [a-f0-9]{6}$/i',
                    $ecbs_value ) ||
            preg_match( '/^[0-9]*px [0-9]*px [0-9]*px [a-f0-9]{3}$/i',
                    $ecbs_value );
}

function ecbs_get_what_is_wrong( $ecbs_style, $ecbs_type ) {
    if ( $ecbs_type === 'px' )
        $error_string = ECBS_PX_FAIL;
    else
        $error_string = ECBS_HEX_FAIL;

    return sprintf( $error_string, $ecbs_style );
}
