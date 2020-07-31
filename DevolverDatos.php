<?php
session_start();
include("strconexion.inc");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$query = "";

$_SESSION['user'] = 'root';
$_SESSION['password'] = '';
$pdo = new PDO('mysql:host=localhost;dbname=cuentascorrientes', $_SESSION['user'], $_SESSION['password']);

$Opcion = $_GET["Opcion"];
$Parametros = $_GET["Param"];

$datos = array();
$rows = array();
//include_once("stringconexion.inc");  // CORRE EN EL HOSTING    
switch ($Opcion) {
    case "CargarResidentes":
        $sql = "SELECT * FROM tblPacientes WHERE PacienteActivo=1";
        //echo $sql;
        $resultado = $pdo->prepare($sql);
        $resultado->execute();
        $datos = $resultado->fetchAll();
        break;
    case "CargarDatosResidente":
        $query = "SELECT * FROM tblPacientes WHERE NroDocumento=" . $Parametros;
        $result = mysqli_query($link, $query);
        $datos = $result->fetch_assoc();

        //$resultado = $pdo->prepare($sql);
        //$resultado->execute();
        //$datos = $resultado->fetchAll();
        break;
    case "CargarElementos_panales":
        $query = "SELECT * FROM tblElementos WHERE Pendiente=1 and Elemento='Panales' and IdResidente=$Parametros ORDER BY Anio, Mes";
        //echo $query;
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $saldo = 0;
        $saldoCantidad  = 0;
        for ($i = 0; $i < count($rows); $i++) {
            $saldo = $saldo + $rows[$i]["Debe"] - $rows[$i]["Haber"];
            $saldoCantidad = $saldoCantidad + $rows[$i]["Cantidad"];
        }
        $datos['SaldoPanales'] = $saldo;
        $datos['SaldoPanalesCantidad'] = $saldoCantidad;
        break;
    case "CargarElementos_descartables":
        $query = "SELECT * FROM tblElementos WHERE Pendiente=1 and Elemento='Descartables' and IdResidente=$Parametros ORDER BY Anio, Mes";
        //echo $query;
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $saldo = 0;
        $saldoCantidad  = 0;
        for ($i = 0; $i < count($rows); $i++) {
            $saldo = $saldo + $rows[$i]["Debe"] - $rows[$i]["Haber"];
            //$saldoCantidad = $saldoCantidad + $rows[$i]["Cantidad"];
        }
        $datos['SaldoDescartables'] = $saldo;
        //$datos['SaldoDescartablesCantidad'] = $saldoCantidad;
        break;
    case "CargarElementos_servicios":
        $query = "SELECT * FROM tblElementos WHERE Pendiente=1 and Elemento='Servicios' and IdResidente=$Parametros ORDER BY Anio, Mes";
        //echo $query;
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $saldo = 0;
        $saldoCantidad  = 0;
        for ($i = 0; $i < count($rows); $i++) {
            $saldo = $saldo + $rows[$i]["Debe"] - $rows[$i]["Haber"];
            //$saldoCantidad = $saldoCantidad + $rows[$i]["Cantidad"];
        }
        $datos['SaldoServicios'] = $saldo;
        //$datos['SaldoDescartablesCantidad'] = $saldoCantidad;
        break;
    case "CargarElementos_insumos":
        $query = "SELECT * FROM tblElementos WHERE Pendiente=1 and Elemento='Insumos' and IdResidente=$Parametros ORDER BY Anio, Mes";
        //echo $query;
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $saldo = 0;
        $saldoCantidad  = 0;
        for ($i = 0; $i < count($rows); $i++) {
            $saldo = $saldo + $rows[$i]["Debe"] - $rows[$i]["Haber"];
            //$saldoCantidad = $saldoCantidad + $rows[$i]["Cantidad"];
        }
        $datos['SaldoInsumos'] = $saldo;
        //$datos['SaldoDescartablesCantidad'] = $saldoCantidad;
        break;
}

$datos = json_encode($datos);
//print_r($datos);
//echo $sql;
echo $datos;
