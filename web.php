<?php 
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Http\Request;
Route::get('/facebook/login', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb,Request $request)
{
	$first = $request->get('first');
	if(!empty($first)){
		echo 'İlk kez sisteme girdiniz. Sizi tekrardan içeri kabul edeceğiz. Ancak şu adımları izlemeniz gerekiyor.
		<ul><li>Facebook giriş yapın ve bildirimlerden <b>Tolga Taşçı Sizi TT test kullanıcısı olarak ekliyor.</b> Tıklayın.</li>
		<li>Kabul edin ve tekrardan bu sayfaya dönün ve giriş yapın. </li>
		<li>İstenilen izinlere, izin veriniz.</li>
		<li><font color="red">Size gelen bildirimi kabul etmeden sisteme tekrar giriş yapmayın sakın. Aksi halde kabul permission hataları karşılaşcaksın.</font></li>
		</ul>
		<br />';
	}
    $login_url = $fb->getLoginUrl([
        'email','manage_pages','ads_management',
        'ads_read','business_management',
        'instagram_basic','instagram_manage_comments',
        'instagram_manage_comments','instagram_manage_insights',
        'pages_messaging','pages_messaging_subscriptions',
        'pages_messaging_phone_number','pages_show_list','publish_actions',
        'publish_pages','read_audience_network_insights','rsvp_event',
        'read_page_mailboxes','read_insights'
    ]);

    // Obviously you'd do this in blade :)
    echo '<a href="' . $login_url . '">Login with Facebook</a>';
});

Route::get('/facebook/callback', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
{
	
	
    // Obtain an access token.
    try {
        $token = $fb->getAccessTokenFromRedirect();
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        dd($e->getMessage());
    }

    // Access token will be null if the user denied the request
    // or if someone just hit this URL outside of the OAuth flow.
    if (! $token) {
        // Get the redirect helper
        $helper = $fb->getRedirectLoginHelper();

        if (! $helper->getError()) {
            abort(403, 'Unauthorized action.');
        }

        // User denied the request
        dd(
            $helper->getError(),
            $helper->getErrorCode(),
            $helper->getErrorReason(),
            $helper->getErrorDescription()
        );
    }

    if (! $token->isLongLived()) {
        // OAuth 2.0 client handler
        $oauth_client = $fb->getOAuth2Client();

        // Extend the access token.
        try {
            $token = $oauth_client->getLongLivedAccessToken($token);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }
    }

    $fb->setDefaultAccessToken($token);


    session(['fb_user_access_token'=> $token]);


    // Get basic info on the user from Facebook.
    try {
        $response = $fb->get('/me?fields=id,name,email');
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        dd($e->getMessage());
    }

    // Convert the response to a `Facebook/GraphNodes/GraphUser` collection
    $facebook_user = $response->getGraphUser();
 
 


    // Create the user if it does not exist or update the existing entry.
    // This will only work if you've added the SyncableGraphNodeTrait to your User model.
    $user = App\User::register_or_update([
        'name' => $facebook_user->getName(),
        'email' => $facebook_user->getId().'@'.$facebook_user->getId().'.com',
        'password' => bcrypt("nonpasswordx"),
        'fb_id' =>$facebook_user->getId(),
        'token' =>$token,
    ],$fb,$facebook_user,$token);

    // Log the user into Laravel
	
	if($user=="first"){
		  return redirect('/facebook/login?first=true')->with('message', 'Successfully logged in with Facebook');
	}
   Auth::login($user);

  return redirect('/pages')->with('message', 'Successfully logged in with Facebook');
});
