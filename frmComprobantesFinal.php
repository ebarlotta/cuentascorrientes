<?
session_start();
try {
    require_once("../ajax/xajaxcore/xajax.inc.php");
    include_once("stringconexion.inc");
    include_once("librerias.php");
} catch (Exception $e) {
	echo '<script language="javascript">window.location="../sistema/index.php"</script>';
}

$EmpresaGlobal;

function CargarComboDetalle($Combo,$tabla,$campo,$sql,$EnDiv,$Primero,$Eventos,$LongCombo,$Imprime,$multiple) {
//Combo es el nombre del control donde se van a asignar los datos recuperados
//Tabla es la tabla de la base de datos que vamos a recorrer para encontrar los datos
//Campo es el campo dentro de la tabla en el que queremos buscar datos. Experimental: Se pueden colocar varios campos separados por comas
//SQL es la instrucción Sql si se quiere filtrar los datos, se puede o no pasar como parámetros, es decir es opcional
//EnDiv es el nombre del control DIV donde vamos a asignar todo el Html del combo que queremos mostrar. Es el Id del div
//Primero es el valor que queremos que aparezca como valor por defecto, puede ser blanco.
//Eventos: lista de los eventos y parámetros de los mismos para ser cargados en el html del control antes de ser enviados
	if (substr(strtoupper($campo),0,8)=="DISTINCT") { $dist=" DISTINCT "; $campo=substr(strtoupper($campo),9); }
	if ($sql<>"") {	$sSql="SELECT ".$dist.$campo." FROM ".$tabla." WHERE ".$sql; } else { $sSql="SELECT ".$dist.$campo." FROM ".$tabla; }
	$AUX='<SELECT id="' . $Combo . '" '.$multiple.'name="' . $Combo . '" ' . $Eventos . ' style="max-width : '.$LongCombo.'px;">';
	if ($Primero<>"") {$AUX=$AUX.'<OPTION value="'.$Primero.'">'.$Primero.'</option>';}
	if ($sql<>"Meses") {
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
 	while($row=$stmt->fetch()) { $AUX=$AUX.'<OPTION value="'.$row["$campo"] .'">'.utf8_decode($row["$campo"]).'</option>'; }
	} else { $AUX=$AUX.'<OPTION> </option><OPTION value="enero">enero</option><OPTION value="febrero">febrero</option><OPTION value="marzo">marzo</option><OPTION value="abril">abril</option><OPTION value="mayo">mayo</option><OPTION value="junio">junio</option><OPTION value="julio">julio</option><OPTION value="agosto">agosto</option><OPTION value="setiembre">setiembre</option><OPTION value="octubre">octubre</option><OPTION value="noviembre">noviembre</option><OPTION value="diciembre">diciembre</option>'; }
	$AUX=$AUX.'</SELECT>';
	if ($Imprime) {	print utf8_decode($AUX); }
	else { return utf8_decode($AUX); }
}

function CargarDatosComprobante($Campos) {
	$objResponse = new xajaxResponse();
	$pieces = explode("  ", $Campos);
	$sSql="SELECT * FROM ViewComprobantes WHERE IdComp=".$Campos; // and NroComp='".$pieces[1]."' and CuitComp='".$pieces[2]."'";
 	$stmt =  $GLOBALS['pdo']->prepare($sSql);$stmt->execute();
 	while($row=$stmt->fetch()) {
		$objResponse->assign("Fecha","value",$row[FechaComp]);
		$objResponse->assign("txtCuitProveedores","value",$row[CuitComp]);
		$objResponse->assign("Comprobante","value",$row[NroComp]);
		$objResponse->assign("Detalle","value",$row[DetalleComp]);
		$objResponse->assign("Anio","value",$row[Anio]);
		$objResponse->assign("ParticIva","value",$row[ParticipaEnIva]);
		$objResponse->assign("PasadoEnMes","value",$row[PasadoEnMes]);
		$objResponse->assign("Areas","value",$row[DescripcionAreas]);
		$objResponse->assign("Cuentas","value",$row[DescripcionCuentas]);
		$objResponse->assign("Bruto","value",$row[BrutoComp]);
		$objResponse->assign("Iva","value",$row[IvaComp]);
		$objResponse->assign("Exento","value",$row[ExentoComp]);
		$objResponse->assign("ImpInterno","value",$row[ImpInternoComp]);
		$objResponse->assign("PercIva","value",$row[PercepcionIvaComp]);
		$objResponse->assign("RetencionIB","value",$row[RetencionIB]);
		$objResponse->assign("RetencionGan","value",$row[RetencionGan]);
		$objResponse->assign("Neto","value",$row[NetoComp]);
		$objResponse->assign("MontoPagado","value",$row[MontoPagadoComp]);
		$objResponse->assign("PagosParcial","value","falta");
		$objResponse->assign("cmbProveedores","value",$row[NombreProveedor]);
		$objResponse->assign("CantLitros","value",$row[CantidadLitroComp]); 

		$objResponse->assign("VartxtFecha","value",$row[FechaComp]);
		$objResponse->assign("VarComprobante","value",$row[NroComp]);
		$objResponse->assign("VartxtCuitProveedores","value",$row[CuitComp]);
		
		$objResponse->assign("IdComp","value",$row[IdComp]);
	} 
	return $objResponse;
}

function CargarCuitProveerdor($Proveedor) {
	$objResponse = new xajaxResponse();
	$sSql="SELECT Cuit FROM tblProveedores WHERE NombreProveedor='$Proveedor' and Empresa='".$_SESSION['CuitEmpresa']."'";
	$GLOBALS['EmpresaGlobal']<-$Empresa;
	$stmt =  $GLOBALS['pdo']->prepare($sSql);
  	$stmt->execute();
 	while($row=$stmt->fetch()) { $objResponse->assign("txtCuitProveedores", "value", $row[Cuit]); }
	return $objResponse;
}
function LlamarFiltro($Proveedores,$Mes,$ParticipaEnIva,$Areas,$Cuentas,$Anio,$Detalle2,$Orden,$ConSaldo,$Pagina) {
	$objResponse = new xajaxResponse();
		if ($Proveedores<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." NombreProveedor='".($Proveedores)."' ";
		}
		if ($Mes<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." PasadoEnMes='$Mes' ";
		}
		if ($ParticipaEnIva<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." ParticipaEnIva='$ParticipaEnIva' ";
		}
		if ($Areas<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." DescripcionAreas='$Areas' ";
		}
		if ($Cuentas<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." DescripcionCuentas='$Cuentas' ";
		}
		if ($Anio<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." Anio='$Anio' ";
		}
		if ($a<>"") { $a=$a." and "; $a=$a." Empresa='".$_SESSION['CuitEmpresa']."'";}
		//Controla que no hayan mas de 100 registros
		 /*$Botonera="SELECT count(NroComp) as CantidadDeRegistros FROM ViewComprobantes WHERE ". $a . " LIMIT 0, 110";
		 $stmt2 =  $GLOBALS['pdo']->prepare($Botonera); $stmt2->execute();
		 $row=$stmt2->fetch();
		 if($row[CantidadDeRegistros]>100) { $objResponse->assign("Anterior", "button", "text"); 
		 									 $objResponse->assign("Posterior", "button", "text");
		 									 $objResponse->assign("Pagina", "button", "text");
		 									 $objResponse->assign("Pagina", "value", 0);
		 } else { 							 $objResponse->assign("Anterior", "type", "hidden"); 
		 									 $objResponse->assign("Posterior", "type", "hidden");
		 									 $objResponse->assign("Pagina", "type", "hidden"); }*/
		// Esto llena solo el combo del detalle
		
		  $x="SELECT DISTINCT DetalleComp FROM ViewComprobantes WHERE ". $a . " ORDER BY DetalleComp ";
		if ($Detalle2<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." DetalleComp='".$Detalle2."' ";
		}
	//if ($Orden) { $ASC="ASC LIMIT ". $Pagina . ",50"; } else { $ASC="DESC LIMIT ". $Pagina . ",50"; }
	if ($Orden) { $ASC="ASC"; } else { $ASC="DESC"; }
        $a="SELECT * FROM ViewComprobantes WHERE ".$a." and EmpresaProveedor='".$_SESSION['CuitEmpresa']."' ORDER BY Anio,FechaComp,NroComp,CuitComp,DetalleComp ".$ASC;
        //$objResponse->alert($a);
 	$stmt =  $GLOBALS['pdo']->prepare($a); $stmt->execute();
	//$c='<table  border=1 style="font-size : 10px;"><tr bgcolor="#b0e3ff"><td align="center">Fecha</td><td align="center">Comprobante</td><td align="center">Proveedor</td><td align="center">Detalle</td><td align="center">Bruto</td><td align="center">Iva</td><td align="center">Exento</td><td align="center">Imp<br>Interno</td><td align="center">Percec<br>Iva</td><td align="center">Retenc<br>IB</td><td align="center">RetGan</td><td align="center">Neto</td><td align="center">Pagado</td><div class="content" style="none repeat scroll 0 0;overflow:auto;color:#ffffff;width:98%;height:60%;padding:4px;">';
	//$c='<div class="content" style="none repeat scroll 0 0;overflow:auto;color:#ffffff;width:98%;height:60%;	:4px;">			<table  border=1 style="font-size : 10px;"><tr bgcolor="#b0e3ff"><td align="center">Fecha</td><td align="center">Comprobante</td><td align="center">Proveedor</td><td align="center">Detalle</td><td align="center">Bruto</td><td align="center">Iva</td><td align="center">Exento</td><td align="center">Imp<br>Interno</td><td align="center">Percec<br>Iva</td><td align="center">Retenc<br>IB</td><td align="center">RetGan</td><td align="center">Neto</td><td align="center">Pagado</td>';
 	$c='<div class="content-responsive" style="none repeat scroll 0 0;overflow:auto;color:#ffffff;width:100%;height:50%;	:4px;">
			<table class="table table-responsive table-hover" border=1 style="font-size : 9px;"><tr bgcolor="#b0e3ff"><td align="center">Fecha</td><td align="center">Comprobante</td><td align="center">Proveedor</td><td align="center">Detalle</td><td align="center">Bruto</td><td align="center">Iva</td><td align="center">Exento</td><td align="center">Imp<br>Interno</td><td align="center">Percec<br>Iva</td><td align="center">Retenc<br>IB</td><td align="center">RetGan</td><td align="center">Neto</td><td align="center">Pagado</td>';
 	
	if ($ConSaldo) {	$c=$c.'<td align="center" width=60>Saldo</td>';}
	$c=$c.'<td align="center">Cant<br>Litros</td><td align="center">Partic<br>Iva</td><td align="center">Pasado<br>EnMes</td><td align="center">Area</td><td align="center">Cuenta</td></tr>';	//<td align="center">CUIT</td>
	$SaldoAcum=0;
	while($row=$stmt->fetch()) {
	$nombre=$row[IdComp];
	$evento=' onClick="xajax.call(\'CargarDatosComprobante\', {method: \'get\', parameters:['.$nombre.']}); return false;"';
	IF (($NroRegistro % 2)==1) { $colorFondo="lightGray"; } else {  $colorFondo=""; }
	$NroRegistro++;
	if ($Orden) {
	$b=$b.'<tr '.$evento.'bgcolor=\''.$colorFondo.'\' onmouseover="this.style.backgroundColor=\'#ffff66\';" onmouseout="this.style.backgroundColor=\''.$colorFondo.'\';">';
	$b=$b.'	<td width=60>' . $row[FechaComp] . '</td> <td align="right" width=70>' . $row[NroComp] . '</td><td >'.$row[NombreProveedor].'</td><td>'.$row[DetalleComp].'</td><td 
		align="right" width=25>'.$row[BrutoComp]. '</td><td  align="right" width=25>'.number_format(($row[BrutoComp]*$row[IvaComp]/100),2).'</td><td  align="right" width=25>'.$row[ExentoComp].'</td><td align="right" width=15>'.$row[ImpInternoComp].'</td><td align="right" width=25>'.$row[PercepcionIvaComp].'</td><td align="right" width=25>'.$row[RetencionIB].'</td><td align="right" width=25>'.$row[RetencionGan].'</td><td align="right" width=25>'.$row[NetoComp].'</td><td align="right" style="color:#ff0000;" width=25>-'.$row[MontoPagadoComp].'</td>';
		$SaldoAcum=$SaldoAcum-$row[MontoPagadoComp]+$row[NetoComp];
	if ($ConSaldo) { 
							if ($SaldoAcum>0) {$b=$b.'<td align="right" width=60>'.number_format($SaldoAcum,2).'</td>';}
							else { $b=$b.'<td align="right" width=60 style="color:#ff0000;">'.number_format($SaldoAcum,2).'</td>';}
	}
	$b=$b.'<td align="right" width=15>'.$row[CantidadLitroComp].'</td><td align="center" width=15>' . $row[ParticipaEnIva] . '</td><td width=25>' . $row[PasadoEnMes] . '</td><td width=65>' . $row[DescripcionAreas] . '</td><td width=65>' . $row[DescripcionCuentas] . '</td></tr>';
	} 
 	 else { 
$cont=$cont+1;
 $b='<tr '.$evento.'bgcolor=\''.$colorFondo.'\' onmouseover="this.style.backgroundColor=\'#ffff66\';" onmouseout="this.style.backgroundColor=\''.$colorFondo.'\';">'.'<td width=60>' . $row[FechaComp] . '</td> <td align="right" width=70>' . $row[NroComp] .'</td><td width=140>'.$row[NombreProveedor].'</td><td width=160>'.$row[DetalleComp].'</td><td 		align="right" width=25>'.$row[BrutoComp]. '</td><td  align="right" width=25>'.number_format(($row[BrutoComp]*$row[IvaComp]/100),2).'</td><td  align="right" width=25>'.$row[ExentoComp].'</td><td align="right" width=15>'.$row[ImpInternoComp].'</td><td align="right" width=25>'.$row[PercepcionIvaComp].'</td><td align="right" width=25>'.$row[RetencionIB].'</td><td align="right" width=25>'.$row[RetencionGan].'</td><td align="right" width=25>'.$row[NetoComp].'</td><td align="right" style="color:#ff0000;" width=25>-'.$row[MontoPagadoComp].'</td><td align="right" width=15>'.$row[CantidadLitroComp].'</td><td align="center" width=15>' . $row[ParticipaEnIva] . '</td><td width=25>' . $row[PasadoEnMes] . '</td><td width=65>' . $row[DescripcionAreas] . '</td><td width=65>' . $row[DescripcionCuentas] . '</td></tr>'.$b; }
		$AcumBruto=$AcumBruto+$row[BrutoComp];
		$AcumExento=$AcumExento+$row[ExentoComp];
		$AcumImpInt=$AcumImpInt+$row[ImpInternoComp];
		$AcumPerIva=$AcumPerIva+$row[PercepcionIvaComp];
		$AcumRetIB=$AcumRetIB+$row[RetencionIB];
		$AcumRetGan=$AcumRetGan+$row[RetencionGan];
		$AcumNeto=$AcumNeto+$row[NetoComp];
		$AcumLitros=$AcumLitros+$row[CantidadLitroComp];
		$AcumPagado=$AcumPagado+$row[MontoPagadoComp];
		$AcumIva=$AcumIva+($row[BrutoComp]*$row[IvaComp]/100);
	}
	$Saldo=$AcumNeto-$AcumPagado;
	//$b=$b.'</div>';
	$totales='<tr bgcolor=\'#A4FF9C\'><td></td><td></td><td></td><td>Totales</td><td align="right">'.number_format($AcumBruto,2).'</td><td align="right">'. number_format($AcumIva,2).'</td><td align="right">'.number_format($AcumExento,2).'</td><td align="right">'.number_format($AcumImpInt,2).'</td><td align="right">'.number_format($AcumPerIva,2).'</td><td align="right">'.number_format($AcumRetIB,2).'</td><td align="right">'.number_format($AcumRetGan,2).'</td><td align="right"></b>'.number_format($AcumNeto,2).'</b></td><td align="right" style="color:#ff0000;"><b>-'.number_format($AcumPagado,2).'</b></td><td align="right"><b>'.number_format($Saldo,2).'</b></td><td align="right">'.number_format($AcumLitros,2).'</td><td align="right">Saldo</td><td></td><td></td><td></td></tr>';
if ($Orden) { $b=$c.$b.$totales; } else {$b=$c.$totales.$b;}
	$b=$b.'</table></div>';
	$objResponse->assign("Filtro", "innerHTML", "");
	$objResponse->assign("Filtro", "innerHTML", $b);

// CArga la parte del filtro del Detalle de los comprobantes
		$stmt =  $GLOBALS['pdo']->prepare($x); $stmt->execute();
$Filtro='onchange="xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});return false;"';
//xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,cmbEmpresas.value,FDetalle2.value,FOrden.checked,Pagina.value]});return false;"';
//$Filtro='onchange="xajax.call('\LlamarFiltro\', {method: '\get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,cmbEmpresas.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});"';

    $AUX='<SELECT id="FDetalle2" name="FDetalle2"' . $Filtro .'>';
	$AUX=$AUX.'<OPTION value=" "> </option>';
	while($row=$stmt->fetch()) { $AUX=$AUX.'<OPTION value="'.utf8_decode($row[DetalleComp]) .'">'.utf8_decode($row[DetalleComp]).'</option>'; }
 	$AUX=$AUX.'</SELECT>';
	$objResponse->assign("Detallen", "innerHTML", $AUX);
	$objResponse->assign("cargando","innerHTML","");
	return $objResponse;
}
 	
