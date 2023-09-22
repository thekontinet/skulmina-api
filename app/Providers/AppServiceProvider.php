<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Scribe;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole(RoleEnum::ADMIN->value)) {
                return true;
            }
        });

        if (config('app.config') !== 'production' && class_exists(\Knuckles\Scribe\Scribe::class)) {
            Scribe::beforeResponseCall(function (Request $request, ExtractedEndpointData $endpointData) {
                // Customise the request however you want (e.g. custom authentication)
                $token = User::where('email', 'admin@example.com')->first()->createToken('test')->plainTextToken;

                $request->headers->set('Authorization', "Bearer $token");
                // You also need to set the headers in $_SERVER
                $request->server->set('HTTP_AUTHORIZATION', "Bearer $token");
            });
        }
    }
}
