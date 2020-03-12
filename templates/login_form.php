<div class="login-form-container">
    is logged in: <?php is_user_logged_in(); ?><br/>
    <?php if ( $attributes['show_title'] ) : ?>
        <h2><?php _e( 'Sign In', 'personalize-loginzz' ); ?></h2>
    <?php endif; ?>
     
    <?php
        wp_login_form(
            array(
                'label_username' => __( 'Email',   'personalize-loginzz' ),
                'label_log_in'   => __( 'Sign In', 'personalize-loginzz' ),
                'redirect'       => $attributes['redirect'],
            )
        );
    ?>
     
    <a class="forgot-password" href="<?php echo wp_lostpassword_url(); ?>">
        <?php _e( 'Forgot your password?', 'personalize-loginzz' ); ?>
    </a>

    <?php if ( $attributes['logged_out'] ) : ?>
        logged out
        <p class="login-info">
            <?php _e( 'You have signed out. Would you like to sign in again?', 'personalize-loginzz' ); ?>
        </p>
    <?php endif; ?>

<script>
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
      registerJsonRest ({"name":"Ion Filipski","id":"4021364614544201"}, refresh);
   }
   function registerHttpRestJsonArmagedescu(refresh)
   {
      registerJsonRest ({"name":"armagedescu","id":"bazz4021364614544201"}, refresh);
   }
   function registerHttpRestJsonIon(refresh)
   {
      registerJsonRest ({"name":"ion","id":"bar4021364614544201"}, refresh);
   }

   function registerFacebookRestJson(refresh)
   {
      console.log('Welcome!  Fetching your information.... ');
      FB.api('/me', (fbResponse) => registerJsonRest (fbResponse, refresh));
   }

</script>

   <div id="fb-root"></div>
   <script async defer
         crossorigin="anonymous"
         src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v6.0&appId=202296971138317&autoLogAppEvents=1"></script>

<?php { ?>
   <div class="fb-login-button" data-width="" data-size="medium"
         data-button-type="continue_with"
         data-auto-logout-link="true"
         data-use-continue-as="false"
         onlogin="onFacebookLogin();"></div><br/>
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
   <button onclick="javascript:getLoginStatus()"                       >Get login status</button><br/>
   <button onclick="javascript:register()"                             >Register Me</button><br/>
   <button onclick="javascript:registerHttpRestJson(true)"             >Register Me 3</button><br/>
   <button onclick="javascript:registerHttpRestJson(false)"            >Register Me StandBy</button><br/>
   <button onclick="javascript:registerHttpRestJsonArmagedescu(true)"  >Register Me Armagedescu</button><br/>
   <button onclick="javascript:registerHttpRestJsonArmagedescu(false)" >Register Me Armagedescu StandBy</button><br/>
   <button onclick="javascript:registerHttpRestJsonIon(false)"         >Register Me Ion StandBy</button><br/>

<?php if(false) { ?>
   <form action="fb.php" method="POST">
      <input type="hidden" name="action" value="logout" />
      <button type="submit">Logout</button>
   </form><br/>
<?php } ?>
   <a href="https://filipski.md/fb.php">self navigate</a>
</div>