function LlamarFiltro2($FProveedores2,$FMes2,$FParticipaIva2,$FAreas2,$FCuentas2,$F2Anio,$FFDesde,$FFHasta,$Orden,$Detalle) {
	$objResponse = new xajaxResponse();
		if ($FProveedores2<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." NombreProveedor='$FProveedores2' ";
		}
		if ($FMes2<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." PasadoEnMes='$FMes2' ";
		}
		if ($FParticipaIva2<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." ParticipaEnIva='$FParticipaIva2' ";
		}
		if ($FAreas2<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." DescripcionAreas='$FAreas2' ";
		}
		if ($FCuentas2<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." DescripcionCuentas='$FCuentas2' ";
		}
		if ($FFDesde<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." FechaComp>='$FFDesde' ";
		}
		if ($FFHasta<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." FechaComp<='$FFHasta' ";
		}
		if ($F2Anio<>"Todos") {
		  if ($a<>"") { $a=$a." and "; }
		  $a=$a." Anio='$F2Anio' ";
		} 
		if ($cmbDeudaEmpresa<>" ") {
		  if ($a<>"") { $a=$a." and "; }
		  //$a=$a." Empresa='$cmbDeudaEmpresa' ";
		  $a=$a." Empresa='".$_SESSION['CuitEmpresa']."'";
		}
		
		$x="SELECT DISTINCT DetalleComp FROM ViewComprobantes WHERE ". $a . " ORDER BY DetalleComp ";
		//$a=$a." Detalle444 ".$Detalle;
		if ($Detalle<>" ") {
		    if ($a<>"") { $a=$a." and "; }
		    $a=$a." DetalleComp='".$Detalle."' ";
		}  
	if ($Orden) { $ASC="ASC"; } else { $ASC="DESC"; }
	$a="SELECT * FROM ViewComprobantes WHERE ".$a." and EmpresaProveedor='".$_SESSION['CuitEmpresa']."' ORDER BY FechaComp,NroComp,CuitComp,DetalleComp ".$ASC;
	$stmt =  $GLOBALS['pdo']->prepare($a); $stmt->execute();
	$c='<table  border=1><tr bgcolor="#b0e3ff"><td align="center">Opc.</td><td align="center">Fecha</td><td align="center">Comprobante</td><td align="center">Proveedor</td><td align="center">Detalle</td><td align="center">Bruto</td><td align="center">Iva</td><td align="center">Exento</td><td align="center">Imp<br>Interno</td><td align="center">Percec<br>Iva</td><td align="center">Retenc<br>IB</td><td align="center">RetGan</td><td align="center">Neto</td><td align="center">Pagado</td><td align="center">Saldo</td><td align="center">Cant<br>Litros</td><td align="center">Partic<br>Iva</td><td align="center">Pasado<br>EnMes</td><td align="center">Area</td><td align="center">Cuenta</td></tr>';
	//$c=$c.$a; //control 
	$Saldo=0;
	while($row=$stmt->fetch()) {
		$nombre=$row[IdComp];
	$evento=' onClick="xajax.call(\'CargarDatosComprobante\', {method: \'get\', parameters:['.$nombre.']}); return false;"';
	IF (($NroRegistro % 2)==1) { $colorFondo="lightGray"; }  else {  $colorFondo=""; } //$colorFondo=' bgcolor="lightGray"'; } else {  $colorFondo=""; }
	$NroRegistro++;	
	if ($Orden) {
	$b=$b.'<tr '.$evento.'bgcolor=\''.$colorFondo.'\' onmouseover="this.style.backgroundColor=\'#ffff66\';" onmouseout="this.style.backgroundColor=\''.$colorFondo.'\';">
	<td align="center"><input type="checkbox" name="pepe"'.$evento.'> </td>';
	$Saldo=$Saldo-$row[MontoPagadoComp]+$row[NetoComp];
	$b=$b.'	<td width=60>' . $row[FechaComp] . '</td> <td align="right" width=70>' . $row[NroComp] . '</td><td>'.$row[NombreProveedor].'</td><td>'.$row[DetalleComp].'</td>
	<td align="right" width=25>'.$row[BrutoComp]. '</td><td  align="right" width=25>'.number_format(($row[BrutoComp]*$row[IvaComp]/100),2).'</td><td  align="right" width=25>'.$row[ExentoComp].'</td><td align="right" width=15>'.$row[ImpInternoComp].'</td><td align="right" width=25>'.$row[PercepcionIvaComp].'</td><td align="right" width=25>'.$row[RetencionIB].'</td><td align="right" width=25>'.$row[RetencionGan].'</td><td align="right" width=25>'.$row[NetoComp].'</td><td align="right" style="color:#ff0000;" width=25>-'.$row[MontoPagadoComp].'</td><td align="right" width=60>' . number_format($Saldo,2) . '</td><td align="right" width=15>'.$row[CantidadLitroComp].'</td><td align="center" width=15>' . $row[ParticipaEnIva] . '</td><td width=25>' . $row[PasadoEnMes] . '</td><td width=65>' . $row[DescripcionAreas] . '</td><td width=65>' . $row[DescripcionCuentas] . '</td></tr>';
	}
else { 
	$b=$b.'<tr'.$colorFondo.'>
		<td align="center"><input type="checkbox" name="pepe"'.$evento.'> </td>
		<td>' . $row[FechaComp] . '</td> <td align="right">' . $row[NroComp] . '</td>	<td>'.$row[NombreProveedor].'</td><td>'.$row[DetalleComp].'</td><td 
		align="right">'.$row[BrutoComp]. '</td><td 
 align="right">'.number_format(($row[BrutoComp]*$row[IvaComp]/100),2).'</td><td  align="right">'.$row[ExentoComp].'</td><td align="right">'.$row[ImpInternoComp].'</td><td align="right">'.$row[PercepcionIvaComp].'</td></td><td align="right">'.$row[RetencionIB].'</td></td><td align="right">'.$row[RetencionGan].'</td><td align="right">'.$row[NetoComp].'</td><td align="right" style="color:#ff0000;">-'.$row[MontoPagadoComp].'</td><td align="right">'.$row[CantidadLitroComp].'</td><td align="center">'.$row[ParticipaEnIva].'</td><td>'.$row[PasadoEnMes].'</td><td>'.$row[DescripcionAreas].'</td><td>'.$row[DescripcionCuentas].'</td></tr>';}
		$AcumBruto=$AcumBruto+$row[BrutoComp];
		$AcumExento=$AcumExento+$row[ExentoComp];
		$AcumImpInt=$AcumImpInt+$row[ImpInternoComp];
		$AcumPerIva=$AcumPerIva+$row[PercepcionIvaComp];
		$ACumRetIB=$ACumRetIB + $row[RetencionIB];
		$AcumRetGan=$AcumRetGan+$row[RetencionGan];
		$AcumNeto=$AcumNeto+$row[NetoComp];
		$AcumLitros=$AcumLitros+$row[CantidadLitroComp];
		$AcumPagado=$AcumPagado+$row[MontoPagadoComp];
		$AcumIva=$AcumIva+($row[BrutoComp]*$row[IvaComp]/100);
	
}
	$Saldo=$AcumNeto-$AcumPagado;
	$totales='<tr bgcolor=\'#A4FF9C\'><td></td><td></td><td></td><td></td><td>Totales</td><td align="right">'. number_format($AcumBruto,2).'</td><td align="right">'. number_format($AcumIva,2).'</td><td align="right">'.number_format($AcumExento,2).'</td><td align="right">'.number_format($AcumImpInt,2).'</td><td align="right">'.number_format($AcumPerIva,2).'</td><td align="right">'.number_format($ACumRetIB,2).'</td><td align="right">'.number_format($AcumRetGan,2).'</td><td align="right">'.number_format($AcumNeto,2).'</td><td align="right" style="color:#ff0000;">-'.number_format($AcumPagado,2).'</td><td align="right">'.number_format($Saldo,2).'</td><td align="right">'.number_format($AcumLitros,2).'</td><td align="right">Saldo</td><td></td><td></td><td></td></tr>';

	if ($Orden) { $b=$c.$b.$totales; } else {$b=$c.$totales.$b;}
	$b=$b.'</table>';
	$objResponse->assign("Filtro2", "innerHTML", "");
	$objResponse->assign("Filtro2", "innerHTML", $b);
	
	$stmt =  $GLOBALS['pdo']->prepare($x); $stmt->execute();
	$AUX='<SELECT id="FDetalleSCBancarias" name="FDetalleSCBancarias"' . $Filtro .'>';
	$AUX=$AUX.'<OPTION value=" "> </option>';
	while($row=$stmt->fetch()) { $AUX=$AUX.'<OPTION value="'.utf8_decode($row[DetalleComp]) .'">'.utf8_decode($row[DetalleComp]).'</option>'; }
	$AUX=$AUX.'</SELECT>';
	$objResponse->assign("DetalleCB", "innerHTML", $AUX);
	
return $objResponse;
}

function AgregarComprobante ( $Anio1,$PasadoEnMes,$ParticipaEnIva,$Areas1,$Cuentas1,$IvaComp,$CuitProveedores,$Comprobante,$Fecha,$Detalle1,$Bruto,$Exento,$ImpInterno,$PercIva,$RetencionIB,$RetencionGan,$Neto,$MontoPagado,$CantLitros) {
	$objResponse = new xajaxResponse();

	//Controla si el comprobante se encuentra en un libro cerrado, si es así no lo deja modificar
	$sSql="SELECT * FROM tblComprobantes WHERE Anio='$Anio1' and Empresa='".$_SESSION['CuitEmpresa']."' and PasadoEnMes='$PasadoEnMes' and ParticipaEnIva='Si'";
	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
	$row=$stmt->fetch();
	if ($row['Cerrado']>1 and $ParticipaEnIva=='Si') { 
		$objResponse->alert("El comprobante no se ha dado de alta porque se encuentra dentro de un libro cerrado!!!");
		return $objResponse;
	} 
	//Controla si el comprobante tiene asignada area y cuenta
	if ($_SESSION['CuitEmpresa']==" " or $CuitProveedores==" " or $PasadoEnMes==" " or $Areas1==" " or $Cuentas1==" ") { 
		$objResponse->alert("El comprobante esta incompleto. NO se da ALTA!!!");
		$objResponse->alert($Areas1.$Cuentas1);
		return $objResponse;
	} 
	//AGREGA UN COMPROBANTE NUEVO
	$sql="SELECT IdArea FROM tblAreas WHERE DescripcionAreas='".$Areas1."' and Empresa='".$_SESSION['CuitEmpresa']."'";
	$stmt =  $GLOBALS['pdo']->prepare($sql); $stmt->execute();
 	$row=$stmt->fetch();
	$Area=$row[IdArea];
	$sql="SELECT IdCuenta FROM tblCuentas WHERE DescripcionCuentas='".$Cuentas1."' and Empresa='".$_SESSION['CuitEmpresa']."'";
	$stmt =  $GLOBALS['pdo']->prepare($sql); $stmt->execute();
	$row=$stmt->fetch();
	$Cuenta=$row[IdCuenta];
	$lafecha=$Fecha;
	$usuario=$_SESSION['usuario'];
	$fecha=date("Y/m/d H:i:s");
	if ($IvaComp<>"-") {
	//$sql="INSERT INTO tblComprobantes (FechaComp,CuitComp,NroComp,DetalleComp,ParticipaEnIva,PasadoEnMes,IdArea,IdCuenta,BrutoComp,IvaComp,MontoIva,ExentoComp, ImpInternoComp,PercepcionIvaComp,RetencionIB,RetencionGan,NetoComp,MontoPagadoComp,IdPagosParciales,CantidadLitroComp,usuario,fechamodif,Anio,Empresa) VALUES ('$lafecha','$CuitProveedores', '$Comprobante','$Detalle1','$ParticipaEnIva','$PasadoEnMes','$Area','$Cuenta','$Bruto','$IvaComp','$Bruto*$IvaComp/100','$Exento','$ImpInterno','$PercIva','$RetencionIB','$RetencionGan','$Neto','$MontoPagado','$PagosParcial','$CantLitros','$usuario','$fecha','$Anio1','$Empresa')";
	//MODIFICADO EL 28-08-17
	$CalculoIva=$Bruto*$IvaComp/100;
	$sql="INSERT INTO tblComprobantes (FechaComp,CuitComp,NroComp,DetalleComp,ParticipaEnIva,PasadoEnMes,IdArea,IdCuenta,BrutoComp,IvaComp,MontoIva,ExentoComp, ImpInternoComp,PercepcionIvaComp,RetencionIB,RetencionGan,NetoComp,MontoPagadoComp,IdPagosParciales,CantidadLitroComp,usuario,fechamodif,Anio,Empresa) VALUES ('$lafecha','$CuitProveedores', '$Comprobante','$Detalle1','$ParticipaEnIva','$PasadoEnMes','$Area','$Cuenta','$Bruto','$IvaComp',$CalculoIva,'$Exento','$ImpInterno','$PercIva','$RetencionIB','$RetencionGan','$Neto','$MontoPagado','$PagosParcial','$CantLitros','$usuario','$fecha','$Anio1','$_SESSION[CuitEmpresa]')";
	} else {
	$sql="INSERT INTO tblComprobantes (FechaComp,CuitComp,NroComp,DetalleComp,ParticipaEnIva,PasadoEnMes,IdArea,IdCuenta,BrutoComp,IvaComp,MontoIva,ExentoComp, ImpInternoComp,PercepcionIvaComp,RetencionIB,RetencionGan,NetoComp,MontoPagadoComp,IdPagosParciales,CantidadLitroComp,usuario,fechamodif,Anio,Empresa) VALUES ('$lafecha','$CuitProveedores', '$Comprobante','$Detalle1','$ParticipaEnIva','$PasadoEnMes','$Area','$Cuenta','$Bruto','-','".AcumuladoIVA($lafecha,$CuitProveedores,$Comprobante)."','$Exento','$ImpInterno','$PercIva','$RetencionIB','$RetencionGan','$Neto','$MontoPagado','$PagosParcial','$CantLitros','$usuario','$fecha','$Anio1','$_SESSION[CuitEmpresa]')";
	}
 	$stmt =  $GLOBALS['pdo']->prepare($sql); $stmt->execute();
	if (!$stmt->affected_rows) { 
		//$objResponse->alert("Se agregó correctamente"); 
	}
	else { $objResponse->alert("Se produjo un error"); }
	return $objResponse;
}

function ModificarComprobante($Anio1,$PasadoEnMes,$ParticipaEnIva,$Areas1,$Cuentas1,$IvaComp,$CuitProveedores,$Comprobante,$Fecha,$Detalle1,$Bruto,$Exento,$ImpInterno,$PercIva,$RetencionIB,$RetencionGan,$Neto,$MontoPagado,$CantLitros,$IdComp) {
	$objResponse = new xajaxResponse();
	//Controla si el comprobante se encuentra en un libro cerrado, si es así no lo deja modificar
	$sSql="SELECT * FROM tblComprobantes WHERE Anio=$Anio1 and Empresa='".$_SESSION['CuitEmpresa']."' and PasadoEnMes='$PasadoEnMes' and ParticipaEnIva='Si'";
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
	$row=$stmt->fetch();
	if ($row['Cerrado']>1 and $ParticipaEnIva=='Si') { 
		$objResponse->alert("El comprobante no se puede modificar porque se encuentra dentro de un libro cerrado!!!");
		return $objResponse;
	}
	if ($ParticipaEnIva=="Si") {
	    $os=["FA","FB","FC","XA","XB","XC","CA","CB","CC","DA","DB","DC"];
    	if (!in_array(substr($Comprobante,0,2), $os) or !is_numeric(substr($Comprobante,3,5)) or substr($Comprobante,8,1)!="-" or substr($Comprobante,2,1)!="-" or !is_numeric(substr($Comprobante,9)) or strlen(substr($Comprobante,9))<8) {
    	    $objResponse->alert("Formato incorrecto en el numero de comprobante!!! \n Formato sugerido: XX - NNNNN - NNNNNNNN \n 
            X: (F)actura / (R)ecibo + (A)(B)(C)     Ejemplo: 'FA'\n
            N: Numeros del 0 - 9 \n
            No lleva espacios y deben respetarse los guiones");
    	    return $objResponse;
    	}
	}

	//Controla si el comprobante tiene asignada area y cuenta
	if ($Areas1==" " or $Cuentas1==" ") { 
		$objResponse->alert("El comprobante no tiene asignado Area y Cuenta!!!");
		$objResponse->alert($Areas1.$Cuentas1);
		return $objResponse;
	} 
	// comienza a modificar el comprobante
	$sql="SELECT IdArea FROM tblAreas WHERE DescripcionAreas='".$Areas1."' and Empresa='".$_SESSION['CuitEmpresa']."'";
 	$stmt =  $GLOBALS['pdo']->prepare($sql); $stmt->execute();
	$row=$stmt->fetch();
	$Area=$row[IdArea];
	$sql="SELECT IdCuenta FROM tblCuentas WHERE DescripcionCuentas='".$Cuentas1."' and Empresa='".$_SESSION['CuitEmpresa']."'";
 	$stmt =  $GLOBALS['pdo']->prepare($sql); $stmt->execute();
	$row=$stmt->fetch();
	$Cuenta=$row[IdCuenta];
	//Controla si el comprobante se encuentra en un libro cerrado, si es así no lo deja modificar
	$sSql="SELECT * FROM tblComprobantes WHERE IdComp=$IdComp";
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
	$row=$stmt->fetch();
	if ($row['Cerrado']>0) { 
		$objResponse->alert("El comprobante no se ha modificado porque se encuentra dentro de un libro cerrado!!!");
		return $objResponse;
	}
	// Modifica los datos del comprobante
	$usuario=$_SESSION['usuario'];
	$fecha=date("Y/m/d H:i:s");
	$CalculoIva=$Bruto*$IvaComp/100;
	$sSql="UPDATE tblComprobantes SET FechaComp='".$Fecha."', CuitComp='$CuitProveedores', NroComp='$Comprobante', DetalleComp='$Detalle1', ParticipaEnIva='$ParticipaEnIva', PasadoEnMes='$PasadoEnMes', IdArea='$Area', IdCuenta='$Cuenta', BrutoComp='$Bruto', IvaComp='$IvaComp', MontoIva='".number_format($CalculoIva,2)."',ExentoComp='$Exento', ImpInternoComp='$ImpInterno', PercepcionIvaComp='$PercIva', RetencionIB='$RetencionIB', RetencionGan='$RetencionGan', NetoComp='$Neto', MontoPagadoComp='$MontoPagado', CantidadLitroComp='$CantLitros',usuario='$usuario',fechamodif='$fecha',Anio='$Anio1' ,Empresa='".$_SESSION['CuitEmpresa']."' WHERE IdComp=$IdComp";
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
	if (!$stmt->affected_rows) {
		//$objResponse->alert("Se modificó correctamente"); 
	}
	else { $objResponse->alert("Se produjo un error"); }
	return $objResponse;
}

//function EliminarComprobante($IdComp,$Borrar) {
function EliminarComprobante($IdComp) {
	$objResponse = new xajaxResponse();
	//Controla si el comprobante se encuentra en un libro cerrado, si es así no lo deja modificar
	$sSql="SELECT * FROM tblComprobantes WHERE IdComp=$IdComp";
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
	$row=$stmt->fetch();
	if ($row['Cerrado']>1) { 
		$objResponse->alert("El comprobante no se puede eliminar porque se encuentra dentro de un libro cerrado!!!");
		return $objResponse;
	}
	//Comienza a borrar
	//if ($Borrar=='1') { 
	  $sSql="DELETE FROM tblComprobantes WHERE IdComp=$IdComp"; 
	   $stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute(); 
	   if (!$stmt->affected_rows) {
	       //$objResponse->alert("Se Eliminó correctamente"); 
	       $objResponse->assign("Borrar","value","0");
	   }
	   //else 
	   //    { $objResponse->alert("Se produjo un error"); }
	  return $objResponse;
	//}
}

function AcumuladoIVA($fecha,$cuit,$Comp) {
	$sSql="SELECT * FROM tblDesgloceIva WHERE Fecha='$fecha' and Cuit='$cuit' and NroComp='$Comp'";
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute(); // 	$result = mysql_db_query($_SESSION['db'],$sql);
 	while($row=$stmt->fetch()) { // $row = mysql_fetch_array($result);
		$Acumulado=$Acumulado+$row[Iva]*$row[MontoBruto]/100;
	}
	return $Acumulado;
}

function SolicitarDeudaProveedores($CmbDeuda,$DeudaAnio,$Desde,$Hasta) {
	$objResponse = new xajaxResponse();
	if ($CmbDeuda=="Todos")
		{$mas="";}
	else 
	   { $mas=" DescripcionAreas='" . $CmbDeuda. "'"; }
	if ($DeudaAnio=="Todos") 
	   {   }
	else { if (strlen($mas>1)) { $mas=$mas." and Anio=".$DeudaAnio; }
	       else { $mas=$mas." Anio=".$DeudaAnio;}
	}
	if (strlen($mas)) { $mas=$mas." and Empresa='".$_SESSION['CuitEmpresa']."'"; }
	//else { $mas=$mas." Empresa='".$Empresa."'";}
	else { $mas=$mas." Empresa='".$_SESSION['CuitEmpresa']."'";}

	if (strlen($mas)) {$mas=" WHERE ".$mas;}
	$sSql="SELECT CuitComp as NombreProveedor,Cuit, Saldo as Deuda FROM (SELECT sum(NetoComp-MontoPagadoComp) as Saldo, CuitComp, Cuit FROM ViewComprobantes ".$mas." and FechaComp>='".$Desde."' and FechaComp<='".$Hasta."' GROUP BY CuitComp) as f WHERE Saldo>1";
	//$sSql="SELECT CuitComp as NombreProveedor,Cuit, Saldo as Deuda FROM (SELECT sum(NetoComp-MontoPagadoComp) as Saldo, CuitComp, Cuit FROM ViewComprobantes ".$mas." GROUP BY CuitComp) as f WHERE Saldo>1";
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
	$p=$sSql;
	$p="<table border=1>
		<tr bgcolor=\"#b0e3ff\"><td align=\"center\">Nombre</td><td align=\"center\">Cuit</td><td align=\"center\">Deuda</td></tr>";
 	while($row=$stmt->fetch()) {
		$p=$p."<tr><td>$row[NombreProveedor]</td><td>$row[Cuit]</td><td align='right'>$row[Deuda]</td></tr>";
		$Acumulado=$Acumulado+$row[Deuda];
	}
	$p=$p."<tr bgcolor=\"#A4FF9C\"><td colspan=2 align='right'>Total Deuda a Proveedores</td><td>$Acumulado</td></tr>";
	$objResponse->assign("DeudaProveedores","innerHTML",$p);
	return $objResponse;
}
function SolicitarCreditoProveedores($CmbCredito,$CreditoAnio,$Desde,$Hasta) {
	$objResponse = new xajaxResponse();
	if ($CmbCredito=="Todos")
	   {$mas="";}
	else 
	   { $mas=" DescripcionAreas='" . $CmbCredito. "'"; }
	if ($CreditoAnio=="Todos")
	   {	}
	else { 
	    if (strlen($mas>1)) { $mas=$mas." and Anio=".$CreditoAnio; }
	   else { $mas=$mas." Anio=".$CreditoAnio;}
	}
	if (strlen($mas)) { $mas=$mas." and Empresa='".$_SESSION['CuitEmpresa']."'"; }
	//else { $mas=$mas." Empresa='".$Empresa."'";}
	else { $mas=$mas." Empresa='".$_SESSION['CuitEmpresa']."'";}

	if (strlen($mas)) {$mas=" WHERE ".$mas;}
	$sSql="SELECT CuitComp as NombreProveedor,Cuit, Saldo as Credito FROM (SELECT sum(MontoPagadoComp-NetoComp) as Saldo, CuitComp, Cuit FROM ViewComprobantes ".$mas." and FechaComp>='".$Desde."' and FechaComp<='".$Hasta."' and Empresa='".$_SESSION['CuitEmpresa']."' GROUP BY CuitComp) as f WHERE Saldo>2";
	//$sSql="SELECT CuitComp as NombreProveedor,Cuit, Saldo as Deuda FROM (SELECT sum(NetoComp-MontoPagadoComp) as Saldo, CuitComp, Cuit FROM ViewComprobantes ".$mas." GROUP BY CuitComp) as f WHERE Saldo>1";
	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
	// $p=$sSql;
	$p=$p."<table border=1>
		<tr bgcolor=\"#b0e3ff\"><td align=\"center\">Nombre</td><td align=\"center\">Cuit</td><td align=\"center\">Credito</td></tr>";
	while($row=$stmt->fetch()) {
		$p=$p."<tr><td>$row[NombreProveedor]</td><td>$row[Cuit]</td><td align='right'>$row[Credito]</td></tr>";
		$Acumulado=$Acumulado+$row[Credito];
	}
	$p=$p."<tr bgcolor=\"#A4FF9C\"><td colspan=2 align='right'>Total Credito a Proveedores</td><td>$Acumulado</td></tr>";
	$objResponse->assign("CreditoProveedores","innerHTML",$p);
	return $objResponse;
}


function SolicitarDeudaPorAreas($AreasAnio,$IncluirAreas,$Quienes) {
	$objResponse = new xajaxResponse();
	
	switch ($Quienes) {
		case "Todos": $SQLQuien=""; break;
		case "Si": $SQLQuien=' ParticipaenIva="Si" and '; break;
		case "No": $SQLQuien=' ParticipaenIva="No" and '; break;
	}
	if($IncluirAreas) {
	    $sSql='SELECT  PasadoEnMes, DescripcionAreas, DescripcionCuentas, sum(CASE IvaComp WHEN "10.5" THEN (BrutoComp) ELSE 0 END) as "10.5", sum(CASE IvaComp WHEN "21.0" THEN (BrutoComp) ELSE 0 END) as "21.0", sum(CASE IvaComp WHEN "27.0" THEN (BrutoComp) ELSE 0 END) as "27.0", sum(ExentoComp) as Exento, sum(PercepcionIvaComp) as Percepcion, sum(ImpInternoComp) as ImpInt, sum(MontoIva) as Iva, sum(NetoComp) as Neto, sum(BrutoComp) as Bruto FROM ViewComprobantes WHERE '.$SQLQuien.' Anio='.$AreasAnio.' and Empresa='.$_SESSION['CuitEmpresa'].' GROUP BY PasadoEnMes,DescripcionAreas, DescripcionCuentas ORDER BY DescripcionAreas, DescripcionCuentas, PasadoEnMes'; 
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
 	$p="<div class='content' style='none repeat scroll 0 0;overflow:auto;color:#ffffff;width:10%;height:60%;	:4px;'>
		<table border=1>
 		<tr bgcolor=\"#b0e3ff\"><td align=\"center\">Mes</td><td align=\"center\">Area</td><td align=\"center\">Cuenta</td><td align=\"center\">Bruto</td><td align=\"center\">10,5</td><td align=\"center\">21,0</td><td align=\"center\">27,0</td> <td align=\"center\">Exento</td><td align=\"center\">Percepcion</td><td align=\"center\">ImpInt</td><td align=\"center\">Iva</td><td align=\"center\">Neto</td></tr>";
 	while($row = $stmt->fetch()) {
		
		if($row[PasadoEnMes]<>$MesColorFondo) {
			if ($bgcolor=="bgcolor=\"lightGray\"") { $bgcolor="bgcolor=\"\""; } else { $bgcolor="bgcolor=\"lightGray\""; }
		}
		$MesColorFondo=$row[PasadoEnMes];
 		$p=$p."<tr $bgcolor><td>$row[PasadoEnMes]</td><td>$row[DescripcionAreas]</td><td >$row[DescripcionCuentas]</td><td align='right'>".number_format($row[Bruto],2,',','.')."</td><td align='right'>".number_format($row[3],2,',','.')."</td><td align='right'>".number_format($row[4],2,',','.')."</td><td align='right'>".number_format($row[5],2,',','.')."</td><td align='right'>".number_format($row[Exento],2,',','.')."</td><td align='right'>".number_format($row[Percepcion],2,',','.')."</td><td align='right'>".number_format($row[ImpInt],2,',','.')."</td><td align='right'>".number_format($row[Iva],2,',','.')."</td><td align='right'>".number_format($row[Neto],2,',','.')."</td></tr>";
 		$Acumulado=$Acumulado+$row[Neto];
 	}
 	$p=$p."</div>";
	}

else {
	$sSql='SELECT  PasadoEnMes, DescripcionCuentas, sum(CASE IvaComp WHEN "10.5" THEN (BrutoComp) ELSE 0 END) as "10.5", sum(CASE IvaComp WHEN "21.0" THEN (BrutoComp) ELSE 0 END) as "21.0", sum(CASE IvaComp WHEN "27.0" THEN (BrutoComp) ELSE 0 END) as "27.0", sum(ExentoComp) as Exento, sum(PercepcionIvaComp) as Percepcion, sum(ImpInternoComp) as ImpInt, sum(MontoIva) as Iva, sum(NetoComp) as Neto, sum(BrutoComp) as Bruto FROM ViewComprobantes WHERE '.$SQLQuien.' Anio='.$AreasAnio.' and Empresa='.$Empresa.' GROUP BY PasadoEnMes, DescripcionCuentas ORDER BY PasadoEnMes, DescripcionCuentas';
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
 	$p="<table border=1>
 		<tr bgcolor=\"#b0e3ff\"><td align=\"center\">Mes</td><td align=\"center\">Cuenta</td><td align=\"center\">Bruto</td><td align=\"center\">10,5</td><td align=\"center\">21,0</td><td align=\"center\">27,0</td> <td align=\"center\">Exento</td><td align=\"center\">Percepcion</td><td align=\"center\">ImpInt</td><td align=\"center\">Iva</td><td align=\"center\">Neto</td></tr>";
 	while($row = $stmt->fetch()) {
		if($row[PasadoEnMes]<>$MesColorFondo) {
			if ($bgcolor=="bgcolor=\"lightGray\"") { $bgcolor="bgcolor=\"\""; } else { $bgcolor="bgcolor=\"lightGray\""; }
		}
		$MesColorFondo=$row[PasadoEnMes];
 		$p=$p."<tr $bgcolor><td>$row[PasadoEnMes]</td><td >$row[DescripcionCuentas]</td><td align='right'>".number_format($row[Bruto],2,',','.')."</td><td align='right'>".number_format($row[3],2,',','.')."</td><td align='right'>".number_format($row[4],2,',','.')."</td><td align='right'>".number_format($row[5],2,',','.')."</td><td align='right'>".number_format($row[Exento],2,',','.')."</td><td align='right'>".number_format($row[Percepcion],2,',','.')."</td><td align='right'>".number_format($row[ImpInt],2,',','.')."</td><td align='right'>".number_format($row[Iva],2,',','.')."</td><td align='right'>".number_format($row[Neto],2,',','.')."</td></tr>";
 		$Acumulado=$Acumulado+$row[Neto];
 	}
}
 	$p=$p."<tr bgcolor=\"#A4FF9C\"><td colspan=2 align='right'>Total Deuda por Areas</td><td>".number_format($Acumulado,2,',','.')."</td></tr>";
 	$objResponse->assign("DeudaPorAreas","innerHTML",$p);
	return $objResponse;
}

function CargarAreasCuentas1($Empresa) {
	$objResponse = new xajaxResponse();
	//                                                                                                                 $objResponse->alert("Empresa ?".$Empresa);
	$ElFiltro='xajax.call(\'MostrarEspera\', {method: \'get\',parameters:[]}); xajax.call(\'LlamarFiltro\', {method: \'get\',parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value,FOrden.checked,FConSaldo.checked]}); xajax.call(\'OcultarEspera\', {method: \'get\',parameters:[]}); return false;"';
	//$ElFiltro2='xajax.call(\'MostrarEspera\', {method: \'get\',parameters:[]}); onClick="xajax.call(\'LlamarFiltro2\', {method: \'get\',parameters:[FProveedores2.value,FMes2.value,FParticipaIva2.value,FAreas2.value,FCuentas2.value,F2Anio.value,FFDesde.value,FFHasta.value]});"; onChange="xajax.call(\'LlamarFiltro2\', {method: \'get\',parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value]}); xajax.call(\'OcultarEspera\', {method: \'get\',parameters:[]}); return false;"';
	$ElFiltro2='xajax.call(\'MostrarEspera\', {method: \'get\',parameters:[]}); onChange="xajax.call(\'LlamarFiltro2\', {method: \'get\',parameters:[FProveedores2.value,FMes2.value,FParticipaIva2.value,FAreas2.value,FCuentas2.value,F2Anio.value,FFDesde.value,FFHasta.value]});"; onChange="xajax.call(\'LlamarFiltro2\', {method: \'get\',parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value]}); xajax.call(\'OcultarEspera\', {method: \'get\',parameters:[]}); return false;"';
	
	$sSql="SELECT * FROM tblAreas WHERE Empresa='".$_SESSION['CuitEmpresa']."' ORDER BY DescripcionAreas";
	$Areas='<SELECT id="Areas" name="Areas" style="max-width:100px;">';
	//$FAreas='<SELECT id="FAreas" name="FAreas" style="max-width:100px;" onClick="'.$ElFiltro.'" onChange="'.$ElFiltro.'">';
	//$FAreas2='<SELECT id="FAreas2" name="FAreas2" style="max-width:100px;" onClick="'.$ElFiltro2.'" onChange="'.$ElFiltro2.'">';
	$FAreas='<SELECT id="FAreas" name="FAreas" style="max-width:100px;" onChange="'.$ElFiltro.'">';
	$FAreas2='<SELECT id="FAreas2" name="FAreas2" style="max-width:100px;" onChange="'.$ElFiltro2.'">';
	$CmbDeuda='<SELECT id="CmbDeuda" name="CmbDeuda" style="max-width:100px;">';
	$CmbCredito='<SELECT id="CmbCredito" name="CmbCredito" style="max-width:100px;">';
	
	//$objResponse->alert($sSql);
   	
	$stmt = $GLOBALS['pdo']->prepare($sSql); $stmt->execute(); 
		$Areas=$Areas.'<OPTION value=" "> </option>';
		$FAreas=$FAreas.'<OPTION value=" "> </option>';
		$FAreas2=$FAreas2.'<OPTION value=" "> </option>';
		$CmbDeuda=$CmbDeuda.'<OPTION value="Todos">Todos</option>';
		$CmbCredito=$CmbCredito.'<OPTION value="Todos">Todos</option>';
 		while($row=$stmt->fetch()) {

			$Areas=$Areas.'<OPTION value="'.$row["DescripcionAreas"] .'">'.$row["DescripcionAreas"].'</option>';
			$FAreas=$FAreas.'<OPTION value="'.$row["DescripcionAreas"] .'">'.$row["DescripcionAreas"].'</option>';
			$FAreas2=$FAreas2.'<OPTION value="'.$row["DescripcionAreas"] .'">'.$row["DescripcionAreas"].'</option>';
			$CmbDeuda=$CmbDeuda.'<OPTION value="'.$row["DescripcionAreas"] .'">'.$row["DescripcionAreas"].'</option>';
			$CmbCredito=$CmbCredito.'<OPTION value="'.$row["DescripcionAreas"] .'">'.$row["DescripcionAreas"].'</option>';
		}
	$Areas=$Areas.'</SELECT>';
	
	//$objResponse->alert($Areas);
	
	$FAreas=$FAreas.'</SELECT>';
	$FAreas2=$FAreas2.'</SELECT>';
	$CmbDeuda=$CmbDeuda.'</SELECT>';
	$CmbCredito=$CmbCredito.'</SELECT>';

	$sSql="SELECT * FROM tblCuentas WHERE Empresa='".$_SESSION['CuitEmpresa']."' ORDER BY DescripcionCuentas";
	$Cuentas='<SELECT id="Cuentas" name="Cuentas" style="max-width:100px;">';
	//$FCuentas='<SELECT id="FCuentas" name="FCuentas" style="max-width:100px;" onClick="'.$ElFiltro.'" onChange="'.$ElFiltro.'">';
	$FCuentas='<SELECT id="FCuentas" name="FCuentas" style="max-width:100px;" onChange="'.$ElFiltro.'">';

	//$FCuentas2='<SELECT id="FCuentas2" name="FCuentas2" style="max-width:100px;" onClick="'.$ElFiltro2.'" onChange="'.$ElFiltro2.'">';
	$FCuentas2='<SELECT id="FCuentas2" name="FCuentas2" style="max-width:100px;" onChange="'.$ElFiltro2.'">';

   	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute(); 	
		$Cuentas=$Cuentas.'<OPTION value=" "> </option>';
		$FCuentas=$FCuentas.'<OPTION value=" "> </option>';
		$FCuentas2=$FCuentas2.'<OPTION value=" "> </option>';
 		while($row=$stmt->fetch()) {
			$Cuentas=$Cuentas.'<OPTION value="'.$row["DescripcionCuentas"] .'">'.$row["DescripcionCuentas"].'</option>';
			$FCuentas=$FCuentas.'<OPTION value="'.$row["DescripcionCuentas"] .'">'.$row["DescripcionCuentas"].'</option>';
			$FCuentas2=$FCuentas2.'<OPTION value="'.$row["DescripcionCuentas"] .'">'.$row["DescripcionCuentas"].'</option>';
		}

	$Cuentas=$Cuentas.'</SELECT>';
	$FCuentas=$FCuentas.'</SELECT>';
	$FCuentas2=$FCuentas2.'</SELECT>';

 	$Eventos='  onChange="xajax.call(\'CargarCuitProveerdor\', {method: \'get\', parameters:[cmbProveedores.value]});"';
 	//$Eventos='  onChange="xajax.call(\'CargarCuitProveerdor\', {method: \'get\', parameters:[cmbProveedores.value,cmbEmpresas.value]});" onClick="xajax.call(\'CargarCuitProveerdor\', {method: \'get\', parameters:[cmbProveedores.value,cmbEmpresas.value]});"';
 	$a=CargarCombo("cmbProveedores","tblProveedores","NombreProveedor","NombreProveedor","Cuit<>'' and Empresa='".$_SESSION['CuitEmpresa']."' ORDER BY NombreProveedor","Proveedores"," ",$Eventos,150,0,"");
	$objResponse->assign("Proveedores","innerHTML",$a);

	$Eventos=' onchange="xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]})"';
	//$a=CargarCombo("FProveedores","tblProveedores","NombreProveedor","NombreProveedor","Cuit<>'' and Empresa='".$_SESSION['CuitEmpresa']."' ORDER BY NombreProveedor","FProveedores"," ",'onclick="xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,cmbEmpresas.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});"',150,0,"");
	$a=CargarCombo("FProveedores","tblProveedores","NombreProveedor","NombreProveedor","Cuit<>'' and Empresa='".$_SESSION['CuitEmpresa']."' ORDER BY NombreProveedor","FProveedores"," ",$Eventos,200,0,"");
	$objResponse->assign("ProveedoresComprobantes","innerHTML",$a);

	$a=CargarCombo("FProveedores2","tblProveedores","NombreProveedor","NombreProveedor","Cuit<>'' and Empresa='".$_SESSION['CuitEmpresa']."' ORDER BY NombreProveedor","FProveedores2"," ",$Eventos,50,0,"");
	$objResponse->assign("ProveedoresDeuda","innerHTML",$a);

	$objResponse->assign("DivAreas","innerHTML",$Areas);
	$objResponse->assign("DivFAreas","innerHTML",$FAreas);
	$objResponse->assign("DivFAreas2","innerHTML",$FAreas2);
	$objResponse->assign("DivCmbDeuda","innerHTML",$CmbDeuda);
	$objResponse->assign("DivCmbCredito","innerHTML",$CmbCredito);

	$objResponse->assign("DivCuentas","innerHTML",$Cuentas);
	$objResponse->assign("DivFCuentas","innerHTML",$FCuentas);
	$objResponse->assign("DivFCuentas2","innerHTML",$FCuentas2);

	return $objResponse;
}

function CerrarLibro($Cerrar,$LibroAnio,$LibroMes)  {
	$objResponse = new xajaxResponse();
	if ($Cerrar=='1') { 
		//Controla si el comprobante se encuentra en un libro cerrado, si es así no lo deja modificar
		$sSql="SELECT * FROM tblComprobantes WHERE Anio=$LibroAnio and Empresa='".$_SESSION['CuitEmpresa']."' and PasadoEnMes='$LibroMes' and ParticipaEnIva='Si'";
 		$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
		$row=$stmt->fetch();
		if ($row['Cerrado']>1) { 
			$objResponse->alert("El Libro ya se encontraba cerrado!!!");
			return $objResponse;
		}
	// 		Obtiene el mayor número de hoja del libro de compras 35
		$sSql="SELECT max(Cerrado) as Libro FROM tblComprobantes WHERE Empresa='".$_SESSION['CuitEmpresa']."'";
 			$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
			$row=$stmt->fetch();
			$Folio=$row['Libro']+1;
			//$Listado="SELECT IdComp,NroComp,FechaComp,CuitComp FROM tblComprobantes WHERE PasadoEnMes='$LibroMes' and (ParticipaEnIva='Si' or ParticipaEnIva='IB' or ParticipaEnIva='BsPersonal') and Anio=$LibroAnio and Empresa=$Empresa ORDER BY FechaComp,NroComp,CuitComp,DetalleComp";
			//Modificado el 29/08/2017
			$Listado="SELECT NroComp,FechaComp,Cuit,CuitComp,sum(BrutoComp) as BrutoComp, sum(MontoIva) as MontoIva, sum(ExentoComp) as ExentoComp, sum(ImpInternoComp) as ImpInternoComp, sum(PercepcionIvaComp) as PercepcionIvaComp, sum(RetencionIB) as RetencionIB, sum(RetencionGan) as RetencionGan, sum(NetoComp) as NetoComp, Cerrado FROM ViewComprobantes WHERE PasadoEnMes='$LibroMes' and (ParticipaEnIva='Si' or ParticipaEnIva='IB' or ParticipaEnIva='Ganancias' or ParticipaEnIva='BsPersonal') and Anio=$LibroAnio and Empresa=".$_SESSION['CuitEmpresa']." Group BY NroComp,FechaComp,CuitComp ORDER BY FechaComp,NroComp,CuitComp,DetalleComp";
 			$stmt =  $GLOBALS['pdo']->prepare($Listado); $stmt->execute();
 			$cont=0;
 			while($row=$stmt->fetch()) {
				if ($cont==35) { $Folio++; $cont=0; }
				$cont++;
				if ($NroComp==$row[NroComp] and $FechaComp==$row[FechaComp] and $Cuit==$row[CuitComp]) { $cont=$cont-1; } 
				//$Listado="UPDATE tblComprobantes SET Cerrado=$Folio WHERE IdComp=$row[IdComp]";
				//Modificado el 29/08/2017
				$Listado="UPDATE tblComprobantes SET Cerrado=$Folio WHERE CuitComp='$row[CuitComp]' and FechaComp='$row[FechaComp]' and PasadoEnMes='$LibroMes' and (ParticipaEnIva='Si' or ParticipaEnIva='IB' or ParticipaEnIva='Ganancias' or ParticipaEnIva='BsPersonal') and Anio='$LibroAnio' and Empresa='".$_SESSION['CuitEmpresa']."'";
				$RecModif = $GLOBALS['pdo']->prepare($Listado); $RecModif->execute();
				//$objResponse->alert($Listado);
				$IdComp=$row[IdComp]; $NroComp=$row[NroComp]; $FechaComp=$row[FechaComp]; $Cuit=$row[CuitComp];
			}
			if ($RecModif->affected_rows) { 
				$objResponse->alert("Se Cerró correctamente"); 
				$objResponse->assign("Cerrar","value","0");
			}
	  else { $objResponse->alert("NO SE PUDO CERRAR EL LIBRO"); }
	}
	return $objResponse;
}

function DibujarLibrosCerrados($LibroAnio) {
	$objResponse = new xajaxResponse();
	$sSql="SELECT PasadoEnMes, Max(Cerrado) as Cerrado FROM tblComprobantes WHERE ParticipaEnIva='Si' and Anio='$LibroAnio' and Empresa='".$_SESSION['CuitEmpresa']."' GROUP BY Anio,PasadoEnMes ORDER BY FechaComp";
 	$stmt =  $GLOBALS['pdo']->prepare($sSql); $stmt->execute();
	$P=$P.'<div class=”contenedor-tabla”>';
	while($row = $stmt->fetch()) {
		$P=$P.
		"    <div class=\"contenedor-fila\">
        		<div class=\"contenedor-columna\">";
				$P=$P.$row[PasadoEnMes];
        	$P=$P."</div>";
			if($row[Cerrado]) { 
			   $P=$P."<div class=\"contenedor-columna-cerrado\">";
			   $P=$P."CERRADO"; }
			ELSE { 
   			   $P=$P."<div class=\"contenedor-columna\">";
			   $P=$P."ABIERTO"; }
        	$P=$P."</div>
    		     </div>
	</div>";
	}
	$objResponse->assign("DivLibrosC","innerHTML",$P);
	return $objResponse;
}

function MostrarEspera() {
	$objResponse = new xajaxResponse();
	$objResponse->assign("cargando","innerHTML","<IMG src=\"cargando.gif\" width=\"64\" height=\"64\" align=\"left\" border=\"0\">");
	return $objResponse;
}

function OcultarEspera() {
	$objResponse = new xajaxResponse();
	$objResponse->assign("cargando","innerHTML","");
	return $objResponse;
}

$xajax = new xajax();
$xajax->registerFunction("CargarCuitProveerdor");
$xajax->registerFunction("LlamarFiltro");
$xajax->registerFunction("LlamarFiltro2");
$xajax->registerFunction("AgregarComprobante");
$xajax->registerFunction("CargarDatosComprobante");
$xajax->registerFunction("ModificarComprobante");
$xajax->registerFunction("EliminarComprobante");
$xajax->registerFunction("AcumuladoIVA");
$xajax->registerFunction("SolicitarDeudaProveedores");
$xajax->registerFunction("SolicitarCreditoProveedores");
$xajax->registerFunction("SolicitarDeudaPorAreas");
$xajax->registerFunction("CargarAreasCuentas1");
$xajax->registerFunction("CargarComboDetalle");
$xajax->registerFunction("CerrarLibro");
$xajax->registerFunction("DibujarLibrosCerrados");

$xajax->registerFunction("MostrarEspera");
$xajax->registerFunction("OcultarEspera");

$xajax->processRequest();

echo "<head><title>Compras</title>";
	$xajax_files = array();
	$xajax_files[] = array("ajax/xajaxjs/xajax_core.js", "xajax");
	$xajax->printJavascript("../", $xajax_files)
?>
<script type="text/javascript">
    function setupCallback() {
        xajax.callback.global.onRequest = function() {
            alert('In global.onRequest');
        };
        xajax.callback.global.onFailure = function(args) {
            alert("In global.onFailure...HTTP status code: " + args.request.status);
        }
        xajax.callback.global.onComplete = function() {
            alert('In global.onComplete');
        };
        var cb = xajax.callback.create();
        cb.onRequest = function() {
            alert('Original onRequest');
        };
        cb.onResponseDelay = function() {
            alert('Original onRequestdelay');
        };
        cb.timers.onResponseDelay.delay = 2600;
        return cb;
    }
</script>
<?php include_once("Estilos.css"); ?>

<!-- Tags para el tab -->
<!--<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.1/build/fonts/fonts-min.css" /> 
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.1/build/tabview/assets/skins/sam/tabview.css" /> 
 <script type="text/javascript" src="http://yui.yahooapis.com/2.8.1/build/yahoo-dom-event/yahoo-dom-event.js"></script> 
<script type="text/javascript" src="http://yui.yahooapis.com/2.8.1/build/element/element-min.js"></script> 
<script type="text/javascript" src="../../sistema/bootstrap/librerias/tabview-min.js"></script> -->
<!-- <script type="text/javascript" src="http://yui.yahooapis.com/2.8.1/build/tabview/tabview-min.js"></script>  -->
<link rel="stylesheet" type="text/css" href="../../sistema/bootstrap/librerias/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="../../sistema/bootstrap/librerias/tabview.css" />
<script type="text/javascript" src="../../sistema/bootstrap/librerias/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../../sistema/bootstrap/librerias/element-min.js"></script>
<script language="javascript" type="text/javascript" src="/sistema/calendario/datetimepicker.js"></script>


<!-- FORMULARIO MODAL 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->

<script src="bootstrap/js/jquery.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>




<!-- MASCARA PARA NUMEROS -->
<script src="../../sistema/jquery.js" type="text/javascript"></script>
<script src="../../sistema/jquery.maskedinput.js" type="text/javascript"></script>
<script src="../../sistema/jquery.maskMoney.js" type="text/javascript"></script>

<script>
    jQuery(function($) {
        $("#Bruto").maskMoney({
            thousands: '',
            allowNegative: true,
            decimal: '.'
        });
        $("#Exento").maskMoney({
            thousands: '',
            allowNegative: true,
            decimal: '.'
        });
        $("#ImpInterno").maskMoney({
            thousands: '',
            allowNegative: true,
            decimal: '.'
        });
        $("#PercIva").maskMoney({
            thousands: '',
            allowNegative: true,
            decimal: '.'
        });
        $("#RetencionIB").maskMoney({
            thousands: '',
            allowNegative: true,
            decimal: '.'
        });
        $("#RetencionGan").maskMoney({
            thousands: '',
            allowNegative: true,
            decimal: '.'
        });
        $("#MontoPagado").maskMoney({
            thousands: '',
            allowNegative: true,
            decimal: '.'
        });
        //$("#DeudaDesde").mask("9999-99-99");
        //$("#DeudaHasta").mask("9999-99-99");
        //$("#CreditoDesde").mask("9999-99-99");
        //$("#CreditoHasta").mask("9999-99-99");

    });
</script>
<style type="text/css">
    /*margin and padding on body element can introduce errors in determining element position and are not recommended;
  we turn them off as a foundation for YUI CSS treatments. */
    body {
        margin: 0;
        padding: 0;
    }

    table,
    th,
    td {
        font-weight: normal;
        font-family: arial;
        font-family: trebuchet MS, Lucida sans, Arial;
        /*   font-size: 10px; */
        color: #444;

        border: solid #ccc 1px;
        border-collapse: separate;
        border-spacing: 0;

    }

    table>tbody>tr>td {
        padding: 0px;
    }

    table {
        -moz-border-radius: 6px;
        -webkit-border-radius: 6px;
        border-radius: 6px;
    }

    navbar,
    navbar-default {
        margin: 0;
        padding: 0;
        margin-bottom: 10px;
    }

    yui-navset {
        margin-top: 200px;
        padding: 100;
    }

    /*.contenedor-tabla{
display: table;
	padding:2px;
	border: 3px solid red;
}
.contenedor-fila{
display: table-row;
}
.contenedor-columna{
display: table-cell;
	border: 1px solid #f5b65b;
	width: 100px;
	text-align: left;
	padding-left: 7px;
 	background: #FFEDBD 
}
.contenedor-columna-cerrado{
display: table-cell;
	border: 1px solid #f5b65b;
	width: 100px;
	text-align: left;
	padding-left: 7px;
	background-color: #DBC1FF
}
.contenedor-columna div{
	margin: 1px;
	border: 1px solid black;
}*/
</style>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">

<center>

    <? print $encabezado; ?>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/js/bootstrap.js" rel="stylesheet">
    </head>
    <!-- <body class="yui-skin-sam" onload="xajax.call('CargarAreasCuentas1', {method: 'get', parameters:[cmbEmpresas.value]});"> -->
    <?php
    $_SESSION['Modulo'] = "C O M P R A S";
    include "navbar.php";

    echo '<body class="yui-skin-sam" onload="xajax.call(\'CargarAreasCuentas1\', {method: \'get\', parameters:[' . $_SESSION['CuitEmpresa'] . ']});">';

    //.'?Empresa='.$_SESION['CuitEmpresa']

    //$_SESSION['CuitEmpresa']=$_GET['Empresa'];
    //echo "esta es la empresa".$_SESSION['IdEmpresa']. " este es el cuit" . $_SESSION['CuitEmpresa'];

    ?>
    <?php
    $ElFiltro = ' onchange="xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});"';
    //$ElFiltro= ' onclick="xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,'.$_SESSION['CuitEmpresa'].',FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});"';
    //$ElFiltro='xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,cmbEmpresas.value,FDetalle2.value,Pagina.value]});'
    ?>

    <form id="testForm3" onSubmit="return false">

        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Gestionar Comprobantes</a></li>
            <li class="nav-item"><a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Cuentas Corrientes</a></li>
            <li class="nav-item"><a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Cr&eacute;dito de Proveedores</a></li>
            <li class="nav-item"><a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Deuda por area/sector</a></li>
            <li class="nav-item"><a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Libros de Iva</a></li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <font face="Verdana" size="1" style="margin:0; padding:0;">
                    <!-- <p style="background: #e14642;"> -->
                    <table border="1" style="font-size : 1px; margin-bottom: 0px;" class="table table-responsive table-hover">
                        <tbody bgcolor="#EFF3F7" bordercolor="#FFFFFF" style="font-family : Verdana; font-size : 12px; font-weight : 300;">
                            <tr>
                                <td>
                                    <div id="Empresas">
                                        <? 
                ////$Eventos=" onChange=\"xajax.call('CargarAreasCuentas1', {method: 'get', parameters:[cmbEmpresas.value]});\" onClick=\"xajax.call('CargarAreasCuentas1', {method: 'get', parameters:[cmbEmpresas.value]});\"";
                ////CargarCombo("cmbEmpresas","ViewUsuariosEmpresas WHERE NombreUsuario='".$_SESSION['usuario']."'","Nombre","Cuit","","Empresas"," ",$Eventos,250,1,0);
                $ElFiltro2=' xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});';
                ?>
                                        <div class="col-md-12 col-xs-12" style="align-content: center;">
                                            <div class="col-xs-3 col-md-3 col-md-offset-1">
                                                <!--  <input class="ButtonAceptar form-control col-md-3 btn btn-success" type="submit" value="Agregar" onClick="xajax.call('AgregarComprobante', {method: 'get', parameters:[Anio.value,PasadoEnMes.value,ParticIva.value,Areas.value,Cuentas.value,Iva.value,cmbProveedores.value,Comprobante.value,Fecha.value,Detalle.value,Bruto.value,Exento.value,ImpInterno.value,PercIva.value,RetencionIB.value,RetencionGan.value,Neto.value,MontoPagado.value,CantLitros.value]}); <?php echo $ElFiltro2; ?> "> -->
                                                <input class="ButtonAceptar form-control col-md-3 btn btn-success" data-toggle="modal" data-target="#myModalAgregar" type="submit" value="Agregar">

                                            </div>
                                            <div class="col-xs-3 col-md-3">
                                                <!-- <input class="ButtonModificar form-control col-xs-3 col-md-3 btn btn-warning" type="submit" value="Modificar" onClick="xajax.call('ModificarComprobante', {method: 'get', parameters:[Anio.value,PasadoEnMes.value,ParticIva.value,Areas.value,Cuentas.value,Iva.value,cmbProveedores.value,Comprobante.value,Fecha.value,Detalle.value,Bruto.value,Exento.value,ImpInterno.value,PercIva.value,RetencionIB.value,RetencionGan.value,Neto.value,MontoPagado.value,CantLitros.value,IdComp.value]}); <?php echo $ElFiltro2; ?> "> -->
                                                <input class="ButtonModificar form-control col-xs-3 col-md-3 btn btn-warning" data-toggle="modal" data-target="#myModalModificar" type="submit" value="Modificar">
                                            </div>
                                            <div class="col-xs-3 col-md-3">
                                                <input type="hidden" id="VartxtFecha" name="VartxtFecha">
                                                <input type="hidden" id="VarComprobante" name="VarComprobante">
                                                <input type="hidden" id="VartxtCuitProveedores" name="VartxtCuitProveedores">

                                                <!-- <input class="ButtonEliminar form-control col-xs-3 col-md-3 btn btn-danger" type="submit" value="Eliminar" onClick="Preguntar(); ElimBorrar=document.getElementById('Borrar').value; ElimIdComp=document.getElementById('IdComp').value; xajax.call('EliminarComprobante', {method: 'get', parameters:[ElimIdComp,ElimBorrar]}); <?php echo $ElFiltro2; ?> "> -->
                                                <input class="ButtonEliminar form-control col-xs-3 col-md-3 btn btn-danger" data-toggle="modal" data-target="#myModalEliminar" type="submit" value="Eliminar">
                                                <input type="hidden" id="Borrar" name="Borrar" value="0">
                                                <input type="hidden" id="Cerrar" name="Cerrar" value="0" onchange="xajax.call('CargarAreasCuentas1', {method: 'get', parameters:[cmbEmpresas.value]});">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="content-responsive" style="none repeat scroll 0 0; overflow:auto;color:#ffffff;height:70%;padding:4px;"> -->
                                    <!--  <table border="1" class="table table-responsive table-hover"> -->
                                    <table border="1" class="table table-responsive table-hover">
                                        <tbody bgcolor="#EFF3F7" bordercolor="#FFFFFF" style="font-family : Verdana; font-size : 12px; font-weight : 300;">
                                            <tr align="center">
                                                <td align="center"><b>Fecha</b></td>
                                                <td align="center"><b>Proveedor</b></td>
                                                <td align="center"><b>Cuit</b></td>
                                                <td align="center"><b>Comprobante</b></td>
                                                <td align="center"><b>Detalle</b></td>
                                                <td align="center"><b>A&ntilde;o</b></td>
                                                <!-- <td align="center"><b>Haber</b></td> -->
                                                <td align="center"><b>PasadoEnMes</b></td>
                                                <td align="center"><b>Area</b></td>
                                                <td align="center"><b>Cuenta</b></td>
                                            </tr>
                                            <tr>

                                                <input id="IdComp" name="IdComp" type="hidden" size="10">
                                                <td>
                                                    <input id="Fecha" name="Fecha" type="date">
                                                    <!-- <input id="Fecha" name="txtFecha" type="text" size="8" >
                    <a href="javascript:NewCal('Fecha','YYYYMMDD')">
                    	<img src="calendario/cal.gif" width="16" height="16" border="0" alt="Ingrese fecha">
                    </a> -->
                                                </td>
                                                <td>
                                                    <div id="Proveedores">
                                                        <?php setlocale(LC_MONETARY, 'en_ES'); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div id="CuitProveedores">
                                                        <input type="text" name="txtCuitProveedores" size="11" id="txtCuitProveedores">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="Comprobante" id="Comprobante" align="left" size="13">
                                                </td>
                                                <td>
                                                    <input type="text" id="Detalle" name="Detalle" size="15" onkeypress="javascript:if(getKeyCode(event)==13) document.forms[0].ParticIva.focus();">
                                                </td>
                                                <td>
                                                    <SELECT id="Anio" name="Anio">
                                                        <OPTION>2020</OPTION>
                                                        <OPTION>2019</OPTION>
                                                        <OPTION>2018</OPTION>
                                                        <OPTION>2017</OPTION>
                                                        <OPTION>2016</OPTION>
                                                        <OPTION>2015</OPTION>
                                                        <OPTION>2014</OPTION>
                                                        <OPTION>2013</OPTION>
                                                        <OPTION>2012</OPTION>
                                                    </SELECT>
                                                </td>
                                                <!-- <td> -->
                                                <input type="hidden" id="Haber" name="Haber" size="2" onkeypress="javascript:if(getKeyCode(event)==13) document.forms[0].PasadoEnMes.focus();" onkeyup="CalcularNeto();">
                                                <!-- </td>  -->
                                                <td>
                                                    <div id="DivPasadoEnMes">
                                                        <?php $Eventos = 'onkeypress="javascript:if(getKeyCode(event)==13) document.forms[0].cmbAreas.focus();"';
                                                        CargarCombo("PasadoEnMes", "", "", "Meses", "Meses", "PasadoEnMes", " ", $Eventos, 90, 1, 0); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div id="DivAreas"> <?php $Eventos = 'onkeypress="javascript:if(getKeyCode(event)==13) document.forms[0].cmbCuentas.focus();"'; ?></div>
                                                </td>
                                                <td>
                                                    <div id="DivCuentas"><?php ?> </div>
                                                </td>
                                            </tr>
                                            <tr align="center">
                                                <td>Bruto</td>
                                                <td>ParticIva&nbsp;&nbsp;&nbsp;&nbsp;Iva</td>
                                                <td>Exento</td>
                                                <td>Imp.Interno</td>
                                                <td colspan="2">Ret/Perc.Iva|Ret/Perc.IB|RetGan</td>
                                                <td>Neto</td>
                                                <td onclick="javascript:document.getElementById('MontoPagado').value=document.getElementById('Neto').value;">Monto Pagado</td>
                                                <td>CantLitros</td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <input type="text" name="Bruto" style="text-align:right;" id="Bruto" size="8" onfocus="this.select();" onblur="CalcularNeto();" onchange="CalcularNeto();" onkeyup=" CalcularNeto(); return soloNumero(this.value,10,2);">
                                                    <!-- <td><INPUT type="text" name="Bruto" align="right" id="Bruto" size="10" onkeypress="javascript:if(getKeyCode(event)==13) document.forms[0].Iva.focus();" onfocus="return validacion(this); CalcularNeto();" onblur="return validacion(this); CalcularNeto();" onkeyup="CalcularNeto();"></td> -->
                                                </td>
                                                <td align="center">
                                                    <div id="DivParticIva">
                                                        <?php $Eventos = 'onkeypress="javascript:if(getKeyCode(event)==13) document.forms[0].PasadoEnMes.focus();"';
                                                        CargarCombo("ParticIva", "tblParticIva", "DescripcionPartic", "DescripcionPartic", "", "ParticIva", "", $Eventos, 50, 1, 0); ?>
                                                        <SELECT name="Iva" id="Iva" onkeypress="javascript:if(getKeyCode(event)==13) document.forms[0].Exento.focus();" onchange="CalcularNeto();" onclick="javascript:if(getKeyCode(event)==13) document.forms[0].Exento.focus();">
                                                            <OPTION>21.00</OPTION>
                                                            <OPTION>10.50</OPTION>
                                                            <OPTION>27.00</OPTION>
                                                            <OPTION>0.00</OPTION>
                                                        </SELECT>
                                                    </div>
                                                </td>
                                                <td align="center">
                                                    <input type="text" name="Exento" style="text-align:right;" id="Exento" size="10" onblur="CalcularNeto();" onkeyup="CalcularNeto();">
                                                </td>
                                                <td align="center">
                                                    <input type="text" name="ImpInterno" style="text-align:right;" id="ImpInterno" size="10" onblur="CalcularNeto();" onkeyup="CalcularNeto();">
                                                </td>
                                                <td align="center" colspan="2">
                                                    <input type="text" name="PercIva" style="text-align:right;" id="PercIva" size="5" onblur="CalcularNeto();" onkeyup="CalcularNeto();"><input type="text" name="RetencionIB" align="right" id="RetencionIB" size="5" onblur="CalcularNeto();" onkeyup="CalcularNeto();"><input type="text" name="RetencionGan" align="right" id="RetencionGan" size="5" onblur="CalcularNeto();" onkeyup="CalcularNeto();">
                                                </td>
                                                <td align="center">
                                                    <input type="text" name="Neto" style="text-align:right;" id="Neto" size="10" onblur="CalcularNeto();" onkeyup="CalcularNeto();">
                                                </td>
                                                <td align="center">
                                                    <input type="text" name="MontoPagado" style="text-align:right;" id="MontoPagado" size="10" onfocus="this.select();" onblur="CalcularNeto();" onkeyup="CalcularNeto();">
                                                </td>
                                                <td align="center">
                                                    <input type="text" name="CantLitros" style="text-align:right;" id="CantLitros" size="10">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <hr style="border-width: 4px; color: black; margin-top: 9px; margin-bottom: 9px; border-top: 1px solid #3e3333;">

                    <Table title="Filtrar por:" border="1" class="table table-responsive table-hover">
                        <tr>
                            <td align="center"><b>Actualiz</b></td>
                            <td align="center">Mes</td>
                            <td align="center">Proveedor</td>
                            <td align="center">ParticIva</td>
                            <td align="center">Detalle</td>
                            <td align="center">Area</td>
                            <td align="center">Cuenta</td>
                            <td align="center">MostrarTodos</td>
                            <td align="center">A&ntilde;o</td>
                            <td align="center">Asc. C/Saldo</td>
                        </tr>
                        <tr>
                            <?php //$Filtro='onChange="xajax.call(\'MostrarEspera\', {method: \'get\',parameters:[]}); xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,cmbEmpresas.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]}); xajax.call(\'OcultarEspera\', {method: \'get\',parameters:[]}); return false;"';
                            //$Filtro= 'onclick="xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,cmbEmpresas.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});"';
                            //$Filtro=' onclick="xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,'.$_SESSION['CuitEmpresa'].',FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});"';
                            $Filtro = ' onchange="xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});"';
                            echo '<td align="center">
                <input type="button" onclick="xajax.call(\'LlamarFiltro\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value,FOrden.checked,FConSaldo.checked,Pagina.value]});">
              </td>';
                            ?>
                            <td align="center">
                                <?php CargarCombo("FMes", "", "Meses", "Meses", "MesActual", "FMes2", " ", $Filtro, 100, 1, ""); ?>
                            </td>
                            <td align="center">
                                <div id="ProveedoresComprobantes"></div>
                            </td>
                            <td align="center">
                                <?php CargarCombo("FParticipaIva", "tblParticIva", "DescripcionPartic", "DescripcionPartic", "", "ParticIva", " ", $Filtro, 30, 1, 0); ?>
                            </td>
                            <td align="center">
                                <div id="Detallen">
                                    <?php CargarCombo("FDetalle2", "tblComprobantes",  "distinct DetalleComp", "", "", "Detalle", " ", $Filtro, 100, 1, 0); ?>
                                </div>
                            </td>
                            <td align="center">
                                <div id=DivFAreas></div>
                            </td>
                            <td align="center">
                                <div id=DivFCuentas></div>
                            </td>
                            <td align="center">
                                <SELECT name="FFiltrar">
                                    <OPTION>Si</OPTION>
                                    <OPTION>No</OPTION>
                                </SELECT>
                            </td>
                            <td align="center">
                                <SELECT name="FAnio" id="FAnio" <?php echo $Filtro; ?>>
                                    <OPTION value="2020">2020</OPTION>
                                    <OPTION value="2019">2019</OPTION>
                                    <OPTION value="2018">2018</OPTION>
                                    <OPTION value="2017">2017</OPTION>
                                    <OPTION value="2016">2016</OPTION>
                                    <OPTION value="2015">2015</OPTION>
                                    <OPTION value="2014">2014</OPTION>
                                    <OPTION value="2013">2013</OPTION>
                                    <OPTION value="2012">2012</OPTION>
                                </SELECT>
                            </td>
                            <td align="center">
                                <input type="checkbox" name="FOrden" checked="true" id="FOrden"><input type="checkbox" name="FConSaldo" checked="false" id="FConSaldo">
                            </td>
                        </tr>
                    </Table>
                    <div id="Filtro"></div>
                    <!-- </p> -->
                    <div id="Botonera">
                        <input type="button" value="&lt;" id="Anterior" name="Anterior">
                        <input type="button" value="0" id="Pagina" name="Pagina">
                        <input type="button" value="&gt;" id="Posterior" name="Posterior">
                    </div>
                </font>

                <!-- Modal AGREGAR COMPROBANTE -->
                <div class="modal fade" id="myModalAgregar" role="dialog" onload="document.forms[0].btnAceptar.focus();">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Agregar Comprobante</h4>
                            </div>
                            <div class="modal-body">
                                <p>Est&aacute; seguro de agregar el comprobante?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" data-dismiss="modal" value="btnAceptar" onclick="xajax.call('AgregarComprobante', {method: 'get', parameters:[Anio.value,PasadoEnMes.value,ParticIva.value,Areas.value,Cuentas.value,Iva.value,cmbProveedores.value,Comprobante.value,Fecha.value,Detalle.value,Bruto.value,Exento.value,ImpInterno.value,PercIva.value,RetencionIB.value,RetencionGan.value,Neto.value,MontoPagado.value,CantLitros.value]}); <?php echo $ElFiltro2; ?> return false;">Agregar Comprobante</button>
                                <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal MODIFICAR COMPROBANTE -->
                <div class="modal fade" id="myModalModificar" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Modificar Comprobante</h4>
                            </div>
                            <div class="modal-body">
                                <p>Est&aacute; seguro de modificar el comprobante? <br>Una vez modificado no se podran recuperar los datos.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning" data-dismiss="modal" onclick="xajax.call('ModificarComprobante', {method: 'get', parameters:[Anio.value,PasadoEnMes.value,ParticIva.value,Areas.value,Cuentas.value,Iva.value,cmbProveedores.value,Comprobante.value,Fecha.value,Detalle.value,Bruto.value,Exento.value,ImpInterno.value,PercIva.value,RetencionIB.value,RetencionGan.value,Neto.value,MontoPagado.value,CantLitros.value,IdComp.value]}); <?php echo $ElFiltro2; ?> return false;">Modificar Comprobante</button>
                                <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal ELIMINAR COMPROBANTE -->
                <div class="modal fade" id="myModalEliminar" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Eliminar Comprobante</h4>
                            </div>
                            <div class="modal-body">
                                <p>Est&aacute; seguro de eliminar el comprobante? <br>Una vez eliminado no se podran recuperar los datos.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="ElimIdComp=document.getElementById('IdComp').value; xajax.call('EliminarComprobante', {method: 'get', parameters:[ElimIdComp]}); <?php echo $ElFiltro2; ?> return false;">Eliminar Comprobante</button>
                                <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                <font face="Verdana" size="1">
                    <Table title="Filtrar por:" border="1">
                        <tr>
                            <td>Mes</td>
                            <td>Proveedor</td>
                            <td>ParticIva</td>
                            <td>Detalle</td>
                            <td>Area</td>
                            <td>Cuenta</td>
                            <td>MostrarTodos</td>
                            <td>Desde</td>
                            <td>Hasta</td>
                            <td>Empresa</td>
                            <td>IVA</td>
                            <td>A&ntilde;o</td>
                            <td>ASC/DESC</td>
                        </tr>
                        <tr><?php
                            $Filtro2 = 'onClick="
xajax.call(\'LlamarFiltro2\', {method: \'get\', parameters:[FProveedores2.value,FMes2.value,FParticipaIva2.value,FAreas2.value,FCuentas2.value,F2Anio.value,FFDesde.value,FFHasta.value,F2Orden.checked,FDetalleSCBancarias.value]});"

onChange="
xajax.call(\'LlamarFiltro2\', {method: \'get\', parameters:[FProveedores.value,FMes.value,FParticipaIva.value,FAreas.value,FCuentas.value,FAnio.value,FDetalle2.value,F2Orden.checked,FDetalleSCBancarias.value]});"';

                            ?>
                            <td><?php CargarCombo("FMes2", "", "", "Meses", "Meses", "FMes2", " ", $Filtro2, 100, 1, 0); ?></td>
                            <td><?php ?><div id="ProveedoresDeuda"></div>
                            </td>
                            <td>
                                <?php CargarCombo("FParticipaIva2", "tblParticIva", "DescripcionPartic", "DescripcionPartic", "", "ParticIva", " ", $Filtro2, 30, 1, 0); ?></td>
                            <td>
                                <div id="DetalleCB"><?php CargarCombo("FDetalleSCBancarias", "tblComprobantes",  "distinct DetalleComp", "", "", "Detalle", " ", $Filtro2, 100, 1, 0); ?></div>
                            </td>
                            <td>
                                <div id=DivFAreas2></div>
                            </td>
                            <td>
                                <div id=DivFCuentas2></div>
                            </td>
                            <td><SELECT name="FFiltrar">
                                    <OPTION>Si</OPTION>
                                    <OPTION>No</OPTION>
                                </SELECT></td>
                            <td><input type="date" id="FFDesde" name="FFDesde" value="2019-01-01" size="10" <?echo $Filtro2;?>> </td>
                            <td><input type="date" id="FFHasta" name="FFHasta" size="10" <?echo $Filtro2; echo " value=\"".date(" Y-m-d")."\""; ?> > </td>
                            <td>
                                <?php
                                //  CargarCombo("cmbDeudaEmpresa","ViewUsuariosEmpresas WHERE NombreUsuario='".$_SESSION['usuario']."'","Nombre","Cuit","","Empresas"," ",$Eventos,250,1,0);
                                echo $_SESSION['NombreEmpresa'];
                                //CargarCombo("cmbDeudaEmpresa","tblComprobantes","DISTINCT Empresa","Empresa","","Empresas"," ",$Eventos,100,1,0);
                                ?>
                            </td>
                            <td><SELECT id="FFiltrar" name="FFiltrar">
                                    <OPTION>Si</OPTION>
                                    <OPTION>No</OPTION>
                                </SELECT></td>
                            <td><SELECT id="F2Anio" name="F2Anio" <?php $Filtro2 ?>>
                                    <OPTION>Todos</OPTION>
                                    <OPTION>2020</OPTION>
                                    <OPTION>2019</OPTION>
                                    <OPTION>2018</OPTION>
                                    <OPTION>2017</OPTION>
                                    <OPTION>2016</OPTION>
                                    <OPTION>2015</OPTION>
                                    <OPTION>2014</OPTION>
                                    <OPTION>2013</OPTION>
                                    <OPTION>2012</OPTION>
                                </SELECT></td>
                            <td><input type="checkbox" name="F2Orden" checked="true" id="F2Orden"></input></td>
                        </tr>
                    </Table>
                    <div class="content" style="none repeat scroll 0 0;overflow:auto;color:#ffffff;width:100%;height:60%;	:4px;">
                        <div id="Filtro2"></div>
                    </div>
                </font>
            </div>




            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                <div id="DivCmbDeuda"></div>

                <input class="ButtonAceptar" type="button" onclick="xajax.call('SolicitarDeudaProveedores', {method: 'get', parameters:[CmbDeuda.value,DeudaAnio.value,DeudaDesde.value,DeudaHasta.value]}); " value="Solicitar Deuda">
                <input class="ButtonAceptar" type="button" onclick="var xxx='../sistema/DeudaProveedoresPDF.php?mas='+getElementById('cmbAreasDeudasProov').value; window.open(xxx,'nuevaVentana','width=300, height=400');" value="Generar PDF">
                <input class="ButtonAceptar" type="button" onclick="	var CmbDeuda=getElementById('CmbDeuda') ;	var DeudaAnio=getElementById('DeudaAnio');	var DeudaDesde=getElementById('DeudaDesde');	var DeudaHasta=getElementById('DeudaHasta'); var xxx='../sistema/DeudaProveedoresPDF.php?CmbDeuda=' + CmbDeuda.value + '&DeudaAnio=' + DeudaAnio.value + '&DeudaDesde=' + DeudaDesde.value + '&DeudaHasta=' + DeudaHasta.value; window.open(xxx,'nuevaVentana','width=300, height=400');" value="Generar PDF">
                <!-- 	   <input class="ButtonAceptar" type="button" onclick=" 
	 
// 	   var CmbDeuda=getElementById('CmbDeuda') ;
//     	var DeudaAnio=getElementById('DeudaAnio');
//     	var DeudaDesde=getElementById('DeudaDesde');
//     	var DeudaHasta=getElementById('DeudaHasta');
//     	var xxx='../sistema/DeudaProveedoresPDF.php?CmbDeuda=' + CmbDeuda.value + '&DeudaAnio=' + DeudaAnio.value + '&DeudaDesde=' + DeudaDesde.value + '&DeudaHasta=' + DeudaHasta.value + ';
//         window.open(xxx,'nuevaVentana','width=300, height=400');\" value=\"Generar PDF\">";
-->

                <SELECT name="DeudaAnio" id="DeudaAnio">
                    <OPTION value="Todos">Todos</OPTION>
                    <OPTION value="2020">2020</OPTION>
                    <OPTION value="2019">2019</OPTION>
                    <OPTION value="2018">2018</OPTION>
                    <OPTION value="2017">2017</OPTION>
                    <OPTION value="2016">2016</OPTION>
                    <OPTION value="2015">2015</OPTION>
                    <OPTION value="2014">2014</OPTION>
                    <OPTION value="2013">2013</OPTION>
                    <OPTION value="2012">2012</OPTION>
                </SELECT><br>
                Desde<input id="DeudaDesde" name="DeudaDesde" type="date" onclick="xajax.call('SolicitarDeudaProveedores', {method: 'get', parameters:[CmbDeuda.value,DeudaAnio.value,DeudaDesde.value,DeudaHasta.value]}); ">
                Hasta<input id="DeudaHasta" name="DeudaHasta" type="date" onclick="xajax.call('SolicitarDeudaProveedores', {method: 'get', parameters:[CmbDeuda.value,DeudaAnio.value,DeudaDesde.value,DeudaHasta.value]}); "><br>

                <input class="ButtonAceptar" type="button" onclick="var xxx='../sistema/DeudaProveedoresPDF.php?UD='+getElementById('CmbDeuda').value+'&amp;Anio='+getElementById('DeudaAnio').value+'&amp;
                Anio='+getElementById('DeudaDesde').value+'&amp;
                Anio='+getElementById('DeudaHasta').value';
                window.open(xxx,'nuevaVentana','width=300, height=400');" value="Generar PDF">
                <div id="DeudaProveedores"></div>
            </div>

            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                <div id="DivCmbCredito"></div>

                <?php
                echo '<input class="ButtonAceptar" type="button" onclick="xajax.call(\'SolicitarCreditoProveedores\', {method: \'get\', parameters:[CmbCredito.value,CreditoAnio.value,CreditoDesde.value,CreditoHasta.value]}); " value="Solicitar Credito">';
                echo '<input class="ButtonAceptar" type="button" onclick="var xxx=\'../sistema/DeudaProveedoresPDF.php?mas=\'+getElementById(\'cmbAreasDeudasProov\').value; window.open(xxx,\'nuevaVentana\',\'width=300, height=400\');" value="Generar PDF">';

                echo '<SELECT name="CreditoAnio" id="CreditoAnio"> <OPTION value="Todos">Todos</OPTION><OPTION value="2018">2018</OPTION><OPTION value="2019">2019</OPTION><OPTION value="2020">2020</OPTION><OPTION value="2017">2017</OPTION><OPTION value="2016">2016</OPTION><OPTION value="2015">2015</OPTION><OPTION value="2014">2014</OPTION><OPTION value="2013">2013</OPTION><OPTION value="2012">2012</OPTION></SELECT><br>';

                echo 'Desde<input type="date" onclick="xajax.call(\'SolicitarCreditoProveedores\', {method: \'get\', parameters:[CmbCredito.value,CreditoAnio.value,CreditoDesde.value,CreditoHasta.value]}); " id="CreditoDesde" name="CreditoDesde">';
                echo 'Hasta<input type="date" onclick="xajax.call(\'SolicitarCreditoProveedores\', {method: \'get\', parameters:[CmbCredito.value,CreditoAnio.value,CreditoDesde.value,CreditoHasta.value]}); " id="CreditoHasta" name="CreditoHasta">';
                ?>
                <div id="CreditoProveedores"></div>
            </div>



            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                <?php
                echo '<input class="ButtonAceptar" type="button" onclick="xajax.call(\'SolicitarDeudaPorAreas\', {method: \'get\', parameters:[AreasAnio.value,chkIncluirAreas.checked,DeudaQuienes.value]}); return false;" value="Solicitar Deuda por Area"><br>';
                ?>
                <!-- 	<input class="ButtonAceptar" type="button" onclick="var xxx='../sistema/DeudaPorAreasPDF.php'; window.open(xxx,'nuevaVentana','width=300, height=400');" value="Generar PDF"><br> -->
                Incluir Areas <input type="checkbox" id="chkIncluirAreas" name="chkIncluirAreas" checked="true">
                <select id="DeudaQuienes" name="DeudaQuienes">
                    <option value="Todos">Todos</option>
                    <option value="Si">SI</option>
                    <option value="No">NO</option>
                </select>
                A&ntilde;o <SELECT id="AreasAnio" name="AreasAnio" <?php $Filtro ?>>
                    <OPTION>2020</OPTION>
                    <OPTION>2019</OPTION>
                    <OPTION>2018</OPTION>
                    <OPTION>2017</OPTION>
                    <OPTION>2016</OPTION>
                    <OPTION>2015</OPTION>
                    <OPTION>2014</OPTION>
                    <OPTION>2013</OPTION>
                    <OPTION>2012</OPTION>
                </SELECT><br>
                <div id="DeudaPorAreas"></div>

            </div>

            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                <?php
                $EventImprimir = "onClick=\"var PasadoEnMes=getElementById('LibroMes') ; var Anio=getElementById('LibroAnio'); var xxx='../sistema/LibroIvaPDF.php?PasadoEnMes=' + PasadoEnMes.value + '&Anio=' + Anio.value + '&Compra=1'; window.open(xxx,'nuevaVentana','width=300, height=400')\"";
                $EventArchivos = "onClick=\"var PasadoEnMes=getElementById('LibroMes') ; var Anio=getElementById('LibroAnio'); var xxx='../sistema/CITI/ComprasCBTE.php?PasadoEnMes=' + PasadoEnMes.value + '&Anio=' + Anio.value; window.open(xxx,'nuevaVentana','width=300, height=400')\"";
                $EventCerrar = 'onClick=" xajax.call(\'DibujarLibrosCerrados\', {method: \'get\', parameters:[LibroAnio.value]});"';
                //$Filtro="onChange=\"var PasadoEnMes=getElementById('LibroMes') ; var Empresa=getElementById('cmbEmpresasLibroIva'); var Anio=getElementById('LibroAnio'); var xxx='../sistema/LibroIvaPDF.php?PasadoEnMes=' + PasadoEnMes.value + '&Anio=' + Anio.value + '&Empresa=' + Empresa.value + '&Compra=1'; window.open(xxx,'nuevaVentana','width=300, height=400')\"";
                //CargarCombo("cmbEmpresasLibroIva","ViewUsuariosEmpresas WHERE NombreUsuario='".$_SESSION['usuario']."'","Nombre","Cuit","","Empresas"," ",$Filtro,250,1,0);
                //echo $_SESSION['NombreEmpresa'];
                echo '<table><tr><td><label style="font-size: 10px;">Mes</label><br>';
                CargarCombo("LibroMes", "", "", "Meses", "Meses", "LibroMes", " ", $Filtro, 100, 1, 0);
                echo '<br><label style="font-size: 10px;">A&nacute;o</label><br>';
                echo '<SELECT name="LibroAnio" id="LibroAnio" ' . $EventCerrar . '><OPTION>2020</OPTION><OPTION>2019</OPTION><OPTION>2018</OPTION><OPTION>2017</OPTION><OPTION>2016</OPTION><OPTION>2015</OPTION><OPTION>2014</OPTION><OPTION>2013</OPTION><OPTION>2012</OPTION> <OPTION>2011</OPTION> <OPTION>2010</OPTION> </SELECT></td>';


                //CargarCombo("cmbEmpresasCerrarLibro","ViewUsuariosEmpresas WHERE NombreUsuario='".$_SESSION['usuario']."'","Nombre","Cuit","","Empresas"," ",$Filtro,250,1,"");
                ////CargarCombo("LibroMesCerrarLibro","","","Meses","Meses","LibroMesCerrarLibro"," ",$Filtro,100,1,"");
                ////echo '<br><SELECT name="LibroAnio" id="LibroAnio"><OPTION>2019</OPTION><OPTION>2020</OPTION><OPTION>2018</OPTION><OPTION>2017</OPTION><OPTION>2016</OPTION> <OPTION>2015</OPTION><OPTION>2014</OPTION><OPTION>2013</OPTION><OPTION>2012</OPTION> <OPTION>2011</OPTION><OPTION>2010</OPTION></SELECT>'; 
                ?>
                <td>
                    <input class="ButtonAceptar" type="button" <?php echo $EventImprimir; ?>value="Imprimir Libro"><br>
                    <input class="ButtonAceptar" type="button" <?php echo $EventArchivos; ?>value="Crear Archivos CITI"><br>
                    <input class="ButtonEliminar" type="button" value="Cerrar Libro" onclick="PreguntarCerrar(); 
  xajax.call('CerrarLibro', {method: 'get', parameters:[Cerrar.value,LibroAnio.value,LibroMes.value]});
  xajax.call('DibujarLibrosCerrados', {method: 'get', parameters:[LibroAnio.value]}); return false;"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="DivLibrosC"></div>
                    </td>
                </tr>
                </table>

            </div>



            <div id="cargando" style="position: absolute; left: 40%; top: 200px;"></div>
            <div class="yui-content">


                <div class="BotonVolver2 form-group col-md-2">
                </div>
                <?php include_once('footer.php'); ?>
            </div>

            <script type="text/javascript">
                function CalcularNeto() {
                    var Haber = Number(document.getElementById('Haber').value);
                    var Bruto = Number(document.getElementById('Bruto').value);
                    var Iva = Number(document.getElementById('Iva').value);
                    var Exento = Number(document.getElementById('Exento').value);
                    var ImpInterno = Number(document.getElementById('ImpInterno').value);
                    var PercIva = Number(document.getElementById('PercIva').value);
                    var RetIB = Number(document.getElementById('RetencionIB').value);
                    var RetGan = Number(document.getElementById('RetencionGan').value);
                    var Neto = Number(Bruto + (Bruto * Iva / 100) + Exento + ImpInterno + PercIva + RetIB + RetGan + Haber);

                    document.getElementById('Neto').value = Number(Neto.toFixed(2));;
                }

                function validacion(f) {
                    // f.value=f.value.replace(",",".")
                    // f.value=parseFloat(f.value.split(",").join("."));
                    f.value = parseFloat(f.value.replace(",", "."));

                }

                function esEntero(num1) {
                    var dig;
                    for (var x = 0; x < num1.length; x++) {
                        dig = num1.charAt(x);
                        if (dig < "0" || dig > "9") return false;
                    }
                    return true;
                }

                function soloNumero(vnum, nent, nfra) {
                    if (event.keyCode !== 46 && ((event.keyCode < 48) || (event.keyCode > 57))) return false;
                    if (event.keyCode == 46 && (vnum.indexOf(".") !== -1)) return false;
                    if (event.keyCode !== 46 && vnum.length >= nent && vnum.indexOf(".") == -1) return false;
                    var auxn = vnum.split(".");
                    if (auxn[1] && auxn[1].length >= nfra) return false;
                    return true;
                }

                //*** Este Codigo permite Validar que sea un campo Numerico
                function Solo_Numerico(variable) {
                    Numer = parseFloat(variable);
                    if (isNaN(Numer)) {
                        return "";
                    }
                    return Numer;
                }

                function ValNumero(Control) {
                    Control.value = Solo_Numerico(Control.value);
                }
                //*** Fin del Codigo para Validar que sea un campo Numerico



                function getKeyCode(e) {
                    e = (window.event) ? event : e;
                    intKey = (e.keyCode) ? e.keyCode : e.charCode;
                    return intKey;
                }

                function Preguntar() {
                    Respuesta = confirm("Seguro que quiere eliminar?");
                    if (Respuesta) {
                        document.getElementById('Borrar').value = 1;
                    }
                }

                function PreguntarCerrar() {
                    Respuesta = confirm("Seguro que quiere CERRAR el libro?");
                    if (Respuesta) {
                        document.getElementById('Cerrar').value = 1;
                    }
                }
            </script>

    </form>


    <script>
        (function() {
            var tabView = YAHOO.widget.TabView('demo');

            YAHOO.log("The example has finished loading; as you interact with it, you'll see log messages appearing here.", "info", "example");
        })();
    </script>

    </body>

    </html>

    <script type="text/javascript" src="../../sistema/bootstrap/librerias/rto1_78.js"></script>
    <!-- <script type="text/javascript" src="http://l.yimg.com/d/lib/rt/rto1_78.js"></script> -->
    <script>
        var rt_page = "792404224:FRTMA";
        var rt_ip = "190.113.129.12";
        if ("function" == typeof(rt_AddVar)) {
            rt_AddVar("ys", escape("F04C9345"));
            rt_AddVar("cr", escape("41xC54zZn9T"));
            rt_AddVar("sg", escape("/SIG=13n6nr41i3tbvl1d66ktf9&b=4&d=nU4tQkNpYFYMrN3CObnGfzjWNWBgk94Yk7xy&s=f0&i=ySTaqLFCBDlgVRVuZXoP/1287691342/190.113.129.12/F04C9345"));
            rt_AddVar("yd", escape("633095664"));
        }
    </script>
    <script language=javascript>
        if (window.yzq_d == null) window.yzq_d = new Object();
        window.yzq_d['tGuDH0wNPRg-'] = '&U=13e2mp49r%2fN%3dtGuDH0wNPRg-%2fC%3d289534.9603437.10326224.9298098%2fD%3dFOOT%2fB%3d4123617%2fV%3d1';
    </script>
    <script language=javascript>
        if (window.yzq_p == null) document.write("<scr" + "ipt language=javascript src=../../sistema/bootstrap/librerias/bc_2.0.4.js></scr" + "ipt>");
    </script>
    <script language=javascript>
        if (window.yzq_p) yzq_p('P=GxX8I0WTTNI.r_ULTGp16RBhvnGBDEzAnE4AA9kr&T=17smmmg4n%2fX%3d1287691342%2fE%3d792404224%2fR%3ddev_net%2fK%3d5%2fV%3d1.1%2fW%3dJ%2fY%3dYAHOO%2fF%3d1443549700%2fH%3dc2VydmVJZD0iR3hYOEkwV1RUTkkucl9VTFRHcDE2UkJodm5HQkRFekFuRtrBQTlrciIgc2l0ZUlkPSI0NDY1NTUxIiB0U3RtcD0iMTI4NzY5MTM0MjI2MjY2NSIg%2fS%3d1%2fJ%3dF04C9345');
        if (window.yzq_s) yzq_s();
    </script>
</center>