## Feature

* 跨域处理
* 统一响应
* 验证器统一处理
* 业务异常&处理器
* JWT验证
* 请求日志记录
* 常用工具类

## 安装

todo

### 发布配置文件

`php bin/hyperf.php vendor:publish zero0719/hyperf-api`

### 配置中间件

```php
return [
    'http' => [
        \Zero0719\HyperfApi\Middleware\CorsMiddleware::class,
        \Zero0719\HyperfApi\Middleware\RequestLogMiddleware::class,
        \Hyperf\Validation\Middleware\ValidationMiddleware::class,
        \Phper666\JWTAuth\Middleware\JWTAuthDefaultSceneMiddleware::class
    ],
];
```

### 配置异常处理器

```php
return [
    'http' => [
        \Zero0719\HyperfApi\Exception\Handler\ValidationExceptionHandler::class,
        \Zero0719\HyperfApi\Exception\Handler\JWTExceptionHandler::class,
        \Zero0719\HyperfApi\Exception\Handler\BusinessExceptionHandler::class,
        .
        .
    ]   
];
```

## 跨域处理

跨域处理我们只需要在响应的时候给 `response header` 做一定处理就行

修改配置文件中响应的 `header` 信息

`config/autoload/api.php`

```php
return [
    'cors' => [
        'headers' => [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Allow-Headers' => 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization'
        ]
    ]
];
```

在中间件配置文件 `http` 模块引入中间件

`config/autoload/middlewares.php`

```php
return [
    'http' => [
        \Zero0719\HyperfApi\Middleware\CorsMiddleware::class
    ],
];
```

## 统一响应

在大部分 `api` 中我们都是响应一个 `json` 格式的数据即可

我们约定响应的数据结构为

```php
[
    'code' => 0,
    'message' => '1',
    'data' => []
]
```

为什么 `code` 等于 `0` 的时候认为成功，因为这样可以和前端约定，只要是 `0` 都是成功，当 `code` 不为 `0` 的情况再特定处理；如果约定 `1` 为成功，`0` 为失败，当发生其他情况又把大于 `1` 当成失败去处理，理解逻辑上会有点奇怪。

可以选择继承 `Zero0719\HyperfApi\Controller\BaseController`

```php
class IndexController extends \Zero0719\HyperfApi\Controller\BaseController
{
    public function index() {
        return $this->success([
            'test' => 1
        ]);
    }
}
```

也可以直接引入 `trait`
```php
use Zero0719\HyperfApi\Trait\ResponseTrait;

class Demo {
    use ResponseTrait;
    
    public function test() {
        return $this->error('错误响应');
    }
}
```

## 验证器统一处理

当我们使用验证器类注入验证时，验证失败会抛出一个异常，框架提供的异常处理器不能满足我们 `api` 响应的格式，所以在这里我们要重写一个异常处理器，并响应成我们统一的数据格式。

`config/autoload/exceptions.php`
```php
return [
    'handler' => [
        'http' => [
            \Zero0719\HyperfApi\Exception\Handler\ValidationExceptionHandler::class,
            .
            .
            .
        ],
    ],
];
```

## 业务异常&处理器

在业务逻辑中，我们经常会有代码执行逻辑达到某个条件则认为不该继续往下执行了，这种可控的业务逻辑异常，我们统一的抛出异常，然后用统一的异常处理器捕捉处理。

异常类: `Zero0719\HyperfApi\Exception\BusinessException`

`config/autoload/exceptions.php`
```php
return [
    'handler' => [
        'http' => [
            \Zero0719\HyperfApi\Exception\Handler\BusinessExceptionHandler::class,
            .
            .
            .
        ],
    ],
];
```

```php
class IndexController extends \Zero0719\HyperfApi\Controller\BaseController
{
    public function index() 
    {
        $a = 1;
        
        if ($a == 2) {
            throw new \Zero0719\HyperfApi\Exception\BusinessException('业务逻辑有问题');
        }
        
        return $this->success();
    }   
}
```

## JWT 验证

`json web token` 几乎是开发 `api` 时离不开的一个模块，所以在这里直接把相关的业务嵌套进来

