### PHP Fatal error:  Uncaught PhpAmqpLib\Exception\AMQPProtocolChannelException: PRECONDITION_FAILED - inequivalent arg 'durable' for queue 'task' in vhost '/': received 'true' but current is 'false'
 
原因是：
```
由于第一次创建队列时参数durable设置的是false,RabbitMQ不允许重新定义一个已有的队列信息，也就是说不允许修改已经存在的队列的参数
```

解决方法：重新声明队列
```angular2html
$channel->queue_declare($queueName, true, true, false, false);
```