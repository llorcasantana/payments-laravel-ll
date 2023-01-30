<?php

namespace Llorcasantana\PaymentsLaravelLl;


use \Ssheduardo\Redsys\Facades\Redsys;

class PaymentGateway
{
    public string $payment_method = '';
    public string $num_operacion = '';
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
    public string $payment_enviroiment = '';
    public string $payment_key = '';

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
        $key = $this->payment_key;
        Redsys::setAmount($this->payment_amount);
        Redsys::setOrder($this->num_operacion);
        Redsys::setMerchantcode($this->payment_merchant_code);
        Redsys::setCurrency($this->payment_currency);
        Redsys::setTransactiontype($this->payment_transaction_type);
        Redsys::setTerminal($this->payment_terminal);
        Redsys::setMethod($this->payment_redsys_method);
        Redsys::setNotification($this->payment_redsys_notif);
        Redsys::setUrlOk($this->payment_api_url_ok);
        Redsys::setUrlKo($this->payment_api_url_ko);
        Redsys::setVersion($this->payment_version);
        Redsys::setTradeName($this->payment_trade_name);
        Redsys::setTitular($this->payment_titular);
        Redsys::setProductDescription($this->payment_product_description);
        Redsys::setEnviroment($this->payment_enviroiment);

        $signature = Redsys::generateMerchantSignature($key);
        Redsys::setMerchantSignature($signature);

        return Redsys::createForm();
    }
}