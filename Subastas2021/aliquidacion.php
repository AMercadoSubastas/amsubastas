<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors','Yes');

require_once('Connections/amercado.php'); 
include_once "funcion_mysqli_result.php";
mysqli_select_db($amercado, $database_amercado);

if (isset($_POST['remate']))
	$rematenum = $_POST['remate'];
else
	if (isset($_GET['remate']))
		$rematenum = $_GET['remate'];
if (isset($_POST['fecha_desde']))
	$f_desde = $_POST['fecha_desde'];
else
	if (isset($_GET['fecha_desde']))
		$f_desde = $_GET['fecha_desde'];
if (isset($_POST['fecha_hasta']))
	$f_hasta = $_POST['fecha_hasta'];
else
	if (isset($_GET['fecha_hasta']))
		$f_hasta = $_GET['fecha_hasta'];
//echo "FECHA DESDE = ".$f_desde."  FECHA HASTA = ".$f_hasta."  REMATE =  ".$rematenum."  -  ";
$f_desde = substr($f_desde,6,4)."-".substr($f_desde,3,2)."-".substr($f_desde,0,2);
$f_hasta = substr($f_hasta,6,4)."-".substr($f_hasta,3,2)."-".substr($f_hasta,0,2);

//echo "FECHA DESDE 2 = ".$f_desde."  FECHA HASTA 2 = ".$f_hasta."  REMATE 2 =  ".$rematenum."  -  ";

$cod_usuario = $_SESSION['id'];
validoUsu($cod_usuario, $amercado);

$clientes = 0;
$fecha_hoy = date("Y-m-d");

$nro = 2;
$query_ser = "SELECT * FROM series WHERE  codnum ='$nro'";
$ser       = mysqli_query($amercado, $query_ser) or die("ERROR LEYENDO SERIES 1: ".$query_ser." - ");

$row_ser = mysqli_fetch_array($ser, MYSQLI_BOTH);
$fechatope_ser = $row_ser['fechatope'];
$nrotope_ser = $row_ser['nrohasta'];
$nroact_ser = $row_ser['nroact'];
if ($fechatope_ser < $fecha_hoy) {
    echo '<script language="javascript">alertaError("EL CAI DEL FORMULARIO DE LIQUIDACION A0006 VENCIO EL ".$fechatope_ser.")</script>';
    exit;
}
if ($nrotope_ser - 20 <= ($nroact_ser)) {
    echo '<script language="javascript">alertaError("EL TALONARIO DE LIQUIDACION A0006 TIENE MENOS DE 20 FORMULARIOS")</script>' ;
   
}

$nro = 13;
$query_ser = "SELECT * FROM series WHERE  codnum ='$nro'";
$ser       = mysqli_query($amercado, $query_ser) or die("ERROR LEYENDO SERIES 2: ".$query_ser." - ");

$row_ser = mysqli_fetch_array($ser, MYSQLI_BOTH);
$fechatope_ser = $row_ser['fechatope'];
$nrotope_ser = $row_ser['nrohasta'];
$nroact_ser = $row_ser['nroact'];
if ($fechatope_ser < $fecha_hoy) {
    echo '<script language="javascript">alertaError("EL CAI DEL FORMULARIO DE LIQUIDACION B0006 VENCIO EL ".$fechatope_ser."")';
    
}
if ($nrotope_ser-20 <= ($nroact_ser)) {
    echo '<script language="javascript">alertaError("EL TALONARIO DE LIQUIDACION B0006 TIENE MENOS DE 20 FORMULARIOS")';
    
}

mysqli_select_db($amercado, $database_amercado);
$query_Recordset1 = "SELECT * FROM rubros";
$Recordset1 = mysqli_query($amercado, $query_Recordset1) or die("ERROR LEYENDO RUBROS");
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

// LEO EL USUARIO
mysqli_select_db($amercado, $database_amercado);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
	$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$tipo_de_iva = 1;
