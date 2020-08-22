<?php
session_start();

$Id = $_GET["Id"];

$Fecha = $_GET["Fecha"];
$Fecha = date("Y-m-d", substr($Fecha, 0, 10));
$Anio = substr($Fecha, 0, 4);
$Mes = substr($Fecha, 5, 2);

$Elemento = $_GET["Elemento"];

$Debe = $_GET["Debe"];
$Haber = $_GET["Haber"];
$Detalle = $_GET["Detalle"];
$Cantidad = $_GET["Cantidad"];
$Precio = $_GET["Precio"];

$datos = array();

$mysqli = new mysqli("localhost", "root", "", "cuentascorrientes");

/* Comprueba la conexión */
if (mysqli_connect_errno()) {
  printf("Connect failed: %s\n", mysqli_connect_error());
  exit();
}

$sql = "INSERT INTO tblElementosDescartables (IdResidente, Anio, Mes, Elemento, Fecha, Debe, Haber, Detalle, Cantidad, PrecioUnitario, Pendiente) VALUES ($Id,$Anio,$Mes,'$Elemento','$Fecha',$Debe,$Haber,'$Detalle',$Cantidad,$Precio,1)";

$mysqli->query($sql);
if (mysqli_affected_rows($mysqli)) {
  $datos['Mensaje'] = "Se guardó el elemento";
} else {
  $datos['Mensaje'] = "No se grabaron los datos";
}

$datos = json_encode($datos);
//print_r($datos);
//echo $sql;
echo $datos;
