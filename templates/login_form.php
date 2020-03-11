<?php
//$loggedin = false;
//"{"action":"register","registertype":"facebook","fbuser":{"name":"Ion Filipski","id":"4021364614544201"}}"
?>
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

   function registerHttpRestJson(refresh)
   {
      let xhttp = new XMLHttpRequest();
      // /wp-json/f/oauth/v1/register
      xhttp.open("POST", "/wp-json/f/oauth/v1/register", true);
      //xhttp.open("POST", "/?rest_route=/f/oauth/v1/register", true);
      xhttp.onreadystatechange = () =>
         {
            if (xhttp.readyState == XMLHttpRequest.DONE && xhttp.status == 200)
            {
               let response = JSON.parse(xhttp.responseText);
               console.log (xhttp.responseText);
               if (refresh && response.registered.status == "ok")
                  switch(response.registered.type) { case "new": case "login": window.location = window.location.href; break; }
            }
         };
      xhttp.setRequestHeader("Content-type", "application/json");
      xhttp.setRequestHeader("Accept", "application/json");
	  let fbResponse = {"name":"Ion Filipski","id":"4021364614544201"};
      let action = {action:"register", registertype:"facebook", fbuser:fbResponse};
      xhttp.send(JSON.stringify(action));
   }
   function registerHttpRestJsonArmagedescu(refresh)
   {
      let xhttp = new XMLHttpRequest();
      // /wp-json/f/oauth/v1/register
      xhttp.open("POST", "/wp-json/f/oauth/v1/register", true);
      //xhttp.open("POST", "/?rest_route=/f/oauth/v1/register", true);
      xhttp.onreadystatechange = () =>
         {
            if (xhttp.readyState == XMLHttpRequest.DONE && xhttp.status == 200)
            {
               let response = JSON.parse(xhttp.responseText);
               console.log (xhttp.responseText);
               if (refresh && response.registered.status == "ok")
                  switch(response.registered.type) { case "new": case "login": window.location = window.location.href; break; }
            }
         };
      xhttp.setRequestHeader("Content-type", "application/json");
      xhttp.setRequestHeader("Accept", "application/json");
	  let fbResponse = {"name":"armagedescu","id":"bazz4021364614544201"};
      let action = {action:"register", registertype:"facebook", fbuser:fbResponse};
      xhttp.send(JSON.stringify(action));
   }
   function registerHttpRestJsonIon(refresh)
   {
      let xhttp = new XMLHttpRequest();
      // /wp-json/f/oauth/v1/register
      xhttp.open("POST", "/wp-json/f/oauth/v1/register", true);
      //xhttp.open("POST", "/?rest_route=/f/oauth/v1/register", true);
      xhttp.onreadystatechange = () =>
         {
            if (xhttp.readyState == XMLHttpRequest.DONE && xhttp.status == 200)
            {
               let response = JSON.parse(xhttp.responseText);
               console.log (xhttp.responseText);
               if (refresh && response.registered.status == "ok")
                  switch(response.registered.type) { case "new": case "login": window.location = window.location.href; break; }
            }
         };
      xhttp.setRequestHeader("Content-type", "application/json");
      xhttp.setRequestHeader("Accept", "application/json");
	  let fbResponse = {"name":"ion","id":"bar4021364614544201"};
      let action = {action:"register", registertype:"facebook", fbuser:fbResponse};
      xhttp.send(JSON.stringify(action));
   }

   function registerFacebookRestJson(refresh)
   {
      console.log('Welcome!  Fetching your information.... ');
      FB.api('/me', (fbResponse) =>
         {
            //console.log('FB Login for: name=' + fbResponse.name + "; id=" + fbResponse.id);
            let xhttp = new XMLHttpRequest();
			xhttp.open("POST", "/wp-json/f/oauth/v1/register", true);
			//xhttp.open("POST", "/?rest_route=/f/oauth/v1/register", true);
            xhttp.onreadystatechange = () =>
               {
                  if (xhttp.readyState == XMLHttpRequest.DONE && xhttp.status == 200)
                  {
                     console.log (xhttp.responseText);
                     let response = JSON.parse(xhttp.responseText);
                     if (response.registered.status == "ok" && refresh)
                        switch(response.registered.type) { case "new": case "login": window.location = window.location.href; break; }
                  }
               };
            xhttp.setRequestHeader("Content-type", "application/json");
            xhttp.setRequestHeader("Accept",       "application/json");
            let action = {action:"register", registertype:"facebook", fbuser:fbResponse};
            xhttp.send(JSON.stringify(action));
         });
   }

</script>

   <div id="fb-root"></div>
   <script async defer
         crossorigin="anonymous"
         src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v6.0&appId=202296971138317&autoLogAppEvents=1"></script>

<?php //if(!$loggedin) { error_log("content: " . $loggedin); ?>
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
   <button onclick="javascript:getLoginStatus()"                   >Get login status</button><br/>
   <button onclick="javascript:register()"                         >Register Me</button><br/>
   <button onclick="javascript:registerHttpRestJson(true)"             >Register Me 3</button><br/>
   <button onclick="javascript:registerHttpRestJson(false)"            >Register Me StandBy</button><br/>
   <button onclick="javascript:registerHttpRestJsonArmagedescu(true)"  >Register Me Armagedescu</button><br/>
   <button onclick="javascript:registerHttpRestJsonArmagedescu(false)" >Register Me Armagedescu StandBy</button><br/>
   <button onclick="javascript:registerHttpRestJsonIon(false)" >Register Me Ion StandBy</button><br/>
<?php //if($loggedin) { ?>
<?php if(false) { ?>
   <form action="fb.php" method="POST">
      <input type="hidden" name="action" value="logout" />
      <button type="submit">Logout</button>
   </form><br/>
<?php } ?>
   <a href="https://filipski.md/fb.php">self navigate</a>
</div>