$total_remate = 0.0;
$netonograv = 0.00;
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "liquidacion")) { 
	// SI YA GRABE ENTRO ACA ===============================================
	$fecha_liquidacion = GetSQLValueString($_POST['fecha_liq'], "date");
	$fecha_liquidacion = substr($fecha_liquidacion,7,4)."-".substr($fecha_liquidacion,4,2)."-".substr($fecha_liquidacion,1,2);
	$tcomp = $_POST['tcomp'];
	$serie = $_POST['serie'];
	$liquidacion = $_POST['liquidacion'];
	if ($liquidacion<10) {
		$doc ="0000000".$liquidacion;
	}
	if ($liquidacion<100 and $liquidacion>9) {
		$doc ="000000".$liquidacion;
	}
	if ($liquidacion<1000 and $liquidacion>99) {
		$doc ="00000".$liquidacion;
	}
	if ($liquidacion<10000 and $liquidacion>999) {
		$doc ="0000".$liquidacion;
	}
	if ($liquidacion<100000 and $liquidacion>9999) {
		$doc ="000".$liquidacion;
	}
	if ($tcomp==3 and $serie==2) {
		$nrodoc = 'A0006-'.$doc;

	} 
	else {
		$nrodoc = 'B0006-'.$doc;
	}
	//if (isset($_POST['usuario']))
	//	$usuario = $_POST['usuario'];
	//else
	//	$usuario = $_GET['usuario'];
    /*
	$usuario = "\"".$usuario."\"";
    $query_usuarios = sprintf("SELECT * FROM usuarios WHERE usuario = %s",$usuario);
    //echo "QUERY = ".$query_usuarios."   ";
    $res_usuarios = mysqli_query($amercado, $query_usuarios) or  die("ERROR LEYENDO USUARIOS");
    $row_usuarios = mysqli_fetch_assoc($res_usuarios);
    */
    //$cod_usuario = 1; //$row_usuarios['codnum'];

	$insertSQL = sprintf("INSERT INTO liquidacion (tcomp, serie, cliente, codrem, fecharem, totremate, totneto1, totiva21, subtot1, totneto2, totiva105, subtot2, totacuenta, totgastos, totvarios, saldoafav , ncomp ,  fechaliq , codpais , codprov , codloc , rubro ,nrodoc, usuario, usuarioultmod, fecultmod) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s , '$fecha_liquidacion' , %s, %s, %s,%s ,'$nrodoc', %s, %s, '$fecha_liquidacion')",
                       	GetSQLValueString($_POST['tcomp'], "int"),
                       	GetSQLValueString($_POST['serie'], "int"),
                       	GetSQLValueString($_POST['num_cliente'], "int"),
                       	GetSQLValueString($_POST['remate_num'], "int"),
                       	GetSQLValueString($_POST['fecha_remate'], "date"),
                       	GetSQLValueString($_POST['importe_total'], "double"),
                       	GetSQLValueString($_POST['neto21'], "double"),
                       	GetSQLValueString($_POST['iva21'], "double"),
                       	GetSQLValueString($_POST['total21'], "double"),
                       	GetSQLValueString($_POST['neto105'], "double"),
                       	GetSQLValueString($_POST['iva105'], "double"),
                       	GetSQLValueString($_POST['total105'], "double"),
                       	GetSQLValueString($_POST['acuenta'], "double"),
                       	GetSQLValueString($_POST['gastos_autor'], "double"),
                       	is_null($netonograv) ? 0 : $netonograv, //GetSQLValueString($_POST['otros_gastos'], "double"),
                       	GetSQLValueString($_POST['total_general'], "double"),
						GetSQLValueString($_POST['liquidacion'], "int"),
						GetSQLValueString($_POST['codpais'], "int"),
						GetSQLValueString($_POST['codprov'], "int"),
						GetSQLValueString($_POST['codloc'], "int"),
						GetSQLValueString($_POST['rubro'], "int"),
						is_null($cod_usuario) ? 1 : $cod_usuario,
						is_null($cod_usuario) ? 1 : $cod_usuario);
	mysqli_select_db($amercado, $database_amercado);
	$numero_serie = GetSQLValueString($_POST['serie'], "int");
	$Result1 = mysqli_query($amercado, $insertSQL) or die("ERROR GRABANDO LIQUIDACION");
	$liquid_num = GetSQLValueString($_POST['liquidacion'], "int");
	$actualiza = "UPDATE series SET nroact='$liquid_num' WHERE codnum='$numero_serie'";
   
	$actualiza_serie = mysqli_query($amercado, $actualiza) or die("ERROR ACTUALIZANDO LA SERIE");
	$actualizafac = sprintf("UPDATE cabfac SET tcompsal=%s , seriesal=%s  , ncompsal = %s  WHERE codrem=%s ",
                      	GetSQLValueString($_POST['tcomp'], "int"),
                      	GetSQLValueString($_POST['serie'], "int"),
						GetSQLValueString($_POST['liquidacion'], "int"),
						GetSQLValueString($_POST['remate_num'], "int"));
	$actualiza_fac = mysqli_query($amercado, $actualizafac) or die("ERROR ACTUALIZANDO CABFAC");	

	// Actualizo detalle factura HAY QUE AGREGAR NCRED Y NDEB
	$actualizadet = sprintf("UPDATE detfac  SET tcomsal=%s , seriesal=%s  , ncompsal = %s  WHERE (codrem=%s AND (concafac IS NULL OR concafac = 0) AND codlote IS NOT NULL)",
                       	GetSQLValueString($_POST['tcomp'], "int"),
                       	GetSQLValueString($_POST['serie'], "int"),
						GetSQLValueString($_POST['liquidacion'], "int"),
						GetSQLValueString($_POST['remate_num'], "int"));
	$actualiza_det = mysqli_query($amercado, $actualizadet ) or die("ERROR ACTUALIZANDO DETFAC");
	// fin actualizacion

	$insertGoTo = "liquidacion_ok.php";
	if (isset($_SERVER['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    	$insertGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $insertGoTo));
}


if(isset($_POST['remate']) || isset($_GET['remate'])){
	if (isset($_POST['remate']))
	$rematenum = $_POST['remate'];
else
	if (isset($_GET['remate']))
		$rematenum = $_GET['remate'];
if (isset($_POST['fecha_desde']))
	$f_desde = $_POST['fecha_desde'];
else
	if (isset($_GET['fecha_desde']))
		$f_desde = $_GET['fecha_desde'];
if (isset($_POST['fecha_hasta']))
	$f_hasta = $_POST['fecha_hasta'];
else
	if (isset($_GET['fecha_hasta']))
		$f_hasta = $_GET['fecha_hasta'];
//echo "FECHA DESDE = ".$f_desde."  FECHA HASTA = ".$f_hasta."  REMATE =  ".$rematenum."  -  ";
$f_desde = substr($f_desde,6,4)."-".substr($f_desde,3,2)."-".substr($f_desde,0,2);
$f_hasta = substr($f_hasta,6,4)."-".substr($f_hasta,3,2)."-".substr($f_hasta,0,2);

//echo "FECHA DESDE 2 = ".$f_desde."  FECHA HASTA 2 = ".$f_hasta."  REMATE 2 =  ".$rematenum."  -  ";

	$res = mysqli_query($amercado, "select * from remates where ncomp='$rematenum'") or die("ERROR ACTUALIZANDO EL REMATE");
	$row_res = mysqli_fetch_assoc($res);
	$clientes = $row_res['codcli'];
	$direccion = $row_res['direccion'] ;
	$codpais = $row_res['codpais'] ;
	$codprov = $row_res['codprov'] ;
	$codloc = $row_res['codloc'] ;
	$fremate = $row_res['fecreal'] ;
	
	$fecha_remate = substr($fremate,8,2).'-'.substr($fremate,5,2).'-'.substr($fremate,0,4);
	//$iva_21 = mysqli_query($amercado, "select * from impuestos") or die("ERROR LEYENDO IMPUESTOS");
	$porcen_iva = 0.21; //(mysqli_result($iva_21,0,1)/100) ;

	//================================ DESDE ACA =====================================
	$query_cliente = "SELECT * FROM entidades  WHERE entidades.codnum ='$clientes'";

	$cliente_t     = mysqli_query($amercado, $query_cliente) or die("ERROR LEYENDO CLIENTE");
	$row_cliente   = mysqli_fetch_assoc($cliente_t);
	$totalRows_cliente = mysqli_num_rows($cliente_t);
	$nom_clientes  = $row_cliente['codnum'];
	$tipo_de_iva   = $row_cliente['tipoiva'];
	//echo "nom_clientes = ".$nom_clientes."  tipo_de_iva = ".$tipo_de_iva." - ";
	if ($tipo_de_iva==1) {
		$tserie       = 2 ;
		$tcomprobante = 3 ;
		$numero       = 2 ; 
	} else {
		$tserie       = 13 ;
		$tcomprobante = 31 ;
		$numero       = 13 ;
	}
	//=================================== HASTA ACA ======================================	
	
	// ACA SUMO TODAS LAS FACTURAS Y NDEB QUE AFECTAN LA LIQUIDACION
	
    $res1 ="SELECT SUM( `totneto105` ) , SUM( `totiva105` ) , SUM( `totneto105` + `totiva105` ) as total_105 , SUM( `totneto21` ) ,SUM( `totneto21` *'$porcen_iva' ) ,  SUM( (`totneto21`  * '$porcen_iva') + `totneto21`)  as total_21 FROM `cabfac` WHERE `codrem` = '$rematenum' AND `fecreg` BETWEEN '$f_desde' AND '$f_hasta' AND `tcomp`!='125'  AND `tcomp`!='126' AND `tcomp`!='119'  AND `tcomp`!='120'  AND `tcomp`!='122'    AND `tcomp`!='123'  AND `tcomp`!='127'   AND `tcomp`!='133'  AND `tcomp`!='134'  AND `tcomp`!='121'  AND `tcomp`!='135'  AND `tcomp`!='137'   AND `tcomp`!='103' AND `en_liquid` = 1" ; 
    //echo "res1 = ".$res1." ";
    $query_remate = mysqli_query($amercado, $res1) or die("ERROR LEYENDO CABFAC ".$res1." ");
    $totalRows_remate = mysqli_num_rows($query_remate);
    $rows_remate = mysqli_fetch_assoc($query_remate);
	
	//echo "RES 1 = ".$res1." - ";
    // ACA SUMO LAS FACTURAS DE EXPORTACION
	
    if ($tipo_de_iva== 1) {
		$res11 ="SELECT SUM( `totneto105` ) , SUM( `totiva105` ) , SUM( `totneto105` + `totiva105` ) as total_105 , SUM( `totneto21` ) ,SUM( `totiva21`  ) ,  SUM( `totneto21`)  as total_21 FROM `cabfac` WHERE (`codrem` = '$rematenum' AND  `fecreg` BETWEEN '$f_desde' AND '$f_hasta' AND `tcomp`='103' AND `en_liquid` = 1)" ; 
		
		$query_remate11 = mysqli_query($amercado, $res11) or die("ERROR LEYENDO CABFAC 6");
		$totalRows_remate11 = mysqli_num_rows($query_remate11); 
	}
	else {
		$res11 ="SELECT SUM( `totneto105` ) , SUM( `totiva105` ) , SUM( `totneto105` + `totiva105` ) as total_105 , SUM( `totneto21` ) ,SUM( `totiva21`  ) ,  SUM( `totneto21`)  as total_21 FROM `cabfac` WHERE (`codrem` = '$rematenum' AND `fecreg` BETWEEN '$f_desde' AND '$f_hasta' AND `tcomp`='103' AND `en_liquid` = 1)" ; 
		
		$query_remate11 = mysqli_query($amercado, $res11) or die("ERROR LEYENDO CABFAC 7");
		$totalRows_remate11 = mysqli_num_rows($query_remate11); 

	}
	
	// ACA SUMO TODAS LAS NCRED QUE AFECTAN LA LIQUIDACION
	
    $res2 ="SELECT SUM( `totneto105` ) , SUM( `totiva105` ) , SUM( `totneto105` + `totiva105` ) as total_105_2 , SUM( `totneto21` ) , SUM( `totneto21` *'$porcen_iva' ) , SUM( `totneto21` *'$porcen_iva' + `totneto21`)  as total_21_2 FROM `cabfac` WHERE (`codrem` = '$rematenum' AND (`tcomp`='119' OR `tcomp`='120' OR `tcomp`='121' OR `tcomp`='135' OR `tcomp`='137' OR `tcomp`='144') AND `fecreg` BETWEEN '$f_desde' AND '$f_hasta' AND `en_liquid` = 1 AND `cliente` != '$clientes')" ;
    $query_remate2 = mysqli_query($amercado, $res2) or die("ERROR LEYENDO CABFAC 4");
    $totalRows_remate2 = mysqli_num_rows($query_remate2);
    $rows_remate2 = mysqli_fetch_assoc($query_remate2);
	
	//echo "RES 2 = ".$res2." - ";
	// ACA SACO  LAS TASAS ADM DE LAS NCRED QUE AFECTAN LA LIQUIDACION
    
	
    $res3 ="SELECT SUM(`detfac`.`neto`) , SUM(`detfac`.`neto` * 0.21), SUM(`detfac`.`neto` * 1.21) FROM `detfac`, `cabfac` WHERE (`detfac`.`codrem` = '$rematenum' AND (`detfac`.`tcomp`='119' OR `detfac`.`tcomp`='120' OR `detfac`.`tcomp`='121' OR `detfac`.`tcomp`='135' OR `detfac`.`tcomp`='144')  AND `fecreg` BETWEEN '$f_desde' AND '$f_hasta' AND `detfac`.`tcomp` = `cabfac`.`tcomp` AND `detfac`.`ncomp` = `cabfac`.`ncomp` AND `cabfac`.`en_liquid` = 1  AND `detfac`.`concafac` = '18')";
    $query_remate3 = mysqli_query($amercado, $res3) or die("ERROR LEYENDO DETFAC 5");
    $totalRows_remate3 = mysqli_num_rows($query_remate3);
    $rows_remate3 = mysqli_fetch_assoc($query_remate3);

    
	// ========================================================================

	$totneto105 = mysqli_result($query_remate,0,0) - mysqli_result($query_remate2,0,0);
	
	$iva105 = mysqli_result($query_remate,0,1)  - mysqli_result($query_remate2,0,1);
	$total_105 = mysqli_result($query_remate,0,2)  - mysqli_result($query_remate2,0,2);
	$totneto21 = mysqli_result($query_remate,0,3) - mysqli_result($query_remate2,0,3) + mysqli_result($query_remate11,0,3);
	
	
	$total_21 = mysqli_result($query_remate,0,5)  - mysqli_result($query_remate2,0,5) + mysqli_result($query_remate11,0,5);// + mysqli_result($query_remate3,0,2);
	$iva21 = mysqli_result($query_remate,0,4)  - mysqli_result($query_remate2,0,4);// + mysqli_result($query_remate3,0,1);
	
    $netonograv = 0.00;
    //$netonograv = mysqli_result($query_remate11,0,3);
	//echo "mysqli_result(query_remate,0,0) = ".mysqli_result($query_remate,0,0)." mysqli_result(query_remate2,0,1) = ".mysqli_result($query_remate2,0,1)." _ ";
    //echo "IVA_105 = ".$iva105." TOTAL_105 = ".$total_105." TOTNETO21 = ".$totneto21." IVA21 = ".$iva21." - ";

	
	//echo "TOTAL 21 = ".$total_21." - ";
	/*
    $totneto21 = round($totneto21,2);
	$iva21 = round($iva21,2);
	$iva105 = round($iva105,2);
	$total_21 = round($total_21,2);

	$total_105 = round($total_105,2);
    $netonograv = round($netonograv,2);
    */
	$total_remate = $total_105 + $total_21 + $netonograv; //round($total_105 + $total_21 + $netonograv);
	
  
}
mysqli_select_db($amercado, $database_amercado);
$query_cheques_total = "SELECT SUM(cartvalores.importe) AS cart_importe FROM cartvalores WHERE  cartvalores.estado = 'S' AND cartvalores.codrem ='$rematenum' AND fechapago BETWEEN '$f_desde' AND '$f_hasta'" ;

//echo "query_cheques_total = ".$query_cheques_total."  ";
$cheques_total       = mysqli_query($amercado, $query_cheques_total) or die("ERROR LEYENDO CARTVALORES");
$row_cheques_total   = mysqli_fetch_assoc($cheques_total);
//if (isset($row_cheques_total['SUM( cartvalores.importe)'])) {
  $totcheques1  =  mysqli_result($cheques_total,0,0) ; //$row_cheques_total['cart_importe'];
  $totalcheques = mysqli_result($cheques_total, 0, 0) ?: 0;

	//echo " totalcheques = ".$totalcheques." ";
/*
}
else {
  $totcheques1  = 0;//number_format ($row_cheques_total['SUM( cartvalores.importe)'] , 2 , ',' , '.');
  $totcheques   = 0;//$row_cheques_total['SUM( cartvalores.importe)'] ;
  $totalcheques = 0;//$totcheques; //round($totcheques,2, PHP_ROUND_HALF_UP);
}
*/
//echo "CHEQUES = ".$row_cheques_total['SUM( cartvalores.importe)']."   ";
if ($tipo_de_iva== 1) {
	$query_factura_total     = sprintf("SELECT SUM( cabfac.totbruto) FROM cabfac WHERE (cabfac.tcomp = 125 || cabfac.tcomp = 126 || cabfac.tcomp = 122 || cabfac.tcomp = 127 || cabfac.tcomp = 133 || cabfac.tcomp = 134 || cabfac.tcomp = 136 || cabfac.tcomp = 143) AND `fecreg` BETWEEN '$f_desde' AND '$f_hasta' AND cabfac.estado != 'A' AND cabfac.codrem ='$rematenum' AND cabfac.en_liquid = 1 AND cabfac.cliente = %s", $clientes);
	$factura_total           = mysqli_query($amercado, $query_factura_total) or die("ERROR LEYENDO CABFAC 125, 126,122,127 ".$query_factura_total);
	$row_factura_total 	     = mysqli_fetch_assoc($factura_total);
	$totalRows_factura_total = mysqli_num_rows($factura_total);
	if (isset($row_factura_total['SUM( cabfac.totbruto)'])) {
		$totfactura1             = $row_factura_total['SUM( cabfac.totbruto)'];
		$totfactura              = $row_factura_total['SUM( cabfac.totbruto)'];
	}
	else {
		$totfactura1             = 0; //number_format ($row_factura_total['SUM( cabfac.totbruto)'] , 2 , "," , ".");
		$totfactura              = 0; //$row_factura_total['SUM( cabfac.totbruto)'];
	}
	
    //Veo si tiene alguna Nota de Credito
    $query_nc_total     = sprintf("SELECT SUM( cabfac.totbruto) FROM cabfac WHERE (cabfac.tcomp = 119 || cabfac.tcomp = 120 || cabfac.tcomp = 121 || cabfac.tcomp = 135 || `tcomp`='144') AND `fecreg` BETWEEN '$f_desde' AND '$f_hasta' AND cabfac.codrem ='$rematenum' AND cabfac.en_liquid = 1 AND cabfac.cliente = %s", $clientes);
	$nc_total           = mysqli_query($amercado, $query_nc_total) or die("ERROR LEYENDO CABFAC 119 , 120 , 121 y 135");
	$row_nc_total 	     = mysqli_fetch_assoc($nc_total);
	$totalRows_nc_total = mysqli_num_rows($nc_total);
	if (isset($row_nc_total['SUM( cabfac.totbruto)'])) {
        $totnc1             = $row_nc_total['SUM( cabfac.totbruto)'];
        $totnc              = $row_nc_total['SUM( cabfac.totbruto)'];
        $totfactura         = $totfactura - $totnc;
        $totfactura         = round($totfactura,2);
	}
	else {
		$totnc1             = 0;//number_format ($row_nc_total['SUM( cabfac.totbruto)'] , 2 , "," , ".");
        $totnc              = 0;//$row_nc_total['SUM( cabfac.totbruto)'];
        $totfactura         = $totfactura - $totnc;
        $totfactura         = round($totfactura,2);
	}
}
else {
	$query_factura_total     = sprintf("SELECT SUM( cabfac.totbruto) FROM cabfac WHERE (cabfac.tcomp = 125 || cabfac.tcomp = 126 || cabfac.tcomp = 122 || cabfac.tcomp = 127 || cabfac.tcomp = 133 || cabfac.tcomp = 134 || cabfac.tcomp = 136 || cabfac.tcomp = 143)  AND `fecreg` BETWEEN '$f_desde' AND '$f_hasta' AND cabfac.codrem ='$rematenum' AND cabfac.en_liquid = 1 AND cabfac.cliente = %s", $clientes);
	$factura_total           = mysqli_query($amercado, $query_factura_total) or die("ERROR LEYENDO CABFAC 125, 126,122,127 ".$query_factura_total);
	$row_factura_total 	     = mysqli_fetch_assoc($factura_total);
	$totalRows_factura_total = mysqli_num_rows($factura_total);
	if (isset($row_factura_total['SUM( cabfac.totbruto)'])) {
		$totfactura1             = $row_factura_total['SUM( cabfac.totbruto)'];
		$totfactura              = $row_factura_total['SUM( cabfac.totbruto)'];
	}
	else {
		$totfactura1             = 0; //number_format ($row_factura_total['SUM( cabfac.totbruto)'] , 2 , "," , ".");
		$totfactura              = 0; //$row_factura_total['SUM( cabfac.totbruto)'];
	}
	
    //Veo si tiene alguna Nota de Credito
    $query_nc_total     = sprintf("SELECT SUM( cabfac.totbruto) FROM cabfac WHERE (cabfac.tcomp = 119 || cabfac.tcomp = 120 || cabfac.tcomp = 121 || cabfac.tcomp = 135 || `tcomp`='144') AND cabfac.codrem ='$rematenum'  AND `fecreg` BETWEEN '$f_desde' AND '$f_hasta' AND cabfac.en_liquid = 1 AND cabfac.cliente = %s", $clientes);
	$nc_total           = mysqli_query($amercado, $query_nc_total) or die("ERROR LEYENDO CABFAC 119 , 120 , 121 y 135");
	$row_nc_total 	     = mysqli_fetch_assoc($nc_total);
	$totalRows_nc_total = mysqli_num_rows($nc_total);
	if (isset($row_nc_total['SUM( cabfac.totbruto)'])) {
        $totnc1             = $row_nc_total['SUM( cabfac.totbruto)'];
        $totnc              = $row_nc_total['SUM( cabfac.totbruto)'];
        $totfactura         = $totfactura - $totnc;
        $totfactura         = round($totfactura,2);
	}
	else {
		$totnc1             = 0;//number_format ($row_nc_total['SUM( cabfac.totbruto)'] , 2 , "," , ".");
        $totnc              = 0;//$row_nc_total['SUM( cabfac.totbruto)'];
        $totfactura         = $totfactura - $totnc;
        $totfactura         = round($totfactura,2);
	}

}

// ACA LE PONGO UN ECHO 
//echo "  Total Factura = ".$totfactura1."<br>";

$totalgastos   = $totalcheques + $totfactura;
//echo "Total Gastos".$totfactura1." Total cheques = ".$totalcheques." total factura = ".$totfactura."  ";

$total_general = round(($total_remate - $totalgastos),2); //$total_remate - $totalgastos ; //round(($total_remate-$totalgastos),2);
//echo "TotalGeneral = ".$total_general."  ";

$cliente_t = 0;
$query_cliente = "SELECT * FROM entidades  WHERE entidades.codnum ='$clientes'";

$cliente_t     = mysqli_query($amercado, $query_cliente) or die("ERROR LEYENDO CLIENTE");
$row_cliente   = mysqli_fetch_assoc($cliente_t);
$totalRows_cliente = mysqli_num_rows($cliente_t);
$nom_clientes  = $row_cliente['razsoc']; //mysqli_result($cliente_t,0,1) ;
$tipo_de_iva   = $row_cliente['tipoiva'];//mysqli_result($cliente_t,0,12);

if ($tipo_de_iva==1) {
	$tserie       = 2 ;
	$tcomprobante = 3 ;
	$numero       = 2 ; 
} else {
	$tserie       = 13 ;
	$tcomprobante = 31 ;
	$numero       = 13 ;
}


$query_serie = "SELECT * FROM series WHERE  codnum ='$numero'";
$serieq       = mysqli_query($amercado, $query_serie) or die("ERROR LEYENDO SERIES 367");

$totalRows_serie = mysqli_num_rows($serieq);

while ($row_serie = mysqli_fetch_array($serieq)) {
	$liquidacion = 1+$row_serie['nroact'];
}

$query_impuesto     = "SELECT * FROM impuestos";
$impuesto           = mysqli_query($amercado, $query_impuesto) or die("ERROR LEYENDO IMPUESTOS");
$row_impuesto       = mysqli_fetch_assoc($impuesto);
$totalRows_impuesto = mysqli_num_rows($impuesto);
$iva_21_desc        = "21 %"; //mysqli_result($impuesto,0,2);
$iva_21_porcen      = 21; //mysqli_result($impuesto,0,1);


$iva_15_desc        = "10,5 %"; //mysqli_result($impuesto,1,2);
$iva_15_porcen      = 10.5; //mysqli_result($impuesto,1,1);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="v_estilo_factura.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../js/ajax.js"></script>

<script type="text/javascript" src="../js/dhtmlSuite-common.js"></script>
<script type="text/javascript" src="../js/dhtmlSuite-dynamicContent.js"></script>
<script type="text/javascript" src="../js/dhtmlSuite-windowWidget.js"></script>
<script type="text/javascript" src="../js/dhtmlSuite-dragDropSimple.js"></script>
<script type="text/javascript" src="../js/dhtmlSuite-resize.js"></script>
<SCRIPT type="text/javascript">
var DHTML_SUITE_THEME_FOLDER = 'themes/';
</SCRIPT> 
<SCRIPT type="text/javascript">
var DHTML_SUITE_THEME = 'zune';
</SCRIPT> 
<script type="text/javascript" src="AJAX/ajax.js"></script>
</script>
<form name="cheque_tercero" action="cheques_terceros_1.php" method="post">
      <input name="tcomp_nombre_a" type="hidden">
      <input name="tcomp_a" type="hidden">
      <input name="serie_nombre_a" type="hidden">
      <input name="serie_a" type="hidden">
      <input name="liquidacion_a" type="hidden">
      <input name="cliente_a" type="hidden">
      <input name="remate_num_a" type="hidden">
      <input name="fecha_remate_a" type="hidden">
      <input name="lugar_remate_a" type="hidden">
      <input name="importe_total_a" type="hidden">
      <input name="neto105_a"  type="hidden">
      <input name="iva105_a" type="hidden">
      <input name="total105_a" type="hidden">
      <input name="neto21_a" type="hidden">
      <input name="iva21_a" type="hidden">
      <input name="total21_a" type="hidden">
      <input name="acuenta_a" type="hidden">
      <input name="gastos_autor_a" type="hidden">
      <input name="otros_gastos_a" type="hidden">
      <input name="total_resta_a" type="hidden">
      <input name="total_gene_a" type="hidden">
</form>
 
 <form name="gastos_autorizados" action="gastos_autorizados.php">
      <input name="tcomp_b" type="hidden">
      <input name="serie_b" type="hidden">
      <input name="liquidacion_b" type="hidden">
      <input name="remate_num_b" type="hidden">
      <input name="acuenta_b" type="hidden">
      <input name="gastos_autor_b" type="hidden">
      <input name="otros_gastos_b" type="hidden">
      <input name="total_resta_b" type="hidden">
      <input name="total_gene_b" type="hidden">
 </form>
 

 
 <script language="javascript">
 
function manda_cheque_terceros(form)
{
 

	cheque_tercero.tcomp_nombre_a.value = liquidacion.tcomp_nombre.value ;
	cheque_tercero.tcomp_a.value = liquidacion.tcomp.value ;
	cheque_tercero.serie_nombre_a.value = liquidacion.serie_nombre.value ;
	cheque_tercero.serie_a.value = liquidacion.serie.value ;
	cheque_tercero.liquidacion_a.value = liquidacion.liquidacion.value ;
	cheque_tercero.cliente_a.value = liquidacion.cliente.value ;
	cheque_tercero.fecha_remate_a.value = liquidacion.fecha_remate.value ;

	cheque_tercero.remate_num_a.value = liquidacion.remate_num.value ;
	cheque_tercero.lugar_remate_a.value = liquidacion.lugar_remate.value ;
	cheque_tercero.importe_total_a.value = liquidacion.importe_total.value ;
	cheque_tercero.neto105_a.value = liquidacion.neto105.value ;
	cheque_tercero.iva105_a.value = liquidacion.iva105.value ;
	cheque_tercero.neto21_a.value = liquidacion.neto21.value ;
	cheque_tercero.iva21_a.value = liquidacion.iva21.value ;
	cheque_tercero.total105_a.value = liquidacion.total105.value ;
	cheque_tercero.total21_a.value = liquidacion.total21.value ;
	cheque_tercero.acuenta_a.value = liquidacion.acuenta.value ;
	cheque_tercero.gastos_autor_a.value = liquidacion.gastos_autor.value ;
	cheque_tercero.otros_gastos_a.value = liquidacion.otros_gastos.value ;
	cheque_tercero.total_resta_a.value = liquidacion.total_resta.value ;
	cheque_tercero.total_gene_a.value = liquidacion.total_general.value ;
	//alert(cheque_tercero.cliente_a.value);
	//alert(cheque_tercero.remate_num_a.value);
	cheque_tercero.submit();
 
}

function manda_gastos_autorizados(form)
{

	gastos_autorizados.tcomp_b.value = liquidacion.tcomp.value ;
	gastos_autorizados.serie_b.value = liquidacion.serie.value ;
	gastos_autorizados.liquidacion_b.value = liquidacion.liquidacion.value ;
	gastos_autorizados.remate_num_b.value = liquidacion.remate_num.value ;
	gastos_autorizados.submit();
 
} 

function otros_medios1(form)
{

	otros_medios.tot_general.value = liquidacion.total_general.value ;
	otros_medios.liquidacion.value = liquidacion.liquidacion.value ;
	otros_medios.remate.value = liquidacion.remate_num.value ;
	otros_medios.submit();
 
}
</script>
<script type="text/javascript">
var windowModel = new DHTMLSuite.windowModel();
windowModel.createWindowModelFromMarkUp('myWindow');
var colorWindow = new DHTMLSuite.windowWidget();
colorWindow.setLayoutThemeWindows();
colorWindow.addWindowModel(windowModel);
colorWindow.init();
</script>
<script type="text/javascript">

var ajax = new sack();
var currentClientID=false;
	
function getClientData()
{
	var clientId = document.getElementById('remate_num').value.replace(/[^0-9]/g,'');
	
	if( clientId!=currentClientID){
		currentClientID = clientId
		ajax.onCompletion = showClientData;	// Specify function that will be executed after file has been found
		ajax.runAJAX();		// Execute AJAX function
	}
}
	
function showClientData()
{
	var formObj = document.forms['liquidacion'];
	eval(ajax.response);
}
function initFormEvents()
{
	
	var document.liquidacion.otros_gastos.value = 0;
	//alert (document.liquidacion.otros_gastos.value)
		
}
window.onload = initFormEvents;

</script>
<script language="javascript">


<!--
function gastos (form)
{

	var gastos1 = liquidacion.acuenta.value ;
	//alert (gastos1);
	var gastos2 = liquidacion.gastos_autor.value ;
	//alert (gastos2);
	var total1 = eval(gastos1+('+')+gastos2);
	var totgen =liquidacion.importe_total.value
	//alert (totgen);
	var gastos3 = liquidacion.otros_gastos.value ;
	//alert (gastos3);
	var total = eval(total1+('+')+gastos3);
    // impuesto+('+')+total;
	liquidacion.total_resta.value = total ;
	var total_general = totgen - total  ;
	liquidacion.total_gene.value = total_general ;
 
	//var tot_gen =

}

function otros (form)
{

	var imp_total = liquidacion.importe_total.value;
	var acuenta = liquidacion.acuenta.value;
	var gastos = liquidacion.gastos_autor.value;
	var otros = liquidacion.otros_gastos.value ;
	var total = eval(acuenta+('+')+gastos+('+')+otros);
	var total_gene = eval(imp_total+('-')+total);

 	liquidacion.total_resta.value = total;
 	liquidacion.total_general.value = total_gene;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}
</script> 
<SCRIPT LANGUAGE="JavaScript">
//function cerrar(){
//	window.close();
//	window.open("http://localhost/subastas11man/a_liquid.php","","");
//}

//-->
</script>
<style type="text/css">
.Estilo1 {color: #FFFFFF}
#cheques_tercero {
	position:absolute;
	width:582px;
	height:115px;
	z-index:4;
	visibility: hidden;
	background-color: #00CCFF;
}
#liquidacion1 {
	position:absolute;
	width:640px;
	height:700px;
	z-index:1;
	visibility: visible;
	background-color: #00CCFF;
}
</style>
<script language="javascript" src="cal2.js"></script>
<script language="javascript" src="cal_conf2.js"></script>
</head>
<body>
<form id="liquidacion" name="liquidacion" method="POST" action="<?php echo $editFormAction; ?>">
  <table width="640" border="1" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td><div align="center"><img src="images/liquid_remate.gif" width="400" height="30" /></div></td>
    </tr>
    
    <tr>
      <td bgcolor="#FFFFFF"><table width="640" border="0" cellspacing="1" cellpadding="1">
        <?php if ($tipo_de_iva==1) { ?>
       <tr>
          <td height="20" bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Tipo de comprobante</span> </td>
          <td ><input name="tcomp_nombre" type="text" class="phpmaker" value="LIQUIDACION A" />
          <input name="tcomp" type="hidden" value="3" />          </td>
          <td height="20" bgcolor="#FFFFFF" class="ewTableHeader">&nbsp;Serie</td>
          <td><input name="serie_nombre" type="text" class="phpmaker" value="SERIE DE LIQUIDACION A" /></td><input name="serie" type="hidden" value="2" />
        </tr> <?php } else { ?>
         <tr>
          <td height="20" bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Tipo de comprobante</span> </td>
          <td ><input name="tcomp_nombre" type="text" class="phpmaker" value="LIQUIDACION B" />
          <input name="tcomp" type="hidden" value="31" />          </td>
          <td height="20" bgcolor="#FFFFFF" class="ewTableHeader">&nbsp;Serie</td>
          <td><input name="serie_nombre" type="text" class="phpmaker" value="SERIE DE LIQUIDACION B" /></td><input name="serie" type="hidden" value="13" />
        </tr> 
        
      <?php } ?>
        <tr>
          <td height="20" bgcolor="#FFFFFF" class="ewTableHeader">&nbsp;Num de liquidacion </td>
          <td><input name="liquidacion" type="text"  value="<?php echo $liquidacion ?>" readonly=""/>          </td>
          <td bgcolor="#FFFFFF" class="ewTableHeader">&nbsp;Fecha</td>
          <td><input name="fecha_liq" type="text" size="11" maxlength="11" /><a href="javascript:showCal('Calendar8')"><img src="images/ew_calendar.gif" width="15" height="15" border="0"></td>
        </tr><input name="codpais" type="hidden" size="11" maxlength="11" value="<?php echo $codpais ?>"/>
		<input name="codprov" type="hidden" size="11" maxlength="11" value="<?php echo $codprov ?>"/>
		<input name="codloc" type="hidden" size="11" maxlength="11" value="<?php echo $codloc ?>"/>
        <tr>
          <td height="20" bgcolor="#FFFFFF" class="ewTableHeader">&nbsp;Cliente</td>
          <td><input name="cliente" type="text"  value="<?php echo $nom_clientes ?>" readonly=""/>          </td>
          <td bgcolor="#FFFFFF" class="ewTableHeader" >&nbsp;Rubro</td>
          <td bgcolor="#FFFFFF" class="ewTableHeader" ><select name="rubro">
          <option value="" >[Seleccione un Rubro]</option>
            <?php
            do {  
            ?>
            <option value="<?php echo $row_Recordset1['codnum']?>"><?php echo $row_Recordset1['descripcion']?></option>
            <?php
            } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
            $rows = mysqli_num_rows($Recordset1);
            if($rows > 0) {
                mysqli_data_seek($Recordset1, 0);
	            $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
            }
            ?>
              </select></td>
        </tr>
          <input name="num_cliente" type="hidden"  value="<?php echo $clientes ?>" readonly=""/>
          <input name="fecha_remate" type="hidden"  value="<?php echo $fremate ?>" readonly=""/>
        
        <tr>
          <td height="20" bgcolor="#FFFFFF" class="ewTableHeader">&nbsp;Num de Remate</td>
          <td><input name="remate_num" type="text" class="phpmaker" id="remate_num"  value='<?php echo $rematenum ?>' onchange="copia_valor(this.form)"/></td>
          <td height="20" bgcolor="#FFFFFF" class="ewTableHeader">&nbsp;Fecha Remate </td>
          <td><input name="fecha_remate1" type="text" class="phpmaker" size="11" value ="<?php echo  $fecha_remate ?>"/></td>
        </tr>
        <tr>
          <td height="20" bgcolor="#FFFFFF" class="ewTableHeader">&nbsp;Lugar de Remate </td>
          <td colspan="3"><input name="lugar_remate" type="text" size="60" value="<?php echo $direccion  ?>"  /></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td background="images/separador3.gif"></td>
    </tr>
    <tr>
      <td><table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
        <tr>
          <td colspan="3" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
          <?php if ($tipo_de_iva==1) { ?>
            <tr>
              <td width="31%" bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Importe Total del Remate </span></td>
              <td width="69%"><input name="importe_total" type="text" class="phpmaker" value="<?php echo $total_remate ?>"/></td>
            </tr>
        </tr>
          </table></td>
        
        <tr>
          <td width="31%" bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Neto Gravado&nbsp;<?php echo $iva_15_porcen ?>&nbsp;%</span> </td>
          <td width="27%"><input name="neto105" class="phpmaker" type="text" id="neto105" value="<?php echo $totneto105 ?>"/></td>
          <td width="42%" >&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF" ><span class="ewTableHeader">&nbsp;<?php echo $iva_15_desc ?>&nbsp;% </span></td>
          <td  ><input  name="iva105" type="text" class="phpmaker" value="<?php echo $iva105 ?>" /></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Total <?php echo $iva_15_porcen ?>&nbsp;%</span></td>
          <td><input name="total105" type="text" class="phpmaker" value="<?php echo $total_105 ?>" /></td>
          <td><input name="total105_1" type="text" class="phpmaker" value="<?php echo $total_105 ?>"/></td>
        </tr>
        <tr>
          <td width="31%" bgcolor="#FFFFFF" class="ewTableHeader"><span class="Estilo1">&nbsp;Neto Gravado&nbsp;<?php echo $iva_21_porcen ?>&nbsp;%</td>
          <td width="27%"><input name="neto21" type="text" class="phpmaker" value="<?php echo $totneto21 ?>"/></td>
          <td width="42%">&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;<?php echo $iva_21_desc ?>&nbsp;% </span></td>
          <td><input name="iva21" type="text" class="phpmaker" value="<?php echo $iva21 ?>"/></td>
          <td bgcolor="#FFFFFF" class="ewLayout">&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Total <?php echo $iva_21_porcen ?>&nbsp;%</span></td>
          <td><input name="total21" type="text" class="phpmaker" value="<?php echo $total_21 ?>"/></td>
          <td><input name="total21_1" type="text" class="phpmaker" value="<?php echo $total_21 ?>"/></td>
        </tr> <?php }
                 else {
                    $total_remate = $totneto105+$totneto21;
                    $iva105 = 0 ;
                    $iva21 = 0;
                    $total_105 = $totneto105;
                    $total_21  = $totneto21;
                    $total_general = $total_remate - $totalgastos ;
            ?>
		
        <tr>
          <td width="31%" bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Importe Total del Remate </span></td>
          <td width="69%"><input name="importe_total" type="text" class="phpmaker" value="<?php echo $total_remate ?>"/></td>
        </tr>
          </tr>
          </table></td>
        
        <tr>
          <td width="31%" bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Neto Gravado&nbsp;<?php echo $iva_15_porcen ?>&nbsp;%</span> </td>
          <td width="27%"><input name="neto105" type="text" class="phpmaker" id="neto105" value="<?php echo $totneto105 ?>"/></td>
          <td width="42%" >&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF" ><span class="ewTableHeader">&nbsp;<?php echo $iva_15_desc ?>&nbsp;% </span></td>
          <td  ><input  name="iva105" type="text" value="<?php echo $iva105 ?>" /></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Total <?php echo $iva_15_porcen ?>&nbsp;%</span></td>
          <td><input name="total105" type="text" class="phpmaker" value="<?php echo $total_105 ?>" /></td>
          <td><input name="total105_1" type="text" class="phpmaker" value="<?php echo $total_105 ?>"/></td>
        </tr>
        <tr>
          <td width="31%" bgcolor="#FFFFFF" class="ewTableHeader"><span class="Estilo1">&nbsp;Neto Gravado&nbsp;<?php echo $iva_21_porcen ?>&nbsp;%</td>
          <td width="27%"><input name="neto21" type="text" class="phpmaker" value="<?php echo $totneto21 ?>"/></td>
          <td width="42%">&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;<?php echo $iva_21_desc ?>&nbsp;% </span></td>
          <td><input name="iva21" type="text" class="phpmaker" value="<?php echo $iva21 ?>"/></td>
          <td bgcolor="#FFFFFF" class="ewLayout">&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Total <?php echo $iva_21_porcen ?>&nbsp;%</span></td>
          <td><input name="total21" type="text" class="phpmaker" value="<?php echo $total_21 ?>"/></td>
          <td><input name="total21_1" type="text" class="phpmaker" value="<?php echo $total_21 ?>"/></td>
        </tr> 
		
	        <?php } ?>
        <tr>
          <td background="images/separador3.gif" colspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td width="31%" bgcolor="#FFFFFF"><span class="ewTableHeader">&nbsp;Entrega a cuenta &nbsp;</span></td>
          <td width="27%"><input name="acuenta" type="text" class="phpmaker" value="<?php echo $totalcheques ?>" /></td>
          <td width="42%"><img src="images/cheques_tercero_azul.gif" width="210" height="20" onclick="manda_cheque_terceros(this.form)" /></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><span class="Estilo1">&nbsp;</span><span class="ewTableHeader">Gastos autorizados </span></td>
          <td><input name="gastos_autor" type="text" class="phpmaker"  value="<?php echo $totfactura ?>" /></td>
          <td><img src="images/gastos_autorizados.gif" width="154" height="20" onclick="manda_gastos_autorizados(this.form)" /></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><span class="Estilo1">&nbsp;</span><span class="ewTableHeader">Otros conceptos </span></td>
          <td><input name="otros_gastos" type="text" class="phpmaker" onchange="otros(this.form)"value="0" /></td>
          <td><input name="total_resta" type="text" class="phpmaker" value="<?php echo $totalgastos ?>" /></td>
        </tr>
        <tr>
          <td background="images/separador3.gif" colspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td  bgcolor="#FFFFFF"><span class="Estilo1"><span class="ewTableHeader">Saldo a favor del cliente </span></td><td><input name="total_general" type="text" class="phpmaker" value="<?php echo $total_general ?>" /></td>
          <td><a href="otros_gastos_liq.php?tot_general=<?php echo $total_general ?>&&liquidacion=<?php echo $liquidacion ?>&&remate=<?php echo $rematenum ?>&&serie=<?php echo $tserie ?>&&tipocomp=<?php echo $tcomprobante ?>&&cliente=<?php echo $clientes ?>" target="_blank"><img src="images/medios_pago1.gif" width="103" height="19" border="0" ></a></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td><table width="100%" border="1" cellspacing="1" cellpadding="1">
        <tr>
          <td bgcolor="#FFFFFF"><div align="center">
            <input name="Submit" type="submit" bgcolor="#FFFFFF" class="phpmaker"  value="Grabar"/>
            <td height="33" colspan="4" align="center" bgcolor="#FFFFFF"><a href="ALiquid"><img src="images/salir_but.gif" width="55" height="33" border="0" /></a></td>
          </div></td>
          </tr>
      </table></td>
    </tr>

   <?php //</tr>?>
  <?php //</table>?>
  <input type="hidden" name="MM_insert" value="liquidacion">
</form>
</body>
</html>