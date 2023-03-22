# Hyperf-Api

基于 `hyperf` 快速开发 `api` 项目

## 环境依赖

* php >= 7.4
* swoole >= 4.8
* redis

## 安装

```git 
git clone https://github.com/Zero0719/hyperf-api.git
composer install
```

## 特性

* 统一响应
* CORS 中间件
* 请求日志中间件
* 日志封装
* JWT
* 异常

### 配置文件

`config/autoload/api.php`

### 响应

响应统一返回 `json` 格式数据

如果需要在响应中返回 `requestId`, 请在 `.env` 文件中添加 `WITH_REQUEST_ID=true`

```php 
use App\Util\ResponseUtil;

// 成功响应
return ResponseUtil::success([
    'user' => 'test1'
]);

// 失败响应
return ResponseUtil::error('some thing was wrong.');

// 更多的自定义参数响应
// 参数对应: 自定义状态码 响应数组数据 响应文本 响应http状态码 响应headers
return ResponseUtil::send(2, ['test' => 1], 'message', 404, ['Authoirzation' => 'xxxxxxxx']);
```

### 中间件的使用

按需加载即可

`config/autoload/middlewares.php`

```php 
return [
    'http' => [
        \App\Middleware\RequestMiddleware::class, // 请求日志中间件
        \App\Middleware\CorsMiddleware::class, // 跨域处理中间件
    ],
];
```

### 使用日志

```php 
use App\Util\LogUtil;

$logger = LogUtil::get('app')->info('info', $somedata);
```

### JWT 使用

在需要的地方打开中间件,比如路由组或者全局

修改`jwt.php`配置文件中的不检查路由

打开`exceptions.php`中对 `jwt`相关的异常捕捉

```php 
use App\Util\JWTUtil;

$tokenData = JWTUtil::generateToken(['uid' => '1', 'username' => 'test']); // 注意 uid 键是必须的
```

### 异常

自定义了一个业务异常和业务异常处理器，建议业务可控的错误，抛出该业务异常并统一处理响应

`App\Exception\BusinessException.php`

`App\Exception\BusinessExceptionHandler.php`

```php 
throw new BusinessExcption('you do some thing wrong.')
```

对于所有未捕捉的异常统一交给 `App\Exception\AppExceptionHandler.php` 处理

对于验证器的异常进行了统一处理 `App\Exception\ValidationExceptionHander.php`

