<?php

class CP_Polls extends CP_POLLS_BaseClass {

    private $menu_parameter = 'CP_Polls';
    private $prefix = 'CP_Polls';
    private $plugin_name = 'CP Polls';
    private $plugin_URL = 'https://wordpress.dwbooster.com/forms/cp-polls';
    protected $table_items = "cppolls_forms";
    public $table_messages = "cppolls_messages";
    private $print_counter = 1;
    private $include_user_data_csv = false;

    public $shorttag = 'CP_POLLS';

    function _install() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        $charset_collate = $wpdb->get_charset_collate();

        $results = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix.$this->table_messages."'");
        if (!count($results))
        {
            $sql = "CREATE TABLE ".$wpdb->prefix.$this->table_messages." (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                formid INT NOT NULL,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                ipaddr VARCHAR(250) DEFAULT '' NOT NULL,
                notifyto VARCHAR(250) DEFAULT '' NOT NULL,
                data mediumtext,
                posted_data mediumtext,
                UNIQUE KEY id (id)
            ) ".$charset_collate.";";
            $wpdb->query($sql);
        }

        $results = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix.$this->table_items."'");
        if (!count($results))
        {
            $sql = "CREATE TABLE ".$wpdb->prefix.$this->table_items." (
                 id mediumint(9) NOT NULL AUTO_INCREMENT,

                 form_name VARCHAR(250) DEFAULT '' NOT NULL,

                 form_structure mediumtext,

                 poll_limit VARCHAR(10) DEFAULT '' NOT NULL,
                 poll_private_reports VARCHAR(10) DEFAULT '' NOT NULL,
                 poll_see_results VARCHAR(10) DEFAULT '' NOT NULL,
                 poll_text_seeres VARCHAR(250) DEFAULT '' NOT NULL,
                 poll_text_private VARCHAR(250) DEFAULT '' NOT NULL,
                 poll_text_votes VARCHAR(250) DEFAULT '' NOT NULL,

                 fp_from_email VARCHAR(250) DEFAULT '' NOT NULL,
                 fp_destination_emails text,
                 fp_subject VARCHAR(250) DEFAULT '' NOT NULL,
                 fp_inc_additional_info VARCHAR(10) DEFAULT '' NOT NULL,
                 fp_return_page VARCHAR(250) DEFAULT '' NOT NULL,
                 fp_message text,
                 fp_emailformat VARCHAR(10) DEFAULT '' NOT NULL,

                 cu_enable_copy_to_user VARCHAR(10) DEFAULT '' NOT NULL,
                 cu_user_email_field VARCHAR(250) DEFAULT '' NOT NULL,
                 cu_subject VARCHAR(250) DEFAULT '' NOT NULL,
                 cu_message text,
                 cu_emailformat VARCHAR(10) DEFAULT '' NOT NULL,
                 fp_emailfrommethod VARCHAR(10) DEFAULT '' NOT NULL,

                 vs_use_validation VARCHAR(10) DEFAULT '' NOT NULL,
                 vs_text_is_required VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_is_email VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_datemmddyyyy VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_dateddmmyyyy VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_number VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_digits VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_max VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_min VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_submitbtn VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_previousbtn VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_nextbtn VARCHAR(250) DEFAULT '' NOT NULL,                 

                 rep_enable VARCHAR(10) DEFAULT '' NOT NULL,
                 rep_days VARCHAR(10) DEFAULT '' NOT NULL,
                 rep_hour VARCHAR(10) DEFAULT '' NOT NULL,
                 rep_emails text,
                 rep_subject text,
                 rep_emailformat VARCHAR(10) DEFAULT '' NOT NULL,
                 rep_message text,

                 cv_enable_captcha VARCHAR(10) DEFAULT '' NOT NULL,
                 cv_width VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_height VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_chars VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_font VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_min_font_size VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_max_font_size VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_noise VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_noise_length VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_background VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_border VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_text_enter_valid_captcha VARCHAR(250) DEFAULT '' NOT NULL,

                 UNIQUE KEY id (id)
            ) ".$charset_collate.";";
            $wpdb->query($sql);
        }

        // insert initial data
        $count = $wpdb->get_var(  "SELECT COUNT(id) FROM ".$wpdb->prefix.$this->table_items  );
        if (!$count)
        {
            define('CP_POLLS_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
            define('CP_POLLS_DEFAULT_fp_destination_emails', CP_POLLS_DEFAULT_fp_from_email);
            $wpdb->insert( $wpdb->prefix.$this->table_items, array( 'id' => 1,
                                      'form_name' => 'Simple Poll',

                                      'form_structure' => $this->get_option('form_structure', CP_POLLS_DEFAULT_form_structure),
                                      
                                      'poll_limit' => $this->get_option('poll_limit', CP_POLLS_POLL_LIMIT),
                                      'poll_private_reports' => $this->get_option('poll_private_reports', CP_POLLS_POLL_PRIVATE_REPORTS),
                                      'poll_see_results' => $this->get_option('poll_see_results', CP_POLLS_POLL_SEE_RESULTS),
                                      'poll_text_seeres' => $this->get_option('poll_text_seeres', CP_POLLS_POLL_TEXT_SEERES),
                                      'poll_text_private' => $this->get_option('poll_text_private', CP_POLLS_POLL_TEXT_PRIVATE),  
                                      'poll_text_votes' => $this->get_option('poll_text_votes', CP_POLLS_POLL_TEXT_VOTES),                                                                            

                                      'fp_from_email' => $this->get_option('fp_from_email', CP_POLLS_DEFAULT_fp_from_email),
                                      'fp_destination_emails' => $this->get_option('fp_destination_emails', CP_POLLS_DEFAULT_fp_destination_emails),
                                      'fp_subject' => $this->get_option('fp_subject', CP_POLLS_DEFAULT_fp_subject),
                                      'fp_inc_additional_info' => $this->get_option('fp_inc_additional_info', CP_POLLS_DEFAULT_fp_inc_additional_info),
                                      'fp_return_page' => $this->get_option('fp_return_page', CP_POLLS_DEFAULT_fp_return_page),
                                      'fp_message' => $this->get_option('fp_message', CP_POLLS_DEFAULT_fp_message),
                                      'fp_emailformat' => $this->get_option('fp_emailformat', CP_POLLS_DEFAULT_email_format),

                                      'cu_enable_copy_to_user' => $this->get_option('cu_enable_copy_to_user', CP_POLLS_DEFAULT_cu_enable_copy_to_user),
                                      'cu_user_email_field' => $this->get_option('cu_user_email_field', CP_POLLS_DEFAULT_cu_user_email_field),
                                      'cu_subject' => $this->get_option('cu_subject', CP_POLLS_DEFAULT_cu_subject),
                                      'cu_message' => $this->get_option('cu_message', CP_POLLS_DEFAULT_cu_message),
                                      'cu_emailformat' => $this->get_option('cu_emailformat', CP_POLLS_DEFAULT_email_format),

                                      'vs_use_validation' => $this->get_option('vs_use_validation', CP_POLLS_DEFAULT_vs_use_validation),
                                      'vs_text_is_required' => $this->get_option('vs_text_is_required', CP_POLLS_DEFAULT_vs_text_is_required),
                                      'vs_text_is_email' => $this->get_option('vs_text_is_email', CP_POLLS_DEFAULT_vs_text_is_email),
                                      'vs_text_datemmddyyyy' => $this->get_option('vs_text_datemmddyyyy', CP_POLLS_DEFAULT_vs_text_datemmddyyyy),
                                      'vs_text_dateddmmyyyy' => $this->get_option('vs_text_dateddmmyyyy', CP_POLLS_DEFAULT_vs_text_dateddmmyyyy),
                                      'vs_text_number' => $this->get_option('vs_text_number', CP_POLLS_DEFAULT_vs_text_number),
                                      'vs_text_digits' => $this->get_option('vs_text_digits', CP_POLLS_DEFAULT_vs_text_digits),
                                      'vs_text_max' => $this->get_option('vs_text_max', CP_POLLS_DEFAULT_vs_text_max),
                                      'vs_text_min' => $this->get_option('vs_text_min', CP_POLLS_DEFAULT_vs_text_min),
                                      'vs_text_submitbtn' => $this->get_option('vs_text_submitbtn', 'Submit'),
                                      'vs_text_previousbtn' => $this->get_option('vs_text_previousbtn', 'Previous'),
                                      'vs_text_nextbtn' => $this->get_option('vs_text_nextbtn', 'Next'),                                      

                                      'rep_enable' => $this->get_option('rep_enable', 'no'),
                                      'rep_days' => $this->get_option('rep_days', '1'),
                                      'rep_hour' => $this->get_option('rep_hour', '0'),
                                      'rep_emails' => $this->get_option('rep_emails', ''),
                                      'rep_subject' => $this->get_option('rep_subject', 'Submissions report...'),
                                      'rep_emailformat' => $this->get_option('rep_emailformat', 'text'),
                                      'rep_message' => $this->get_option('rep_message', 'Attached you will find the data from the form submissions.'),

                                      'cv_enable_captcha' => $this->get_option('cv_enable_captcha', CP_POLLS_DEFAULT_cv_enable_captcha),
                                      'cv_width' => $this->get_option('cv_width', CP_POLLS_DEFAULT_cv_width),
                                      'cv_height' => $this->get_option('cv_height', CP_POLLS_DEFAULT_cv_height),
                                      'cv_chars' => $this->get_option('cv_chars', CP_POLLS_DEFAULT_cv_chars),
                                      'cv_font' => $this->get_option('cv_font', CP_POLLS_DEFAULT_cv_font),
                                      'cv_min_font_size' => $this->get_option('cv_min_font_size', CP_POLLS_DEFAULT_cv_min_font_size),
                                      'cv_max_font_size' => $this->get_option('cv_max_font_size', CP_POLLS_DEFAULT_cv_max_font_size),
                                      'cv_noise' => $this->get_option('cv_noise', CP_POLLS_DEFAULT_cv_noise),
                                      'cv_noise_length' => $this->get_option('cv_noise_length', CP_POLLS_DEFAULT_cv_noise_length),
                                      'cv_background' => $this->get_option('cv_background', CP_POLLS_DEFAULT_cv_background),
                                      'cv_border' => $this->get_option('cv_border', CP_POLLS_DEFAULT_cv_border),
                                      'cv_text_enter_valid_captcha' => $this->get_option('cv_text_enter_valid_captcha', CP_POLLS_DEFAULT_cv_text_enter_valid_captcha)
                                     )
                      );
                      $wpdb->insert( $wpdb->prefix.$this->table_items, array( 'id' => 2,
                                      'form_name' => 'Multi-Field Poll',

                                      'form_structure' => CP_POLLS_DEFAULT_form_structure2,

                                      'fp_from_email' => $this->get_option('fp_from_email', CP_POLLS_DEFAULT_fp_from_email),
                                      'fp_destination_emails' => $this->get_option('fp_destination_emails', CP_POLLS_DEFAULT_fp_destination_emails),
                                      'fp_subject' => $this->get_option('fp_subject', CP_POLLS_DEFAULT_fp_subject),
                                      'fp_inc_additional_info' => $this->get_option('fp_inc_additional_info', CP_POLLS_DEFAULT_fp_inc_additional_info),
                                      'fp_return_page' => $this->get_option('fp_return_page', CP_POLLS_DEFAULT_fp_return_page),
                                      'fp_message' => $this->get_option('fp_message', CP_POLLS_DEFAULT_fp_message),
                                      'fp_emailformat' => $this->get_option('fp_emailformat', CP_POLLS_DEFAULT_email_format),

                                      'cu_enable_copy_to_user' => $this->get_option('cu_enable_copy_to_user', CP_POLLS_DEFAULT_cu_enable_copy_to_user),
                                      'cu_user_email_field' => $this->get_option('cu_user_email_field', CP_POLLS_DEFAULT_cu_user_email_field),
                                      'cu_subject' => $this->get_option('cu_subject', CP_POLLS_DEFAULT_cu_subject),
                                      'cu_message' => $this->get_option('cu_message', CP_POLLS_DEFAULT_cu_message),
                                      'cu_emailformat' => $this->get_option('cu_emailformat', CP_POLLS_DEFAULT_email_format),

                                      'vs_use_validation' => $this->get_option('vs_use_validation', CP_POLLS_DEFAULT_vs_use_validation),
                                      'vs_text_is_required' => $this->get_option('vs_text_is_required', CP_POLLS_DEFAULT_vs_text_is_required),
                                      'vs_text_is_email' => $this->get_option('vs_text_is_email', CP_POLLS_DEFAULT_vs_text_is_email),
                                      'vs_text_datemmddyyyy' => $this->get_option('vs_text_datemmddyyyy', CP_POLLS_DEFAULT_vs_text_datemmddyyyy),
                                      'vs_text_dateddmmyyyy' => $this->get_option('vs_text_dateddmmyyyy', CP_POLLS_DEFAULT_vs_text_dateddmmyyyy),
                                      'vs_text_number' => $this->get_option('vs_text_number', CP_POLLS_DEFAULT_vs_text_number),
                                      'vs_text_digits' => $this->get_option('vs_text_digits', CP_POLLS_DEFAULT_vs_text_digits),
                                      'vs_text_max' => $this->get_option('vs_text_max', CP_POLLS_DEFAULT_vs_text_max),
                                      'vs_text_min' => $this->get_option('vs_text_min', CP_POLLS_DEFAULT_vs_text_min),
                                      'vs_text_submitbtn' => $this->get_option('vs_text_submitbtn', 'Submit'),
                                      'vs_text_previousbtn' => $this->get_option('vs_text_previousbtn', 'Previous'),
                                      'vs_text_nextbtn' => $this->get_option('vs_text_nextbtn', 'Next'),                                      

                                      'rep_enable' => $this->get_option('rep_enable', 'no'),
                                      'rep_days' => $this->get_option('rep_days', '1'),
                                      'rep_hour' => $this->get_option('rep_hour', '0'),
                                      'rep_emails' => $this->get_option('rep_emails', ''),
                                      'rep_subject' => $this->get_option('rep_subject', 'Submissions report...'),
                                      'rep_emailformat' => $this->get_option('rep_emailformat', 'text'),
                                      'rep_message' => $this->get_option('rep_message', 'Attached you will find the data from the form submissions.'),

                                      'cv_enable_captcha' => $this->get_option('cv_enable_captcha', CP_POLLS_DEFAULT_cv_enable_captcha),
                                      'cv_width' => $this->get_option('cv_width', CP_POLLS_DEFAULT_cv_width),
                                      'cv_height' => $this->get_option('cv_height', CP_POLLS_DEFAULT_cv_height),
                                      'cv_chars' => $this->get_option('cv_chars', CP_POLLS_DEFAULT_cv_chars),
                                      'cv_font' => $this->get_option('cv_font', CP_POLLS_DEFAULT_cv_font),
                                      'cv_min_font_size' => $this->get_option('cv_min_font_size', CP_POLLS_DEFAULT_cv_min_font_size),
                                      'cv_max_font_size' => $this->get_option('cv_max_font_size', CP_POLLS_DEFAULT_cv_max_font_size),
                                      'cv_noise' => $this->get_option('cv_noise', CP_POLLS_DEFAULT_cv_noise),
                                      'cv_noise_length' => $this->get_option('cv_noise_length', CP_POLLS_DEFAULT_cv_noise_length),
                                      'cv_background' => $this->get_option('cv_background', CP_POLLS_DEFAULT_cv_background),
                                      'cv_border' => $this->get_option('cv_border', CP_POLLS_DEFAULT_cv_border),
                                      'cv_text_enter_valid_captcha' => $this->get_option('cv_text_enter_valid_captcha', CP_POLLS_DEFAULT_cv_text_enter_valid_captcha)
                                     )
                      );                      
        }
    }


    /* Filter for placing the maps into the contents */
    public function filter_content($atts) {
        global $wpdb;
        extract( shortcode_atts( array(
    		                           'id' => '',
    		                           'results' => '',
    	                        ), $atts ) );
        if ($id != '')
            $this->item = intval($id);
        ob_start();
        $this->insert_public_item($results);
        $buffered_contents = ob_get_contents();
        ob_end_clean();
        return $buffered_contents;
    }


    function insert_public_item($results) {
        global $wpdb;
        
        $previous_label = $this->get_option('vs_text_previousbtn', 'Previous');
        $previous_label = ($previous_label==''?'Previous':$previous_label);
        $next_label = $this->get_option('vs_text_nextbtn', 'Next');
        $next_label = ($next_label==''?'Next':$next_label);  
        
        if (CP_POLLS_DEFER_SCRIPTS_LOADING)
        {
            wp_deregister_script('query-stringify');
            wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', __FILE__));

            wp_deregister_script($this->prefix.'_validate_script');
            wp_register_script($this->prefix.'_validate_script', plugins_url('/js/jquery.validate.js', __FILE__));

            wp_enqueue_script( $this->prefix.'_builder_scriptcppolls',
               plugins_url('/js/fbuilder-loader-public.php', __FILE__),array("jquery","jquery-ui-core","jquery-ui-datepicker","jquery-ui-widget","jquery-ui-position","jquery-ui-tooltip","query-stringify",$this->prefix."_validate_script"), false, true );

            wp_localize_script($this->prefix.'_builder_scriptcppolls', $this->prefix.'_fbuilder_config'.('_'.$this->print_counter), array('obj' =>
            '{"pub":true,"identifier":"'.('_'.$this->print_counter).'","messages": {
            	                	"required": "'.str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_is_required', CP_POLLS_DEFAULT_vs_text_is_required))).'",
            	                	"email": "'.str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_is_email', CP_POLLS_DEFAULT_vs_text_is_email))).'",
            	                	"datemmddyyyy": "'.str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_datemmddyyyy', CP_POLLS_DEFAULT_vs_text_datemmddyyyy))).'",
            	                	"dateddmmyyyy": "'.str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_dateddmmyyyy', CP_POLLS_DEFAULT_vs_text_dateddmmyyyy))).'",
            	                	"number": "'.str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_number', CP_POLLS_DEFAULT_vs_text_number))).'",
            	                	"digits": "'.str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_digits', CP_POLLS_DEFAULT_vs_text_digits))).'",
            	                	"max": "'.str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_max', CP_POLLS_DEFAULT_vs_text_max))).'",
            	                	"min": "'.str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_min', CP_POLLS_DEFAULT_vs_text_min))).'",
    	                    	    "previous": "'.str_replace(array('"'),array('\\"'), esc_html($previous_label)).'",
    	                    	    "next": "'.str_replace(array('"'),array('\\"'),esc_html($next_label)).'"
            	                }}'
            ));
        }
        else
        {
            wp_enqueue_script( "jquery" );
            wp_enqueue_script( "jquery-ui-core" );
            wp_enqueue_script( "jquery-ui-datepicker" );
        }
        wp_enqueue_style('cppolls-stylepublic', plugins_url('css/stylepublic.css', __FILE__) );
        wp_enqueue_style('cppolls-jcupertino', plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__) );
        ?>
        <script type="text/javascript">
         function <?php echo $this->prefix; ?>_pform_doValidate<?php echo '_'.$this->print_counter; ?>(form)
         {
            $dexQuery = jQuery.noConflict();
            document.<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>.cp_ref_page.value = document.location;
            $dexQuery = jQuery.noConflict();<?php if ($this->get_option('cv_enable_captcha', CP_POLLS_DEFAULT_cv_enable_captcha) != 'false') { ?>
            if (document.<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>.hdcaptcha_<?php echo $this->prefix; ?>_post.value == '') { setTimeout( "<?php echo $this->prefix; ?>_cerror<?php echo '_'.$this->print_counter; ?>()", 100); return false; }
            var result = $dexQuery.ajax({ type: "GET", url: "<?php echo $this->get_site_url(); ?>/?ps=<?php echo '_'.$this->print_counter; ?>&<?php echo $this->prefix; ?>_pform_process=2&inAdmin=1&ps=<?php echo '_'.$this->print_counter; ?>&hdcaptcha_<?php echo $this->prefix; ?>_post="+document.<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>.hdcaptcha_<?php echo $this->prefix; ?>_post.value, async: false }).responseText;
            if (result.indexOf("captchafailed") != -1) {
                $dexQuery("#captchaimg<?php echo '_'.$this->print_counter; ?>").attr('src', $dexQuery("#captchaimg<?php echo '_'.$this->print_counter; ?>").attr('src')+'&'+Math.floor((Math.random() * 99999) + 1));
                setTimeout( "<?php echo $this->prefix; ?>_cerror<?php echo '_'.$this->print_counter; ?>()", 100);
                return false;
            } else <?php } ?>
            {
                var cpefb_error = 0;
                $dexQuery("#<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>").find(".cpefb_error").each(function(index){
                    if ($dexQuery(this).css("display")!="none")
                        cpefb_error++;    
                    });
                if (cpefb_error==0)
                {
                    $dexQuery("#<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>").find("select").children().each(function(){
	        	    	    $dexQuery(this).val($dexQuery(this).attr("vt"));
	                });
	                $dexQuery("#<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>").find("input:checkbox,input:radio").each(function(){	            
	        	    	    $dexQuery(this).val($dexQuery(this).attr("vt"));
	                });
	        		$dexQuery("#<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>").find( '.ignore' ).parents( '.fields' ).remove();	        	
	        	/** submit here */	        	
	        	$dexQuery( "#<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>" ).find(".pbSubmit").removeClass("pbSubmit").html('<div class="spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');                
	            document.getElementById("form_structure<?php echo '_'.$this->print_counter; ?>").value = '';    
	        	$dexQuery.ajax({
                        type: "POST",
                        async: true,
                        url: '<?php echo $this->get_site_url(); ?>/',
                        data: $dexQuery( "#<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>" ).serialize()
                      }) .done(function( msg ) {                       <?php if ($this->get_option('fp_return_page', CP_POLLS_DEFAULT_fp_return_page) != '') { ?>
                         document.location = '<?php echo str_replace("'","\'",$this->get_option('fp_return_page', CP_POLLS_DEFAULT_fp_return_page)); ?>';
                         <?php } else { ?>$dexQuery( "#<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>" ).hide();
                         $dexQuery( "#<?php echo $this->prefix; ?>_presults<?php echo '_'.$this->print_counter; ?>" ).html(msg); <?php } ?>
                      });
                }      
                return false;
            }  
         }
         function <?php echo $this->prefix; ?>_cpSeeResults<?php echo '_'.$this->print_counter; ?>() { 
            try {         
            $dexQuery = jQuery.noConflict();
            $dexQuery.ajax({
                        type: "POST",
                        async: true,
                        url: '<?php echo $this->get_site_url(); ?>/',
                        data: {<?php echo $this->prefix; ?>_loadresults: "1", <?php echo $this->prefix; ?>_id: "<?php echo $this->item; ?>"}
                      }) .done(function( msg ) {                                                
                         $dexQuery( "#<?php echo $this->prefix; ?>_pform<?php echo '_'.$this->print_counter; ?>" ).hide();
                         $dexQuery( "#<?php echo $this->prefix; ?>_presults<?php echo '_'.$this->print_counter; ?>" ).html(msg);
                      });
            } catch (e) {
                setTimeout( '<?php echo $this->prefix; ?>_cpSeeResults<?php echo '_'.$this->print_counter; ?>()', 300);
            }
         }
         function <?php echo $this->prefix; ?>_cerror<?php echo '_'.$this->print_counter; ?>(){$dexQuery = jQuery.noConflict();$dexQuery("#hdcaptcha_error<?php echo '_'.$this->print_counter; ?>").css('top',$dexQuery("#hdcaptcha_<?php echo $this->prefix; ?>_post<?php echo '_'.$this->print_counter; ?>").outerHeight());$dexQuery("#hdcaptcha_error<?php echo '_'.$this->print_counter; ?>").css("display","inline");}
        </script>
        <?php
        
        $button_label = $this->get_option('vs_text_submitbtn', 'Submit');
        $button_label = ($button_label==''?'Submit':$button_label);
    
        if (!defined('CP_AUTH_INCLUDE')) define('CP_AUTH_INCLUDE',true);
        @include dirname( __FILE__ ) . '/cp-public-int.inc.php';
        if (!CP_POLLS_DEFER_SCRIPTS_LOADING)
        {
            $prefix_ui = '';
            if (@file_exists(dirname( __FILE__ ).'/../../../wp-includes/js/jquery/ui/jquery.ui.core.min.js'))
                $prefix_ui = 'jquery.ui.';             
            // This code won't be used in most cases. This code is for preventing problems in wrong WP themes and conflicts with third party plugins.
            ?>
                 <?php $plugin_url = plugins_url('', __FILE__); ?>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/jquery.js'; ?>'></script>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'core.min.js'; ?>'></script>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'datepicker.min.js'; ?>'></script>
<?php if (@file_exists(dirname( __FILE__ ).'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'widget.min.js')) { ?><script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'widget.min.js'; ?>'></script><?php } ?>
<?php if (@file_exists(dirname( __FILE__ ).'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'position.min.js')) { ?><script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'position.min.js'; ?>'></script><?php } ?>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'tooltip.min.js'; ?>'></script>
                 <script type='text/javascript' src='<?php echo plugins_url('js/jQuery.stringify.js', __FILE__); ?>'></script>
                 <script type='text/javascript' src='<?php echo plugins_url('js/jquery.validate.js', __FILE__); ?>'></script>
                 <script type='text/javascript'>
                 /* <![CDATA[ */
                 var <?php echo $this->prefix; ?>_fbuilder_config<?php echo '_'.$this->print_counter; ?> = {"obj":"{\"pub\":true,\"identifier\":\"<?php echo '_'.$this->print_counter; ?>\",\"messages\": {\n    \t                \t\"required\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_is_required', CP_POLLS_DEFAULT_vs_text_is_required)));?>\",\n    \t                \t\"email\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_is_email', CP_POLLS_DEFAULT_vs_text_is_email)));?>\",\n    \t                \t\"datemmddyyyy\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_datemmddyyyy', CP_POLLS_DEFAULT_vs_text_datemmddyyyy)));?>\",\n    \t                \t\"dateddmmyyyy\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_dateddmmyyyy', CP_POLLS_DEFAULT_vs_text_dateddmmyyyy)));?>\",\n    \t                \t\"number\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_number', CP_POLLS_DEFAULT_vs_text_number)));?>\",\n    \t                \t\"digits\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_digits', CP_POLLS_DEFAULT_vs_text_digits)));?>\",\n    \t                \t\"max\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_max', CP_POLLS_DEFAULT_vs_text_max)));?>\",\n    \t                \t\"min\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($this->get_option('vs_text_min', CP_POLLS_DEFAULT_vs_text_min)));?>\",\"previous\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($previous_label)); ?>\",\"next\": \"<?php echo str_replace(array('"'),array('\\"'),esc_html($next_label)); ?>\"\n    \t                }}"};
                 /* ]]> */
                 </script>
                 <script type='text/javascript' src='<?php echo plugins_url('js/fbuilder-loader-public.php', __FILE__); ?>'></script>
            <?php
        }
        $this->print_counter++;
    }


    /* Code for the admin area */

    public function plugin_page_links($links) {
        $customAdjustments_link = '<a href="https://wordpress.org/support/plugin/cp-polls#new-post">'.__('Support','cppolls').'</a>';
    	array_unshift($links, $customAdjustments_link);
        $settings_link = '<a href="options-general.php?page='.$this->menu_parameter.'">'.__('Settings','cppolls').'</a>';
    	array_unshift($links, $settings_link);
    	$help_link = '<a href="'.$this->plugin_URL.'">'.__('Help','cppolls').'</a>';
    	array_unshift($links, $help_link);
    	return $links;
    }


    public function admin_menu() {
        add_options_page($this->plugin_name.' Options', $this->plugin_name, 'manage_options', $this->menu_parameter, array($this, 'settings_page') );
        add_menu_page( $this->plugin_name.' Options', $this->plugin_name, 'edit_pages', $this->menu_parameter, array($this, 'settings_page') );
        add_submenu_page( $this->menu_parameter, 'Upgrade', 'Upgrade', 'edit_pages', $this->menu_parameter."_upgrade", array($this, 'settings_page') );
    }


    function insert_button() {
        print '<a href="javascript:send_to_editor(\'[CP_POLLS]\');" title="'.__('Insert','cppolls').' '.$this->plugin_name.'"><img hspace="5" src="'.plugins_url('/images/cp_form.gif', __FILE__).'" alt="'.__('Insert','cppolls').' '.$this->plugin_name.'" /></a>';
    }


    public function settings_page() {
        global $wpdb;
        if ($this->get_param("cal"))
        {
            $this->item = intval($this->get_param("cal"));
            if (isset($_GET["edit"]) && $_GET["edit"] == '1')
                @include_once dirname( __FILE__ ) . '/cp_admin_int_edition.inc.php';
            else if ($this->get_param("list") == '1')
                @include_once dirname( __FILE__ ) . '/cp-admin-int-message-list.inc.php';
            else if ($this->get_param("report") == '1')
                @include_once dirname( __FILE__ ) . '/cp-admin-int-report.inc.php';
            else
                @include_once dirname( __FILE__ ) . '/cp-admin-int.inc.php';
        }
        else if ($this->get_param("page") == $this->menu_parameter.'_upgrade')
        {
            echo("Redirecting to upgrade page...<script type='text/javascript'>document.location='https://wordpress.dwbooster.com/forms/cp-polls#download';</script>");
            exit;
        }   
        else
            @include_once dirname( __FILE__ ) . '/cp-admin-int-list.inc.php';
    }


    function gutenberg_block() {
        global $wpdb;

        wp_enqueue_script( 'cppolls_gutenberg_editor', plugins_url('/js/block.js', __FILE__));

        wp_enqueue_style('cppolls-publicstyle', plugins_url('css/stylepublic.css', __FILE__));

        wp_deregister_script('query-stringify');
        wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', __FILE__));

        wp_deregister_script($this->prefix.'_validate_script');
        wp_register_script($this->prefix.'_validate_script', plugins_url('/js/jquery.validate.js', __FILE__));

        wp_enqueue_script( $this->prefix.'_builder_scriptcppolls',
           plugins_url('/js/fbuilder-loader-public.php', __FILE__),array("jquery","jquery-ui-core","jquery-ui-datepicker","jquery-ui-widget","jquery-ui-position","jquery-ui-tooltip","query-stringify",$this->prefix."_validate_script"), false, true );

        $forms = array();
        $rows = $wpdb->get_results("SELECT id,form_name FROM ".$wpdb->prefix.$this->table_items." ORDER BY form_name");
        foreach ($rows as $item)
           $forms[] = array (
                            'value' => $item->id,
                            'label' => $item->form_name,
                            );

        wp_localize_script( 'cppolls_gutenberg_editor', 'cppolls_forms', array(
                            'forms' => $forms,
                            'siteUrl' => get_site_url()
                          ) );
    }


    public function render_form_admin ($atts) {
        $is_gutemberg_editor = defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
        if (!$is_gutemberg_editor)
            return $this->filter_content (array('id' => $atts["formId"]));
        else if ($atts["formId"])
        {
            $this->setId($atts["formId"]);
            return '<input type="hidden" name="form_structure'.$atts["instanceId"].'" id="form_structure'.$atts["instanceId"].'" value="'.esc_attr($this->get_option('form_structure',CP_POLLS_DEFAULT_form_structure)).'" /><fieldset class="ahbgutenberg_editor" disabled><div id="fbuilder"><div id="fbuilder_'.$atts["instanceId"].'"><div id="formheader_'.$atts["instanceId"].'"></div><div id="fieldlist_'.$atts["instanceId"].'"></div></div></div></fieldset>';
        }
        else
            return '';
    }
    

    function insert_adminScripts($hook) {
        if ($this->get_param("page") == $this->menu_parameter)
        {
            wp_deregister_script('query-stringify');
            wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', __FILE__));
            wp_enqueue_script( $this->prefix.'_builder_scriptcppolls', plugins_url('/js/fbuilder-loader-admin.php', __FILE__),array("jquery","jquery-ui-core","jquery-ui-sortable","jquery-ui-tabs","jquery-ui-droppable","jquery-ui-button","query-stringify","jquery-ui-datepicker") );
            wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
            if ($this->get_param("cal"))
            {
                wp_enqueue_style('cppolls-stylecss', plugins_url('css/style.css', __FILE__));
                wp_enqueue_style('cppolls-cupertinocss', plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__));     
            }
        }
        if( 'post.php' != $hook  && 'post-new.php' != $hook )
            return;
        // space to include some script in the post or page areas if needed
    }

    /* hook for checking posted data for the admin area */

    function data_management() {
        global $wpdb;

        $this->check_reports();

        if ($this->get_param($this->prefix.'_captcha') == 'captcha' )
        {
            include_once dirname( __FILE__ ) . '/captcha/captcha.php';
            exit;
        }

        if ($this->get_param($this->prefix.'_loadresults') == '1' )
        {
            $this->item = intval($this->get_param($this->prefix.'_id'));
            if ($this->get_option('poll_private_reports', CP_POLLS_POLL_PRIVATE_REPORTS) == 'false' || $this->get_option('poll_private_reports', CP_POLLS_POLL_PRIVATE_REPORTS) == '')
                $this->print_poll_results();
            else 
                echo esc_html($this->get_option('poll_text_private', CP_POLLS_POLL_TEXT_PRIVATE)); 
            exit;
        }

        if ($this->get_param($this->prefix.'_csv') && is_admin() && current_user_can('edit_pages') )
        {
            $this->export_csv();
            return;
        }

        if ( $this->get_param($this->prefix.'_post_options') && is_admin() && current_user_can('edit_pages') )
        {
            $this->save_options();
            return;
        }
    
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['CP_POLLS_post_edition'] ) && current_user_can('edit_pages') && is_admin() )
        {
            $this->save_edition();
            return;
        }  
    

    	if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST[$this->prefix.'_pform_process'] ) )
    	    if ( 'GET' != $_SERVER['REQUEST_METHOD'] || !isset( $_GET['hdcaptcha_'.$this->prefix.'_post'] ) )
    		    return;

        if ($this->get_param($this->prefix.'_id')) $this->item = intval($this->get_param($this->prefix.'_id'));

        if (function_exists('session_start')) @session_start();
        if (isset($_GET["ps"])) $sequence = $_GET["ps"]; else if (isset($_POST["cp_pform_psequence"])) $sequence = $_POST["cp_pform_psequence"];
        if (
               ($this->get_option('cv_enable_captcha', CP_POLLS_DEFAULT_cv_enable_captcha) != 'false') &&
               ( (strtolower($this->get_param('hdcaptcha_'.$this->prefix.'_post')) != strtolower($_SESSION['rand_code'.$sequence])) ||
                 ($_SESSION['rand_code'.$sequence] == '')
               )
               &&
               ( (md5(strtolower($this->get_param('hdcaptcha_'.$this->prefix.'_post'))) != ($_COOKIE['rand_code'.$sequence])) ||
                 ($_COOKIE['rand_code'.$sequence] == '')
               )
           )
        {
            echo 'captchafailed';
            exit;
        }

    	// if this isn't the real post (it was the captcha verification) then echo ok and exit
        if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST[$this->prefix.'_pform_process'] ) )
    	{
    	    echo 'ok';
            exit;
    	}


        $alreadyvoted = $this->has_already_voted();
        if ($alreadyvoted) 
            return;

        foreach ($_POST as $item => $value)
            $_POST[$item] = (is_array($value)?$value:stripcslashes($value));
            
        // get form info
        //---------------------------
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        $form_data = json_decode($this->cleanJSON($this->get_option('form_structure', CP_POLLS_DEFAULT_form_structure)));
        $fields = array();
        foreach ($form_data[0] as $item)
        {
            $fields[$item->name] = $item->title;
            if ($item->ftype == 'fPhone') // join fields for phone fields
            {
                $_POST[$item->name.$sequence] = '';
                for($i=0; $i<=substr_count($item->dformat," "); $i++)
                {
                    $_POST[$item->name.$sequence] .= ($_POST[$item->name.$sequence."_".$i]!=''?($i==0?'':'-').$_POST[$item->name.$sequence."_".$i]:'');
                    unset($_POST[$item->name.$sequence."_".$i]);
                }
            }
            else if (isset($_POST[$item->name.$sequence]) && ($item->ftype == 'fradio' || $item->ftype == 'fcheck' || $item->ftype == 'fdropdown'))
            {
                $tfound = false;
                for ($j=0; $j < count($item->choices); $j++) 
                    if ($_POST[$item->name.$sequence] == $item->choices[$j] || $_POST[$item->name.$sequence] == __($item->choices[$j],'cppolls'))
                    {
                        $tfound = true;
                        break;
                    }
                if (!$tfound)
                    unset($_POST[$item->name.$sequence]);
            }
        }


        // grab posted data
        //---------------------------
        $buffer = "";
        foreach ($_POST as $item => $value)
            if (isset($fields[str_replace($sequence,'',$item)]))
            {
                $buffer .= $fields[str_replace($sequence,'',$item)] . ": ". htmlspecialchars ((is_array($value)?(implode(", ",$value)):($value))) . "\n\n";
                $params[str_replace($sequence,'',$item)] = (is_array($value)?$value:htmlspecialchars($value));
            }

        foreach ($_FILES as $item => $value)
            if (isset($fields[str_replace($sequence,'',$item)]) && $this->check_upload($_FILES[$item]))
            {
                $buffer .= $fields[str_replace($sequence,'',$item)] . ": ". $value["name"] . "\n\n";
                $params[str_replace($sequence,'',$item)] = $value["name"];
                $movefile = wp_handle_upload( $_FILES[$item], array( 'test_form' => false ) );
                if ( $movefile )
                {
                    $params[str_replace($sequence,'',$item)."_link"] = $movefile["file"];
                    $params[str_replace($sequence,'',$item)."_url"] = $movefile["url"];
                }
                // else {print_r($movefile);exit;}    // un-comment this line if the uploads aren't working
            }
        $buffer_A = $buffer;

	    /**
	     * Action called before insert the data into database.
	     * To the function is passed an array with submitted data.
	     */
	    do_action_ref_array( 'cppolls_process_data_before_insert', array(&$params) );
        
        // insert into database
        //---------------------------        
        $to = $this->get_option('cu_user_email_field', CP_POLLS_DEFAULT_cu_user_email_field);
        $rows_affected = $wpdb->insert( $wpdb->prefix.$this->table_messages, array( 'formid' => $this->item,
                                                                                    'time' => current_time('mysql'),
                                                                                    'ipaddr' => $_SERVER['REMOTE_ADDR'],
                                                                                    'notifyto' => sanitize_email((@$_POST[$to.$sequence]?$_POST[$to.$sequence]:'')),
                                                                                    'posted_data' => serialize($params),
                                                                                    'data' =>$buffer_A
                                                                                   ) ,
                                                                                    array('%d','%s','%s','%s','%s','%s')
                                                                                   );
        if (!$rows_affected)
        {
            echo 'Error saving data! Please try again.';
            echo '<br /><br />Error debug information: '.mysql_error();
            echo '<br /><br />If the error persists contact support service at https://wordpress.org/support/plugin/cp-polls#new-post';
            exit;
        }

        $myrows = $wpdb->get_results( "SELECT MAX(id) as max_id FROM ".$wpdb->prefix.$this->table_messages );
        $item_number = $myrows[0]->max_id;

	    // Call action for data processing
	    //---------------------------------
	    $params[ 'itemnumber' ] = $item_number;

	    /**
	     * Action called after inserted the data into database.
	     * To the function is passed an array with submitted data.
	     */
	    do_action_ref_array( 'cppolls_process_data',  array(&$params) );

        $this->ready_to_go_reservation($item_number, "", $params);

        setcookie($this->prefix."_voted_".$this->item, "1", time()+3600*24*365*10, "/"); // 10 years
       
        //header("Location: ".$this->get_option('fp_return_page', CP_POLLS_DEFAULT_fp_return_page));
        if ($this->get_option('poll_private_reports', CP_POLLS_POLL_PRIVATE_REPORTS) == 'false')
            $this->print_poll_results();
        else 
            echo esc_html($this->get_option('poll_text_private', CP_POLLS_POLL_TEXT_PRIVATE));
        exit();
    }

    function has_already_voted() {
        global $wpdb;
        $limit = $this->get_option('poll_limit', CP_POLLS_POLL_LIMIT);
        if ($limit == '2' || $limit == '')
            return false;
        
        if ($limit == '1' || $limit == '3')
        {   
            $events = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM ".$wpdb->prefix.$this->table_messages." WHERE formid=%d AND ipaddr=%s", $this->item, $_SERVER["REMOTE_ADDR"] ) );
            if ($limit != '3' || count($events) > 0)
                 return count($events);     
        }
           
        // other case apply cookie restriction
        return (isset ($_COOKIE[$this->prefix."_voted_".$this->item]) && $_COOKIE[$this->prefix."_voted_".$this->item] == "1");  
        
    }

    function print_poll_results() {
        global $wpdb;
        
        $color_array = array(/**'ff0000',*/'ff4500','ff6347','f08080', 'ff7f50', 'ff8c00', 'ffa500', 'ffa07a', 'fa8072', 'e9967a', 'f5deb3', 'ffe4c4', 'ffebcd', 'ffefd5');
        
        $form_setup = json_decode($this->cleanJSON($this->get_option('form_structure', CP_POLLS_DEFAULT_form_structure)));
        $form = $form_setup[0];
        
        $fobjects = array();
        foreach ($form as $item) 
            $fobjects[$item->name] = $item->title;
               
        $events = $wpdb->get_results( $wpdb->prepare( "SELECT ipaddr,time,notifyto,posted_data FROM ".$wpdb->prefix.$this->table_messages." WHERE formid=%d ORDER BY `time` DESC", $this->item ) );

        // general initialization
        $fields = array();
        foreach ($events as $item)
        {
            $params = unserialize($item->posted_data);
            if (is_array($params))
                foreach ($params as $param => $value)
                    if (strlen($value) < 100)
                    {
                        if (is_array($value))
                            $value = implode (",", $value); 
                        if (!isset($fields[$param]["k".$value]))
                            $fields[$param]["k".$value] = 0;
                        @$fields[$param]["k".$value]++;    
                    }    
        }
        if ($form_setup[1][0]->title != '')
            echo '<h1>'.esc_html($form_setup[1][0]->title).'</h1>';
        $counter = 0;   
        foreach ($fields as $fieldname => $arr) 
            if (is_array($arr) && count($arr) > 0)
            {
                echo '<div class="cpbox" id="cpres'.$counter.'"><div class="cpquestion">'.esc_html($fobjects[$fieldname]).'</div>';
                arsort($arr, SORT_NUMERIC);
                $total = 0;
                $totalsize = 100;
                foreach ($arr as $item => $value)  
                    $total += $value;
                $max = max($arr);        
                $totalsize = round(100 / ($max/$total) );
                $count = 0;    
                foreach ($arr as $item => $value)
                {
                    echo '<div class="cpitem">';
                    echo esc_html(strlen($item)>50?substr($item,1,50).'...':substr($item,1)).' ('.esc_html($value).' '. esc_html($this->get_option('poll_text_votes', CP_POLLS_POLL_TEXT_VOTES)) .')';
                    echo '<div class="cpbar" style="width:'.round($value/$total*$totalsize).'%;background-color:#'.$color_array[$count].'"><nobr>'.round($value/$total*100,2).'%</nobr></div>'; 
                    echo '</div>';      
                    $count++;
                    if ($count >= count($color_array)) $count = count($color_array)-1;
                }    
                echo '</div>';     
            }
    }

    function check_upload($uploadfiles) {
        $filename = $uploadfiles['name'];
        $filetype = wp_check_filetype( basename( $filename ), null );

        if ( in_array ($filetype["ext"],array("php","asp","aspx","cgi","pl","perl","exe")) )
            return false;
        else
            return true;
    }


    function ready_to_go_reservation($itemnumber, $payer_email = "", $params = array())
    {

        global $wpdb;

        $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE id=%d", $itemnumber ) );


        $mycalendarrows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.$this->table_items.' WHERE `id`=%d', $myrows[0]->formid ) );

        $this->item = $myrows[0]->formid;

        $buffer_A = $myrows[0]->data;
        $buffer = $buffer_A;

        if ('true' == $this->get_option('fp_inc_additional_info', CP_POLLS_DEFAULT_fp_inc_additional_info))
        {
            $buffer .="ADDITIONAL INFORMATION\n"
                  ."*********************************\n"
                  ."IP: ".$myrows[0]->ipaddr."\n"
                  ."Server Time:  ".date("Y-m-d H:i:s")."\n";
        }

        // 1- Send email
        //---------------------------
        $attachments = array();
        if ('html' == $this->get_option('fp_emailformat', CP_POLLS_DEFAULT_email_format))
            $message = str_replace('<%INFO%>',str_replace("\n","<br />",str_replace('<','&lt;',$buffer)),$this->get_option('fp_message', CP_POLLS_DEFAULT_fp_message));
        else
            $message = str_replace('<%INFO%>',$buffer,$this->get_option('fp_message', CP_POLLS_DEFAULT_fp_message));
        $subject = $this->get_option('fp_subject', CP_POLLS_DEFAULT_fp_subject);
        foreach ($params as $item => $value)
        {
            $message = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$message);
            $subject = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$subject);
            if (strpos($item,"_link"))
                $attachments[] = $value;
        }

        $message = str_replace('<%itemnumber%>',$itemnumber,$message);
        $subject = str_replace('<%itemnumber%>',$itemnumber,$subject);        
        
        $from = $this->get_option('fp_from_email', (defined('CP_POLLS_DEFAULT_fp_from_email')?CP_POLLS_DEFAULT_fp_from_email:'') );
        $to = explode(",",$this->get_option('fp_destination_emails', (defined('CP_POLLS_DEFAULT_fp_destination_emails')?CP_POLLS_DEFAULT_fp_destination_emails:'') ));
        if ('html' == $this->get_option('fp_emailformat', CP_POLLS_DEFAULT_email_format)) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";

        $replyto = $myrows[0]->notifyto;
        if ($this->get_option('fp_emailfrommethod', "fixed") == "customer")
            $from_1 = $replyto;
        else
            $from_1 = $from;
