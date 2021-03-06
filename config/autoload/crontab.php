<?php

return [
    // 是否开启定时任务
    "enable" => false,
    // 通过配置文件定义的定时任务
    "crontab" => [
        // 定时任务的执行规则，在分钟级的定义时，与 Linux 的 crontab 命令的规则一致，
        // 在秒级的定义时，规则长度从 5 位变成 6 位，在规则的前面增加了对应秒级的节点，也就是 5 位时以分钟级规则执行，6 位时以秒级规则执行，
        //如 */5 * * * * * 则代表每 5 秒执行一次。注意在注解定义时，规则存在 \ 符号时，需要进行转义处理，即填写 *\/5 * * * * *。
        (new \Hyperf\Crontab\Crontab())->setType("command")->setName("foo")->setRule("*/5 * * * * *")->setCallback([
            "command" => "foo:command",
            // (optional) arguments
            "name" => "eto",
            // (optional) options
            "--age" => 18
        ]),
        /*
         * 当您完成上述的配置后，以及定义了定时任务后，只需要直接启动 Server，定时任务便会一同启动。
         * 在您启动后，即便您定义了足够短周期的定时任务，定时任务也不会马上开始执行，所有定时任务都会等到下一个分钟周期时才会开始执行，
         * 比如您启动的时候是 10 时 11 分 12 秒，那么定时任务会在 10 时 12 分 00 秒 才会正式开始执行。
         */

        // Callback类型定时任务（默认）
        (new \Hyperf\Crontab\Crontab())->setName("call")->setRule("*/3 * * * * *")->setCallback([\App\Command\CrontabCommand::class, "handle"])->setMemo("这是闭包定时任务")
    ]
];