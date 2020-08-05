<?php
session_start();

include("../../strconexion.inc");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$ID = $_GET['ID'];
$query = "SELECT * FROM tblElementos WHERE Pendiente=1 and Elemento='Panales' and IdResidente=$ID ORDER BY Anio, Mes, Fecha";
$resultR = mysqli_query($link, $query);
$saldoParcial = 0; $CantidadParcial = 0; $Acum='';
while ($row = $resultR->fetch_assoc()) {
    //$resutl = $result . "<td>" . $row['Anio'] . "</td>" . "<td>" . $row['Mes'] . "</td>";
    $Acum .= "<tr>";
    $Acum .= "<td style=\"text-align: center;\">" . $row['Anio'] . "</td>";
    $Acum .= "<td style=\"text-align: center;\">" . $row['Mes'] . "</td>";
    $Acum .= "<td style=\"text-align: center;\">" . $row['Elemento']."</td>";
    $Acum .= "<td style=\"text-align: center;\">".substr($row['Fecha'], 8, 2) . "-" . substr($row['Fecha'], 5, 2) . "-" . substr($row['Fecha'], 0, 4)."</td>";
    $Acum .= "<td style=\"text-align: right;\">" . $row['Cantidad'] . "</td>";
    $Acum .= "<td style=\"text-align: right;\">" . $row['PrecioUnitario']."</td>";
    $Acum .= "<td style=\"text-align: right;\">" . $row['Debe']."</td>";
    $Acum .= "<td style=\"text-align: right;\">" . $row['Haber']."</td>";
    $saldoParcial = $saldoParcial + $row["Debe"] - $row["Haber"];   // Cuenta el saldo parcial de cada item
    $Acum .= "<td style=\"text-align: right;\">" . $saldoParcial."</td>";

    $CantidadParcial = $CantidadParcial + $row['Cantidad'];     // Cuenta la cantidad de pañales totales
    $Acum .= "</tr>";    
}

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
$item->title = 'Pañales descartables para adulto';
$item->quantity = 1;
$item->unit_price = $saldoParcial;
$preference->items = array($item);
$preference->save();

?>
<!DOCTYPE html>
<html lang="en">

<head>
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
    <table class="table-responsive table-striped table-bordered">
        <tr>
            <td>Año</td>
            <td>Mes</td>
            <td>Elemento</td>
            <td>Fecha Movimiento</td>
            <td>Cantidad</td>
            <td>Precio Unitario</td>
            <td>Debe</td>
            <td>Haber</td>
            <td>Saldo</td>
        </tr>
        
            <?php echo $Acum; ?>
        
    </table>
    <form action="https://localhost/cuentascorrientes/recursos/pagos/aprobado.php" method="POST">
        <script src="https://www.mercadopago.com.ar/integrations/v1/web-payment-checkout.js" data-preference-id="<?php echo $preference->id; ?>">
        </script>
    </form>
</body>

</html>