/**
        // using multiple choice fields to send emails to selected recipients
        $arr["A"] = $_POST["fieldname1"];
        $arr["B"] = $_POST["fieldname9"];
        foreach ($arr as $arrayitems)
            if (is_array($arrayitems))
            {
                foreach ($arrayitems as $value)
                {
                    $string = substr($value,0,strpos($value,"-"));
                    $string = explode(",",trim($string));
                    foreach ($string as $emailkk)
                        if (strpos($emailkk,"@"))
                            $to[] = trim($emailkk);
                }
            }
        $to = array_unique($to);
*/
        foreach ($to as $item)
            if (trim($item) != '')
            {
                wp_mail(trim($item), $subject, $message,
                    "From: \"$from_1\" <".$from_1.">\r\n".
                    ($replyto!=''?"Reply-To: ".$replyto."\r\n":'').
                    $content_type.
                    "X-Mailer: PHP/" . phpversion(), $attachments);
            }

        // 2- Send copy to user
        //---------------------------
        $to = $this->get_option('cu_user_email_field', CP_POLLS_DEFAULT_cu_user_email_field);
        $recipient = $myrows[0]->notifyto;
        if ((trim($recipient) != '' || $payer_email != '') && 'true' == $this->get_option('cu_enable_copy_to_user', CP_POLLS_DEFAULT_cu_enable_copy_to_user))
        {
            if ('html' == $this->get_option('cu_emailformat', CP_POLLS_DEFAULT_email_format))
                $message = str_replace('<%INFO%>',str_replace("\n","<br />",str_replace('<','&lt;',$buffer_A)).'</pre>',$this->get_option('cu_message', CP_POLLS_DEFAULT_cu_message));
            else
                $message = str_replace('<%INFO%>',$buffer_A,$this->get_option('cu_message', CP_POLLS_DEFAULT_cu_message));
            $subject = $this->get_option('cu_subject', CP_POLLS_DEFAULT_cu_subject);
            foreach ($params as $item => $value)
            {
                $message = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$message);
                $subject = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$subject);
            }
            
            $message = str_replace('<%itemnumber%>',$itemnumber,$message);
            $subject = str_replace('<%itemnumber%>',$itemnumber,$subject);
                    
            if ('html' == $this->get_option('cu_emailformat', CP_POLLS_DEFAULT_email_format)) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
            if ($recipient != '')
                wp_mail(trim($recipient), $subject, $message,
                        "From: \"$from\" <".$from.">\r\n".
                        $content_type.
                        "X-Mailer: PHP/" . phpversion());
            if ($recipient != $payer_email && $payer_email != '')
                wp_mail(trim($payer_email), $subject, $message,
                        "From: \"$from\" <".$from.">\r\n".
                        $content_type.
                        "X-Mailer: PHP/" . phpversion());
        }

    }


    function save_edition()
    {
        $verify_nonce = wp_verify_nonce( $_POST['rsave'], 'cfpoll_update_actions_post');
        if (!$verify_nonce)
        {
            echo 'Error: Form cannot be authenticated. Please contact our <a href="https://wordpress.org/support/plugin/cp-polls#new-post">support service</a> for verification and solution. Thank you.';
            return;
        }        
        foreach ($_POST as $item => $value)
            if (!is_array($value))
                $_POST[$item] = stripcslashes($value);        
        if (substr_count($_POST['editionarea'],"\\\""))
            $_POST["editionarea"] = stripcslashes($_POST["editionarea"]);
        if ($_POST["cfwpp_edit"] == 'css')  
            update_option('CP_POLLS_CSS', base64_encode(sanitize_textarea_field($_POST["editionarea"])));  
    }
    
    
    function save_options()
    {
        global $wpdb;
        
        $verify_nonce = wp_verify_nonce( $_POST['rsave'], 'cfpoll_update_actions_post');
        if (!$verify_nonce)
        {
            echo 'Error: Form cannot be authenticated. Please contact our <a href="https://wordpress.org/support/plugin/cp-polls#new-post">support service</a> for verification and solution. Thank you.';
            return;
        }
            
        $this->item = intval($_POST[$this->prefix."_id"]);

        if (substr_count($_POST['form_structure'],"\\") > 30)
            foreach ($_POST as $item => $value)
                $_POST[$item] = stripcslashes($value);

        $this->add_field_verify($wpdb->prefix.$this->table_items, "poll_limit", "VARCHAR(10)");
        $this->add_field_verify($wpdb->prefix.$this->table_items, "poll_private_reports", "VARCHAR(10)");
        $this->add_field_verify($wpdb->prefix.$this->table_items, "poll_see_results", "VARCHAR(10)");
        $this->add_field_verify($wpdb->prefix.$this->table_items, "poll_text_seeres", "VARCHAR(250)");
        $this->add_field_verify($wpdb->prefix.$this->table_items, "poll_text_private", "VARCHAR(250)");     
        $this->add_field_verify($wpdb->prefix.$this->table_items, "poll_text_votes", "VARCHAR(250)");  
        
        $wpdb->update ( $wpdb->prefix.$this->table_items, 
                        array(
                              'form_structure' => $_POST['form_structure'],
                              
                              'poll_limit' => sanitize_text_field($_POST['poll_limit']),
                              'poll_private_reports' => sanitize_text_field($_POST['poll_private_reports']),
                              'poll_see_results' => sanitize_text_field($_POST['poll_see_results']),
                              'poll_text_seeres' => sanitize_text_field($_POST['poll_text_seeres']),
                              'poll_text_private' => sanitize_text_field($_POST['poll_text_private']),
                              'poll_text_votes' => sanitize_text_field($_POST['poll_text_votes']),
                              
                              'fp_from_email' => sanitize_text_field($_POST['fp_from_email']),
                              'fp_destination_emails' => sanitize_text_field($_POST['fp_destination_emails']),
                              'fp_subject' => sanitize_text_field($_POST['fp_subject']),
                              'fp_inc_additional_info' => sanitize_text_field($_POST['fp_inc_additional_info']),
                              'fp_return_page' => sanitize_text_field($_POST['fp_return_page']),
                              'fp_message' => $_POST['fp_message'],
                              'fp_emailformat' => sanitize_text_field($_POST['fp_emailformat']),
                              
                              'cu_enable_copy_to_user' => sanitize_text_field($_POST['cu_enable_copy_to_user']),
                              'cu_user_email_field' => sanitize_text_field(isset($_POST['cu_user_email_field']) ? $_POST['cu_user_email_field'] : ''),
                              'cu_subject' => sanitize_text_field($_POST['cu_subject']),
                              'cu_message' => $_POST['cu_message'],
                              'cu_emailformat' => sanitize_text_field($_POST['cu_emailformat']),
                              'fp_emailfrommethod' => sanitize_text_field($_POST['fp_emailfrommethod']),
                              
                              //'vs_use_validation' => $_POST['vs_use_validation'],
                              'vs_text_is_required' => sanitize_text_field($_POST['vs_text_is_required']),
                              'vs_text_is_email' => sanitize_text_field($_POST['vs_text_is_email']),
                              'vs_text_datemmddyyyy' => sanitize_text_field($_POST['vs_text_datemmddyyyy']),
                              'vs_text_dateddmmyyyy' => sanitize_text_field($_POST['vs_text_dateddmmyyyy']),
                              'vs_text_number' => sanitize_text_field($_POST['vs_text_number']),
                              'vs_text_digits' => sanitize_text_field($_POST['vs_text_digits']),
                              'vs_text_max' => sanitize_text_field($_POST['vs_text_max']),
                              'vs_text_min' => sanitize_text_field($_POST['vs_text_min']),
                              'vs_text_submitbtn' => sanitize_text_field($_POST['vs_text_submitbtn']),
                              'vs_text_previousbtn' => sanitize_text_field($_POST['vs_text_previousbtn']),
                              'vs_text_nextbtn' => sanitize_text_field($_POST['vs_text_nextbtn']),
                              
                              'rep_enable' => sanitize_text_field($_POST['rep_enable']),
                              'rep_days' => sanitize_text_field($_POST['rep_days']),
                              'rep_hour' => sanitize_text_field($_POST['rep_hour']),
                              'rep_emails' => sanitize_text_field($_POST['rep_emails']),
                              'rep_subject' => sanitize_text_field($_POST['rep_subject']),
                              'rep_emailformat' => sanitize_text_field($_POST['rep_emailformat']),
                              'rep_message' => $_POST['rep_message'],
                              
                              'cv_enable_captcha' => sanitize_text_field( isset($_POST['cv_enable_captcha']) ? $_POST['cv_enable_captcha'] : 0 ),
                              'cv_width' => sanitize_text_field($_POST['cv_width']),
                              'cv_height' => sanitize_text_field($_POST['cv_height']),
                              'cv_chars' => sanitize_text_field($_POST['cv_chars']),
                              'cv_font' => sanitize_text_field($_POST['cv_font']),
                              'cv_min_font_size' => sanitize_text_field($_POST['cv_min_font_size']),
                              'cv_max_font_size' => sanitize_text_field($_POST['cv_max_font_size']),
                              'cv_noise' => sanitize_text_field($_POST['cv_noise']),
                              'cv_noise_length' => sanitize_text_field($_POST['cv_noise_length']),
                              'cv_background' => sanitize_text_field(str_replace('#','',$_POST['cv_background'])),
                              'cv_border' => sanitize_text_field(str_replace('#','',$_POST['cv_border'])),
                              'cv_text_enter_valid_captcha' => sanitize_text_field($_POST['cv_text_enter_valid_captcha'])
    	                ), 
                        array( 'id' => $this->item )
                      );
    }


    function get_form_field_label ($fieldid, $form)
    {
            foreach($form as $item)
                if ($item->name == $fieldid)
                {
                    if (isset($item->shortlabel) && $item->shortlabel != '')
                        return $item->shortlabel;
                    else
                        return $item->title;
                }
        return $fieldid;
    }


    function generateSafeFileName($filename) {
        $filename = strtolower(strip_tags($filename));
        $filename = str_replace(";","_",$filename);
        $filename = str_replace("#","_",$filename);
        $filename = str_replace(" ","_",$filename);
        $filename = str_replace("'","",$filename);
        $filename = str_replace('"',"",$filename);
        $filename = str_replace("__","_",$filename);
        $filename = str_replace("&","and",$filename);
        $filename = str_replace("/","_",$filename);
        $filename = str_replace("\\","_",$filename);
        $filename = str_replace("?","",$filename);
        return $filename;
    }

    function export_csv ()
    {
        if (!is_admin())
            return;
        global $wpdb;

        $this->item = intval($this->get_param("cal"));

        if ($this->item)
        {
            $form = json_decode($this->cleanJSON($this->get_option('form_structure', CP_POLLS_DEFAULT_form_structure)));
            $form = $form[0];
        }
        else
            $form = array();

        $cond = '';
        if ($this->get_param("search")) $cond .= " AND (data like '%".esc_sql($this->get_param("search"))."%' OR posted_data LIKE '%".esc_sql($this->get_param("search"))."%')";
        if ($this->get_param("dfrom")) $cond .= " AND (`time` >= '".esc_sql($this->get_param("dfrom"))."')";
        if ($this->get_param("dto")) $cond .= " AND (`time` <= '".esc_sql($this->get_param("dto"))." 23:59:59')";
        if ($this->item != 0) $cond .= " AND formid=".$this->item;

        $events = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC" );

        if ($this->include_user_data_csv)
            $fields = array("Form ID", "Time", "IP Address", "email");
        else
            $fields = array("Form", "Time", "email");
        $values = array();
        foreach ($events as $item)
        {
            if ($this->include_user_data_csv)
                $value = array($this->get_option('form_name',''), $item->time, $item->ipaddr, $item->notifyto);
            else
                $value = array($this->get_option('form_name',''),  $item->time, $item->notifyto);
            if ($item->posted_data)
                $data = unserialize($item->posted_data);
            else
                $data = array();

            $end = count($fields);
            for ($i=0; $i<$end; $i++)
                if (isset($data[$fields[$i]]) ){
                    $value[$i] = $data[$fields[$i]];
                    unset($data[$fields[$i]]);
                }

            if (is_array($data)) foreach ($data as $k => $d)
            {
               $fields[] = $k;
               $value[] = $d;
            }
            $values[] = $value;
        }

        $filename = $this->generateSafeFileName(strtolower($this->get_option('form_name','export'))).'_'.date("m_d_y");

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=".$filename.".csv");

        $end = count($fields);
        for ($i=0; $i<$end; $i++)
        {
            $hlabel = $this->encodeFormula(iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $this->get_form_field_label($fields[$i],$form)));
            echo '"'.str_replace('"','""', $hlabel).'",';
        } 
        echo "\n";
        foreach ($values as $item)
        {
            for ($i=0; $i<$end; $i++)
            {
                if (!isset($item[$i]))
                    $item[$i] = '';
                if (is_array($item[$i]))
                    $item[$i] = implode($item[$i],',');
                $item[$i] = $this->encodeFormula(iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $item[$i]));
                echo '"'.str_replace('"','""', $item[$i]).'",';
            }
            echo "\n";
        }

        exit;
    }

    
    public function encodeFormula($text)
    {
        $text = trim($text);
        if ( strlen($text) > 1 && (in_array(substr($text,0,1), array('=','@','+','-'))) )
        {
            if (substr($text,0,1) != '-' || floatval($text)."" != $text)
                $text = chr(9).$text;
        }
        return $text;
    }
    
    
    public function setId($id)
    {
        $this->item = intval($id);
    }

    public function translate_json($str)
    {
        $form_data = json_decode($this->cleanJSON($str));      
        
        $form_data[1][0]->title = __($form_data[1][0]->title,'cppolls');   
        $form_data[1][0]->description = __($form_data[1][0]->description,'cppolls');   
              
        for ($i=0; $i < count($form_data[0]); $i++)    
        {
            $form_data[0][$i]->title = __($form_data[0][$i]->title,'cppolls');   
            $form_data[0][$i]->userhelpTooltip = __($form_data[0][$i]->userhelpTooltip,'cppolls'); 
            $form_data[0][$i]->userhelp = __($form_data[0][$i]->userhelp,'cppolls'); 
            if ($form_data[0][$i]->ftype == 'fCommentArea')
                $form_data[0][$i]->userhelp = __($form_data[0][$i]->userhelp,'cppolls');   
            else 
                if ($form_data[0][$i]->ftype == 'fradio' || $form_data[0][$i]->ftype == 'fcheck' || $form_data[0][$i]->ftype == 'fradio')    
                {
                    for ($j=0; $j < count($form_data[0][$i]->choices); $j++)  
                        $form_data[0][$i]->choices[$j] = __($form_data[0][$i]->choices[$j],'cppolls'); 
                } 
        }    
        $str = json_encode($form_data);
        return $str;
    }



    private function get_records_csv($formid, $form_name = "")
    {
        global $wpdb;

        $last_sent_id = get_option('CP_POLLS_last_sent_id_'.$formid, '0');        
        
        $events = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE formid=%d AND id>%d ORDER BY id ASC" , $formid, $last_sent_id ) );

        if ($wpdb->num_rows <= 0) // if no rows, return empty
            return '';

        if ($this->item)
        {
            $form = json_decode($this->cleanJSON($this->get_option('form_structure', CP_POLLS_DEFAULT_form_structure)));
            $form = $form[0];
        }
        else
            $form = array();

        $buffer = '';
        if ($this->include_user_data_csv)
            $fields = array("Submission ID", "Form", "Time", "IP Address", "email");
        else
            $fields = array("Submission ID", "Form", "Time", "email");
        $values = array();
        foreach ($events as $item)
        {
            if ($this->include_user_data_csv)
                $value = array($item->id, $form_name, $item->time, $item->ipaddr, $item->notifyto);
            else
                $value = array($item->id, $form_name, $item->time, $item->notifyto);
            $last_sent_id = $item->id;
            if ($item->posted_data)
                $data = unserialize($item->posted_data);
            else
                $data = array();

            $end = count($fields);
            for ($i=0; $i<$end; $i++)
                if (isset($data[$fields[$i]]) ){
                    $value[$i] = $data[$fields[$i]];
                    unset($data[$fields[$i]]);
                }

            if (is_array($data)) foreach ($data as $k => $d)
            {
               $fields[] = $k;
               $value[] = $d;
            }
            $values[] = $value;
        }
        update_option('CP_POLLS_last_sent_id_'.$formid, $last_sent_id);

        $end = count($fields);
        for ($i=0; $i<$end; $i++)
        {
            $hlabel = $this->encodeFormula(iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $this->get_form_field_label($fields[$i],$form)));
            $buffer .= '"'.str_replace('"','""', $hlabel).'",';
        }   
        $buffer .= "\n";
        foreach ($values as $item)
        {
            for ($i=0; $i<$end; $i++)
            {
                if (!isset($item[$i]))
                    $item[$i] = '';
                if (is_array($item[$i]))
                    $item[$i] = implode($item[$i],',');
                $item[$i] = $this->encodeFormula(iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $item[$i]));
                $buffer .= '"'.str_replace('"','""', $item[$i]).'",';
            }
            $buffer .= "\n";
        }
        return $buffer;

    }

    private function check_reports() {
        global $wpdb;

        $last_verified = get_option('CP_POLLS_last_verified','');
        if ( $last_verified == '' || $last_verified < date("Y-m-d H:i:s", strtotime("-1 minutes")) )  // verification to don't check too fast to avoid overloading the site
        {
            update_option('CP_POLLS_last_verified',date("Y-m-d H:i:s"));

            // global reports for all forms
            if (get_option('CP_POLLS_rep_enable', 'no') == 'yes' && get_option('CP_POLLS_rep_days', '') != '' && get_option('CP_POLLS_rep_emails', '') != '' )
            {
                $formid = 0;
                $verify_after = date("Y-m-d H:i:s", strtotime("-".get_option('CP_POLLS_rep_days', '')." days"));
                $last_sent = get_option('CP_POLLS_last_sent'.$formid, '');                
                if ($last_sent == '' || $last_sent < $verify_after)  // check if this form needs to check for a new report
                {
                    update_option('CP_POLLS_last_sent'.$formid, date("Y-m-d ".(get_option('CP_POLLS_rep_hour', '')<'10'?'0':'').get_option('CP_POLLS_rep_hour', '').":00:00"));
                    $text = '';
                    $forms = $wpdb->get_results("SELECT id,fp_from_email,form_name,rep_days,rep_hour,rep_emails,rep_subject,rep_emailformat,rep_message,rep_enable FROM ".$wpdb->prefix.$this->table_items." WHERE rep_emails<>'' AND rep_enable='yes'");                    
                    foreach ($forms as $form)  // for each form with the reports enabled
                    {                                    
                        $attachments = array();
                        $csv = $this->get_records_csv($form->id, $form->form_name);
                        if ($csv != '')
                        {
                            $text = "- ".substr_count($csv,",\n\"").' submissions from '.$form->form_name."\n";
                            $filename = $this->generateSafeFileName(strtolower($form->form_name)).'_'.date("m_d_y");
                            $filename = WP_CONTENT_DIR . '/uploads/'.$filename .'.csv';
                            $handle = fopen($filename, 'w');
                            fwrite($handle,$csv);
                            fclose($handle);
                            $attachments[] = $filename;
                        }
                    }                    
                    if ('html' == get_option('CP_POLLS_rep_emailformat','')) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
                    if (count($attachments))
                        wp_mail( str_replace(" ","",str_replace(";",",",get_option('CP_POLLS_rep_emails',''))), get_option('CP_POLLS_rep_subject',''), get_option('CP_POLLS_rep_message','')."\n".$text,
                                    "From: \"".get_option('CP_POLLS_fp_from_email','')."\" <".get_option('CP_POLLS_fp_from_email','').">\r\n".
                                    $content_type.
                                    "X-Mailer: PHP/" . phpversion(),
                                    @$attachments);
                }
            }          

            // reports for specific forms
            $forms = $wpdb->get_results("SELECT id,form_name,fp_from_email,rep_days,rep_hour,rep_emails,rep_subject,rep_emailformat,rep_message,rep_enable FROM ".$wpdb->prefix.$this->table_items." WHERE rep_emails<>'' AND rep_enable='yes'");
            foreach ($forms as $form)  // for each form with the reports enabled
            {
                $formid = $form->id;
                $verify_after = date("Y-m-d H:i:s", strtotime("-".$form->rep_days." days"));
                $last_sent = get_option('CP_POLLS_last_sent'.$formid, '');
                if ($last_sent == '' || $last_sent < $verify_after)  // check if this form needs to check for a new report
                {
                    update_option('CP_POLLS_last_sent'.$formid, date("Y-m-d ".($form->rep_hour<'10'?'0':'').$form->rep_hour.":00:00"));
                    $csv = $this->get_records_csv($formid, $form->form_name);
                    if ($csv != '')
                    {
                        $filename = $this->generateSafeFileName(strtolower($form->form_name)).'_'.date("m_d_y");
                        $filename = WP_CONTENT_DIR . '/uploads/'.$filename .'.csv';
                        $handle = fopen($filename, 'w');
                        fwrite($handle,$csv);
                        fclose($handle);
                        $attachments = array( $filename );
                        if ('html' == $form->rep_emailformat) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
                        wp_mail( str_replace(" ","",str_replace(";",",",$form->rep_emails)), $form->rep_subject, $form->rep_message,
                                "From: \"".$form->fp_from_email."\" <".$form->fp_from_email.">\r\n".
                                $content_type.
                                "X-Mailer: PHP/" . phpversion(),
                                $attachments);
                    }
                }
            } // end foreach
        } // end if
    }  // end check_reports function


} // end class


?>