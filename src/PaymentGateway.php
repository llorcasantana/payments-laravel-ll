<?php

namespace Llorcasantana\PaymentsLaravelLl;

use Llorcasantana\PaymentsLaravelLl\payments_libraries\Redsys;

class PaymentGateway
{
    public string $payment_method = '';
    public string $payment_order = '';
    public string $payment_merchant_code = '';
    public string $payment_currency = '978';
    public string $payment_redsys_method = 'T';
    public string $payment_redsys_notif = 'http://localhost/';
    public float $payment_amount = 0.00;
    public string $payment_transaction_type = '0';
    public string $payment_terminal = '1';
    public string $payment_url_ok = '';
    public string $payment_url_no_ok = '';
    public string $payment_api_url = '';
    public string $payment_api_url_ok = '';
    public string $payment_version = 'HMAC_SHA256_V1';
    public string $payment_trade_name = 'ECOMMERCE';
    public string $payment_titular = 'Pedro Risco';
    public string $payment_product_description = 'Compras varias';
    public string $payment_api_url_ko = '';

    public function loadData(): string
    {
        switch ($this->payment_method){
            case 'redsys':
                return $this->initRedsys();
                break;
            default:
                break;
        }
        return $this->payment_method;
    }

     /*
     * PAYMENTS METHODS
     */
    private function initRedsys(): string
    {
        $key = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
        $redsys = new Redsys();

        $redsys->setAmount($this->payment_amount);
        $redsys->setOrder($this->payment_order);
        $redsys->setMerchantcode($this->payment_merchant_code); //Reemplazar por el código que proporciona el banco
        $redsys->setCurrency($this->payment_currency);
        $redsys->setTransactiontype($this->payment_transaction_type);
        $redsys->setTerminal($this->payment_terminal);
        $redsys->setMethod($this->payment_redsys_method); //Solo pago con tarjeta, no mostramos iupay
        $redsys->setNotification($this->payment_redsys_notif); //Url de notificacion
        $redsys->setUrlOk($this->payment_api_url_ok); //Url OK
        $redsys->setUrlKo($this->payment_api_url_ko); //Url KO
        $redsys->setVersion($this->payment_version);
        $redsys->setTradeName($this->payment_trade_name);
        $redsys->setTitular($this->payment_titular);
        $redsys->setProductDescription($this->payment_product_description);
        $redsys->setEnviroment('live'); //Entorno test

        $signature = $redsys->generateMerchantSignature($key);
        $redsys->setMerchantSignature($signature);

        return $redsys->createForm();
    }
}