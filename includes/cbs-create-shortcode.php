<?php

/* cbs-create-shortcode.php
 * 
 * Creates the shortcode for the Easy Callouts by SwiftWP plugin
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

include_once 'cbs-constants.php';

// Sets up shortcode for [callout]Callout text here.[/callout]
add_shortcode( 'callout',
        'ecbs_insert_callout' );

// Creates the callout
function ecbs_insert_callout( $atts, $content ) {

    // If somehow this shortcode is used before any templates have been
    // added to the user options, then default to using the included
    // stylesheet.
    //
    // Since the default options are added on activation of the plugin,
    // this should not happen unless something has gone terribly wrong
    if ( !get_option( 'ecbs_callout_templates' ) ) {
        return '<div id="callout">' . do_shortcode( $content ) . '</div>';
    } else {
        $ecbs_callout_template_options = get_option( 'ecbs_callout_templates' );
    }

    //echo '<pre>' . print_r($atts) . '</pre>';
    // If the user hasn't specified a specific callout template set the
    // template to default
    if ( !is_array( $atts ) ) {
        $ecbs_style_template = 'default';
    } else {
        $ecbs_style_template = $atts['template'];
        //echo '<pre>' . print_r($ecbs_style_template) . '</pre>';
    }

    // If the user is trying to use a template that does not exist (usually
    // due to a type), then use the default template
    if ( !array_key_exists( $ecbs_style_template,
                    $ecbs_callout_template_options ) ) {
        $ecbs_style_template = 'default';
    }

    return ecbs_build_callout_div(
            $ecbs_callout_template_options[$ecbs_style_template],
            $content,
            $atts );
}

// Uses the template to build a div for the callout that contains the user's
// desired style elements.
function ecbs_build_callout_div( $ecbs_style_template, $content,
        $ecbs_style_overrides ) {

    // WordPress shortcode doesn't work with hyphens, so the overrides
    // use underscores that have to be turned back into hyphens 
    if ( is_array( $ecbs_style_overrides ) )
        $ecbs_style_overrides = ecbs_hyphenate_this( $ecbs_style_overrides );

    $ecbs_style_data = new ecbs_style_data();
    $ecbs_style_keys = $ecbs_style_data->ecbs_get_style_data();

    //echo '<pre>' . print_r( $ecbs_style_overrides ) . '</pre>';
    //return;
    // Create each style element in the form 'key: value; 
    foreach ( $ecbs_style_template as $ecbs_style => $ecbs_value ) {

        // Handle inline template overrides
        if ( isset( $ecbs_style_overrides ) && is_array( $ecbs_style_overrides ) &&
                array_key_exists( $ecbs_style,
                        $ecbs_style_overrides ) ) {
            $ecbs_value = $ecbs_style_overrides[$ecbs_style];
        }

        // Special Handling for box shadows
        if ( $ecbs_style === 'shadow' && $ecbs_value != '' ) {

            $ecbs_style_tag .= ecbs_format_shadow( $ecbs_value,
                    $ecbs_style_overrides );
        } else if ( $ecbs_value !== '' && $ecbs_style !== 'default-content' ) {
            $ecbs_style_tag .= $ecbs_style .
                    ': ' . ecbs_format_value( $ecbs_value,
                            $ecbs_style_keys[$ecbs_style][ECBS_VALUE_TYPE] ) . '; ';
        }
    }

    // return the formatted callout div after decoding the default value
    return '<div style="' . $ecbs_style_tag . '" >' .
            do_shortcode( htmlspecialchars_decode($ecbs_style_template['default-content']) . $content ) .
            '</div>';
}

function ecbs_format_value( $ecbs_value, $ecbs_value_type ) {
    if ( $ecbs_value_type === 'px' ) {
        if ( strpos( $ecbs_value,
                        'px' ) === FALSE ) {
            //echo '<pre>test</pre>';
            $ecbs_value .= 'px';
        }
    }

    if ( $ecbs_value_type === 'hex' ) {
        if ( strpos( $ecbs_value,
                        '#' ) === FALSE ) {
            $ecbs_value = '#' . $ecbs_value;
        }
    }

    return $ecbs_value;
}

// shortcode doesn't handle dashes well, so the user needs to use
// underscores in the style name in place of dahses. This replaces the
// underscores with dashes.
function ecbs_hyphenate_this( $ecbs_overrides ) {

    foreach ( $ecbs_overrides as $ecbs_key => $ecbs_value ) {
        $ecbs_new_key = str_replace( '_',
                '-',
                $ecbs_key );
        $ecbs_new_overrides[$ecbs_new_key] = $ecbs_value;
    }

    return $ecbs_new_overrides;
}

function ecbs_format_shadow( $ecbs_value, $ecbs_overrides ) {
    if ( is_array( $ecbs_overrides ) && array_key_exists( 'shadow',
                    $ecbs_overrides ) ) {
        $box_shadow_style = $ecbs_overrides['shadow'];
    } else {
        $box_shadow_style = $ecbs_value;
    }

    $ecbs_value = 'box-shadow: ' . $box_shadow_style . '; ' .
            '-webkit-box-shadow: ' . $box_shadow_style . '; ' .
            '-moz-box-shadow: ' . $box_shadow_style . '';

    return $ecbs_value;
}
