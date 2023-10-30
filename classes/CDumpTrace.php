<?php

namespace Shasoft\CDump;


// Класс для работы со стеком вызовов
class CDumpTrace
{
    // Вызовы
    protected array $traces;
    // Конструктор
    public function __construct(int $limit = 0, int $options = DEBUG_BACKTRACE_IGNORE_ARGS)
    {
        $this->traces = debug_backtrace($options, $limit);
    }
    // Определить имя вызова
    static public function getName(array $trace): string
    {
        $prefix = ($trace['class'] ?? '');
        if (array_key_exists('object', $trace)) {
            $prefix = CDumpHtml::toString($trace['object']);
        }
        return $prefix . ($trace['type'] ?? '') . ($trace['function'] ?? '');
    }
    // Имя файла:номер строки
    static public function getLocation(array $trace): string
    {
        // Имя файла
        $file = str_replace('\\', '/', $trace['file'] ?? '-');
        // Номер строки
        $line = $trace['line'] ?? '-';
        // Заголовок
        return $file . ':' . $line;
    }
    // Получить все значения
    public function all(): array
    {
        return $this->traces;
    }
    // Получить количество значений
    public function size(): int
    {
        return count($this->traces);
    }
    // Получить стек номер $index
    public function get(int $index): array
    {
        return $this->traces[$index] ?? [];
    }
    // Имя
    public function name(int $index): string
    {
        return self::getName($this->get($index));
    }
    // Место расположения
    public function location(int $index): string
    {
        return self::getLocation($this->get($index));
    }
    // Искать по имени первое вхождение
    public function findNameFirst(string $name, int $startIndex = 0): int|false
    {
        for ($i = $startIndex; $i < count($this->traces); $i++) {
            if (self::getName($this->traces[$i]) == $name) {
                return $i;
            }
        }
        return false;
    }
    // Искать по имени последнее вхождение
    public function findNameLast(string $name, int $startIndex = 0): int|false
    {
        $ret = false;
        for ($i = $startIndex; $i < count($this->traces); $i++) {
            if (self::getName($this->traces[$i]) == $name) {
                $ret = $i;
            }
        }
        return $ret;
    }
}
