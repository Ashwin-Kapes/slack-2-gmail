<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('pages.app');
});



Route::group(['middleware' => ['guest']], function () {
    Route::get('/login', function () {
        Auth::logout();
        return view('pages.login');
    })->name('login');
    Route::get('/join', function () {
        Auth::logout();
        return view('pages.join');
    })->name('join');
    Route::get('/oauth2/gmail', 'Auth\GoogleOauth2Controller@redirectToProvider');
    Route::get('/oauth2/gmail/validate', 'Auth\GoogleOauth2Controller@handleProviderCallback');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/app', function () {
        return redirect('/slack/orgs');
    });
    Route::get('/logout', 'Auth\LogoutController@logout');

    Route::get('/slack/orgs', 'Slack\OrgsController@list');
    Route::post('/slack/orgs', 'Slack\OrgsController@new');

    Route::group(['middleware' => ['is_org_owner']], function () {
        Route::get('/slack/orgs/{id}/remove', 'Slack\OrgsController@delete');
        Route::get('/slack/orgs/{id}/channels', 'Slack\ChannelsController@list');
        Route::post('/slack/orgs/{id}/channels', 'Slack\ChannelsController@update');
    });
});
