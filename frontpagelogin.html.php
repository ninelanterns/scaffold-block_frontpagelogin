<?php
/**
 * HTML for alternative login page.
 *
 * @package    auth
 * @subpackage redcross
 * @copyright  &copy; 2011 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     evan.irving-pease
 * @version    1.0
 */

?>
<div class="loginbox" style="margin:0px;width:100%;text-align:center">
  <div>
<?php
  if (($CFG->registerauth == 'email') || !empty($CFG->registerauth)) { ?>
      <div class="skiplinks"><a style="color:#EE352A" class="skip" href="signup.php"><?php print_string("tocreatenewaccount"); ?></a></div>
<?php
  } ?>
    <h2 style="padding-left:10px;"><?php print_string("returningtosite") ?></h2>
    <div class="subcontent loginsub">
        <div class="desc" style="text-align: left;">
          <?php
            print_string("loginusing");
            if (empty($CFG->usesid)) {
                echo '. ('.get_string("cookiesenabled").')';
                echo $OUTPUT->help_icon('cookiesenabled');
            }
           ?>
        </div>
        <?php
          if (!empty($errormsg)) {
              echo '<div class="loginerrors">';
              echo $OUTPUT->error_text($errormsg);
              echo '</div>';
          }

          $lt = null;

          // where do we post the login credentials
          if ($authCAS == 'CAS') {
              global $PHPCAS_CLIENT;

              $casauth = get_auth_plugin('cas');
              $casauth->connectCAS();
              $serviceurl = isset($SESSION->wantsurl) ? $SESSION->wantsurl : $CFG->wwwroot;
              if ($serviceurl instanceof moodle_url) {
              } else {
                  $serviceurl = new moodle_url($serviceurl);
              }
              $path = $serviceurl->get_path();
              if (substr($path, -4)!='.php') {
                  $path .= substr($path, -1)=='/' ? 'index.php' : '/index.php';
              }
              $serviceurl = $CFG->wwwroot.$path.'?'.$serviceurl->get_query_string();
              phpCAS::setFixedServiceURL($serviceurl);
              $posturl = phpCAS::getServerLoginURL();

              // make a cURL request to fatch the login ticket
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, $PHPCAS_CLIENT->getServerBaseURL().'loginTicket');
              curl_setopt($ch, CURLOPT_POST, 0);
              curl_setopt($ch, CURLOPT_POSTFIELDS, '');
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
              $lt = curl_exec($ch);
              curl_close($ch);
          } else {
              $posturl = $CFG->httpswwwroot.'/login/index.php';
          }
        ?>
        <form action="<?php echo $posturl; ?>" method="post" id="login">
          <div class="loginform">
            <input type="hidden" name="lt" id="lt" value="<?php echo $lt; ?>" />
            <div class="form-label" style="line-height: 28px;"><label for="username"><?php print_string("email") ?></label></div>
            <div class="form-input">
              <input type="text" name="username" id="username" size="15" value="<?php p($frm->username) ?>" autocomplete="on" />
            </div>
            <div class="clearer"><!-- --></div>
            <div class="form-label" style="line-height: 28px;"><label for="password"><?php print_string("password") ?></label></div>
            <div class="form-input">
              <input type="password" name="password" id="password" size="15" value="" autocomplete="on" />
              <input type="submit" id="loginbtn" value="<?php echo strtoupper(get_string("login")) ?>" />
              <div class="forgetpass"><a style="color:black" href="<?php echo $CFG->httpswwwroot.'/auth/redcross/forgot_password.php'; ?>"><?php print_string("forgotten") ?></a></div>
              <a style="float:right; font-size: 0.7em; color:#EE352A" href="/index.php?authCAS=<?php echo ($authCAS == 'CAS') ? 'NOCAS' : 'CAS'; ?>"><?php echo $authCAS; ?></a>
            </div>
            <div class="clearer"><!-- --></div>
          </div>
        </form>

<?php if ($CFG->guestloginbutton) {  ?>
      <div class="subcontent guestsub">
        <div class="desc" style="line-height: 30px;">
          <?php print_string('browsewithoutlogin', 'block_frontpagelogin') ?>
          <form action="/my/" method="post" id="guestlogin" style="float: right;">
          <div class="guestform">
            <input type="hidden" name="username" value="guest" />
            <input type="hidden" name="password" value="guest" />
            <input type="submit" value="<?php echo strtoupper(get_string("guest")) ?>" />
          </div>
        </form>
        </div>

      </div>
<?php } ?>
     </div>
</div>

</div>
