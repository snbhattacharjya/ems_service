<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Carbon\Carbon;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        // Passport::routes(function ($router) {
        //     /** @var \Laravel\Passport\RouteRegistrar $router */
        //     $router->forAuthorization();
        //     $router->forTransientTokens();
        //     $router->forClients();
        //     $router->forPersonalAccessTokens();

        //     // Passport Routes
        //     \Route::post('/token', [
        //         'uses' => 'AccessTokenController@issueToken',
        //                   'middleware' => 'throttle:600,1',
        //     ]);

        //     \Route::group(['middleware' => ['web', 'auth']], function ($router) {
        //         $router->get('/tokens', [
        //             'uses' => 'AuthorizedAccessTokenController@forUser',
        //         ]);

        //         $router->delete('/tokens/{token_id}', [
        //             'uses' => 'AuthorizedAccessTokenController@destroy',
        //         ]);
        //     });
        // });

        Passport::tokensExpireIn(Carbon::now()->addDays(1));
	    Passport::refreshTokensExpireIn(Carbon::now()->addDays(1));
    }
}
