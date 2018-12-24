# 队列（Queue） 

异步并发的服务器里经常使用队列实现生产者消费者模型，解决并发排队问题。PHP的SPL标准库中提供了SplQueue扩展内置的队列数据结构。另外PHP的数组也提供了array_pop和array_shift可以使用数组模拟队列数据结构。

## SplQueue

```
$queue = new SplQueue;
//入队
$queue->push($data);
//出队
$data = $queue->shift();
//查询队列中的排队数量
$n = count($queue);
```

## Array模拟队列

```
$queue = array();
//入队
$queue[] = $data;
//出队
$data = array_shift($queue);
//查询队列中的排队数量
$n = count($queue);
```

## 性能对比

虽然使用Array可以实现队列，但实际上性能会非常差。在一个大并发的服务器程序上，建议使用SplQueue作为队列数据结构。

100万条数据随机入队、出队，使用SplQueue仅用2312.345ms即可完成，而使用Array模拟的队列的程序根本无法完成测试，CPU一直持续在100%。

降低数据条数到1万条后（100倍），也需要260ms才能完成测试。

### SplQueue

```
$splq = new SplQueue;
for($i = 0; $i < 1000000; $i++)
{
    $data = "hello $i\n";
    $splq->push($data);

    if ($i % 100 == 99 and count($splq) > 100)
    {
        $popN = rand(10, 99);
        for ($j = 0; $j < $popN; $j++)
        {
            $splq->shift();
        }
    }
}

$popN = count($splq);
for ($j = 0; $j < $popN; $j++)
{
    $splq->pop();
}
```

### Array队列

```
$arrq = array();
for($i = 0; $i <1000000; $i++)
{
    $data = "hello $i\n";
    $arrq[] = $data;
    if ($i % 100 == 99 and count($arrq) > 100)
    {
        $popN = rand(10, 99);
        for ($j = 0; $j < $popN; $j++)
        {
            array_shift($arrq);
        }
    }
}
$popN = count($arrq);
for ($j = 0; $j < $popN; $j++)
{
    array_shift($arrq);
}
```
