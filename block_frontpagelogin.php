<?php

/**
 * Big login block for the front page
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2012 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

require_once($CFG->dirroot."/auth/redcross/auth.php");

class block_frontpagelogin extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_frontpagelogin');
    }

    public function specialization() {
        $this->title = ''; // title is not displayed for this block
    }

    function applicable_formats() {
        return array('site' => true);
    }

    public function get_content() {
        global $SESSION, $CFG, $OUTPUT;

        if ($this->content !=null) {
            return $this->content;
        }

        //initialize variables
        $errormsg = '';

        /// Check for timed out sessions
        if (!empty($SESSION->has_timed_out)) {
            $session_has_timed_out = true;
            $SESSION->has_timed_out = false;
        } else {
            $session_has_timed_out = false;
        }

        /// Define variables used in page
        $site = get_site();

        $loginsite = get_string("loginsite");

        $loginurl = (!empty($CFG->alternateloginurl)) ? $CFG->alternateloginurl : '';

        // are we using CAS to login (values are NOCAS or CAS)
        $authCAS = optional_param('authCAS', 'CAS', PARAM_TEXT);

        // don't use CAS if it's not enabled
        if (!is_enabled_auth('cas')) {
            $authCAS = 'NOCAS';
        }

        $frm = data_submitted();

        if(!$frm) {
            // initialise the object
            $frm = new stdClass();
            $frm->username = '';
        }

        //error message

        $error_code = isset($_GET['errorcode']) ? $_GET['errorcode'] : '';
        if($error_code !=''){
            $errormsg = get_string("loginerrormsg", 'auth_redcross');
        }

        // Set SAML domain cookie
        $config = get_config('auth/redcross');

        $loginsite = get_string("loginsite");

        ob_start();
        require(__DIR__.'/frontpagelogin.html.php');
        $this->content = new stdClass();
        $this->content->text = ob_get_contents();
        ob_end_clean();
        return $this->content;
    }
}