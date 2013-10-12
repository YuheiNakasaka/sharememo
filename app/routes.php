<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
//HOME
Route::get('/','HomeController@index');
//user page
Route::get('/memo','UserMainController@index');
//create new memo
Route::post('/memo/create',array('before'=>'csrf','uses'=>'UserMainController@create'));
//user notes
Route::get('/memo/notes','UserMainController@notes');
//not authrized user notes
Route::get('/memo/{id?}/notes','UserMainController@notes');
//search for memos
Route::get('/memo/notes/search','UserMainController@searchnotes');
//user drafts
Route::get('/memo/drafts','UserMainController@drafts');
//user drafts
Route::get('/memo/draft/{id}','UserMainController@editdraft');
//user drafts
Route::post('/memo/draft/{id}/delete','UserMainController@deletedraft');
//auto save
Route::post('/memo/autosave','UserMainController@autosave');
//show user memo
Route::get('/memo/{id}','UserMainController@show');
//edit user memo
Route::get('/memo/{id}/edit','UserMainController@edit');
//update user memo
Route::post('/memo/{id}/update',array('before'=>'csrf','uses'=>'UserMainController@update'));
//delete user memo
Route::get('/memo/{id}/delete','UserMainController@delete');


//Auth
Route::filter('user',function(){
   if(Auth::check() == false) return Redirect::to('/');
   //if(!Session::get('laravel')) return Redirect::to('/');
});

//Twitter oauth
Route::get('twlogin',function(){
    if(Auth::check()){
        return Redirect::to('/memo');
    }
    $tokens = Twitter::oAuthRequestToken();
    Twitter::oAuthAuthorize(array_get($tokens,'oauth_token'));
    die;
});
//Twitter oauth calback
Route::get('twlogin/callback',function(){
    $token = Input::get('oauth_token');
    $verifier = Input::get('oauth_verifier');
    $accessToken = Twitter::oAuthAccessToken($token,$verifier);

    if(isset($accessToken['user_id'])){
        $user_id = $accessToken['user_id'];
        $user = User::find($user_id);
        if(empty($user)){
            $user = new User;
            $user->id = $user_id;
        }
        $user->screen_name = $accessToken['screen_name'];
        $user->oauth_token = $accessToken['oauth_token'];
        $user->oauth_token_secret = $accessToken['oauth_token_secret'];
        $user->save();

        Auth::login($user);
        //return Redirect::action('UserMainController@index');
        //return var_dump(Auth::user());die;
        //Session::set('user','user');
        return Redirect::to('/memo');
    }else{
        return Redirect::to('/');
    }
});

//facebook login
Route::get('fblogin',function(){
    $facebook = new Facebook(Config::get('facebook'));
    $config = array(
            'redirect_uri' => url('fblogin/callback')
    );
    return Redirect::to($facebook->getLoginUrl($config));
});

Route::get('fblogin/callback', function() {
    $code = Input::get('code');
    if (strlen($code) == 0) {
        return Redirect::to('/');
    }

    $facebook = new Facebook(Config::get('facebook'));
    $user_id = $facebook->getUser();

    if ($user_id == 0) {
        return Redirect::to('/');
    }

    $user = User::find($user_id);
    if (empty($user)) {
        $user = new User;
        $user->id = $user_id;
    }

    $me = $facebook->api('/me');
    $user->screen_name = $me['name'];
    $user->oauth_token = 'facebook';
    $user->oauth_token_secret = $facebook->getAccessToken();
    $user->save();

    Auth::login($user,true);
    return Redirect::to('/memo');
    //return Redirect::to('/memo');
});

//logout
Route::get('logout',function(){
    Auth::logout();
    return Redirect::to('/');
});

App::missing(function($exception){
    $contents = "<html>
<body>
<h1>404 not found or you are not allowed!</h1>
<p><a href='http://www.sharememo.net/'>←Back to home?</a></p>
</body>
</html>";
    return Response::make($contents);
});
