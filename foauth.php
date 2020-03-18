<?php
/**
 * @package Facebook_Auth2
 * @version 1.0.0.1
 */
/*
Plugin Name: Open Authentication
Plugin URI: http://
Description: This plugin uses for facebook registration/authentication purposes
Author: Ion Filipski
Version: 1.0.0.1
Author URI: http://
*/

#redirect
#header('Location:  /wp-admin/index.php');
#Location: /wp-admin/index.php
error_log("## foauth start!!!");

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
   $entityBody  = file_get_contents('php://input');
   error_log ("## HTTP POST: " . $entityBody);
   
   if ($_SERVER["CONTENT_TYPE"] === 'application/json')
   {
      error_log ("## HTTP POST json:   " . $entityBody);
      $rest = json_decode($entityBody);
      error_log ("## HTTP POST action: " . $rest->action);
   }
}


function f_oauth_register_rest( $request )
{
   error_log ("## " . __FUNCTION__ . "() Encoding Current:  " . $_SERVER["CONTENT_TYPE"]);
   error_log ("## " . __FUNCTION__ . "() Encoding Required: " . $_SERVER["HTTP_ACCEPT"]);
   error_log ("## " . __FUNCTION__ . "() Cotent: " . $request->get_body());

   $rest = json_decode( $request->get_body());
   if ($rest->action === 'register')
      return register_or_login($rest);

   $registeredtype = 'new';
   $status = 'fail';
   $data = ['registered' => ['status' => $status, 'type' =>  $registeredtype, 'type2' =>  'bazz']];
   return $data;
}

add_action( 'rest_api_init', function()
   {
      $ret = register_rest_route( 'f/oauth/v1', '/register',
                array
                (
                   'methods'  => 'POST',
                   'callback' => 'f_oauth_register_rest',
                ) );
      error_log ("## register_rest_route: " . $ret);
   });

function register_or_login($rest)
{
//   error_log ("password: " . wp_hash_password('facebook_la_toti'));
   if($rest->registertype === 'facebook')
      return register_or_login_facebook ($rest);
   return true;
}

function verify_fb_user_stub($rest){return true;}

function verify_fb_user($rest)
{
   $ffuu = base64_decode ( 'aHR0cHM6Ly9ncmFwaC5mYWNlYm9vay5jb20vb2F1dGgvYWNjZXNzX3Rva2VuPw==' );
   $ffid = base64_decode ( 'Y2xpZW50X2lk' );
   $vvid = base64_decode ( 'MjYwOTk5Mjg5NTkwOTUxNQ==' );
   $ffcl = base64_decode ( 'Y2xpZW50X3NlY3JldA==' );
   $vvcl = base64_decode ( 'ZDJlNDQ2ZjY5ODg0YjA5MzA4MWFjYWQ2MjMxZWM5Y2M=' );
   $fftp = base64_decode ( 'Z3JhbnRfdHlwZQ==' );
   $vvtp = base64_decode ( 'Y2xpZW50X2NyZWRlbnRpYWxz' );
   $ch = curl_init();
   curl_setopt($ch,CURLOPT_URL,$ffuu.http_build_query(array($ffid=>$vvid,$ffcl=>$vvcl,$fftp=>$vvtp)));
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $content = curl_exec ($ch);
   $code = curl_getinfo ($ch, CURLINFO_RESPONSE_CODE);
   curl_close ($ch);
   //////////////////////////////////////
   if ($code != 200)
   {
      error_log ( 'Fail to verify user step 1: '  . $code . '; content' . $content);
      return false;
   }
   $fbr = json_decode($content);

   $url_params = array(
               'input_token'  => $rest->access_token,
               'access_token' => $fbr->access_token
            );
   error_log ("guery data : " . http_build_query($url_params));
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/debug_token" . "?" .  http_build_query($url_params));
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $content = curl_exec ($ch);
   $code = curl_getinfo ($ch, CURLINFO_RESPONSE_CODE);
   error_log ("return content : " . $content);
   curl_close ($ch);
   ///////////////////////////////////////
   if ($code != 200)
   {
      error_log ( 'Fail to verify user step 2: code ' . $code);
      return false;
   }

   $fbu = json_decode($content);
   if (! ($fbu->data->is_valid == "true"))
   {
      error_log('Fail to verify step 3, iser is not valid');
      return false;
   }

   if ($fbu->data->user_id == $rest->fbuser->id) return true;
   error_log('Fail to verify step 3, wrong user');
   return false;

}

