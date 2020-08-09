<?php
// SDK de Mercado Pago
require '../../vendor/autoload.php';
//require __DIR__ .  '../../vendor/autoload.php';

// Agrega credenciales
MercadoPago\SDK::setAccessToken('TEST-1562349347568381-080402-1818024ae10d94b0da38c762088672dc-620380348');

// Crea un objeto de preferencia
$preference = new MercadoPago\Preference();

$preference->back_urls = array(
    "success" => "https://localhost/cuentascorrientes/recursos/pagos/aprobado.php",
    "failure" => "http://www.tu-sitio/failure",
    "pending" => "http://www.tu-sitio/pending"
);
$preference->auto_return = "approved";

// Crea un ítem en la preferencia
$item = new MercadoPago\Item();
$item->title = 'Mi producto';
$item->quantity = 1;
$item->unit_price = 75.56;
$preference->items = array($item);
$preference->save();
//echo $preference->id;
//return $preference->id;
$preferenciaId = $preference->id;
$_SESSION['preferenciaId'] = $preference->id;
$datos['preferenciaId'] = '<form action="https://localhost/cuentascorrientes/recursos/pagos/aprobado.php" method="POST"><script src="https://www.mercadopago.com.ar/integrations/v1/web-payment-checkout.js" data-preference-id="' . $preference->id . '"></script></form>';

$datos['preferenciaId2'] = "<p>Esta es una prueba</p>";
//$datos['preferenciaId'] = "<form action=\"https://localhost/cuentascorrientes/recursos/pagos/aprobado.php\" method=\"POST\"><script src=\"https://www.mercadopago.com.ar/integrations/v1/web-payment-checkout.js\" data-preference-id=\"". $preference->id ."\"></script></form>";
//$datos['preferenciaId'] = $preference->id;
echo $datos['preferenciaId'];
////$datos = json_encode($datos);
//print_r($datos);
//echo $sql;
//echo $datos;
?>
