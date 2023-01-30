<?php

namespace Llorcasantana\PaymentsLaravelLl\payments_libraries;

class Redsys
{
    /******  Array de DatosEntrada ******/
    private array $vars_pay = array();

    /******  Set parameter ******/
    function setParameter($key,$value): void
    {
        $this->vars_pay[$key]=$value;
    }

    /******  Get parameter ******/
    function getParameter($key)
    {
        return $this->vars_pay[$key];
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////
    ////////////					FUNCIONES AUXILIARES:							  ////////////
    //////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////


    /******  3DES Function  ******/
    function encrypt_3DES($message, $key): string
    {
        $l = ceil(strlen($message) / 8) * 8;
        return substr(openssl_encrypt($message . str_repeat("\0", $l - strlen($message)), 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, "\0\0\0\0\0\0\0\0"), 0, $l);
    }

    /******  Base64 Functions  ******/
    function base64_url_encode($input): string
    {

        return strtr(base64_encode($input), '+/', '-_');
    }

    function encodeBase64($data): string
    {
        return base64_encode($data);
    }

    function base64_url_decode($input): bool|string
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    function decodeBase64($data): bool|string
    {
        return base64_decode($data);
    }

    /******  MAC Function ******/
    function mac256($ent,$key): string
    {
        //(PHP 5 >= 5.1.2)
        return hash_hmac('sha256', $ent, $key, true);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////
    ////////////	   FUNCIONES PARA LA GENERACIÓN DEL FORMULARIO DE PAGO:			  ////////////
    //////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////

    /******  Obtener Número de pedido ******/
    function getOrder()
    {
        if(empty($this->vars_pay['DS_MERCHANT_ORDER'])) {
            $numPedido = $this->vars_pay['Ds_Merchant_Order'];
        } else {
            $numPedido = $this->vars_pay['DS_MERCHANT_ORDER'];
        }

        return $numPedido;
    }
    /******  Convertir Array en Objeto JSON ******/
    function arrayToJson(): bool|string
    {
        //(PHP 5 >= 5.2.0)

        return json_encode($this->vars_pay);
    }
    function createMerchantParameters(): string
    {
        // Se transforma el array de datos en un objeto Json
        $json = $this->arrayToJson();

        // Se codifican los datos Base64
        return $this->encodeBase64($json);
    }
    function createMerchantSignature($key): string
    {
        // Se decodifica la clave Base64
        $key = $this->decodeBase64($key);
        // Se genera el parámetro Ds_MerchantParameters
        $ent = $this->createMerchantParameters();
        // Se diversifica la clave con el Número de Pedido
        $key = $this->encrypt_3DES($this->getOrder(), $key);
        // MAC256 del parámetro Ds_MerchantParameters
        $res = $this->mac256($ent, $key);

        // Se codifican los datos Base64
        return $this->encodeBase64($res);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////
    //////////// FUNCIONES PARA LA RECEPCIÓN DE DATOS DE PAGO (Notif, URLOK y URLKO): ////////////
    //////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////

    /******  Obtener Número de pedido ******/
    function getOrderNotif()
    {
        $numPedido = "";
        if(empty($this->vars_pay['Ds_Order'])) {
            $numPedido = $this->vars_pay['DS_ORDER'];
        } else {
            $numPedido = $this->vars_pay['Ds_Order'];
        }

        return $numPedido;
    }
    function getOrderNotifSOAP($datos): string
    {
        $posPedidoIni = strrpos($datos, "<Ds_Order>");
        $tamPedidoIni = strlen("<Ds_Order>");
        $posPedidoFin = strrpos($datos, "</Ds_Order>");

        return substr($datos,$posPedidoIni + $tamPedidoIni,$posPedidoFin - ($posPedidoIni + $tamPedidoIni));
    }

    function getRequestNotifSOAP($datos): string
    {
        $posReqIni = strrpos($datos, "<Request");
        $posReqFin = strrpos($datos, "</Request>");
        $tamReqFin = strlen("</Request>");

        return substr($datos,$posReqIni,($posReqFin + $tamReqFin) - $posReqIni);
    }

    function getResponseNotifSOAP($datos): string
    {
        $posReqIni = strrpos($datos, "<Response");
        $posReqFin = strrpos($datos, "</Response>");
        $tamReqFin = strlen("</Response>");

        return substr($datos,$posReqIni,($posReqFin + $tamReqFin) - $posReqIni);
    }

    /******  Convertir String en Array ******/
    function stringToArray($datosDecod): void
    {
        $this->vars_pay = json_decode($datosDecod, true); //(PHP 5 >= 5.2.0)
    }

    function decodeMerchantParameters($datos): bool|string
    {
        // Se decodifican los datos Base64
        $decodec = $this->base64_url_decode($datos);
        // Los datos decodificados se pasan al array de datos
        $this->stringToArray($decodec);

        return $decodec;
    }

    function createMerchantSignatureNotif($key, $datos): string
    {
        // Se decodifica la clave Base64
        $key = $this->decodeBase64($key);
        // Se decodifican los datos Base64
        $decodec = $this->base64_url_decode($datos);
        // Los datos decodificados se pasan al array de datos
        $this->stringToArray($decodec);
        // Se diversifica la clave con el Número de Pedido
        $key = $this->encrypt_3DES($this->getOrderNotif(), $key);
        // MAC256 del parámetro Ds_Parameters que envía Redsys
        $res = $this->mac256($datos, $key);

        // Se codifican los datos Base64
        return $this->base64_url_encode($res);
    }

    /******  Notificaciones SOAP ENTRADA ******/
    function createMerchantSignatureNotifSOAPRequest($key, $datos): string
    {
        // Se decodifica la clave Base64
        $key = $this->decodeBase64($key);
        // Se obtienen los datos del Request
        $datos = $this->getRequestNotifSOAP($datos);
        // Se diversifica la clave con el Número de Pedido
        $key = $this->encrypt_3DES($this->getOrderNotifSOAP($datos), $key);
        // MAC256 del parámetro Ds_Parameters que envía Redsys
        $res = $this->mac256($datos, $key);

        // Se codifican los datos Base64
        return $this->encodeBase64($res);
    }
    /******  Notificaciones SOAP SALIDA ******/
    function createMerchantSignatureNotifSOAPResponse($key, $datos, $numPedido): string
    {
        // Se decodifica la clave Base64
        $key = $this->decodeBase64($key);
        // Se obtienen los datos del Request
        $datos = $this->getResponseNotifSOAP($datos);
        // Se diversifica la clave con el Número de Pedido
        $key = $this->encrypt_3DES($numPedido, $key);
        // MAC256 del parámetro Ds_Parameters que envía Redsys
        $res = $this->mac256($datos, $key);

        // Se codifican los datos Base64
        return $this->encodeBase64($res);
    }
}