<?php
/*
Plugin Name: Polls CP
Plugin URI: https://wordpress.dwbooster.com/forms/cp-polls
Description: Create classic polls and advanced polls with dependant questions.
Version: 1.0.78
Author: CodePeople
Author URI: https://wordpress.dwbooster.com/forms/cp-polls
License: GPL
Text Domain: cp-polls
*/

define('CP_POLLS_DEFER_SCRIPTS_LOADING', (get_option('CP_POLLS_LOAD_SCRIPTS',"0") == "1"?true:false));

define('CP_POLLS_DEFAULT_form_structure', '[[{"form_identifier":"","name":"fieldname1","shortlabel":"","index":0,"ftype":"fradio","userhelp":"","userhelpTooltip":false,"csslayout":"","title":"Select a Choice","layout":"one_column","required":true,"choiceSelected":"","showDep":false,"choices":["First Choice","Second Choice","Third Choice"],"choicesVal":["First Choice","Second Choice","Third Choice"],"choicesDep":[[],[],[]],"fBuild":{}}],[{"title":"Sample Poll","description":"Here is a sample question for the poll.","formlayout":"top_aligned","formtemplate":""}]]');
define('CP_POLLS_DEFAULT_form_structure2', '[[{"form_identifier":"","name":"fieldname1","shortlabel":"","index":0,"ftype":"fradio","userhelp":"","userhelpTooltip":false,"csslayout":"","title":"Do you like this plugin?","layout":"one_column","required":true,"choiceSelected":"","showDep":true,"choices":["Yes","No"],"choicesVal":["Yes","No"],"choicesDep":[["fieldname2"],["fieldname3"]],"fBuild":{}},{"form_identifier":"","name":"fieldname2","shortlabel":"","index":1,"ftype":"fradio","userhelp":"","userhelpTooltip":false,"csslayout":"","title":"Please select why you like this plugin?","layout":"one_column","required":true,"choiceSelected":"","showDep":false,"choices":["The drag and drop form builder","The reports","The availability of mutliple features"],"choicesVal":["The drag and drop form builder","The reports","The availability of mutliple features"],"choicesDep":[[],[],[]],"fBuild":{}},{"form_identifier":"","name":"fieldname3","shortlabel":"","index":2,"ftype":"fradio","userhelp":"","userhelpTooltip":false,"csslayout":"","title":"Please select why you don\'t like this plugin?","layout":"one_column","required":true,"choiceSelected":"","showDep":false,"choices":["I want more features","Form builder isn\'t enough","More reports needed"],"choicesVal":["I want more features","Form builder isn\'t enough","More reports needed"],"choicesDep":[[],[],[]],"fBuild":{}}],[{"title":"Poll with dependent/cascade fields","description":"Sample poll: Plugin evaluation. Note that this poll is just a sample, <b>the results won\'t be sent</b> to the developers.","formlayout":"top_aligned","formtemplate":""}]]');

define('CP_POLLS_POLL_LIMIT', '2');
define('CP_POLLS_POLL_PRIVATE_REPORTS', 'false');
define('CP_POLLS_POLL_SEE_RESULTS', 'true');
define('CP_POLLS_POLL_TEXT_SEERES', 'See results');
define('CP_POLLS_POLL_TEXT_PRIVATE', 'Thank you for your vote!');
define('CP_POLLS_POLL_TEXT_VOTES', 'votes');

define('CP_POLLS_DEFAULT_fp_subject', 'Contact from the blog...');
define('CP_POLLS_DEFAULT_fp_inc_additional_info', 'true');
define('CP_POLLS_DEFAULT_fp_return_page', '');
define('CP_POLLS_DEFAULT_fp_message', "The following contact message has been sent:\n\n<%INFO%>\n\n");

define('CP_POLLS_DEFAULT_cu_enable_copy_to_user', 'false');
define('CP_POLLS_DEFAULT_cu_user_email_field', '');
define('CP_POLLS_DEFAULT_cu_subject', 'Confirmation: Message received...');
define('CP_POLLS_DEFAULT_cu_message', "Thank you for your message. We will reply you as soon as possible.\n\nThis is a copy of the data sent:\n\n<%INFO%>\n\nBest Regards.");
define('CP_POLLS_DEFAULT_email_format','text');

define('CP_POLLS_DEFAULT_vs_use_validation', 'true');

define('CP_POLLS_DEFAULT_vs_text_is_required', 'This field is required.');
define('CP_POLLS_DEFAULT_vs_text_is_email', 'Please enter a valid email address.');

define('CP_POLLS_DEFAULT_vs_text_datemmddyyyy', 'Please enter a valid date with this format(mm/dd/yyyy)');
define('CP_POLLS_DEFAULT_vs_text_dateddmmyyyy', 'Please enter a valid date with this format(dd/mm/yyyy)');
define('CP_POLLS_DEFAULT_vs_text_number', 'Please enter a valid number.');
define('CP_POLLS_DEFAULT_vs_text_digits', 'Please enter only digits.');
define('CP_POLLS_DEFAULT_vs_text_max', 'Please enter a value less than or equal to {0}.');
define('CP_POLLS_DEFAULT_vs_text_min', 'Please enter a value greater than or equal to {0}.');

