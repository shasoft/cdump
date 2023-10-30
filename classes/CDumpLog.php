<?php

namespace Shasoft\CDump;


// Класс для вывода данных в консоль  браузера через console.log(...)
class CDumpLog
{
    // Включить/выключить логирование
    protected static bool $enable = false;
    static public function enable(bool $enable): void
    {
        self::$enable = $enable;
    }
    // Вывести в лог
    static protected function _dump(bool $cond, string $name, string $title, ...$args): void
    {
        if (self::$enable && $cond) {
            // Определить место вызова
            $oTrace = new CDumpTrace(8);
            $index = $oTrace->findNameFirst($name);
            $trace = $oTrace->get($index + 1);
            // Выполнить подстановки
            $title = self::text($title, ...$args);
            // JavaScript код
            $jsCode = 'console.groupCollapsed(' . CDumpHtml::toJsonString($title) . ');' . CDumpHtml::$ENDLINE;
            $jsCode .= 'console.log(' . CDumpHtml::toJsonString(CDumpTrace::getLocation($trace)) . ');' . CDumpHtml::$ENDLINE;
            if (!empty($args)) {
                $jsCode .= CDumpHtml::getJsCode(...$args) . CDumpHtml::$ENDLINE;
            }
            $jsCode .= 'console.groupEnd();' . CDumpHtml::$ENDLINE;
            echo CDumpHtml::getScript($jsCode);
        }
    }
    // Группа
    static protected function _group(bool $cond, string $name, callable $cb, ?string $format = null, ...$args): mixed
    {
        if (self::$enable && $cond) {
            $jsCode = '';
            // Определить место вызова
            $oTrace = new CDumpTrace(8, DEBUG_BACKTRACE_PROVIDE_OBJECT);
            $index = $oTrace->findNameFirst($name);
            $trace = $oTrace->get($index + 1);
            // Если не указан текст группы
            if (is_null($format)) {
                // Это функция
                $hasFn = true;
                // Определить текст заголовка
                $title = null;
                if (array_key_exists('object', $trace)) {
                    // Это вызов метода объекта
                    $title = CDumpHtml::toString($trace['object']) . $trace['type'] . $trace['function'];
                } else if (array_key_exists('class', $trace)) {
                    // Это вызов метода класса (статический метод)
                    $title = $trace['class'] . $trace['type'] . $trace['function'];
                } else if (array_key_exists('function', $trace)) {
                    // Это вызов функции
                    $title = $trace['function'];
                } else {
                    $title = CDumpTrace::getLocation($trace);
                }
            } else {
                // Это группа
                $hasFn = false;
                // Определить текст заголовка
                $title = self::text($format, ...$args);
            }
            // Начало группу
            $jsCode .= 'console.groupCollapsed(' . CDumpHtml::toJsonString($title) . ');' . CDumpHtml::$ENDLINE;
            if ($hasFn) {
                // Если есть объект
                if (array_key_exists('object', $trace)) {
                    $s = CDumpHtml::toString($trace['object']);
                    $jsCode .= 'console.groupCollapsed(">$this "+' . CDumpHtml::toJsonString(CDumpHtml::toString($trace['object'])) . ');' . CDumpHtml::$ENDLINE;
                    $jsCode .= CDumpHtml::getJsCode($trace['object']);
                    $jsCode .= 'console.groupEnd();' . CDumpHtml::$ENDLINE;
                }
                // Если присутствуют аргументы
                if (!empty($trace['args'])) {
                    $jsCode .= 'console.groupCollapsed("$args[' . count($trace['args']) . ']");' . CDumpHtml::$ENDLINE;
                    $jsCode .= CDumpHtml::getJsCode(...$trace['args']);
                    $jsCode .= 'console.groupEnd();' . CDumpHtml::$ENDLINE;
                }
            } else {
                if (!empty($args)) {
                    $jsCode .= 'console.groupCollapsed("$args[' . count($args) . ']");' . CDumpHtml::$ENDLINE;
                    $jsCode .= CDumpHtml::getJsCode(...$args);
                    $jsCode .= 'console.groupEnd();' . CDumpHtml::$ENDLINE;
                }
            }
            echo CDumpHtml::getScript($jsCode);
        }
        // Вызвать функцию
        $ret = $cb();
        if (self::$enable && $cond) {
            $jsCode = '';
            //
            if ($hasFn) {
                // Вывести результат
                $refCb = new \ReflectionFunction($cb);
                // По умолчанию выводить возвращаемый результат
                $hasRetDump = true;
                // Если тип возвращаемого результата у замыкания = 'void'
                if ($refCb->hasReturnType()) {
                    if ((string)$refCb->getReturnType() == 'void') {
                        // то не выводить результат
                        $hasRetDump = false;
                    }
                }
                if ($hasRetDump) {
                    $jsCode .= CDumpHtml::getJsCode($ret);
                }
                // Если есть объект
                if (array_key_exists('object', $trace)) {
                    $jsCode .= 'console.groupCollapsed("<$this "+' . CDumpHtml::toJsonString(CDumpHtml::toString($trace['object'])) . ');' . CDumpHtml::$ENDLINE;
                    $jsCode .= CDumpHtml::getJsCode($trace['object']);
                    $jsCode .= 'console.groupEnd();' . CDumpHtml::$ENDLINE;
                }
            }
            // Конец группы
            $jsCode .= 'console.groupEnd();' . CDumpHtml::$ENDLINE;
            echo CDumpHtml::getScript($jsCode);
        }
        return $ret;
    }
    // Вывести в лог
    static public function dump(bool $cond, string $title, ...$args): void
    {
        self::_dump($cond, __METHOD__, $title, ...$args);
    }
    // Группа
    static public function group(bool $cond, callable $cb, ?string $format = null, ...$args): mixed
    {
        return self::_group($cond, __METHOD__, $cb, $format, ...$args);
    }
    // Получить html по маске
    static public function html(string $format, ...$args): string
    {
        $ret = CDumpHtml::to($format);
        foreach ($args as $num => $arg) {
            $ret = str_replace('&' . ($num + 1),  CDumpHtml::to($arg), $ret);
        }
        return $ret;
    }
    // Получить строку текста по маске
    static public function text(string $format, ...$args): string
    {
        $ret = CDumpHtml::toString($format);
        foreach ($args as $num => $arg) {
            $ret = str_replace('&' . ($num + 1),  CDumpHtml::toString($arg), $ret);
        }
        return $ret;
    }
}
