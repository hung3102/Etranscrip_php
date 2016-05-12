<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],

    'bootstrap' => [
        'log',
        'common\components\Bootstrap',
    ],
    'homeUrl'=>array('student/index'),
];
