<?php

/* cbs-style_class.php
 * 
 * Contains formatting information for callout templates
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

class ecbs_style_data {

    public function __construct() {
        
    }

    // data type and nice name for each editable style attribute
    function ecbs_get_style_data() {
        return array(
            'ecbs-nice-name' => array( 'text', 'Template Name' ),
            'float' => array( 'radio', 'Float', array('right', 'left', 'none') ),
            'background-color' => array( 'hex', 'Background Color' ),
            'border-color' => array( 'hex', 'Border Color' ),
            'border-style' => array( 'radio', 'Border Style',
                array( 'dashed', 'dotted', 'solid', 'double',
                    'inset', 'outset', 'groove', 'ridge', 'none') ),
            'border-width' => array( 'px', 'Border Width' ),
            'margin-bottom' => array( 'px', 'Bottom Margin' ),
            'margin-top' => array( 'px', 'Top Margin' ),
            'margin-right' => array( 'px', 'Right Margin' ),
            'margin-left' => array( 'px', 'Left Margin' ),
            'padding-top' => array( 'px', 'Top Padding' ),
            'padding-bottom' => array( 'px', 'Bottom Padding' ),
            'padding-right' => array( 'px', 'Right Padding' ),
            'padding-left' => array( 'px', 'Left Padding' ),
            'width' => array( 'px', 'Width' ),
            'height' => array( 'px', 'Height' ),
            'color' => array( 'hex', 'Text Color' ),
            'shadow' => array( 'shadow', 'Shadow' ),
            'default-content' => array( 'textarea', 'Default Content' ) );
    }

    // Creates a formatted array with blank values used to setup a new
    // template
    function ecbs_get_dummy_values() {
        return array( '' => array(
                'ecbs-nice-name' => '',
                'float' => 'none',
                'background-color' => '',
                'border-color' => '',
                'border-style' => 'none',
                'border-width' => '',
                'margin-bottom' => '',
                'margin-top' => '',
                'margin-right' => '',
                'margin-left' => '',
                'padding-top' => '',
                'padding-bottom' => '',
                'padding-right' => '',
                'padding-left' => '',
                'width' => '',
                'height' => '',
                'color' => '',
                'shadow' => '',
                'default-content' => '' ) );
    }

}
