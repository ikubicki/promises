<?php

include __DIR__ . '/../vendor/autoload.php';

$promise = new Irekk\Promises\Promise;
$promise->then(function($previous, $promise, $arg1){
    print 'hello' . PHP_EOL;
    var_dump('promise=', $promise, 'previous=', $previous, 'arg1=', $arg1);
    return $promise->promise(function(){
        return time();
    });
})->then(function($previous, $promise, $arg1, $arg2){
    print 'Howdy!' . PHP_EOL;
    var_dump('promise=', $promise, 'previous=', ('' . $previous), 'arg1=', $arg1, 'arg2=', $arg2);
});
$promise->resolve('argument 1', 'argument 2');