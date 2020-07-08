<?php

include __DIR__ . '/../vendor/autoload.php';

$promise = new Irekk\Promises\Promise;
$promise->then(function($previous){
    print 'ok' . PHP_EOL;
    return (object) ['property' => 'value'];
})->then(function($previous, $promise){
    print 'reject' . PHP_EOL;
    $promise->reject('just because');
})->then(function($previous){
    print 'you should not see this' . PHP_EOL;
})->catch(function($error, $previous) {
    print 'catched error: ' . $error . PHP_EOL;
    var_dump($previous);
});
$promise->resolve('argument 1', 'argument 2');