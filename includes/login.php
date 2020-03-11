<?php
class Personalize_Login_Plugin
{
    public function __construct()
	{
       add_shortcode( 'custom-login-form', array( $this, 'render_login_form' ) );
	   add_action   ( 'login_form_login', array( $this, 'redirect_to_custom_login' ) );
	   echo $this->get_template_html('login_form');
    }
	function redirect_to_custom_login()
	{
		echo home_url( 'member-login' );
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
		{
			$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
		 
			if ( is_user_logged_in() ) {
				echo "exit<br/>";
				$this->redirect_logged_in_user( $redirect_to );
				exit;
			}
			// The rest are redirected to the login page
			$login_url = home_url( 'member-login' );
			if ( ! empty( $redirect_to ) )
				$login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
			wp_redirect( $login_url );
			exit;
		}
	}

	public function render_login_form( $attributes, $content = null )
	{
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );
		$show_title = $attributes['show_title'];
	 
		if ( is_user_logged_in() )
			return __( 'You are already signed in.', 'personalize-login' );

		$attributes['redirect'] = '';
		if ( isset( $_REQUEST['redirect_to'] ) )
			$attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );

		return $this->get_template_html( 'login_form', $attributes );
	}
	private function get_template_html( $template_name, $attributes = null )
	{
		if ( ! $attributes ) $attributes = array();
	 
		ob_start();
	 
		do_action( 'personalize_login_before_' . $template_name );
	 
		require( 'login/templates/' . $template_name . '.php');
	 
		do_action( 'personalize_login_after_' . $template_name );
	 
		$html = ob_get_contents();
		ob_end_clean();
	 
		return $html;
	}

	public static function plugin_activated()
	{
		// Information needed for creating the plugin's pages
		$page_definitions = array
			(
				'member-login' => array
				(
					'title' => __( 'Sign In', 'personalize-login' ),
					'content' => '[custom-login-form]'
				),
				'member-account' => array
				(
					'title' => __( 'Your Account', 'personalize-login' ),
					'content' => '[account-info]'
				),
			);
	 
		foreach ( $page_definitions as $slug => $page )
		{
			// Check that the page doesn't exist already
			$query = new WP_Query( 'pagename=' . $slug );
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
echo  __FILE__, "<br/>";
$personalize_login_pages_plugin = new Personalize_Login_Plugin();
register_activation_hook( __FILE__, array( 'Personalize_Login_Plugin', 'plugin_activated' ) );



function my_custom_login()
{
   echo 'BAZZ:<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-style.css" />';
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


?>