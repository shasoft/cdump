<?php

namespace Shasoft\CDump;

// Класс для вывода в консоль значений по умолчанию
class CDumpValueDefault extends CDumpValue
{
    // Значение
    protected string $value;
    // Конструктор
    public function __construct(CDumpDumper $dumper, mixed $value)
    {
        // Вызвать конструктор родителя
        parent::__construct($dumper);
        // Сгенерировать значение
        $this->value = json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    // Значение переменной
    public function jsValue(): string
    {
        return $this->value;
    }
}
