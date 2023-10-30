<?php

namespace Shasoft\CDump\Extension;

use Shasoft\CDump\CDumpValueObject;

class GdImage extends CDumpValueObject
{
    // Инициализация
    public function initialization(\ReflectionClass $refObject, object $image): void
    {
        parent::initialization($refObject, $image);
        // Установить свойства
        $this->setProperty('width', imagesx($image));
        $this->setProperty('height', imagesy($image));
        $this->setProperty('trueColor', imageistruecolor($image));
    }
}
