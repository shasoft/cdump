<?php

use Shasoft\CDump\CDumpLog;
use Shasoft\CDumpTest\TestObject;

require_once __DIR__ . '/../../vendor/autoload.php';

// Создать объект
$obj1 = new TestObject(null);
$obj2 = new TestObject($obj1);
$obj1->objPub = $obj2;
// Вывести
cdump($obj1, 123, false, 1.2, "Test string");


// Включить логирование
CDumpLog::enable(true);

class ClassTest
{
    static public function func0(int $x, int $delta): int
    {
        return CDumpLog::group(true, function () use ($x, $delta) {
            return $x + $delta;
        });
    }
}
// Логирование вызовов
function func1(int $a)
{
    return CDumpLog::group(true, function () use ($a) {
        return ClassTest::func0($a, 8) * 10;
    });
}
function func2(int $b): int
{
    return CDumpLog::group(true, function () use ($b) {
        return func1($b + 1) + 2;
    });
}

// Запустить функцию
func2(2);
