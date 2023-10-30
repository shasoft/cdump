<?php

namespace Shasoft\CDump;

// Класс для генерации кода HTML
class CDumpHtml
{
    // Перевод строки
    static public string $ENDLINE = "\n";
    // Получить код JavaScript который выполняется только если присутствует объект console
    static public function getScript(string $jsCode): string
    {
        $ret = '';
        if (!empty($jsCode)) {
            $ret .= '<script>' . self::$ENDLINE;
            $ret .= '(function() {' . self::$ENDLINE;
            $ret .= 'if(console) {' . self::$ENDLINE;
            $ret .= $jsCode;
            $ret .= '}' . self::$ENDLINE;
            $ret .= '})();' . self::$ENDLINE;
            $ret .= '</script>';
        }
        return $ret;
    }
    // Получить скрипт для вывода
    static public function getJsCode(...$args): string
    {
        return (new CDumpDumper)->getJsCode(...$args);
    }
    // Преобразовать значение в строку HTML
    static public function to(mixed $value): string
    {
        $type = gettype($value);
        $methodName = '_to' . ucfirst($type);
        if (method_exists(self::class, $methodName)) {
            $ret = self::$methodName($value);
        } else {
            $ret = self::toJsonString($value);
        }
        return $ret;
    }
    // Преобразовать список аргументов в строку HTML
    static public function args(array $args): string
    {
        return implode(',', array_map(function ($arg) {
            return self::to($arg);
        }, $args));
    }
    // Идентификатор объекта
    static public function object_id(mixed $id): string
    {
        if (is_object($id)) {
            $id = spl_object_id($id);
        }
        return '<strong style="color:Magenta">.' . $id . '</strong>';
    }
    // Преобразовать в строку JSON
    static public function toJsonString(mixed $value): string|false
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES  | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }
    // Преобразовать объект в строку HTML
    static protected function _toObject(object $object): string
    {
        try {
            // Попробовать преобразование с помощью магического метода __toString
            $ret = (string)$object;
        } catch (\Throwable $th) {
            $ret = get_class($object) . self::object_id($object);
        }
        return $ret;
    }
    // Преобразовать массив в строку HTML
    static protected function _toArray(array $items): string
    {
        $values = [];
        // А может это массив с числовыми индексами
        $hasNum = true;
        $lastI = -1;
        for ($i = 0; $i < count($items); $i++) {
            if (!array_key_exists($i, $items)) {
                $hasNum = false;
                break;
            }
            $lastI = $i;
        }
        $keys = array_keys($items);
        sort($keys, SORT_REGULAR);
        if ($hasNum && $lastI == count($items) - 1) {
            foreach ($keys as $key) {
                $values[] = self::to($items[$key]);
            }
        } else {
            foreach ($keys as $key) {
                $values[] = self::to($key) . '=&gt;' . self::to($items[$key]);
            }
        }
        return '[' . implode(',', $values) . ']';
    }
    // Преобразовать строку в строку HTML
    static protected function _toString(string $value): string
    {
        return self::toJsonString(htmlentities($value));
    }
    // Преобразовать целое число в строку HTML
    static protected function _toInteger(int $value): string
    {
        return $value;
    }
    // Преобразовать вещественное число в строку HTML
    static protected function _toDouble(float $value): string
    {
        return $value;
    }
    // Преобразовать логическое в строку HTML
    static protected function _toBoolean(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
    // Преобразовать логическое в строку HTML
    static protected function _toNULL(mixed $value): string
    {
        return 'null';
    }
    // Преобразовать html в текст
    static public function asString(string $html): string
    {
        return html_entity_decode(strip_tags($html));
    }
    // Преобразовать значение в строку
    static public function toString(mixed $value): string
    {
        $ret =  self::asString(self::to($value));
        if (is_string($value)) {
            $ret = substr($ret, 1, -1);
        }
        return $ret;
    }
}
