# catwrench/l5-dblogs

Log 服务通过单例注册到 laravel 的服务容器，保证日志的读写操作尽量简化

### 安装
- 通过composer引入扩展
```
composer require catwrench/l5-dblogs
```

- 在`config/app.php` 的 `providers` 下添加（laravel5.5+ 可以跳过此步骤）
```
'providers' => [
    ...
    CatWrench\DbLogs\LogProvider::class,
],

```

- 执行迁移生成数据表
```
php artisan migrate
```

***

### 使用

- 写入日志
```
$bizTag = 'test';
$actionTag = 'business tag';
$logContent = [
    'package' => 'catwrench/l5-dblogs',
    'date' => date('Y-m-d H:i:s'),
];
$operator = 'urumuqi';
$traceKey = 'dblogs';

$dblogs = app('dblogs');
$saveRs = $dblogs->write($bizTag, $actionTag, $logContent, $operator, $traceKey);

```

- 读取日志

日志读取支持分页，详情查看 `CatWrench\DbLogs\Model\Log.php`

```
$readRs1 = $dblogs->read($bizTag);
$readRs2 = $dblogs->readByTraceKey($traceKey);
$readRs3 = $dblogs->readByBizTag($bizTag);
$readRs4 = $dblogs->readByBizTraceKey($bizTag, $traceKey);
$readRs5 = $dblogs->readByOperator($operator);
dd($saveRs, $readRs1, $readRs2, $readRs3, $readRs4, $readRs5);
```




