<?php
session_start();
use MercadoPago\Config;



include("../../strconexion.inc");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$ID = $_GET['ID'];
$query = "SELECT tblElementosDescartables.id, tblElementosDescartables.IdResidente, tblPacientes.NombrePaciente, tblElementosDescartables.Anio, tblElementosDescartables.Mes, tblElementosDescartables.Elemento, tblElementosDescartables.Fecha, tblElementosDescartables.Debe, tblElementosDescartables.Haber, tblElementosDescartables.Detalle, tblElementosDescartables.Cantidad, tblElementosDescartables.PrecioUnitario, tblElementosDescartables.Pendiente, tblElementosDescartables.Preferencia FROM tblElementosDescartables, tblPacientes WHERE tblElementosDescartables.Pendiente=1 and tblElementosDescartables.Elemento='Servicios' and tblElementosDescartables.IdResidente=$ID and tblElementosDescartables.IdResidente=tblPacientes.IdPaciente ORDER BY Anio, Mes, Fecha";
//$query = "SELECT * FROM tblElementosDescartables WHERE Pendiente=1 and Elemento='Servicios' and IdResidente=$ID ORDER BY Anio, Mes, Fecha";
$resultR = mysqli_query($link, $query) or die ('No se puedo conectar');
$saldoParcial = 0;
$CantidadParcial = 0;
$Acum = "";
$Contador=0;
while ($row = $resultR->fetch_assoc()) {
    $Contador++;
    $Acum .= "<tr>";
    $Acum .= "<td style=\"text-align: center;\">" . $row['Anio'] . "</td>";
    $Acum .= "<td style=\"text-align: center;\">" . $row['Mes'] . "</td>";
    $Acum .= "<td style=\"text-align: center;\">" . $row['Elemento'] . "</td>";
    $Acum .= "<td style=\"text-align: center;\">" . $row['Detalle'] . "</td>";
    $Acum .= "<td style=\"text-align: center;\">" . substr($row['Fecha'], 8, 2) . "-" . substr($row['Fecha'], 5, 2) . "-" . substr($row['Fecha'], 0, 4) . "</td>";
    $Acum .= "<td style=\"text-align: right;\">" . $row['Cantidad'] . "</td>";
    $Acum .= "<td style=\"text-align: right;\">" . $row['PrecioUnitario'] . "</td>";
    $Acum .= "<td style=\"text-align: right;\">" . $row['Debe'] . "</td>";
    $Acum .= "<td style=\"text-align: right;\">" . $row['Haber'] . "</td>";
    $saldoParcial = $saldoParcial + $row["Debe"] - $row["Haber"];   // Cuenta el saldo parcial de cada item
    $Acum .= "<td style=\"text-align: right;\">" . $saldoParcial . "</td>";

    $CantidadParcial = $CantidadParcial + $row['Cantidad'];     // Cuenta la cantidad de pañales totales
    $Acum .= "</tr>";
}

if ($Contador) {
// SDK de Mercado Pago
require '../../vendor/autoload.php';
//require __DIR__ .  '../../vendor/autoload.php';

// Agrega credenciales
//MercadoPago\SDK::setAccessToken('TEST-1562349347568381-080402-1818024ae10d94b0da38c762088672dc-620380348');
MercadoPago\SDK::setAccessToken('APP_USR-5410638389360943-083101-a6f1979707e15084a4e91d27565d74d8-548162703');

// Crea un objeto de preferencia
$preference = new MercadoPago\Preference();

$preference->back_urls = array(
    "success" => "http://barberdesarrollos.com/cuentascorrientes/recursos/pagos/aprobado.php",
    "failure" => "http://barberdesarrollos.com/cuentascorrientes/recursos/pagos/error.html",
    "pending" => "http://barberdesarrollos.com/cuentascorrientes/recursos/pagos/pendiente.php"
);
$preference->auto_return = "approved";

// Crea un ítem en la preferencia
$item = new MercadoPago\Item();
$item->title = "Servicios utilizados" . " - " . $row['NombrePaciente'];
$item->quantity = 1;
$item->unit_price = $saldoParcial;
$preference->items = array($item);
$preference->save();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900" rel="stylesheet" />
    <link href="../../recursos/pagos/default.css" rel="stylesheet" type="text/css" media="all" />
    <link href="../../recursos/pagos/fonts.css" rel="stylesheet" type="text/css" media="all" />

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../recursos/librerias/bootstrap.min.css">
    <script src="../../recursos/librerias/jquery-3.5.1.slim.min.js"></script>
    <script src="../../recursos/librerias/popper.min.js"></script>
    <script src="../../recursos/librerias/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../recursos/librerias/styles.scss">

    <!-- Angular -->
    <script type="text/javascript" src="../../recursos/librerias/angular.min.js"></script>
    <script type="text/javascript" src="../../controllers/ElementosController.js">
    </script>
    <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
</head>

<body ng-controller="ElementosController" ng-init="Cargar_detalle_panales(<?php echo $ID; ?>);">
    <div id="header-featured">
        <div id="banner" class="container">
            <h2>Hostal de los Abuelos</h2>
            <p><strong>Residencia para Adultos Mayores</strong></p>
        </div>
        <strong>
            <table class="table table-striped" style="color:beige;background: rgba(95, 179, 119, 0.5); margin-bottom: 25px;">
                <tr>
                    <th style="width: 6%;text-align:center">Año</td>
                    <th style="width: 4%;text-align:center">Mes</td>
                    <th style="width: 10%;text-align:center">Elemento</td>
                    <th style="width: 10%;text-align:center">Detalle</td>
                    <th style="width: 12%;text-align:center">Fecha<br>Movimiento</td>
                    <th style="width: 5%;text-align:center">Cantidad</td>
                    <th style="width: 8%;text-align:center">Precio<br>Unitario</td>
                    <th style="width: 5%;text-align:center">Debe</td>
                    <th style="width: 5%;text-align:center">Haber</td>
                    <th style="width: 5%;text-align:center">Saldo</td>
                </tr>

                <?php echo $Acum; ?>

            </table>
        </strong>
        <?php if(!$Contador) echo '<div id="banner-wrapper">
                <div id="banner" class="container" style="font-size: 1em;"><a href="http://barberdesarrollos.com/cuentascorrientes" class="button">Volver al sitio</a></div></div>';?>

        <div style="text-align: center; font-size: 1.2em; <?php if(!$Contador) echo "visibility: hidden;"?>">
            <form action="http://barberdesarrollos.com/cuentascorrientes/recursos/pagos/aprobado.php?id=<?php echo $preference->id."&IDR=".$ID; ?>" method="POST">
                <script src="https://www.mercadopago.com.ar/integrations/v1/web-payment-checkout.js" data-preference-id="<?php echo $preference->id; ?>">
                </script>
            </form>

        </div>

    </div>
</body>

</html>