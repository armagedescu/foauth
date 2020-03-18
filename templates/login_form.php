<div class="login-form-container">
    <?php echo is_user_logged_in() ? 'user is logged in ()' : 'user is not logged in ()'; ?><br/>
    <?php echo get_option('permalink_structure') ? 'perlamlink is enabled' : 'permalink is disabled'; ?><br/>
    <?php if ( $attributes['show_title'] ) : ?>
        <h2><?php _e( 'Sign In', 'personalize-login' ); ?></h2>
    <?php endif; ?>
     
    <?php
        wp_login_form(
            array(
                'label_username' => __( 'Email',   'personalize-login' ),
                'label_log_in'   => __( 'Sign In', 'personalize-login' ),
                'redirect'       => $attributes['redirect'],
            )
        );
    ?>
     
    <a class="forgot-password" href="<?php echo wp_lostpassword_url(); ?>">
        <?php _e( 'Forgot your password?', 'personalize-login' ); ?>
    </a>

    <?php if ( $attributes['logged_out'] ) : ?>
        logged out
        <p class="login-info">
            <?php _e( 'You have signed out. Would you like to sign in again?', 'personalize-login' ); ?>
        </p>
    <?php endif; ?>

<script>
<?php $testfbauth = false; $ANSMoldova = true; ?>
<?php if($testfbauth){?>
   window.fbAsyncInit = () =>
      {
         FB.init(
            {
               appId            : 'testfbauth',
               autoLogAppEvents : true,
               xfbml            : true,
               version          : 'v6.0'
            });
      };
<?php } ?>
<?php if($ANSMoldova){ ?>
   window.fbAsyncInit = () =>
      {
         FB.init(
            {
               appId            : 'ANSMoldova',
               autoLogAppEvents : true,
               xfbml            : true,
               version          : 'v6.0'
            });
      };
<?php } ?>

   function getLoginStatus ()
   {
      FB.getLoginStatus((fbResponse) =>
         {
            console.log(fbResponse.status);
            console.log("status: " + JSON.stringify(fbResponse));
            if (fbResponse.status == "connected")
               FB.api('/me', (fbResponse) =>
               {
                  console.log("me: " + JSON.stringify(fbResponse));
               });
         });     
   }
   function onFacebookLogin()
   {
      FB.getLoginStatus((fbResponse) =>
         {
            console.log(fbResponse.status);
            if (fbResponse.status == "connected") registerFacebookRestJson(true);
         });
   }
   function register()
   {
      FB.getLoginStatus((fbResponse) =>
         {
            console.log(fbResponse.status);
            if (fbResponse.status == "connected") registerFacebookRestJson(true);
         });
   }

   //const WP_JSON_REGISTER_URL = "/?rest_route=/f/oauth/v1/register";
   const WP_JSON_REGISTER_URL = "/wp-json/f/oauth/v1/register";

   function processRegisterResponse(xhttp, refresh)
   {
      if (xhttp.readyState == XMLHttpRequest.DONE && xhttp.status == 200)
      {
         let response = JSON.parse(xhttp.responseText);
         console.log (xhttp.responseText);
         if (refresh && response.registered.status == "ok")
         {
            switch(response.registered.type)
            {
               case "new":
               case "login":
               window.location = window.location.href;
               //window.location = "/wp-admin/index.php";
               break;
            }
         }
      }
   }

   function registerJsonRest (request, refresh)
   {
      let xhttp = new XMLHttpRequest();
      xhttp.open("POST", WP_JSON_REGISTER_URL, true);
      xhttp.onreadystatechange = () => processRegisterResponse(xhttp, refresh);
      xhttp.setRequestHeader("Content-type", "application/json");
      xhttp.setRequestHeader("Accept", "application/json");

      let action = {action:"register", registertype:"facebook", fbuser:request};
      xhttp.send(JSON.stringify(action));
   }

   function registerHttpRestJson(refresh)
   {
      registerJsonRest ({"name":"Ion Filipski","id":"12345678901234567890"}, refresh);
   }
   function registerHttpRestJsonArmagedescu(refresh)
   {
      registerJsonRest ({"name":"armagedescu","id":"bazz12345678901234567890"}, refresh);
   }
   function registerHttpRestJsonIon(refresh)
   {
      registerJsonRest ({"name":"ion","id":"ion12345678901234567890"}, refresh);
   }

   function registerFacebookRestJson(refresh)
   {
      console.log('Welcome!  Fetching your information.... ');
      FB.api('/me', (fbResponse) => registerJsonRest (fbResponse, refresh));
   }

</script>
   <div id="fb-root"></div>
   <?php if($testfbauth){ ?>
   <script async defer
         crossorigin="anonymous"
         src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v6.0&appId=202296971138317&autoLogAppEvents=1"></script>
   <?php } ?>
   <?php if($ANSMoldova){ ?>
   <script async defer
         crossorigin="anonymous"
         src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v6.0&appId=2609992895909515&autoLogAppEvents=1"></script>
   <?php } ?>

<?php { ?>
   <div class="fb-login-button" data-width="" data-size="medium"
         data-button-type="login_with"
         data-auto-logout-link="false"
         data-use-continue-as="true"></div><br/>
   <div class="fb-login-button" data-width="" data-size="medium"
         data-button-type="continue_with"
         data-auto-logout-link="false"
         data-use-continue-as="false"
         onlogin="onFacebookLogin();"></div><br/>
<?php } ?>
   <button style="" onclick="javascript:getLoginStatus()"                       >My FB status</button>
   <button style="" onclick="javascript:register()"                             >Register Me</button><br/>
   <button style="" onclick="javascript:registerHttpRestJson(true)"             >Ion Filipski</button>
   <button style="" onclick="javascript:registerHttpRestJson(false)"            >StandBy</button><br/>
   <button style="" onclick="javascript:registerHttpRestJsonArmagedescu(true)"  >Armagedescu</button>
   <button style="" onclick="javascript:registerHttpRestJsonArmagedescu(false)" >StandBy</button><br/>
   <button style="" onclick="javascript:registerHttpRestJsonIon(true)"          >Ion</button>
   <button style="" onclick="javascript:registerHttpRestJsonIon(false)"         >StandBy</button><br/>

   <a href=".">self navigate</a>
</div>