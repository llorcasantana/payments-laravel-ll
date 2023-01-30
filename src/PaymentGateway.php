<?php

namespace Llorcasantana\PaymentsLaravelLl;

use Llorcasantana\PaymentsLaravelLl\payments_libraries\Redsys;

class PaymentGateway
{
    public string $url_dev = 'https://sis-t.redsys.es:25443/sis/rest/trataPeticionREST';
    public string $url_prod = 'https://sis.redsys.es/sis/rest/trataPeticionREST';
    public string $payment_method = '';
    public string $payment_order = '';
    public string $payment_merchant_code = '';
    public string $payment_currency = '';
    public string $payment_ammount = '';
    public string $payment_transaction_type = '';
    public string $payment_terminal = '';
    public string $payment_url_ok = '';
    public string $payment_url_no_ok = '';
    public string $payment_api_url = '';
    public string $payment_api_url_ok = '';
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
    private function initRedsys(): array
    {
        $miObj = new Redsys();
        $miObj->setParameter("DS_MERCHANT_AMOUNT", $this->payment_ammount);
        $miObj->setParameter("DS_MERCHANT_ORDER", $this->payment_order);
        $miObj->setParameter("DS_MERCHANT_MERCHANTCODE", $this->payment_merchant_code);
        $miObj->setParameter("DS_MERCHANT_CURRENCY", $this->payment_currency);
        $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $this->payment_transaction_type);
        $miObj->setParameter("DS_MERCHANT_TERMINAL", $this->payment_terminal);
        $miObj->setParameter("DS_MERCHANT_MERCHANTURL", $this->payment_api_url);
        $miObj->setParameter("DS_MERCHANT_URLOK", $this->payment_url_ok);
        $miObj->setParameter("DS_MERCHANT_URLKO", $this->payment_url_no_ok);
        $miObj->createMerchantParameters();
        $claveSHA256 = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
        return [
            'signature'=>$miObj->createMerchantSignature($claveSHA256),
            'params'=>$miObj->createMerchantParameters(),
            'result'=>true
        ];
    }
}