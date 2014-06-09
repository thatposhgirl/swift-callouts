<?php

/* cbs-admin-menu.php
 * 
 * Registers the admin page for Callouts by SwiftWP
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
include 'cbs-update-template.php';
include 'cbs-validate-data.php';
include ECBS_PATHS;

// Set menu function in admin_menu hook
add_action( 'admin_menu', 'ecbs_add_plugin_menu' );

// Loads the admin stylesheet
add_action('admin_enqueue_scripts', 'ecbs_get_admin_style');

// function to save and update template options
// found in cbs-update-template.php
add_action( 'admin_post_ecbs_update_template_options', 
        'ecbs_update_template_options' );


// Create a submenu under 'Appearance' to manage the CBS plugin
// ecbs_build_admin_menu found in cbs-admin-menu-page.php
function ecbs_add_plugin_menu() {
    add_submenu_page( 'plugins.php', 
            'Managing Callouts', 
            'Callouts Manager',
            'manage_options',
            'cbs-admin-menu',
            'ecbs_build_admin_menu' );   
    
} 

// Build the admin page for managing callouts
function ecbs_build_admin_menu() {
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    if ( !get_option( 'ecbs_callout_templates' ) ) {
        echo 'No template options!';
    } else {
        ecbs_get_head();
        ecbs_get_templates_dropdown();
        ?>
                <hr>
                <div id="ecbs-edit-area">
                    <?php
                    if ( isset( $_GET['m'] ) && $_GET['m'] == '1' ) {
                        echo '<h2 id="ecbs-updated">Template updated!</h2>';
                    } else {
                        echo 'No data to display.';
                    }
                    ?>
                </div>
                
                
        <?php
        echo '<input id="ajax_button" type="submit" value="Submit">';
        ecbs_add_admin_scripts();
    }
}

function ecbs_get_head() {
    ?>
    <div class="ecbsAdmin">
        <br>
        <br>
        <h1 class="ecbsAdmin">Easy Callouts Manager</h1>
        <br>
        <br>
        <hr>
        <br>
        <br>
        
        <?php
}

// Insert template names in admin form
function ecbs_get_templates_dropdown() {
    $ecbs_templates = get_option( ECBS_TEMPLATES );

    //echo '<pre>' . print_r($ecbs_templates) . '</pre>';
    echo '<form id="ecbs-show-templates">';
     echo '<label for="ecbs_styles">Select a Template to Edit:</label>';
    echo '<select id="ecbs_styles">';
    echo '<option value="dummy">Select Template</option>';
    foreach ( $ecbs_templates as $ecbs_template => $ecbs_template_values) {
        echo '<option value="' . $ecbs_template . '">' .
        $ecbs_template_values['ecbs-nice-name'] . '</option>';
    }
    echo '<option value="new">New Template</option>';
    echo '</select>';
    echo '<button type="button" id="ecbs-delete-template">Delete template</button>';

    echo '</form>';

}

// Loads the admin stylesheet
function ecbs_get_admin_style() {
    wp_register_style( ECBS_ADMIN_STYLESHEET, ECBS_CSS_URL . 'cbs-admin-stylesheet.css' );
    wp_enqueue_style( ECBS_ADMIN_STYLESHEET );
    
}

// Set up jQuery scripts
function ecbs_add_admin_scripts() {
    ?>
    <script src="<?php echo ECBS_JS_URL . 'cbs-edit-options.js' ?>"></script>
    <?php
}

// Gets POST data from the cbs-edit-options.js script. Uses that info
// to format a form containing the customizable template elements. Sends
// that info back to the .js script where it is processed and sent to
// the ecbs-edit-area div.
function ecbs_populate_admin_template() {

    if ( isset( $_POST['data'] ) && $_POST['data']['template'] != '' ) {

        $ecbs_template_id = $_POST['data']['template'];
        
        //echo '<pre>' . print_r($ecbs_template_id) . '</pre>';
        //return;

        if ( $ecbs_template_id === 'new' )
        {
            $ecbs_style_data = new ecbs_style_data();
            $ecbs_template = $ecbs_style_data->ecbs_get_dummy_values();
            $ecbs_template = $ecbs_template[''];
            $ecbs_template_id = '';
            

        }
        else if ( $ecbs_template_id === 'dummy' )
        {
            die( 'No data to display' );
        }
        else {
        $ecbs_templates = get_option( ECBS_TEMPLATES );
        $ecbs_template = $ecbs_templates[$ecbs_template_id];
        
        }
                    //echo '<pre>';
            //echo print_r($ecbs_template);
            //echo '</pre>';
        die( ecbs_format_template_edit_pane( $ecbs_template_id, $ecbs_template ) );
    } else {

            die( 'No data to display' );
        
    }
}

// Formats the edit pane on the admin menu after the user selects which
// template to edit
function ecbs_format_template_edit_pane( $ecbs_template_id, $template ) {
 
    echo ecbs_format_edit_head($ecbs_template_id, $template[ECBS_TEMPLATE_NAME]);

    foreach ( $template as $ecbs_style => $ecbs_value ) {
        if( $ecbs_style != 'default-content')
            $ecbs_values_form .= ecbs_style_handler( $ecbs_style, $ecbs_value );
    }
    
    $content = htmlspecialchars_decode($template['default-content']);
    
    $ecbs_values_form .= "</table>\n";
    
    $ecbs_values_form .= '</form>';
    $ecbs_values_form .=    '</div>' .
     '<div id="ecbs-textbox-area">' .
     '<p align="center"><label for="default-content">Default Content (Optional)</label></p>' .
     '<p align="center"><textarea rows="25" style="width:90%" '
            . 'id="default-content" name="default-content" form="ecbs-edit-template">' .
                      $content .
      '</textarea></p>' .
      '</div>';
    
    return $ecbs_values_form;
}

// Formats the beginning of the template edit form in the admin menu
function ecbs_format_edit_head($ecbs_template_id, $ecbs_template_name)
{
$ecbs_values_form =  
'<div id="ecbs-options-form">' .
'<form  method="post" action="admin-post.php" id="ecbs-edit-template">' .
'<input type="hidden" name="action" value="ecbs_update_template_options" />' .
        wp_nonce_field( 'ecbs-edit-templates' );

        $ecbs_values_form_temp =
            <<< ECBS_FHED
<table style="table-layout: fixed">
<tr>
<td>
<label for="shortcode-text">Shortcode</label>
</td>
<td>
[callout template="%s"]Your Text Here[/callout]
</td>
</tr>
<tr>
<td>
<label for="template-id">Template ID</lable>
</td>
<td>
<input name ="ecbs-template-id" type="text" value="%s" %s>
</td>
</tr>
ECBS_FHED;
    
    $ecbs_values_form .= sprintf(
            //wp_nonce_field( 'ecbs-edit-templates' ),
            $ecbs_values_form_temp,
            $ecbs_template_id,
            $ecbs_template_id,
            ($ecbs_template_id === '' ? '' : 'readonly="readonly"'));
    
    return $ecbs_values_form;
}

// Determines style type (px, hex, radio or shadow) and sends it to the
// correct function for formatting, then returns the formatted HTML
function ecbs_style_handler( $ecbs_style, $ecbs_value ) {

    // Formatting radio options
    if ( $ecbs_style === 'border-style' ||
            $ecbs_style === 'float' ) {
        return '<tr>' . ecbs_get_label( $ecbs_style ) .
                ecbs_format_radio_style( $ecbs_style, 
                        $ecbs_value ) . "\n";
    } 
    
    // Formatting anything with a textbox
    else {

        return ecbs_format_style( $ecbs_style, $ecbs_value );
    }
}

// Formats the form inputs for standard style values (i.e. not a box-shadow
// or a radio
function ecbs_format_style( $ecbs_style, $ecbs_value )
{  
    $ecbs_tmp_format =
            <<< TMP_FORM
<tr> %s
<td>
<input type="text" style="width:90%%" name="%s" value="%s" onLoad="this.value='%s'">
</td>
</tr>   
TMP_FORM;
    
    return sprintf($ecbs_tmp_format, 
            ecbs_get_label( $ecbs_style, $ecbs_value ),
            $ecbs_style,
            $ecbs_value,
            $ecbs_value);
            //$ecbs_style_data[ECBS_VALUE_TYPE]==='hex' ? 'class="my-color-field"' : '');
}

// Formats form fields that use radio buttons for the options
function ecbs_format_radio_style( $ecbs_style, $ecbs_value ) {
        
    $ecbs_style_data = new ecbs_style_data();
    
    $ecbs_style_formats = $ecbs_style_data->ecbs_get_style_data();
    
    $ecbs_radio_options = $ecbs_style_formats[$ecbs_style][ECBS_RADIO_OPTIONS];
    
    
    $ecbs_radio_output = '<td>';
    foreach($ecbs_radio_options as $ecbs_radio_option)
    {
        $ecbs_radio_output .= 
                '<input type="radio" name="' . 
                $ecbs_style . 
                '" value="'. $ecbs_radio_option . '" '.
                ($ecbs_radio_option === $ecbs_value ? 'checked="checked">' :
                '>') . $ecbs_radio_option . ' ';
    }
    
    $ecbs_radio_output .= '</td></tr>' . "\n";
    
    return $ecbs_radio_output;
}

// Formats the labels used for the template edit form
function ecbs_get_label( $ecbs_style ) {
    $ecbs_tmp_edit =
            <<< ECBS_TMP
<td>
<lable style="align:left" for="%s">%s</label>
</td>
ECBS_TMP;
    
    $ecbs_style_data = new ecbs_style_data();
    
    $ecbs_style_formats = $ecbs_style_data->ecbs_get_style_data();
    
    return sprintf($ecbs_tmp_edit,
            $ecbs_style,
            $ecbs_style_formats[$ecbs_style][ECBS_NICE_NAME]);
}