# php server

快速的使用和管理 swoole server

## 相关组件

> More please see https://github.com/swokit

## install

```bash
composer require swokit/server
```

## run flow

```text

new server -> parse config -> 

```

## quick run

- create a TCP server

```php
$server = new TcpServer([
    'rootPath' => __DIR__,
    'server' => [
        'port' => 12091
    ]
]);

....

$server->start();
```

## config refer

```php
$config = [
    'debug' => true,
    'name' => 'demo',
    'rootPath' => __DIR__,
    'pidFile' => __DIR__ . '/logs/test_server.pid',

    // main server
    'server' => [
        'type' => 'tcp', // http https tcp udp ws wss rds
        'port' => 9501,
    ],

    // attach port server by config
    'ports' => [
        'port1' => [
            'host' => '0.0.0.0',
            'port' => '9761',
            'type' => 'udp',

            // must setting the handler class in config.
            'listener' => \Swokit\Server\Listener\Port\UdpListener::class,
        ]
    ],
    
    // for swoole
    'swoole' => [
        'user'    => 'www-data',
        'worker_num'    => 4,
        'task_worker_num' => 2,
        'daemonize'     => false,
        'max_request'   => 10000,
        // 'log_file' => PROJECT_PATH . '/temp/logs/my_swoole_server.log',
    ]
];
```

## 注意事项

- 协程模式

开启协程模式后，swoole_server 和swoole_http_server将以为每一个请求创建对应的协程，开发者可以在`onRequest`、`onReceive`、`onConnect` 3个事件回调中使用协程客户端

- 在主服务器上追加监听的端口服务的事件不生效

```
//file: server.php

$mainServer = new swoole_http_server('0.0.0.0', 9501);

// 追加监听tcp端口
// listen 是 addListener 的别名
$port = $mainServer->listen('0.0.0.0', 9601, SWOOLE_SOCK_TCP);
$port->on('receive', function($srv, $fd, $fromId, $data){
    $srv->send($fd, "Server: ".$data);
});

$mainServer->start();
```

在终端运行server: `php server.php`

再开一个终端测试追加监听的tcp服务是否成功

```
telnet 127.0.0.1 9661
text // 发现输入后什么都没有返回
```

最后发现必须要调用 `$port->set()` 才行。增加一行设置 tcp 服务的配置

```
... ...
$port->set([]); // 设置tcp监听的配置，可以覆盖继承的主server(swoole_http_server)配置

$mainServer->start();
```

重新运行server后再测试:

```
telnet 127.0.0.1 9661
text
Server text // 返回了消息
```

> NOTICE: 增加端口监听后，必须要调用 `$port->set()`. 不然不会触发监听服务上的事件,即使传入空数组也行，但不能不调用。

