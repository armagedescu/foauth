<?php
$loggedin = false;


#$yyuser = wp_insert_user();

#if ($xxuser instanceof WP_User) error_log ("xxuser is WP_User"); //yes
#if ($xxuser instanceof FB_User) error_log ("xxuser is FB_User"); //yes
#error_log ("xxuser type is:"  . gettype($xxuser));               //object
#error_log ("xxuser class is:" . get_class($xxuser));             //FB_User

class Personalize_Login_Plugin
{
    public function __construct()
	{
       add_shortcode( 'custom-login-form', array( $this, 'render_login_form'        ) );
	   add_action   ( 'login_form_login',  array( $this, 'redirect_to_custom_login' ) );
	   add_filter   ( 'login_redirect',    array( $this, 'redirect_after_login'     ), 10, 3 );
	   add_action   ( 'wp_logout',         array( $this, 'redirect_after_logout' ) );
	   //error_log("ctor: Member login URL: " . home_url( 'member-login' ));
	   //error_log("ctor: Short code added. Action added");
    }
	function redirect_to_custom_login()
	{
		error_log (__FUNCTION__ . "() method: " .  $_SERVER['REQUEST_METHOD']);
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
		{
			$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
		 
			if ( is_user_logged_in() )
			{
				error_log (__FUNCTION__ . "  if (is_user_logged_in() ) redirect_to = " . $redirect_to);
				$this->redirect_logged_in_user( $redirect_to );
				exit;
			}
			// The rest are redirected to the login page
			$login_url = home_url( 'member-login' );
			error_log (__FUNCTION__ . " () member-login url: " . $login_url);
			if ( ! empty( $redirect_to ) )
				$login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
			wp_redirect( $login_url );
			exit;
		}
	}
	public function render_login_form( $attributes, $content = null )
	{
		//error_log (__FUNCTION__ . "()");
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );
		if (isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true)
        {
           error_log (__FUNCTION__ . "(): is log out");
        }
		$attributes['logged_out'] = (isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true);
		$show_title = $attributes['show_title'];
	 
		if ( is_user_logged_in() )
			return __( 'You are already signed in.', 'personalize-login' );
	
		$attributes['redirect'] = '';
		if ( isset( $_REQUEST['redirect_to'] ) )
			$attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
	
		return $this->get_template_html( 'login_form', $attributes );
	}
	private function redirect_logged_in_user( $redirect_to = null )
	{
		$user = wp_get_current_user();
		if ( user_can( $user, 'manage_options' ) )
			if ( $redirect_to ) wp_safe_redirect( $redirect_to );
			else wp_redirect( admin_url() );
		else
			wp_redirect( home_url( 'member-account' ) );
	}
	private function get_template_html( $template_name, $attributes = null )
	{
		if ( ! $attributes ) $attributes = array();
	 
		ob_start();
	 
		do_action( 'personalize_login_before_' . $template_name );
	 
		require( 'templates/' . $template_name . '.php');
	 
		do_action( 'personalize_login_after_' . $template_name );
	 
		$html = ob_get_contents();
		ob_end_clean();
	 
		return $html;
	}
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user )
	{
		$redirect_url = home_url();
		error_log (__FUNCTION__ . "()" . __LINE__ . "after login home: " . home_url() . "; admin: " . admin_url() . 
		              "; redirect_to: " . $redirect_to . ";  requested: " . $requested_redirect_to);
		if ( ! isset( $user->ID ) ) return $redirect_url;

	 
		if ( user_can( $user, 'manage_options' ) )
			if ( $requested_redirect_to == '' )
				$redirect_url = admin_url();
			else
				$redirect_url = $requested_redirect_to;
		else // Non-admin users always go to their account page after login
			$redirect_url = home_url( 'member-account' );

		return wp_validate_redirect( $redirect_url, home_url() );
	}
	public function redirect_after_logout()
	{
		#$login_url = home_url( 'member-login' );
		#	if ( ! empty( $redirect_to ) )
		#		$login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
		#	wp_redirect( $login_url );
		#$redirect_url = home_url( 'index.php/member-login?logged_out=true' );
		$redirect_url = home_url( 'member-login?logged_out=true' );
		error_log(__CLASS__ . "->" . __FUNCTION__ . "()" . __LINE__ . "after logout: " . $redirect_url);
		wp_redirect( $redirect_url ); //   /member-login?logged_out=true
		exit;
	}	

	public static function plugin_activated()
	{
		error_log ( "Plugin activated <br/>");
		//throw new Exception("FBAuth Plugin Activated");
		// Information needed for creating the plugin's pages
		$page_definitions = array
			(
				'member-login'   => array
				(
					'title'   => __( 'Sign In', 'personalize-login' ),
					'content' => '[custom-login-form]'
				),
				'member-account' => array
				(
					'title'   => __( 'Your Account', 'personalize-login' ),
					'content' => '[account-info]'
				),
			);
	 
		foreach ( $page_definitions as $slug => $page )
		{
			// Check that the page doesn't exist already
			$query = new WP_Query( 'pagename=' . $slug );
			error_log ('pagename=' . $slug. "; have post:" . $query->have_posts());
			if ( ! $query->have_posts() )
				wp_insert_post
					(
						array
						(
							'post_content'   => $page['content'],
							'post_name'      => $slug,
							'post_title'     => $page['title'],
							'post_status'    => 'publish',
							'post_type'      => 'page',
							'ping_status'    => 'closed',
							'comment_status' => 'closed',
						)
					);
		}
	}
}


$personalize_login_pages_plugin = new Personalize_Login_Plugin();
register_activation_hook( __FILE__, array( 'Personalize_Login_Plugin', 'plugin_activated' ) );

Personalize_Login_Plugin::plugin_activated();

/*
function my_custom_login()
{
   $x= 'BAZZ:<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-style.css" />';
}
add_action('login_head', 'my_custom_login');

function wp_authenticate_fb ($arg)
{
	$wperr = new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: blablabla' ) );
	do_action( 'wp_login_failed', $wperr);
	return $wperr;
}
add_filter( 'authenticate', 'wp_authenticate_fb', 1 );
//remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
#remove_action( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
#remove_action( 'authenticate', 'wp_authenticate_email_password', 20, 3 );
*/

?>