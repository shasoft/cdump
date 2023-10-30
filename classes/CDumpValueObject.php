<?php

namespace Shasoft\CDump;

// Класс для вывода данных в консоль значений объекта
class CDumpValueObject extends CDumpValue
{
    // Объект PHP
    private object $object;
    // Имя класса
    private string $classname;
    // Свойства
    private array $properties = [];
    // Конструктор
    public function __construct(CDumpDumper $dumper, mixed $value)
    {
        // Вызвать конструктор родителя
        parent::__construct($dumper);
        // Объект PHP
        $this->object = $value;
        // Имя класса
        $this->classname = get_class($value);
        // Свойства
        $this->properties = [];
    }
    // Объект PHP
    public function getObject(): object
    {
        return $this->object;
    }
    // Имя класса
    public function classname(): string
    {
        return $this->classname;
    }
    // Имя типа
    public function getTypeName(): string
    {
        return str_replace('\\', '_', $this->classname());
    }
    // Установить значение свойства
    public function setProperty(string $name, mixed $value): void
    {
        // Идентификатор объекта
        $this->properties[$name] = $this->dumper->getValue($value);
    }
    // Префикс для свойства объекта
    public function getPrefixProperty(\ReflectionProperty $refProperty): string
    {
        //
        $ret = $refProperty->isPublic() ? '+' : ($refProperty->isProtected() ? '#' : '-');
        if ($refProperty->isStatic()) {
            $ret .= '~';
        } else {
            $ret .= ' ';
        }
        return $ret;
    }
    // Инициализация
    public function initialization(\ReflectionClass $refObject, object $obj): void
    {
        // Идентификатор объекта
        $this->setProperty('#', spl_object_id($obj));
        // Свойства объекта (кроме статических)
        foreach ($refObject->getProperties() as $property) {
            if (!$property->isStatic()) {
                $property->setAccessible(true);
                $this->setProperty(
                    $this->getPrefixProperty($property) . $property->getName(),
                    $property->isInitialized($obj) ? $property->getValue($obj) : new CDumpValueUndefined()
                );
            }
        }
        // Статические свойства объекта
        $exists = [];
        while ($refObject !== false) {
            foreach ($refObject->getProperties(\ReflectionProperty::IS_STATIC) as $property) {
                if (!array_key_exists($property->getName(), $exists)) {
                    $property->setAccessible(true);
                    $this->setProperty(
                        $this->getPrefixProperty($property) . $property->getName(),
                        $property->isInitialized() ? $property->getValue() : new CDumpValueUndefined()
                    );
                    //
                    $exists[$property->getName()] = 1;
                }
            }
            // Перейти к родительскому объекту
            $refObject = $refObject->getParentClass();
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
        // Имя функции
        $functionName = $this->getTypeName();
        // Ключ
        $key = self::class . '@' . $functionName;
        // Если ранее класс массива уже добавлялся
        if ($this->dumper->hasData($key)) {
            // то вернуть пустую строку
            return '';
        }
        // Иначе установить флаг добавления
        $this->dumper->putData($key, true);
        // И вернуть строку с определением функции массива
        return 'function ' . $functionName . '() {}' . CDumpDumper::$ENDLINE;
    }
    // Создать переменную
    public function jsCreateVar(): string
    {
        $ret = '';
        $ret .= 'var ' . $this->name() . ' = new ' . $this->getTypeName() . '();' . CDumpDumper::$ENDLINE;
        $ret .= $this->name() . '.__proto__ = null;' . CDumpDumper::$ENDLINE;
        return $ret;
    }
    // Установить свойства
    public function jsSetProperties(): string
    {
        //
        $ret = '';
        foreach ($this->properties as $name => $oValue) {
            $ret .= $this->name() . '["' . $name . '"] = ' . $oValue->jsValue() . ';' . CDumpDumper::$ENDLINE;
        }
        return $ret;
    }
}
