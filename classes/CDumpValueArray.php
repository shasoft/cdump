<?php

namespace Shasoft\CDump;

// Класс для вывода в консоль значений массива
class CDumpValueArray extends CDumpValue
{
    // Значения
    protected array $values;
    // Конструктор
    public function __construct(CDumpDumper $dumper, mixed $value)
    {
        // Вызвать конструктор родителя
        parent::__construct($dumper);
        // Сгенерировать значения
        $this->values = [];
        foreach ($value as $key => $val) {
            $this->values[$key] = $dumper->getValue($val);
        }
    }
    // Значение переменной
    public function jsValue(): string
    {
        return $this->name();
    }
    // Определить тип
    public function jsDefineType(): string
    {
        $key = self::class;
        // Если ранее класс массива уже добавлялся
        if ($this->dumper->hasData($key)) {
            // то вернуть пустую строку
            return '';
        }
        // Иначе установить флаг добавления
        $this->dumper->putData($key, true);
        // И вернуть строку с определением функции массива
        return 'function Array() {}' . CDumpDumper::$ENDLINE;
    }
    // Создать переменную
    public function jsCreateVar(): string
    {
        $ret = '';
        $ret .= 'var ' . $this->name() . ' = new Array();' . CDumpDumper::$ENDLINE;
        $ret .= $this->name() . '.__proto__ = null;' . CDumpDumper::$ENDLINE;
        return $ret;
    }
    // Установить свойства
    public function jsSetProperties(): string
    {
        $ret = '';
        foreach ($this->values as $name => $oValue) {
            $ret .= $this->name() . '[' . json_encode($name) . '] = ' . $oValue->jsValue() . ';' . CDumpDumper::$ENDLINE;
        }
        return $ret;
    }
}
