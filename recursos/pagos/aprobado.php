<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
$preference=$_GET['id'];
$ID=$_GET['IDR'];
include("../../strconexion.inc");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//Controla que la preferencia no esté repetida
$query = "SELECT * FROM tblElementosDescartables WHERE preference='$preference'";
$result = mysqli_query($link, $query);
if($result) {
    echo "Error";
    exit;
}
//Calcula los saldos parciales para poder ser agregados en el pago
$query = "SELECT * FROM tblElementosDescartables WHERE Pendiente=1 and Elemento='Panales' and IdResidente=$ID ORDER BY Anio, Mes, Fecha";
$resultR = mysqli_query($link, $query);
$saldoParcial = 0;
$CantidadParcial = 0;
$Acum = "";
//echo $query;
//preference_id=548162703-bce38ba5-c7ec-4085-8d60-0098ff14ae27&external_reference=&back_url=http%3A%2F%2Fbarberdesarrollos.com%2Fcuentascorrientes%2Frecursos%2Fpagos%2Faprobado.php%3Fcollection_id%3D10492670359%26collection_status%3Dapproved%26external_reference%3Dnull%26payment_type%3Daccount_money%26merchant_order_id%3D1845511863%26preference_id%3D548162703-bce38ba5-c7ec-4085-8d60-0098ff14ae27%26site_id%3DMLA%26processing_mode%3Daggregator%26merchant_account_id%3Dnull&payment_id=10492670359&payment_status=approved&payment_status_detail=accredited&merchant_order_id=1845511863&processing_mode=aggregator&merchant_account_id=

while ($row=$resultR->fetch_assoc()) {
    $saldoParcial = $saldoParcial + $row["Debe"] - $row["Haber"];   // Cuenta el saldo parcial de cada item
    $CantidadParcial = $CantidadParcial + $row['Cantidad'];     // Cuenta la cantidad de pañales totales
    $Fecha = substr($row['Fecha'], 0, 4) . "-" . substr($row['Fecha'], 5, 2) . "-" . substr($row['Fecha'], 8, 2); 
}

$Ano=Date("Y");
$Mes=Date("m");
//Agrega el pago en la base de datos
$query="INSERT INTO tblElementosDescartables (IdResidente, Anio, Mes, Elemento, Fecha, Debe, Haber, Detalle, Cantidad, PrecioUnitario, Pendiente, Preferencia) VALUES ($ID, $Ano,$Mes,'Pañales','$Fecha',0,$saldoParcial, 'MercadoPago', $CantidadParcial,0,0,'$preference')";

$resultInsert = mysqli_query($link, $query);
$query = "UPDATE tblElementosDescartables SET Pendiente=0 WHERE IdResidente=$ID and Elemento='Panales'";
$resultUpdate = mysqli_query($link, $query);
//echo $query;
?>

    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title></title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900" rel="stylesheet" />
        <link href="default.css" rel="stylesheet" type="text/css" media="all" />
        <link href="fonts.css" rel="stylesheet" type="text/css" media="all" />

    </head>

    <body>
        <div id="header-featured">
            <div id="banner-wrapper">
                <div id="banner" class="container">
                    <h2>Hostal de los Abuelos</h2>
                    <p><strong>Residencia para Adultos Mayores</strong></p>
                    <h3><strong>PAGO REGISTRADO.</strong></h3>
                    <a href="http://barberdesarrollos.com/cuentascorrientes" class="button">Volver al sitio</a>
                </div>
            </div>
        </div>

        <div id="copyright" class="container">
            <p>&copy; Derechos Reservados. | Diseño por <a href="http://bardberdesarrollos.com" rel="nofollow">BarBerDesarrollos.com</a>.</p>
        </div>
    </body>

    </html>