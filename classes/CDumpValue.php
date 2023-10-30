<?php

namespace Shasoft\CDump;


// Значение
abstract class CDumpValue
{
    // Нумератор
    static protected int $Id = 0;
    // Идентификатор
    protected int $id;
    // Конструктор
    public function __construct(protected ?CDumpDumper $dumper)
    {
        $this->id = ++self::$Id;
    }
    // Идентификатор
    public function id(): int
    {
        return $this->id;
    }
    // Имя переменной
    public function name(): string
    {
        return 'z' . $this->id;
    }
    // Значение переменной
    abstract public function jsValue(): string;
    // Определить тип
    public function jsDefineType(): string
    {
        return '';
    }
    // Создать переменную
    public function jsCreateVar(): string
    {
        return '';
    }

    // Установить свойства
    public function jsSetProperties(): string
    {
        return '';
    }
}
