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

error_log("## foauth start!!!");

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
   $entityBody  = file_get_contents('php://input');
   error_log (__FILE__ . ": POST: " . $entityBody);
   
   if ($_SERVER["CONTENT_TYPE"] === 'application/json')
   {
      error_log ("## POST json:   " . $entityBody);
      $rest = json_decode($entityBody);
      error_log ("## POST action: " . $rest->action);
   }
}

include 'login.php';
include 'includes/wporg.php';

function f_oauth_register_rest( $request )
{
   error_log ("## " . __FUNCTION__ . "() Encoding Current:  " . $_SERVER["CONTENT_TYPE"]);
   error_log ("## " . __FUNCTION__ . "() Encoding Required: " . $_SERVER["HTTP_ACCEPT"]);
   error_log ("## " . __FUNCTION__ . "() Cotent: " . $request->get_body());

   if ($request ['action'] === 'register')
      return register_or_login($request);

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
   error_log ("password: " . wp_hash_password('facebook_la_toti'));
   if($rest ['registertype'] === 'facebook')
      return register_or_login_facebook ($rest);
   return true;
}

function register_or_login_facebook($rest)
{
   $registerUser = $rest ['fbuser'];
   $username     = $registerUser ['name'];
   $externalid   = $registerUser ['id'];
   $registeredtype = 'fail';
   $usertype = 'facebook';
   error_log("## before check: " . $username . "; usertype: " . $usertype);
   $user = null;

   if (($user = check_fb_user($usertype, $externalid))  instanceof WP_User)
   {
      error_log ('## LOGIN: id:' . $user->ID);
      $registeredtype = 'login';
   } else if(($user = register_fb_user($username, $usertype, $externalid))  instanceof WP_User)
   {
      error_log ('## LOGIN: id:' . $user->ID);
      $registeredtype = 'new';
   }
   error_log ('## LOGIN: registeredtype:' . $registeredtype);
   if ( $registeredtype != 'fail')
   {
      error_log ('## LOGIN: registeredtype: NOT FAIL');
      if (is_user_logged_in())
      {
         error_log ('## LOGIN: is logged in');
         $registeredtype = 'continue';
      }
      if (!is_user_logged_in())
      {
         error_log ('LOGIN: is not logged in id:' . $user->ID . "; login:" . $user->user_login);
         //wp_clear_auth_cookie();
         wp_set_auth_cookie  ( $user->ID, false );
         //$usr = wp_set_current_user ( $user->ID, $user->user_login);
		 //var_dump ($usr);
         error_log ('## LOGIN: is logged in: ' . is_user_logged_in());
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
   error_log ('## check_fb_user external_id:' . $externalid);
   $user = get_users(array(
            'meta_key'     => 'f_oauth_facebook_id',
            'meta_value'   => $externalid,
            'count_total'  => false
         ));
   if ($user == null || (is_array($user) && count($user) < 1)) return null;

   if (is_array($user) && count($user) > 1)  error_log ('## check_fb_user warning error: found user duplicates');
   if (is_array($user) && count($user) > 0)  $user = $user[0];

   if (is_wp_error ($result) )
      error_log ('## check_fb_user error:  code:' . $result->get_error_code() . '; message:' . $result->get_error_message () );
   else if (! $user instanceof WP_User)
      error_log ("## Check facebook user: not an instance of facebook user");

   error_log ('## check_fb_user id:' . $user->ID);

   return $user;
}
function register_fb_user($username, $usertype, $externalid)
{
   $user_email =  'e' . uniqid() . '@gmail.com';
   //$result = wp_insert_user(array(   'user_login'   => $username,
   //                                  'nice_name'    => $username,
   //                                  'user_email'   => $user_email,
   //                                  'user_pass'    => 'facebook_la_toti'   ));
   $result = wp_insert_user(array(   'user_login'   => $username,
                                     //'nice_name'    => $username,
                                     'user_email'   => $user_email,
                                     'user_pass'    => 'facebook_la_toti',
                                     'display_name' => $username ));

   //$result = register_new_user( $username, $user_email );
   //$result = wp_create_user( $username, 'facebook_la_toti', $user_email );
   if ($result instanceof WP_Error)
   {
      error_log ('## registration error:  code:' . $result->get_error_code() . '; message:' . $result->get_error_message () );
      if ($result->get_error_code() === 'existing_user_login')
      {
         error_log ('## registration error: duplicate user'); //existing_user_login
         $result = $result; //try resolve user duplication
      }
      return $result;
   }
   else
   {
      $fb_user_id = $result;
      add_user_meta( $fb_user_id, 'f_oauth_facebook_id', $externalid, true /*unique*/ );
      $user = get_user_by('id', $fb_user_id);
      if ($user == null) error_log ('## registration load?: null' );
      if (is_wp_error ($user) ) error_log ('## registration load error:' . $user->get_error_message ());
      if ($user instanceof WP_User)  error_log ('## registration WP Success:');
      return $user;
   }
   return null;
}

error_log ("## foauth end !!!");
