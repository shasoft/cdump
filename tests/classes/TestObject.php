<?php

namespace Shasoft\CDumpTest;

class TestObject extends TestObject0
{
    protected static int $numerator2 = 7;
    public ?TestObject $objPub;
    private ?TestObject $objPvt;
    protected bool $logical;
    protected array $arr = [];
    protected array $arr2 = [];
    private $img;
    public CEnum2 $enum2;
    // Конструктор
    public function __construct(?TestObject $obj)
    {
        $this->objPub = $obj;
        $this->objPvt = $obj;
        $this->logical = true;
        $this->arr['aaa'] = 1;
        $this->arr['bbb'] = [2, false, $obj];
        $this->arr2[] = 10;
        $this->arr2[] = 20;
        $this->arr2[] = 30;
        $this->arr2[] = $this->arr;
        $this->img = imagecreate(32, 32);
        $this->enum1 = CEnum1::Hearts;
        $this->enum2 = CEnum2::Hearts;
    }
}
