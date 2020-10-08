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
        $result = mysqli_query($_SESSION['link'], $query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $datos = $rows;
        break;
    case "CargarFoto":
        $query = "SELECT * FROM tblPacientes WHERE IdPaciente=" . $Parametros;
        $result = mysqli_query($_SESSION['link'], $query);
        while ($row = $result->fetch_assoc()) {
            $datos['NombrePaciente'] = $row['NombrePaciente'];
            //$datos = json_encode($datos);
            //$datos['imgperfil'] = $row['NombrePaciente'];
        }
        break;
    case "CargarDatosResidente":
        $query = "SELECT * FROM tblPacientes WHERE NroDocumento=" . $Parametros . " and (PacienteActivo=1 or PacienteActivo=2)";
        $result = mysqli_query($_SESSION['link'], $query);
        $datos = $result->fetch_assoc();
        break;
    case "CargarElementos_panales":
        $query = "SELECT * FROM tblElementosDescartables WHERE Pendiente=1 and Elemento='Panales' and IdResidente=$Parametros ORDER BY Anio, Mes";
        $result = mysqli_query($_SESSION['link'], $query);
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
        $datos['preferenciaId'] = 1000; //Revisar
        break;
    case "CargarElementos_descartables":
        $query = "SELECT * FROM tblElementosDescartables WHERE Pendiente=1 and Elemento='Descartables' and IdResidente=$Parametros ORDER BY Anio, Mes";
        $result = mysqli_query($_SESSION['link'], $query);
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
        $query = "SELECT * FROM tblElementosDescartables WHERE Pendiente=1 and Elemento='Servicios' and IdResidente=$Parametros ORDER BY Anio, Mes";
        $result = mysqli_query($_SESSION['link'], $query);
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
        $query = "SELECT * FROM tblElementosDescartables WHERE Pendiente=1 and Elemento='Insumos' and IdResidente=$Parametros ORDER BY Anio, Mes";
        $result = mysqli_query($_SESSION['link'], $query);
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
        $query = "SELECT * FROM tblElementosDescartables WHERE Pendiente=1 and Elemento='Panales' and IdResidente=$Parametros ORDER BY Anio, Mes, Fecha";
        $result = mysqli_query($_SESSION['link'], $query);
        $cont = -1;
        $saldoParcial = 0;
        while ($row = $result->fetch_assoc()) {
            $cont++;
            $rows[$cont] = $row;

            $datos[$cont]['Anio']           = $row['Anio'];
            $datos[$cont]['Mes']            = $row['Mes'];
            $datos[$cont]['Elemento']       = $row['Elemento'];
            $datos[$cont]['Fecha']          = substr($row['Fecha'], 8, 2) . "-" . substr($row['Fecha'], 5, 2) . "-" . substr($row['Fecha'], 0, 4);
            $datos[$cont]['PrecioUnitario'] = $row['PrecioUnitario'];
            $datos[$cont]['Debe'] = $row['Debe'];
            $datos[$cont]['Haber'] = $row['Haber'];
            $CantidadParcial = $CantidadParcial + $row['Cantidad'];     // Cuenta la cantidad de pañales totales
            $datos[$cont]['Cantidad']       = $row['Cantidad'];
            $saldoParcial = $saldoParcial + $rows[$cont]["Debe"] - $rows[$cont]["Haber"];   // Cuenta el saldo parcial de cada item
            $datos[$cont]['Saldo'] = $saldoParcial;
        }
        break;
}

$datos = json_encode($datos);
//print_r($datos);
//echo $sql;
echo $datos;
