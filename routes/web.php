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



Route::get('/lang/{lang?}', function($lang=NULL){
    Session::put('language', $lang);
    return Redirect::back();
})->name('lang');

Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('welcome');


Route::get('/imprint', function() {
    return view('imprint');
})->name('imprint');

Route::get('/privacy', [
    'uses' => 'PrivacyAgreementController@intercept'
])->name('privacy');

Route::get('/about', function() {
    return view('about');
})->name('about');

Route::get('/profile/{username}', [
    'uses' => 'FrontendUserController@getProfilePage',
    'as'   => 'account.show'
]);

Route::get('/leaderboard', [
    'uses' => 'FrontendUserController@getLeaderboard',
    'as'   => 'leaderboard',
]);

Route::get('/statuses/active', [
    'uses' => 'FrontendStatusController@getActiveStatuses',
    'as'   => 'statuses.active',
]);

Auth::routes(['verify' => true]);

Route::get('/auth/redirect/{provider}', 'SocialController@redirect');
Route::get('/callback/{provider}', 'SocialController@callback');
Route::get('/status/{id}', 'FrontendStatusController@getStatus');

Route::get('/blog', [
    'uses'  => 'BlogController@all',
    'as'    => 'blog.all'
]);
Route::get('/blog/{slug}', [
    'uses'  => 'BlogController@show',
    'as'    => 'blog.show'
]);
Route::get('/blog/cat/{cat}', [
    'uses'  => 'BlogController@category',
    'as'    => 'blog.category'
]);

/** 
 * These routes can be used by logged in users although they have not signed the privacy policy yet.
 */
Route::middleware(['auth'])->group(function() {
    Route::get('/gdpr-intercept', [
        'uses' => 'PrivacyAgreementController@intercept',
        'as'   => 'gdpr.intercept'
    ]);

    Route::post('/gdpr-ack', [
        'uses' => 'PrivacyAgreementController@ack',
        'as'   => 'gdpr.ack'
    ]);

    Route::get('/settings/destroy', [
        'uses' => 'UserController@destroyUser',
        'as'   => 'account.destroy',
    ]);

});

/**
 * All of these routes can only be used by fully registered users.
 */
Route::middleware(['auth', 'privacy'])->group(function() {

    Route::post('/destroy/provider', [
        'uses'  => 'SocialController@destroyProvider',
        'as'    => 'provider.destroy',
    ]);

    Route::post('/settings/password', [
        'uses' => 'UserController@updatePassword',
        'as'   => 'password.change',
    ]);

    //this has too much dumb logic, that it'll remain inside of the UserController...
    //will leave settings inside of UserController...
    Route::get('/settings', [
        'uses' => 'UserController@getAccount',
        'as'   => 'settings',
    ]);

    Route::post('/settings', [
        'uses' => 'UserController@updateSettings',
        'as'   => 'settings',
    ]);

    Route::get('/settings/delsession', [
        'uses' => 'UserController@deleteSession',
        'as'   => 'delsession',
    ]);

    Route::get('/dashboard', [
        'uses' => 'FrontendStatusController@getDashboard',
        'as'   => 'dashboard',
    ]);

    Route::get('/dashboard/global', [
        'uses' => 'FrontendStatusController@getGlobalDashboard',
        'as'   => 'globaldashboard',
    ]);

    Route::delete('/destroystatus', [
        'uses' => 'FrontendStatusController@DeleteStatus',
        'as'   => 'status.delete',
    ]);

    Route::post('/edit', [
        'uses' => 'FrontendStatusController@EditStatus',
        'as' => 'edit',
    ]);

    Route::post('/createlike', [
        'uses' => 'FrontendStatusController@CreateLike',
        'as'   => 'like.create',
    ]);

    Route::post('/destroylike', [
        'uses' => 'FrontendStatusController@DestroyLike',
        'as'   => 'like.destroy',
    ]);

    Route::get('/export', [
        'uses' => 'FrontendStatusController@exportLanding',
        'as'   => 'export.landing',
    ]);
    Route::get('/exportCSV', [
        'uses' => 'StatusController@exportCSV',
        'as'   => 'export.csv',
    ]);

    Route::post('/createfollow', [
        'uses' => 'FrontendUserController@CreateFollow',
        'as'   => 'follow.create',
    ]);

    Route::post('/destroyfollow', [
        'uses' => 'FrontendUserController@DestroyFollow',
        'as'   => 'follow.destroy',
    ]);


    Route::get('/transport/train/autocomplete/{station}', [
        'uses'  => 'FrontendTransportController@TrainAutocomplete',
        'as'    => 'transport.train.autocomplete',
    ]);

    Route::get('/transport/bus/autocomplete/{station}', [
        'uses'  => 'FrontendTransportController@BusAutocomplete',
        'as'    => 'transport.bus.autocomplete',
    ]);

    Route::get('/trains/stationboard', [
        'uses'  => 'FrontendTransportController@TrainStationboard',
        'as'    => 'trains.stationboard',
    ]);

    Route::get('/trains/trip', [
        'uses'  => 'FrontendTransportController@TrainTrip',
        'as'    => 'trains.trip'
    ]);

    Route::post('/trains/checkin', [
        'uses'  => 'FrontendTransportController@TrainCheckin',
        'as'    => 'trains.checkin'
    ]);

    Route::get('/trains/setHome/{ibnr}', [
        'uses'  => 'FrontendTransportController@setHome',
        'as'    => 'user.setHome'
    ]);

    Route::get('/busses/stationboard', [
        'uses'  => 'FrontendTransportController@trainStationboard',
        'as'    => 'busses.stationboard'
    ]);

    Route::get('/mastodon/test', [
        'uses'  => 'SocialController@testMastodon',
    ]);

});
//Route::get('/trip', 'HafasTripController@getTrip')->defaults('tripID', '1|178890|0|80|13082019')->defaults('lineName', 'ICE 376');
