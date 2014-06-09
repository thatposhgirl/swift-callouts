/* cbs-edit-options.js
 * 
 * jQuery scripts used to manage Template Manager options
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

var $j = jQuery.noConflict();
$j(document).ready(function() {

    // Updates the ecbs-eit-area div when a template is 
    // selected from a drop box.
    $j("#ecbs_styles").change(function() {
        var str = "";
        $j("#ecbs_styles option:selected").each(function() {
            str = $j("#ecbs_styles").val();
            
            // if the selected template has the id 'dummy' it means it is
            // really the "Select Template" option, which is not associated
            // with any actual template, so hide the submit button
            if (str === 'dummy')
                $j("#ajax_button").hide();

            try {
                $j.post(ajaxurl, {
                    data: {template: str},
                    // found in cbs-admin-menu.php
                    action: 'ecbs_populate_admin_template'
                }, function(response) {

                    //
                    if (str !== 'dummy')
                    {
                        // When an actual template is selected, show the
                        // edit area and submit button
                        $j("#ajax_button").show();
                        $j("#ecbs-edit-area").html(response);
                    }
                }
                );
            }
            catch (err) {
                alert(err);
            }
        });
    })
            .change();

    // Before submitting the form, the data in the form needs to be validated
    // if the data validates, the form is submitted by calling submit(),
    // otherwise, an alert is sent to the user showing the fields that did
    // not validate and why
    $j("#ajax_button").on('click', function() {
        try {
            $j.post(ajaxurl, {
                // found in cbs-validate-data.php
                action: 'ecbs_validate_data',
                data: {form_inputs: $j('#ecbs-edit-template').serialize()},
                //dataType: "json"
            }, function(response) {
                if (response === "success")
                {
                    $j("#ecbs-edit-template").submit();
                }
                else {
                    alert(response);
                }
            }
            );
        }
        catch (err) {
            alert(err);
        }
    });

    // Delete a template
    $j("#ecbs-delete-template").on('click', function() {
        if (confirm("Are you sure you want to delete this template?") )
        {
        $j.post(ajaxurl, {
            action: 'ecbs_delete_template',
            data: {template: $j("#ecbs_styles").val()},
        }, function(response) {
            if (response != 'Template deleted!')
                alert(response);
            else
                window.location.reload();
              
              
        });
    }
    });


});