我们使用 `phper666/jwt-auth` 作为基础，详细用法请查看该包[文档](https://github.com/phper666/jwt-auth)

阅读文档可知，用 `Phper666\JWTAuth\Middleware\JWTAuthDefaultSceneMiddleware::class` 中间件就可以进行 `jwt` 授权验证，如果没有通过校验则会抛出 `Phper666\JWTAuth\Exception\JWTException` 或者 `Phper666\JWTAuth\Exception\TokenValidException`，所以我们只需要定义异常处理器捕获这两个异常，并统一响应即可。

`config/autoload/exceptions.php`
```php
return [
    'handler' => [
        'http' => [
            .,
            \Zero0719\HyperfApi\Exception\Handler\JWTExceptionHandler::class,
            .,
            .,
            .
        ],
    ],
];
```
抛出的异常中 `400`, `401` 我们直接响应出去，可以和客户端约定当收到这两个码时可以尝试刷新 `token` 的逻辑，或者直接重定向到登录页面。

`Zero0719\HyperApi\Service\TokenService.php` 简单封装了 `jwt` 一些常用的业务，比如生成，销毁，刷新，解析等，对应我们在业务开发时，登录注册返回的 `token`，退出登录时销毁，刷新时销毁并重新生成，解析获取用户生成时的关键数据进行对应的业务处理

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Contract\RequestInterface;
use Phper666\JWTAuth\Util\JWTUtil;
use Zero0719\HyperfApi\Controller\BaseController;
use Zero0719\HyperfApi\Service\TokenService;

class SessionsController extends BaseController
{
    protected TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function login(RequestInterface $request)
    {
        // 进行了一大堆验证，获取到用户的关键信息，比如userId, username 等
        $user = [
            'userId' => 1,
            'username' => 'test111'
        ];

        $result = $this->tokenService->generate($user);

        return $this->success($result);
    }

    public function logout(RequestInterface $request)
    {
        $token = JWTUtil::getToken($request);

        $this->tokenService->destroy($token);

        return $this->success();
    }

    public function reLogin(RequestInterface $request)
    {
        $token = JWTUtil::getToken($request);

        return $this->success($this->tokenService->refresh($token));
    }

    public function me(RequestInterface $request)
    {
        $token = JWTUtil::getToken($request);

        $data = $this->tokenService->parse($token);

        return $this->success($data);
    }
}

```

如果 `TokenService` 不满足你的业务场景，大可以继承以后重写，也可以完全重新写自己的业务类。

另外建议当某个地方的业务解析了此次请求的 `token` 以后，获取了关键用户信息，我们可以把这个信息用上下文 `Context` 存起来，方便这次请求其他业务还需要用时可以快速获得，这里逻辑请自行处理。

## 请求日志记录

在项目中，记录请求日志对于我们定位排查问题有很大的帮助，比如想知道用户用了什么客户端发起请求，用户发起请求的IP，时间，传了什么参数等等，我们可以定义一个中间件全局记录所有请求或者部分路由。

引入中间件 `config/autoload/middlewares.php`

```php
return [
    'http' => [
        .
        \Zero0719\HyperfApi\Middleware\RequestLogMiddleware::class,
        .
    ]   
];
```

修改配置文件 `config/autoload/api.php`

```php
return [
    'request' => [
        'log' => [
            'handler' => Zero0719\HyperfApi\Service\RequestLogService::class,
            'data' => ['ip', 'method', 'url', 'param', 'time'],
            'meta' => [],
            'config' => env('REQUEST_LOG_CONFIG', 'default'),
            'channel' => env('REQUEST_LOG_CHANNEL', 'request'),
            'level' => env('REQUEST_LOG_LEVEL', 'info'),
        ]
    ]
];
```

这里需要关注 `handler` 和 `data`，因为在中间件中可以看到我们是需要实现了 `RequestLogInterface` 接口的类，而 `data` 中则定义了我们需要记录哪些数据，在对应的实现类中完成对应的方法，实现类的 `handle` 方法会获取 `data` 中的值，再去执行对应方法记录返回值。

如果 `data` 中某个方法的逻辑需要改变或者需要增加新的记录值，我们可以写自己的具体类并继承 `Zero0719\HyperfApi\Service\RequestLogService::class`

```php
'handler' => \App\Service\TestRequestInfo::class,
'data' => ['ip', 'method', 'url', 'param', 'time', 'ua'],
```

```php
<?php
declare(strict_types = 1);

namespace App\Service;

use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;
use Zero0719\HyperfApi\Service\RequestLogService;

class TestRequestInfo extends RequestLogService
{
    public function ua()
    {
        return ApplicationContext::getContainer()->get(RequestInterface::class)->getHeader('User-Agent');
    }
}

```

## 常用工具类

`Zero0719\HyperfApi\Utils\CommonUtil`

```php
// 获取客户端IP
\Zero0719\HyperfApi\Utils\CommonUtil::getIp();
```