define('CP_POLLS_DEFAULT_cv_enable_captcha', 'false');
define('CP_POLLS_DEFAULT_cv_width', '170');
define('CP_POLLS_DEFAULT_cv_height', '60');
define('CP_POLLS_DEFAULT_cv_chars', '5');
define('CP_POLLS_DEFAULT_cv_font', 'font-1.ttf');
define('CP_POLLS_DEFAULT_cv_min_font_size', '25');
define('CP_POLLS_DEFAULT_cv_max_font_size', '35');
define('CP_POLLS_DEFAULT_cv_noise', '200');
define('CP_POLLS_DEFAULT_cv_noise_length', '4');
define('CP_POLLS_DEFAULT_cv_background', 'ffffff');
define('CP_POLLS_DEFAULT_cv_border', '000000');
define('CP_POLLS_DEFAULT_cv_text_enter_valid_captcha', 'Please enter a valid captcha code.');

require_once 'banner.php';
$codepeople_promote_banner_plugins[ 'cp-polls' ] = array( 'plugin_name' => 'CP Polls', 'plugin_url'  => 'https://wordpress.org/support/plugin/cp-polls/reviews/?filter=5#new-post');

/* initialization / install */

include_once dirname( __FILE__ ) . '/classes/cp-base-class.inc.php';
include_once dirname( __FILE__ ) . '/cp-main-class.inc.php';

$cp_pollsnv_plugin = new CP_Polls;

register_activation_hook(__FILE__, array($cp_pollsnv_plugin,'install') ); 
add_action( 'media_buttons', array($cp_pollsnv_plugin, 'insert_button'), 11);
add_action( 'init', array($cp_pollsnv_plugin, 'data_management'));

function cppolls_plugin_init() {
   load_plugin_textdomain( 'cppolls', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function cppolls_plugin_init_loaded() {
   $ao_options = get_option('autoptimize_js_exclude',"seal.js, js/jquery/jquery.js");
   if (!strpos($ao_options,'stringify.js'))
      update_option('autoptimize_js_exclude',"jQuery.stringify.js,jquery.validate.js,".$ao_options);
}

add_action('init', 'cppolls_plugin_init');
add_action('plugins_loaded', 'cppolls_plugin_init_loaded');

//START: activation redirection 
function cppolls_activation_redirect( $plugin ) {
    if(
        $plugin == plugin_basename( __FILE__ ) &&
        (!isset($_POST["action"]) || $_POST["action"] != 'activate-selected') &&
        (!isset($_POST["action2"]) || $_POST["action2"] != 'activate-selected') 
      )
    {
        exit( wp_redirect( admin_url( 'admin.php?page=CP_Polls' ) ) );
    }
}
//add_action( 'activated_plugin', 'cppolls_activation_redirect' );
//END: activation redirection 

if ( is_admin() ) {    
    add_action('admin_enqueue_scripts', array($cp_pollsnv_plugin,'insert_adminScripts'), 1);    
    add_filter("plugin_action_links_".plugin_basename(__FILE__), array($cp_pollsnv_plugin,'plugin_page_links'));   
    add_action('admin_menu', array($cp_pollsnv_plugin,'admin_menu') );
    add_action('enqueue_block_editor_assets', array($cp_pollsnv_plugin,'gutenberg_block') );
} else {    
    add_shortcode( $cp_pollsnv_plugin->shorttag, array($cp_pollsnv_plugin, 'filter_content') );    
} 

// register gutemberg block
if (function_exists('register_block_type'))
{
    register_block_type('cppolls/form-rendering', array(
                        'attributes'      => array(
                                'formId'    => array(
                                    'type'      => 'string'
                                ),
                                'instanceId'    => array(
                                    'type'      => 'string'
                                ),
                            ),
                        'render_callback' => array($cp_pollsnv_plugin, 'render_form_admin')
                    )); 
}

// optional opt-in deactivation feedback
require_once 'cp-feedback.php'; 

// code for compatibility with third party scripts
add_filter('litespeed_cache_optimize_js_excludes', 'cppolls_litespeed_cache_optimize_js_excludes' );
function cppolls_litespeed_cache_optimize_js_excludes($options)
{
    return  "jquery.validate.min.js\njQuery.stringify.js\njquery.validate.js\njquery.js\n".$options;
}

// code for compatibility with third party scripts
add_filter('option_sbp_settings', 'cppolls_sbp_fix_conflict' );
function cppolls_sbp_fix_conflict($option)
{
    if(!is_admin())
    {
       if(is_array($option) && isset($option['jquery_to_footer'])) 
           unset($option['jquery_to_footer']);
    }
    return $option;
}

?>