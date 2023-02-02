<?php

namespace Llorcasantana\PaymentsLaravelLl\Tests;


use Llorcasantana\PaymentsLaravelLl\PaymentGateway;
use PHPUnit\Framework\TestCase;
class Test extends TestCase
{
    /**
     * @test
     */
    public function itSums(){
        $key = 'iXNiOqj/alwvUcf9X0DUvnyRHyyU4uoz';
        $Num_operacion = '123456760';

        $payment = new PaymentGateway();
        $payment->payment_method = 'redsys';
        $payment->payment_amount = 58.00;
        $payment->payment_order = $Num_operacion;
        $payment->num_operacion = $Num_operacion;
        $payment->payment_merchant_code = '36841443';
        $payment->payment_currency = '978';
        $payment->payment_transaction_type = '0';
        $payment->payment_terminal = '1';
        $payment->payment_redsys_method = 'T';
        $payment->payment_redsys_notif = env('APP_URL','').'perfil/orden/ORDER'.$Num_operacion.'?result=ok_payment';
        $payment->payment_api_url_ok = env('APP_URL','').'perfil/orden/ORDER'.$Num_operacion.'?result=ok_payment';
        $payment->payment_api_url_ko = env('APP_URL','').'carrito?steep=3';
        $payment->payment_version = 'HMAC_SHA256_V1';
        $payment->payment_trade_name = 'ECOMMERCE';
        $payment->payment_titular = 'Jose';
        $payment->payment_product_description = 'Compras';
        $payment->payment_enviroiment = 'live';
        $payment->payment_key = $key;
        $payment->custon_button_class = 'btn btn-primary';
        $payment->custom_button_value = 'Comprar';

        return $this->assertSame('',$payment->loadData());
    }
}