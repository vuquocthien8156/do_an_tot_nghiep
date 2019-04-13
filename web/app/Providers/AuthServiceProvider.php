<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Constant\SessionKey;

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
    
    
    public function boot() {
        $this->registerPolicies();
        Gate::define('enable_feature', function ($user, $code_page) {
            if (Session::has('authorization_user')) {
                $arr_group_permission = session(SessionKey::AUTHORIZATION_USER);
                foreach ($arr_group_permission as $key => $value) {
                    if($value->code === $code_page) {
                        return true;
                    }
                }
                return false;
            } else {
                return true;
            }
        });
    }
}
