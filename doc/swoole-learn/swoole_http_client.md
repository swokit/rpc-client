# 使用内置Http异步客户端 

Swoole-1.8.0版本内置了HttpClient，经过多个版本的迭代，内置HttpClient无论从功能、性能、稳定性上都已经非常出色。

## 使用实例

```php
$cli = new swoole_http_client('127.0.0.1', 80);
$cli->setHeaders(['User-Agent' => "swoole"]);

$cli->post('/dump.php', array("test" => 'abc'), function ($cli) {
    echo $cli->body;
});
```

## 并发能力

相比`curl`和 `file_get_contents` 这样PHP提供的Http客户端，`swoole_http_client` 最大的优势是 **支持大量并发**。

file_get_contents 只能同时请求一个URL，并发只能通过开启多进程实现。curl提供了curl_multi功能实现并发基于select和多线程。并发能力都很差。
而swoole_http_client是基于epoll实现的异步客户端，没有并发限制，可在一个进程内同时并发上万请求。

## 性能问题

在PHP中也有纯PHP实现的Http客户端，如 `Guzzle`，这些类库最大的问题是Http协议解析是由PHP代码实现的，PHP代码在这样场景下进行大量运算性能较差，而且还会占用大量内存。
swoole_http_client是由C代码实现的，解析Http协议的性能是非常高的，内存占用也很少。

在解析gzip压缩后的HTML时，`swoole_http_client` 的优势更为明显，它可以使用`download()`方法，以很小的内存占用即可完成超大文件的下载。
由于PHP层面没有提供zlib流式分段解压的支持，只能将Http Body全部放置到内存中，调用gzdecode一次性解压，而这会占用大量内存。

## SSL支持

swoole_http_client 支持SSL和TLS隧道加密的https网址，并且支持配置客户端证书。

```php
$cli = new swoole_http_client('127.0.0.1', 80, true);

//如果服务器需要提供SSL证书
$cli->set(array(
    'ssl_cert_file' => $certFile,
    'ssl_key_file' => $keyFile,
));

$cli->setHeaders(['User-Agent' => "swoole"]);

$cli->get('/index.php', function ($cli) {
    file_put_contents(__DIR__.'/t.html', $cli->body);
});
```

## Socks5代理

swoole_http_client支持Socks5代理，只需要设置几个参数就可以直接使用。

```php
$cli = new swoole_http_client('127.0.0.1', 80);

$cli->set(array(
    'socks5_host'     =>  '192.168.1.100',
    'socks5_port'     =>  1080,
    'socks5_username' => 'username', //用户名和密码为可选项
    'socks5_password' => 'password',
));

$cli->setHeaders(['User-Agent' => "swoole"]);

$cli->get('/index.php', function ($cli) {
    file_put_contents(__DIR__.'/t.html', $cli->body);
});
```

## 上传文件

swoole_http_client 底层使用了 `sendfile` 系统调用实现了http上传超大文件，配合底层的epoll可以实现非常低的消耗完成超巨大文件的上传。
sendfile是零拷贝的，占用内存非常少，并且不存在多次内存复制开销。

```php
$cli = new swoole_http_client('127.0.0.1', 80);

$cli->addFile(__DIR__.'/post.data', 'post');
$cli->addFile(dirname(__DIR__).'/test.jpg', 'debug');

$cli->post('/dump2.php', array("xxx" => 'abc', 'x2' => 'rango'), function ($cli) {
    echo $cli->body;
    $cli->close();
});
```
