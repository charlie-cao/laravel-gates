<?php

namespace App\Providers;

use App\Post;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerPostPolicies();
    }
    
    /**
     * 在认证服务者中检查用户权限
     *
     * @return void
     */
    public function registerPostPolicies()
    {
        //为Gate定义create-post 的检查方法, 当这个被调用的时候
        // 就走后面的闭包,然后这个方法在相应的动作中调用.就可以起到检查当前用户是否有权访问的作用.
        // 看起来后面的策略使用是在路由中..
        // 这里定义的策略很灵活,可以看权限,也可以看角色
        // 主要就是根据用户信息判断
        // 在系统中随时可以通过 Gate::denies('see-all-drafts') 或者 
        Gate::define('create-post', function ($user) {
            //检查当前用户的角色有没有 create-post的权限.
            return $user->hasAccess('create-post');
        });
        Gate::define('update-post', function ($user, Post $post) {
            // dd("aaa");
            return $user->hasAccess('update-post') or $user->id == $post->user_id;
        });
        Gate::define('publish-post', function ($user, Post $post) {
            return $user->hasAccess('publish-post') or $user->id == $post->user_id;;
        });
        Gate::define('delete-post', function ($user, Post $post) {
            return $user->hasAccess('delete-post') or $user->id == $post->user_id;
        });
        /**
         * 如果当前用户是编辑,就返回true,否则返回false.
         */
        Gate::define('see-all-drafts', function ($user) {
            return $user->inRole('editor');
        });
    }
}
