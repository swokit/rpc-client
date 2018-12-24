<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-02-22
 * Time: 9:21
 */

namespace SwoKit\Server;

use Monolog\Logger;

/**
 * Interface ServerInterface
 * @package SwoKit\Server
 * @property \Swoole\Server|\Swoole\Websocket\Server $server
 */
interface ServerInterface
{
    const VERSION = '0.1.1';

    const UPDATE_TIME = '2017-02-17';

    // 运行模式
    // SWOOLE_PROCESS 业务代码在Worker进程中执行
    // SWOOLE_BASE    业务代码在Reactor进程中直接执行
    const MODE_BASE = 'base';
    const MODE_PROCESS = 'process';

    /**
     * the main server allow socket protocol type:
     * tcp udp http https(http + ssl) ws wss(webSocket + ssl)
     */
    const PROTOCOL_TCP = 'tcp';
    const PROTOCOL_UDP = 'udp';
    const PROTOCOL_HTTP = 'http';
    const PROTOCOL_HTTPS = 'https';
    const PROTOCOL_RDS = 'rds';  // redis
    const PROTOCOL_WS = 'ws';  // webSocket
    const PROTOCOL_WSS = 'wss'; // webSocket ssl

    /**
     * @var array
     */
    const SWOOLE_EVENTS = [
        // basic
        'start', 'shutdown', 'workerStart', 'workerStop', 'workerExit', 'workerError', 'managerStart', 'managerStop',
        // special
        'pipeMessage', 'bufferFull', 'bufferEmpty',
        // tcp/udp
        'connect', 'receive', 'packet', 'close',
        // task
        'task', 'finish',
        // http server
        'request',
        // webSocket server
        'message', 'open', 'handShake'
    ];

//    public static function run(array $config = [], $start = true);

//    public function bootstrap($start = true);

    public function start();

    /**
     * record log message
     * @param string $msg
     * @param array $data
     * @param int $level
     * @return void
     */
    public function log(string $msg, array $data = [], $level = Logger::INFO);

    /**
     * @return array
     */
    public function getSupportedProtocols(): array;

    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function config(string $key, $default = null);
}
