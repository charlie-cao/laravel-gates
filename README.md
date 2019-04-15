## 使用laravel的Gate实现用户角色和权限鉴定.

授权是在认证之后的步骤.两者本身没什么关系.

```
git clone
```

modfiy .env db param.

```
composer install
php artisan migrate
```

Then add 
```

INSERT INTO `roles` (`id`, `name`, `slug`, `permissions`, `created_at`, `updated_at`) VALUES
(4, '作家', 'author', '{\"create-post\": 1}', NULL, NULL),
(5, '编辑', 'editor', '{\"delete-post\": 1, \"update-post\": 1, \"publish-post\": 1}', NULL, NULL);

```

整理一下 授权这块, 第一步在auth服务者中定义策略,
第二步,在路由中使用can,或者在控制器中使用Gate来鉴定权利.
用户的角色或者权限,都可以在策略中定义,针对当前用户,或者针对一组用户都可以.
这就是 Gate的玩法.

name :布局中直接生成链接.
中间件认证后的用户可以访问
create-post 这些是策略的名字,会通过中间件去调用相应的策略.
所以,策略是关键.
,post 这个是注入的 post类实例.



-----
1. 在**AuthServiceProvider**中定义策略, 这个服务者会在初始化应用容器时调用, 这个方法中可以手动写也可以用一个策略类来归纳所有的
策略.

```
    public function registerPostPolicies()
    {
        Gate::define('create-post', function ($user) {
            //检查当前用户的角色有没有 create-post的权限.
            return $user->hasAccess('create-post');
        });
    }
```

2. user Model 中增加 hasAccess方法,

```

    public function hasAccess($permission)
    {
        // 判断该用户的所有角色中是否有含有当前鉴定的这个权限.
        // check if the permission is available in any role
        foreach ($this->roles as $role) {
            if($role->hasAccess($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the user belongs to role.
     * 看看用户是不是符合某个角色.slug就是角色的名字.
     */
    public function inRole($roleSlug)
    {
        return $this->roles()->where('slug', $roleSlug)->count() == 1;
    }

```


3. 有了上面的定义,两种用法,一个是在路由中,另一个是在控制器中,获取其他任何可以被调用的地方.

路由中的用法: 走到这个路由的请求会去调用update-post策略,并传入post对象,根据返回判定是否能够访问.
abort(403, 'Unauthorized.'); 如果不能访问会自动重定向到未授权页面.

```

    Route::post('/edit/{post}', 'PostController@update')
        ->name('update_post')
        ->middleware('can:update-post,post');

```

控制器中的用法

Gate::denies('see-all-drafts')

```
    // 策略是当前用户如果为 author 则返回false , 如果为editor 则返回true
    // 但是用的是denies所以获得的结果是 相反的.
    // 最后的结果是作家访问自己的文章,编辑可以访问所有的文章.
    if(Gate::denies('see-all-drafts')) {
        $postsQuery = $postsQuery->where('user_id', Auth::user()->id);
    }

```
对应的策略.
```
    /**
        * 如果当前用户是编辑,就返回true,否则返回false.
        */
    Gate::define('see-all-drafts', function ($user) {
        return $user->inRole('editor');
    });
```

5. 在当前项目api中的应用


```
建立role ,和role_user表
为用户在创建的时候确定角色
1. req
2. pro
3. guest

编写策略,
req 用户不能跟服务类相关的
pro 用户和服务类相关的

在api和中间件中使用策略

角色的改变在后台,或者在用户的action中,比如订阅完毕.或者完成认证之后.


```
用户角色权限列表
req用户 pro用户 guest用户
