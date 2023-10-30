<?php

namespace Shasoft\CDump;

// Класс для разбора данных
class CDumpDumper
{
    // Перевод строки
    static public string $ENDLINE = "\n";
    // Общие данные
    public array $data = [];
    // Все объекты
    public array $objects = [];
    // Все значения
    public array $values = [];
    // Проверить наличие данных по ключу
    public function hasData(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }
    // Установить данные по ключу
    public function putData(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }
    // Получить данные по ключу
    public function getData(string $key, mixed $value = null): mixed
    {
        return $this->data[$key] ?? $value;
    }
    // Получить объект значения
    public function getValue(mixed $value): CDumpValue
    {
        // Определить тип значения
        $type = gettype($value);
        // Определить по типу значения имя класса для обработки значения
        $classname = CDumpValue::class . ucfirst($type);
        // Если такой класс существует
        if (class_exists($classname)) {
            if ($type == 'object') {
                // А может это уже объект значения?
                if ($value instanceof CDumpValue) {
                    $ret = $value;
                } else {
                    // Рефлексия объекта
                    $refClass = new \ReflectionClass($value);
                    // Ключ объекта PHP
                    $key = $refClass->getName() . '.' . spl_object_id($value);
                    // А может такой объект уже есть?
                    if (array_key_exists($key, $this->objects)) {
                        // Берем существующий объект
                        $ret = $this->objects[$key];
                    } else {
                        // А может это перечисление?
                        if ($refClass->isEnum()) {
                            $ret = new CDumpValueEnum($this, $value);
                        } else {
                            // А может есть расширение для текущего объекта?
                            $classnameExtension = substr(self::class, 0, -11) . 'Extension\\' . get_class($value);
                            if (class_exists($classnameExtension)) {
                                $ret = new $classnameExtension($this, $value);
                            } else {
                                $ret = new $classname($this, $value);
                            }
                        }
                        // Добавить в список объектов
                        $this->objects[$key] = $ret;
                        // Инициализировать
                        $ret->initialization(new \ReflectionClass($value), $value);
                    }
                }
            } else {
                $ret = new $classname($this, $value);
            }
        } else {
            $ret = new CDumpValueDefault($this, $value);
        }
        // Добавить в список значений
        $this->values[$ret->id()] = $ret;
        // Вернуть объект значения
        return $ret;
    }
    // Определить типы
    protected function jsDefineTypes(): string
    {
        $ret = '';
        foreach ($this->values as $oValue) {
            $ret .= $oValue->jsDefineType();
        }
        return $ret;
    }
    // Создать переменные
    protected function jsCreateVars(): string
    {
        $ret = '';
        foreach ($this->values as $oValue) {
            $ret .= $oValue->jsCreateVar();
        }
        return $ret;
    }
    // Установить свойства
    protected function jsSetProperties(): string
    {
        $ret = '';
        foreach ($this->values as $oValue) {
            $ret .= $oValue->jsSetProperties();
        }
        return $ret;
    }
    // Получить JavaScript код вывода переменных
    public function getJsCode(...$args): string
    {
        //
        $values = [];
        foreach ($args as $arg) {
            $values[] = $this->getValue($arg);
        }
        // Определить типы
        $ret = $this->jsDefineTypes();
        // Создать переменные
        $ret .= $this->jsCreateVars();
        // Установить свойства
        $ret .= $this->jsSetProperties();
        // Вывести значения
        foreach ($values as $oValue) {
            $ret .= 'console.log(' . $oValue->jsValue() . ');' . self::$ENDLINE;
        }
        return $ret;
    }
}
