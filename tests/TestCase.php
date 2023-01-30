<?php

namespace Llorcasantana\Payments_laravel_ll\Tests;


use Llorcasantana\Payments_laravel_ll\Src\Calculator;
use PHPUnit\Framework\TestCase;
class Test extends TestCase
{
    /**
     * @test
     */
    public function itSums(){
        $calculator = new Calculator();
        $sum = $calculator->sum(5,4);
        $this->assertSame(9, $sum);
    }
}