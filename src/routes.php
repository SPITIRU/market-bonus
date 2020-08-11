<?php

Route::group([
    'prefix' => config('marketBonus.prefix'),
    'namespace' => 'ArtemiyKudin\Bonus\Controllers',
    'middleware' => config('marketBonus.middleware')
], static function () {

    // Market
    Route::get(config('marketBonus.routes.url.bonus'), [
        'uses' => 'MarketApiController@bonus',
        'as' => 'market.bonus'
    ]);

    Route::resource('market', 'MarketApiController')->middleware(config('marketBonus.permission'));
});