function register_or_login_facebook($rest)
{
   $registerUser = $rest->fbuser;
   $username     = $registerUser->name;
   $externalid   = $registerUser->id;
   $registeredtype = 'fail';
   $usertype = 'facebook';
   error_log("## before check: " . $username . "; usertype: " . $usertype);
   $user = null;

   if (verify_fb_user_stub($rest))
   {
      if (($user = check_fb_user($usertype, $externalid))  instanceof WP_User)
      {
         error_log ('## LOGIN: id:' . $user->ID);
         $registeredtype = 'login';
      } else if(($user = register_fb_user($username, $usertype, $externalid))  instanceof WP_User)
      {
         error_log ('## LOGIN: id:' . $user->ID);
         $registeredtype = 'new';
      }
   }else
   {
      $registeredtype = 'fail_remote_verify';
   }
   error_log ('## LOGIN: registeredtype:' . $registeredtype);
   if ( $registeredtype == 'new' ||  $registeredtype == 'login')
   {
      error_log ('## LOGIN: registeredtype: NOT FAIL');
      if (is_user_logged_in())
      {
         error_log ('## LOGIN: is logged in');
         $registeredtype = 'continue';
      }
      if (!is_user_logged_in())
      {
         error_log ('## LOGIN: start logging in: ' . $user->ID . "; login: " . $user->user_login);
         error_log ('## LOGIN: is_ssl:'  . is_ssl());
         //wp_clear_auth_cookie();
         wp_set_auth_cookie  ( $user->ID, 1);//false );
         wp_set_current_user ( $user->ID, $user->user_login);
         //do_action( 'wp_login', $user->user_login );
         //var_dump ($usr);
         error_log ('## LOGIN: is logged in: is_user_logged_in():' . is_user_logged_in());
         //wp_safe_redirect( user_admin_url() );
         //exit();
      }
   }

   $status = 'ok';

   $data = ['registered' => ['status' => $status, 'type' =>  $registeredtype]];

   error_log("## register:" . json_encode($data));
   return $data;
}

function check_fb_user($usertype, $externalid)
{
   error_log ('## ' . __FUNCTION__ . '() external_id:' . $externalid);
   $user = get_users(array(
            'meta_key'     => 'f_oauth_facebook_id',
            'meta_value'   => $externalid,
            'count_total'  => false
         ));
   if ($user == null || (is_array($user) && count($user) < 1)) return null;

   if (is_array($user) && count($user) > 1)  error_log ('## ' . __FUNCTION__ . '() warning error: found user duplicates');
   if (is_array($user) && count($user) > 0)  $user = $user[0];

   if (is_wp_error ($user) )
      error_log ('## ' . __FUNCTION__ . '() error:  code:' . $user->get_error_code() . '; message:' . $user->get_error_message () );
   else if (! $user instanceof WP_User)
      error_log ("## Check facebook user: not an instance of facebook user");

   error_log ('## ' . __FUNCTION__ . '() id:' . $user->ID);

   return $user;
}
function register_fb_user($username, $usertype, $externalid)
{
   $user_email =  'e' . uniqid() . '@gmail.com';
   $password = 'facebook_la_toti';
   //$result = wp_insert_user(array(   'user_login'   => $username,
   //                                  'nice_name'    => $username,
   //                                  'user_email'   => $user_email,
   //                                  'user_pass'    => 'facebook_la_toti'   ));
   //$result = wp_insert_user(array(   'user_login'   => $username,
   //                                  //'nice_name'    => $username,
   //                                  'user_email'   => $user_email,
   //                                  'user_pass'    => 'facebook_la_toti',
   //                                  'display_name' => $username ));
   $result = wp_insert_user(array(  
                     'user_login'    =>  $username,
                     'user_email'    =>  $user_email,
                     'user_pass'     =>  $password,
                     'user_url'      =>  '',
                     'first_name'    =>  '',
                     'last_name'     =>  '',
					 'display_name'  => $username, 
                     'nickname'      =>  $nickname,
					 'nice_name'     => $username,
                     'description'   =>  'hero',
                     'role' => 'editor'
					 ));

   //$result = register_new_user( $username, $user_email );
   //$result = wp_create_user( $username, 'facebook_la_toti', $user_email );
   if ($result instanceof WP_Error)
   {
      error_log ('## ' . __FUNCTION__ . '() registration error:  code:' . $result->get_error_code() . '; message:' . $result->get_error_message () );
      if ($result->get_error_code() === 'existing_user_login')
      {
         error_log ('## ' . __FUNCTION__ . '() registration error: duplicate user'); //existing_user_login
         $result = $result; //try resolve user duplication
      }
      return $result;
   }
   else
   {
      $fb_user_id = $result;
      add_user_meta( $fb_user_id, 'f_oauth_facebook_id', $externalid, true /*unique*/ );
      $user = get_user_by('id', $fb_user_id);
      if ($user == null) error_log ('## ' . __FUNCTION__ . '() registration load?: null' );
      if (is_wp_error ($user) ) error_log ('## ' . __FUNCTION__ . '() registration load error:' . $user->get_error_message ());
      if ($user instanceof WP_User)  error_log ('## ' . __FUNCTION__ . '() registration WP Success:');
      return $user;
   }
   return null;
}


include 'login.php';
include 'includes/wporg.php';

#error_log (__FILE__ . " registering activation plugin");
register_activation_hook( __FILE__, array( 'FLoginPlugin', 'plugin_activated' ) );

###error_log("######## queries ################");
####ob_start();
####var_dump($wpdb->queries);
####$result = ob_get_clean();
###$result = var_export ($wpdb->queries, true);
###error_log ($result);
###error_log("##################################");

error_log ("## foauth end !!!");
