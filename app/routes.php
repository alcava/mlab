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
/*
Route::get('admin/logout', array('as' => 'admin.logout', 'uses' => 'App\Controllers\Subdomain\Admin\AuthController@getLogout'));
Route::get('admin/login', array('as' => 'admin.login', 'uses' => 'App\Controllers\Subdomain\Admin\AuthController@getLogin'));
Route::post('admin/login', array('as' => 'admin.login.post', 'uses' => 'App\Controllers\Admin\Subdomain\AuthController@postLogin'));

Route::group(array('prefix' => 'admin', 'before' => 'auth.admin'), function()
{
        Route::any('/', 'App\Controllers\Admin\PagesController@index');
        //Route::resource('articles', 'App\Controllers\Admin\ArticlesController');
        //Route::resource('pages', 'App\Controllers\Admin\PagesController');
});
*/
Route::pattern('sub', '^((?!www).)*$');

Route::group(array('domain' => '{sub}.mlab.com'), function() {
    // Subdomain routes
  Route::group(["before" => "guest"], function()
  {

   Route::get("/", [
    "as"   => "subdomain",
    "uses" => "IndexController@indexDomainAction"
    ]);

  Route::get('user/login', [
    "as"    => "user/login",
    "uses"  => "SubdomainUserController@getLoginAction"
    ]);

  Route::post('user/login', [
    "as"    => "user/login",
    "uses"  => "SubdomainUserController@postLoginAction"
    ]);

  Route::get("user/request", [
   "as"   => "user/request",
   "uses" => "SubdomainUserController@getRequestAction"
   ]);

  Route::post("user/request", [
   "as"   => "user/request",
   "uses" => "SubdomainUserController@postRequestAction"
   ]);

  Route::get("user/reset{resetCode?}", [
   "as"   => "user/reset",
   "uses" => "SubdomainUserController@getResetAction"
   ]);

  Route::post("user/reset{resetCode?}", [
   "as"   => "user/reset",
   "uses" => "SubdomainUserController@postResetAction"
   ]);

  });

  Route::group(["before" => "auth"], function($user) {

    Route::any("user/profile", [
      "as"   => "user/profile",
      "uses" => "SubdomainUserController@profileAction"
      ]);

    Route::any("user/logout", [
      "as"   => "user/logout",
      "uses" => "SubdomainUserController@logoutAction"
      ]);

  });

});

// Main site routes (works for mylab.com and www.mlab.com)
Route::group(["before" => "guest"], function()
{

  Route::get("/", [
    "as"   => "domain",
    "uses" => "DomainUserController@getIndexDomainAction"
    ]);

  Route::post("/", [
    "as"   => "domain",
    "uses" => "DomainUserController@postIndexDomainAction"
    ]);

  Route::get('user/activate/{activationCode?}/{subdomainId?}', [
    "as"    => "user/activate",
    "uses"  => "DomainUserController@getActivateAction"
    ]);

  Route::get('user/login', [
    "as"    => "user/login",
    "uses"  => "DomainUserController@getLoginAction"
    ]);

  Route::post('user/login', [
    "as"    => "user/login",
    "uses"  => "DomainUserController@postLoginAction"
    ]);

  Route::get("user/request", [
   "as"   => "user/request",
   "uses" => "DomainUserController@getRequestAction"
   ]);

  Route::post("user/request", [
   "as"   => "user/request",
   "uses" => "DomainUserController@postRequestAction"
   ]);

  Route::get("user/reset{resetCode?}", [
   "as"   => "user/reset",
   "uses" => "DomainUserController@getResetAction"
   ]);

  Route::post("user/reset{resetCode?}", [
   "as"   => "user/reset",
   "uses" => "DomainUserController@postResetAction"
   ]);



});

Route::group(["before" => "auth"], function() {

  Route::any("user/chooseProfile", [
      "as"   => "user/chooseProfile",
      "uses" => "DomainUserController@getChooseProfileAction"
      ]);

  Route::any("user/profile", [
    "as"   => "user/profile",
    "uses" => "DomainUserController@profileAction"
    ]);

  Route::any("user/logout", [
    "as"   => "user/logout",
    "uses" => "DomainUserController@logoutAction"
    ]);

});