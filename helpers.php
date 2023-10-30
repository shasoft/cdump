<?php

use Shasoft\CDump\CDumpHtml;
use Shasoft\CDump\CDumpTrace;

if (!function_exists('cdump')) {
    // Вывести значение переменных в консоль браузера
    function cdump(...$args)
    {
        // Определить имя файла и строку вызова
        $oTrace = new CDumpTrace(2);
        // JavaScript код
        $jsCode = 'console.groupCollapsed(' . json_encode($oTrace->location(1), JSON_UNESCAPED_SLASHES) . ');' . "\n";
        $jsCode .= CDumpHtml::getJsCode(...$args) . "\n";
        $jsCode .= 'console.groupEnd();' . "\n";
        echo CDumpHtml::getScript($jsCode);
    }
    // Вывести значение переменных в консоль браузера
    function ctrace(bool $skipCDump = true)
    {
        // Определить имя файла и строку вызова
        $oTrace = new CDumpTrace(0, DEBUG_BACKTRACE_PROVIDE_OBJECT);
        // JavaScript код
        $jsCode = 'console.groupCollapsed(' . json_encode($oTrace->location(1), JSON_UNESCAPED_SLASHES) . ');' . "\n";
        $i = $oTrace->size() - 1;
        while ($i >= 2) {
            //
            $name = $oTrace->name($i);
            if (!$skipCDump || substr($name, 0, 14) != "Shasoft\\CDump\\") {
                //
                $jsCode .= 'console.groupCollapsed(' . json_encode($name, JSON_UNESCAPED_SLASHES) . ');' . "\n";;
                $jsCode .= CDumpHtml::getJsCode($oTrace->get($i)) . "\n";
                $jsCode .= 'console.groupEnd();' . "\n";
            }
            $i--;
        }
        $jsCode .= 'console.groupEnd();' . "\n";
        echo CDumpHtml::getScript($jsCode);
    }
}
