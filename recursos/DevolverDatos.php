<?php
session_start();
include("../strconexion.inc");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$query = "";
$Opcion = $_GET["Opcion"];
$Parametros = $_GET["Param"];

$datos = array();
$rows = array();

switch ($Opcion) {
    case "CargarResidentes":
        $query = "SELECT * FROM tblPacientes WHERE PacienteActivo=1";
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $datos = $rows;
        break;
    case "CargarDatosResidente":
        $query = "SELECT * FROM tblPacientes WHERE NroDocumento=" . $Parametros;
        $result = mysqli_query($link, $query);
        $datos = $result->fetch_assoc();
        break;
    case "CargarElementos_panales":
        $query = "SELECT * FROM tblElementos WHERE Pendiente=1 and Elemento='Panales' and IdResidente=$Parametros ORDER BY Anio, Mes";
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
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $saldo = 0;
        $saldoCantidad  = 0;
        for ($i = 0; $i < count($rows); $i++) {
            $saldo = $saldo + $rows[$i]["Debe"] - $rows[$i]["Haber"];
        }
        $datos['SaldoDescartables'] = $saldo;
        break;
    case "CargarElementos_servicios":
        $query = "SELECT * FROM tblElementos WHERE Pendiente=1 and Elemento='Servicios' and IdResidente=$Parametros ORDER BY Anio, Mes";
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $saldo = 0;
        $saldoCantidad  = 0;
        for ($i = 0; $i < count($rows); $i++) {
            $saldo = $saldo + $rows[$i]["Debe"] - $rows[$i]["Haber"];
        }
        $datos['SaldoServicios'] = $saldo;
        break;
    case "CargarElementos_insumos":
        $query = "SELECT * FROM tblElementos WHERE Pendiente=1 and Elemento='Insumos' and IdResidente=$Parametros ORDER BY Anio, Mes";
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $saldo = 0;
        $saldoCantidad  = 0;
        for ($i = 0; $i < count($rows); $i++) {
            $saldo = $saldo + $rows[$i]["Debe"] - $rows[$i]["Haber"];
        }
        $datos['SaldoInsumos'] = $saldo;
        break;
    case "Cargar_detalle_panales":
        $query = "SELECT * FROM tblElementos WHERE Pendiente=1 and Elemento='Panales' and IdResidente=$Parametros ORDER BY Anio, Mes, Fecha";
        $result = mysqli_query($link, $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;

            $datos[]['Anio']           = $row['Anio'];
            $datos[]['Mes']            = $row['Mes'];
            $datos[]['Elemento']       = $row['Elemento'];
            $datos[]['Fecha']          = $row['Fecha'];
            $datos[]['Cantidad']       = $row['Cantidad'];
            $datos[]['PrecioUnitario'] = $row['PrecioUnitario'];
        }
        $saldoParcial = 0;


        
        for ($i = 0; $i < count($rows); $i++) {
            $saldoParcial = $saldo + $rows[$i]["Debe"] - $rows[$i]["Haber"];
        }
        $datos['SaldoInsumos'] = $saldo;
        break;
}

$datos = json_encode($datos);
//print_r($datos);
//echo $sql;
echo $datos;
