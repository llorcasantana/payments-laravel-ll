<?php

namespace Llorcasantana\PaymentsLaravelLl\Tests;


use Llorcasantana\PaymentsLaravelLl\Calculator;
use Llorcasantana\PaymentsLaravelLl\PaymentGateway;
use PHPUnit\Framework\TestCase;
class Test extends TestCase
{
    /**
     * @test
     */
    public function itSums(){
        $payment = new PaymentGateway();
        $payment->payment_method = 'redsys';
        $payment->payment_ammount = 53.45;
        $payment->payment_order = 'ORDER12345';
        $payment->payment_merchant_code = '36841443';
        $payment->payment_currency = '978';
        $payment->payment_transaction_type = '0';
        $payment->payment_terminal = '1';
        $payment->payment_api_url = 'https://sis-t.redsys.es:25443/sis/rest/trataPeticionREST';
        $payment->payment_url_ok = 'http://localhost';
        $payment->payment_url_no_ok = 'http://localhost?no=true';


        $this->assertSame('redsys', $payment->loadData());
    }
}