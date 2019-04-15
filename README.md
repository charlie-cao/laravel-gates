## 使用laravel的Gate实现用户角色和权限鉴定.

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
