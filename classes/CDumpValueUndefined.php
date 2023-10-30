<?php

namespace Shasoft\CDump;

// Класс для вывода в консоль значения - НЕ ИНИЦИАЛИЗИРОВАНО
class CDumpValueUndefined extends CDumpValue
{
    // Конструктор
    public function __construct()
    {
        // Вызвать конструктор родителя
        parent::__construct(null);
    }
    // Значение переменной
    public function jsValue(): string
    {
        return 'undefined';
    }
}
