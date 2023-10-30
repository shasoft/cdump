<?php

namespace Shasoft\CDump;

// Класс для вывода данных в консоль значений перечисления (enum)
class CDumpValueEnum extends CDumpValue
{
    // Значение
    private string $value;
    // Конструктор
    public function __construct(CDumpDumper $dumper, mixed $value)
    {
        // Вызвать конструктор родителя
        parent::__construct($dumper);
        //
        $refEnum = new \ReflectionEnum($value);
        $str = $refEnum->getName() . '::' . $refEnum->getProperty('name')->getValue($value);
        if ($refEnum->hasProperty('value')) {
            $str .= '<' . $refEnum->getProperty('value')->getValue($value) . '>';
        }
        $this->value = json_encode($str, JSON_UNESCAPED_UNICODE);
    }
    // Инициализация
    public function initialization(\ReflectionClass $refObject, object $obj): void
    {
    }
    // Значение переменной
    public function jsValue(): string
    {
        return $this->value;
    }
}
