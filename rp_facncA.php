<?php
// ob_start();
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 'Yes');
require_once('Connections/amercado.php');
//set_time_limit(0); // Para evitar el timeout
define('FPDF_FONTPATH', 'fpdf17/font/');
require('fpdf17/fpdf.php');
require('numaletras.php');

//Agregamos la libreria para generar códigos QR
require "phpqrcode/qrlib.php";

//Declaramos una carpeta temporal para guardar la imagenes generadas
$dir = 'temp/';

//Si no existe la carpeta la creamos
//if (!file_exists($dir))
//    mkdir($dir);


mysqli_select_db($amercado, $database_amercado);
// Leo los parámetros del formulario anterior
$ptcomp = $_GET['ftcomp'];
$pserie = $_GET['fserie'];
$pncomp = $_GET['fncomp'];
if ($ptcomp == 115 || $ptcomp == 125) {
	$query_cabf = sprintf("SELECT * FROM cabfac WHERE  tcomp IN (115,125) AND serie = %s AND ncomp = %s", $pserie, $pncomp);
	$cabefac = mysqli_query($amercado, $query_cabf) or die("Err. Line 29, EL CBTE NO EXISTE O TIENE UN ERROR");
	$row_cabefac = mysqli_fetch_assoc($cabefac);
	$ptcomp        = $row_cabefac["tcomp"];
}
if ($ptcomp == 117 || $ptcomp == 127) {
	$query_cabf = sprintf("SELECT * FROM cabfac WHERE  tcomp IN (117,127) AND serie = %s AND ncomp = %s", $pserie, $pncomp);
	$cabefac = mysqli_query($amercado, $query_cabf) or die("Err. Line 35, EL CBTE NO EXISTE O TIENE UN ERROR");
	$row_cabefac = mysqli_fetch_assoc($cabefac);
	$ptcomp        = $row_cabefac["tcomp"];
}
//echo " TCOMP =   ".$ptcomp."  SERIE =  ".$pserie."  NCOMP =  ".$pncomp."  -  ";
$totalFilas = 0;
// Cambia el estado de la factura 
$actualizaemitido = "UPDATE cabfac SET emitido='1' WHERE tcomp = '$ptcomp' AND ncomp='$pncomp'";
$Result1 = mysqli_query($amercado, $actualizaemitido) or die("Err. Line 43, EL CBTE NO EXISTE O TIENE UN ERROR");
//echo " 1  - ";
// Leo los renglones
$query_detfac = sprintf("SELECT * FROM detfac WHERE tcomp = %s AND serie = %s AND ncomp = %s ", $ptcomp, $pserie, $pncomp);
$detallefac = mysqli_query($amercado, $query_detfac) or die("Err. Line 47, EL CBTE NO EXISTE O TIENE UN ERROR");
$totalRows_detallefac = mysqli_num_rows($detallefac);
//echo " 2  - ";
// Traigo impuestos
$query_impuestos = "SELECT * FROM impuestos";
$impuestos = mysqli_query($amercado, $query_impuestos) or die("Err. Line 52, EL CBTE NO EXISTE O TIENE UN ERROR");
$row_Recordset2 = mysqli_fetch_assoc($impuestos);
$totalRows_Recordset2 = mysqli_num_rows($impuestos);
$porc_iva105 = 10.5; //(mysqli_result($impuestos,1, 1)/100); 
$porc_iva21 = 21.0; //(mysqli_result($impuestos,0, 1)/100);
//echo " 3  - ";
// Leo la cabecera de factura
$query_cabfac = sprintf("SELECT * FROM cabfac WHERE tcomp = %s  AND ncomp = %s", $ptcomp,  $pncomp);
$cabecerafac = mysqli_query($amercado, $query_cabfac) or die("Err. Line 68, EL CBTE NO EXISTE O TIENE UN ERROR");
$row_cabecerafac = mysqli_fetch_assoc($cabecerafac);
$fecha        = $row_cabecerafac["fecdoc"];
$fecha_qr     = $row_cabecerafac["fecdoc"];
$fecharem     = $row_cabecerafac["fecdoc"];
$fecha        = substr($row_cabecerafac["fecdoc"], 8, 2) . "/" . substr($row_cabecerafac["fecdoc"], 5, 2) . "/" . substr($row_cabecerafac["fecdoc"], 0, 4);
$cliente      = $row_cabecerafac["cliente"];
$tot_neto0   = $row_cabecerafac["totneto"];
$tot_neto21   = $row_cabecerafac["totneto21"];
$tot_neto105  = $row_cabecerafac["totneto105"];
$tot_comision = $row_cabecerafac["totcomis"];
$tot_iva21    = $row_cabecerafac["totiva21"];
$tot_iva105   = $row_cabecerafac["totiva105"];
$tot_resol    = $row_cabecerafac["totimp"];
$total        = $row_cabecerafac["totbruto"];
$totalletras  = $row_cabecerafac["totbruto"];
$remate       = $row_cabecerafac["codrem"];
$estado       = $row_cabecerafac["estado"];
$nro_doc      = $row_cabecerafac["nrodoc"];
$CAE          = $row_cabecerafac["CAE"];
$CAEFchVto    = $row_cabecerafac["CAEFchVto"];
$usuario_fac  = $row_cabecerafac["usuario"];
if ($usuario_fac == 25 || $usuario_fac == 26) {
	$inmob = 0;
} else {
	$inmob = 0;
}

// Leo el remate
if ($remate != "" && $remate != 0) {
	//echo " 5  - ";
	$query_remate = sprintf("SELECT * FROM remates WHERE ncomp = %s", $remate);
	$remates = mysqli_query($amercado, $query_remate) or die("Err. Line 102, EL CBTE NO EXISTE O TIENE UN ERROR");
	$row_remates = mysqli_fetch_assoc($remates);
	$remate_ncomp = $row_remates["ncomp"];
	$remate_direc = $row_remates["direccion"];
	$remate_fecha = $row_remates["fecreal"];
	$loc_remate   = $row_remates["codloc"];
	$prov_remate  = $row_remates["codprov"];
	$remate_fecha = substr($remate_fecha, 8, 2) . "/" . substr($remate_fecha, 5, 2) . "/" . substr($remate_fecha, 0, 4);
	//echo " 6  - ";
	$totalFilas = 0;
	//Leo si hay direccion de exposicion
	$query_remate_expo = sprintf("SELECT * FROM dir_remates WHERE codrem = %s", $remate);
	$remates_expo = mysqli_query($amercado, $query_remate_expo) or die("Err. Line 114, EL CBTE NO EXISTE O TIENE UN ERROR");
	$totalFilas    =    mysqli_num_rows($remates_expo);
	if ($totalFilas != 0) {
		$row_remates_expo = mysqli_fetch_assoc($remates_expo);
		$remate_ncomp = $row_remates_expo["codrem"];
		$remate_direc = $row_remates_expo["direccion"];
		$loc_remate   = $row_remates_expo["codloc"];
		$prov_remate  = $row_remates_expo["codprov"];
	}
	//echo " 7  - ";
	// Leo la localidad del Remate
	$query_localidades_rem = sprintf("SELECT * FROM localidades WHERE  codnum = %s", $loc_remate);
	$localidad_rem = mysqli_query($amercado, $query_localidades_rem) or die("Err. Line 126, EL CBTE NO EXISTE O TIENE UN ERROR");
	$row_localidades_rem = mysqli_fetch_assoc($localidad_rem);
	$nomlocrem = $row_localidades_rem["descripcion"];
	//echo " 8  - ";
	// Leo la Provincia del Remate
	$query_provincia_rem = sprintf("SELECT * FROM provincias WHERE  codnum = %s", $prov_remate);
	$provincia_rem = mysqli_query($amercado, $query_provincia_rem) or die("Err. Line 132, EL CBTE NO EXISTE O TIENE UN ERROR");
	$row_provincia_rem = mysqli_fetch_assoc($provincia_rem);
	$nomprovrem = $row_provincia_rem["descripcion"];
}
//echo " 9  - "; 
// Leo el cliente
$query_entidades = sprintf("SELECT * FROM entidades WHERE  codnum = %s", $cliente);
$enti = mysqli_query($amercado, $query_entidades) or die("Err. Line 141, EL CBTE NO EXISTE O TIENE UN ERROR " . $query_entidades . " ");
$row_entidades = mysqli_fetch_assoc($enti);
if ($cliente != 16701)
	$nom_cliente   = $row_entidades["razsoc"];
else
	$nom_cliente   = "VIALRENT N&V S.A.S";
$calle_cliente = $row_entidades["calle"];
$nro_cliente   = $row_entidades["numero"];
$codp_cliente  = $row_entidades["codpost"];
$loc_cliente   = $row_entidades["codloc"];
$cuit_cliente  = $row_entidades["cuit"];
$tel_cliente   = $row_entidades["tellinea"];
$tipo_iva   =    $row_entidades["tipoiva"];
$mail          = $row_entidades["mailcont"];
$tipoind       = $row_entidades["tipoind"];

//echo " 10  - ";
// Leo la localidad
$query_localidades = sprintf("SELECT * FROM localidades WHERE  codnum = %s", $loc_cliente);
$localidad = mysqli_query($amercado, $query_localidades) or die("Err. Line 157, EL CBTE NO EXISTE O TIENE UN ERROR");
$row_localidades = mysqli_fetch_assoc($localidad);
$nomloc = $row_localidades["descripcion"];
//echo " 11  - ";
// TIPO DE IVA 
$sql_iva = sprintf("SELECT * FROM tipoiva WHERE  codnum = %s", $tipo_iva);
$tipo_de_iva = mysqli_query($amercado, $sql_iva) or die("Err. Line 163, EL CBTE NO EXISTE O TIENE UN ERROR");
$row_tip_iva = mysqli_fetch_assoc($tipo_de_iva);

if ($row_tip_iva["codnum"] == 3) {
	$tip_iva_cliente = "Receptor del comprobante-Responsable Monotributo";
} else {
	$tip_iva_cliente = $row_tip_iva["descrip"];
}
// TIPO DE INDUSTRIA
if (isset($tipoind)) {
	$sql_ind = sprintf("SELECT * FROM tipoindustria WHERE  codnum = %s", $tipoind);
	$tipo_de_ind = mysqli_query($amercado, $sql_ind) or die("Err. Line 175, EL CBTE NO EXISTE O TIENE UN ERROR");
	$row_tip_ind = mysqli_fetch_assoc($tipo_de_ind);
	$tip_ind_cliente = $row_tip_ind["nomre"];
}

//Inicializo los datos de las columnas de lotes
$df_codintlote = "";
$df_descrip1   = "";
$df_descrip2   = "";
$df_descrip3   = "";
$df_neto       = "";
$df_importe    = "";
$descrip1      = "";
$descrip2      = "";
$descrip3      = "";

// Datos de los renglones
if ($remate != "" && $remate != 0 && $ptcomp != 119 && $ptcomp != 121  && $ptcomp != 122) {
	//echo " 12  - ";
	while ($row_detallefac = mysqli_fetch_array($detallefac, MYSQLI_BOTH)) {
		$lote_num =  $row_detallefac["codlote"];
		if ($lote_num == "") {
			$df_lote    =  $row_detallefac["concafac"];
		}
		if ($lote_num != "") {
			$df_lote     = $row_detallefac["codlote"];
		}
		$neto          = $row_detallefac["neto"];
		$importe  = number_format($row_detallefac["neto"], 2, ',', '.');
		$df_neto  = number_format($row_detallefac["neto"], 2, ',', '.');
		$df_importe    = $df_importe . $importe . "\n";
		//echo " 13  - ";
		//echo "REMATE = ".$remate." SECUENCIA = ".$df_lote." - ";
		$query_lotes = sprintf("SELECT * FROM lotes WHERE codrem = %s AND secuencia = %s", $remate, $df_lote);
		$lotes = mysqli_query($amercado, $query_lotes) or die("Err. Line 268, EL CBTE NO EXISTE O TIENE UN ERROR");
		$row_lotes = mysqli_fetch_assoc($lotes);
		$totalRows_lotes = mysqli_num_rows($lotes);

		$codintlote    = $row_lotes['codintlote'];
		if ($lote_num == "") {
			$descrip1      = substr(utf8_decode($row_detallefac['descrip']), 0, 80);
			$descrip2      = substr(utf8_decode($row_detallefac['descrip']), 80, 80);
		} else {
			if ($pncomp == 9743 || $pncomp == 9741 || $pncomp == 9742 || $pncomp == 9744 || $pncomp == 15627) {
				$descrip1      = substr(utf8_decode($row_detallefac['descrip']), 0, 101);
				$descrip2      = substr(utf8_decode($row_detallefac['descrip']), 101, 102);
				$descrip3      = substr(utf8_decode($row_detallefac['descrip']), 203, 100);
			} else {
				$descrip1      = substr(utf8_decode($row_detallefac['descrip']), 0, 100); //substr($row_lotes['descor'],0,92);
				$descrip2      = substr(utf8_decode($row_detallefac['descrip']), 100, 100); //substr($row_lotes['descor'],92,70);
			}
		}


		if (($ptcomp == 115 || $ptcomp == 117) && ($pncomp != 15627)) {
			if ($descrip2 == "") {
				$descrip2 = "...(detalle completo segun catalogo)";
			} else {
				if ($pncomp != 9741  && $pncomp != 9742 && $pncomp != 9743 && $pncomp != 9744 && $pncomp != 15627)
					$descrip2 = $descrip2 . "...(detalle completo segun catalogo)";
			}
		}


		if ($lote_num == "") {
			$codintlote    = $row_detallefac['concafac']; // antes decian $row_lotes['concafac'];
			$df_codintlote = utf8_decode($row_detallefac['concafac']); // antes decian $row_lotes['concafac'];
		} else { //if ($lote_num!="" ){
			$codintlote    = $row_lotes['codintlote'];
			$df_codintlote = $df_codintlote . $codintlote . "\n";
		}
		$df_descrip1   = $df_descrip1 . $descrip1 . "\n";
		$df_descrip2   = $df_descrip2 . $descrip2 . "\n";
		$df_descrip3   = $df_descrip3 . $descrip3 . "\n";
	}
} else {
	//echo " 14  - ";
	$query_detfac1 = sprintf("SELECT * FROM detfac WHERE tcomp = %s AND serie = %s AND ncomp = %s ORDER BY codlote", $ptcomp, $pserie, $pncomp);
	$detallefac1 = mysqli_query($amercado, $query_detfac1) or die("Err. Line 257, EL CBTE NO EXISTE O TIENE UN ERROR");

	$totalRows_detallefac1 = mysqli_num_rows($detallefac1);
	while ($row_detallefac1 = mysqli_fetch_array($detallefac1, MYSQLI_BOTH)) {

		$df_lote       	= $row_detallefac1["concafac"];
		$neto          	= $row_detallefac1["neto"];
		$neto          	= $row_detallefac1["neto"];
		$importe  		= number_format($row_detallefac1["neto"], 2, ',', '.');
		$df_neto 		= number_format($row_detallefac1["neto"], 2, ',', '.');
		$df_importe    	= $df_importe . $importe . "\n";
		$df_codintlote 	= $df_codintlote . $df_lote . "\n";
		$descrip1   	= substr(utf8_decode($row_detallefac1['descrip']), 0, 90);
		$df_descrip1   	= $df_descrip1 . $descrip1 . "\n";
		$descrip2   	= substr(utf8_decode($row_detallefac1['descrip']), 90, 90);
		$df_descrip2   	= $df_descrip2 . $descrip2 . "\n";
		//$descrip3   	= substr($row_detallefac1['descrip'],152,76);
		//$df_descrip3   	= $df_descrip3.$descrip3."\n";
	}
}

$CUIT = '30718033612';
if ($ptcomp == 115 || $ptcomp == 125) {
	$tipo_cbte = '01';
	$cod_cbte = 'Cod. 001';
	$tipoCmp_qr = 1;
}

if ($ptcomp == 119) {
	$tipo_cbte = '03';
	$cod_cbte = 'Cod. 003';
	$tipoCmp_qr = 3;
}
if ($ptcomp == 122) {
	$tipo_cbte = '02';
	$cod_cbte = 'Cod. 002';
	$tipoCmp_qr = 2;
}
if ($ptcomp == 117 || $ptcomp == 127) {
	$tipo_cbte = '201';
	$cod_cbte = 'Cod. 201';
	$tipoCmp_qr = 201;
}
if ($ptcomp == 121) {
	$tipo_cbte = '203';
	$cod_cbte = 'Cod. 203';
	$tipoCmp_qr = 203;
}

if ($ptcomp == 115 || $ptcomp == 125 || $ptcomp == 119 || $ptcomp == 122) {
	$PtoVta = '0002';
	$PtoVta_qr = 2;
} else 
        if ($ptcomp == 117 || $ptcomp == 127 || $ptcomp == 121 || $ptcomp == 124) {
	$PtoVta = '0005';
	$PtoVta_qr = 5;
} else {
	$PtoVta = '0002';
	$PtoVta_qr = 2;
}
//$CAE = '65266269239656';
//$CAEFchVto = '20150711';

// AHORA UTILIZO ESTA PARTE PARA GENERAR EL CODIGO QR, Y NO SE IMPRIME MAS EL DE BARRAS
//Declaramos la ruta y nombre del archivo a generar
$filename = $dir . $pncomp . '.png';
$url = 'https://www.afip.gob.ar/fe/qr/';
$ver_qr = 1;
$cuit_qr =  $CUIT; //30710183437;        
//$ptoVta_qr = (int) 4; 
//$tipoCmp_qr = (int) 1;
$nroCmp_qr = $pncomp; //(int) 9566;
$importe_qr = $total; //(float) 43560.00;
$moneda_qr = 'PES';
$ctz_qr = (float) 1;
$tipoDocRec_qr =  80;
$nroDocRec_qr =  str_replace("-", "", $cuit_cliente); //30660920451;
$tipoCodAut_qr = 'A';
$codAut_qr =  $CAE; //70417396134924;    


$url = 'https://www.afip.gob.ar/fe/qr/';
$datos_cmp_base_64 = json_encode([

	'ver' => $ver_qr,
	'fecha' => $fecha_qr,
	'cuit' =>  $cuit_qr,
	'ptoVta' => $PtoVta_qr,
	'tipoCmp' => $tipoCmp_qr,
	'nroCmp' => $nroCmp_qr,
	'importe' => $importe_qr,
	'moneda' => $moneda_qr,
	'ctz' => $ctz_qr,
	'tipoDocRec' =>  $tipoDocRec_qr,
	'nroDocRec' =>  $nroDocRec_qr,
	'tipoCodAut' => $tipoCodAut_qr,
	'codAut' =>  $codAut_qr
]);
//echo "1 - datos_cmp_base_64 antes de encode = ".$datos_cmp_base_64."  -  ";
$datos_cmp_base_64 = base64_encode($datos_cmp_base_64);

$to_qr = $url . '?p=' . $datos_cmp_base_64;
//echo " TO_QR = ".$to_qr."  ";
//Parametros de Configuración

$tamano = 1.5; //Tamaño de Pixel
$level = 'L'; //Precisión Baja
$framSize = 1.5; //Tamaño en blanco
$contenido = $to_qr;

//Enviamos los parametros a la Función para generar código QR 
QRcode::png($contenido, $filename, $level, $tamano, $framSize);



// HASTA ACA

$CAEFchVto2 = str_replace("-", "", $CAEFchVto);
$digito = '5'; // Ver algoritmo para sacar DigVer
$codigo = $CUIT;
$codigo .= $tipo_cbte;
$codigo .= $PtoVta;
$codigo .= $CAE;
$codigo .= $CAEFchVto2;
// ACA CALCULO EL DIGITO VERIFICADOR
$suma_impares = 0;
$suma_pares = 0;
for ($i = 0; $i < 40; $i++) {
	if (($i % 2) == 0) {
		$suma_pares += (int)substr($codigo, $i, 1);
		//echo " i PAR=  ".$i."   PARES =  ".$suma_pares." IMPARES =  ".$suma_impares;
	} else {
		$suma_impares += (int)substr($codigo, $i, 1);
		//echo " i IMPAR=  ".$i."   PARES =  ".$suma_pares." IMPARES =  ".$suma_impares;
	}
}
$suma_impares = $suma_impares * 3;
$suma_total = $suma_pares + $suma_impares;
//echo "SUMA_TOTAL / 10 =  ".($suma_total / 10)."  -  ";
$digito = 10 - ($suma_total % 10);
//echo " DIGITO =  ".$digito."   PARES =  ".$suma_pares." IMPARES =  ".$suma_impares."  TOTAL  =  ".$suma_total;
if ($digito == 10)
	$digito = 0;
$codigo .= $digito;
//echo " DIGITO =  ".$digito."   ";
mysqli_close($amercado);

// ==================== FACTURA ORIGINAL ========================================
//Creo el PDF file
// Imprimo la cabecera
// Linea de arriba

$pdf = new FPDF();

$pdf->AddPage();
$pdf->SetAutoPageBreak(1, 2);
if ($ptcomp != 111 && $ptcomp != 112 && $ptcomp != 113) {
	$pdf->SetLineWidth(.2);
	$pdf->Line(7, 7.5, 203, 7.5);
	$pdf->Line(7, 7.5, 7, 255);
	//$pdf->Line(7,290,203,290);
	$pdf->Line(203, 7.5, 203, 255);
	$pdf->Line(7, 50, 203, 50);
	$pdf->Line(7, 95, 203, 95);
	$pdf->Line(7, 100, 203, 100);
	$pdf->Line(25, 95, 25, 240);
	$pdf->Line(175, 95, 175, 240);
	$pdf->Line(32, 240, 32, 255);
	$pdf->Line(62, 240, 62, 255);
	$pdf->Line(90, 240, 90, 255);
	$pdf->Line(122, 240, 122, 255);
	$pdf->Line(154, 240, 154, 255);
	$pdf->Line(175, 240, 175, 255);
	$pdf->Line(7, 240, 203, 240);
	$pdf->Line(7, 240, 203, 240);
	$pdf->Line(7, 247, 203, 247);
	$pdf->Line(7, 255, 203, 255);
	$pdf->Line(7, 76, 203, 76);
	$pdf->Line(107, 7.5, 107, 50);
} else {
	$pdf->SetLineWidth(.2);
	$pdf->Line(7, 7.5, 203, 7.5);
	$pdf->Line(7, 7.5, 7, 240);
	//$pdf->Line(7,290,203,290);
	$pdf->Line(203, 7.5, 203, 240);
	$pdf->Line(7, 50, 203, 50);
	$pdf->Line(7, 95, 203, 95);
	$pdf->Line(7, 100, 203, 100);
	$pdf->Line(25, 95, 25, 225);
	$pdf->Line(175, 95, 175, 225);
	$pdf->Line(32, 225, 32, 240);
	$pdf->Line(62, 225, 62, 240);
	$pdf->Line(90, 225, 90, 240);
	$pdf->Line(122, 225, 122, 240);
	$pdf->Line(154, 225, 154, 240);
	$pdf->Line(175, 225, 175, 240);
	$pdf->Line(7, 225, 203, 225);
	$pdf->Line(7, 225, 203, 225);
	$pdf->Line(7, 232, 203, 232);
	$pdf->Line(7, 240, 203, 240);
	$pdf->Line(7, 76, 203, 76);
	$pdf->Line(107, 7.5, 107, 50);
}
$pdf->SetFont('Arial', 'BI', 14);
$pdf->SetY(8);
$pdf->SetX(120);
if ($ptcomp == 115 || $ptcomp == 125)
	$pdf->Cell(160, 10, 'FACTURA');
if ($ptcomp == 117 || $ptcomp == 127)
	$pdf->Cell(160, 10, utf8_decode('FACTURA DE CRÉDITO MiPyMEs'));
if ($ptcomp == 121)
	$pdf->Cell(160, 10, utf8_decode(' NOTA DE CRÉDITO MiPyMEs'));
$pdf->SetFont('Arial', 'BI', 10);
$pdf->SetY(15);
$pdf->SetX(120);
// if ($ptcomp == 51 || $ptcomp == 52 || $ptcomp == 55 || $ptcomp == 57 || $ptcomp == 59 || $ptcomp == 61 || $ptcomp == 63)
$pdf->Cell(150, 10, 'Punto de Venta:  ' . substr($nro_doc, 1, 4) . '  Comp. Nro.:  ' . substr($nro_doc, 6, 8));
//if ($ptcomp == 111 || $ptcomp == 112 || $ptcomp == 113)
// $pdf->Cell(150,10,'Punto de Venta:  '.substr($nro_doc,1,5).'  Comp. Nro.:  '.substr($nro_doc,7,8));
$pdf->SetY(20);
$pdf->SetX(120);
$pdf->Cell(120, 10, utf8_decode('Fecha de Emisión: '));
//$pdf->Cell(150,10,'Fecha: ');
$pdf->SetY(32);
$pdf->SetX(120);
$pdf->Cell(150, 10, 'CUIT: 30-71803361-2');
$pdf->SetY(37);
$pdf->SetX(120);
$pdf->Cell(150, 10, 'Ingresos Brutos:  30-71803361-2');
$pdf->SetY(42);
$pdf->SetX(120);
$pdf->Cell(150, 10, 'Fecha de Inicio de Actividades: 22/12/2022');
$pdf->SetY(48);
$pdf->SetX(10);
$pdf->Cell(100, 10, utf8_decode('Apellido y Nombre / Razón Social: '));
$pdf->SetY(54);
$pdf->SetX(10);
$pdf->Cell(10, 10, 'Domicilio: ');
$pdf->SetX(125);
//$pdf->Cell(10,10,'Localidad:');
$pdf->SetY(66);
$pdf->SetX(10);
$pdf->Cell(100, 10, 'Cond. frente al IVA: ');
$pdf->SetY(54);
$pdf->SetX(130);
if (isset($mail) && $pserie != 31) {
	$pdf->Cell(20, 10, utf8_decode('Teléfono: '));
	$pdf->SetX(147);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(30, 10, $tel_cliente);
	$pdf->SetY(60);
	$pdf->SetX(130);
	$pdf->SetFont('Arial', 'BI', 10);
	$pdf->Cell(100, 10, 'e-mail: ');
	$pdf->SetX(145);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(100, 10, $mail);
} else {

	$pdf->Cell(100, 10, utf8_decode('Teléfono: '));
	$pdf->SetX(147);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(30, 10, $tel_cliente);
}
$pdf->SetY(60);
$pdf->SetX(10);
$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(150, 10, 'CUIT: ');
$pdf->SetFont('Arial', '', 10);
$pdf->SetX(21);
$pdf->Cell(150, 10, $cuit_cliente);
//Fecha

if ($ptcomp == 55 || $ptcomp == 61) {
	//$pdf->SetY(23);
	$pdf->Cell(130);
	if ($inmob == 1)
		$pdf->Image('images/Adrian-Mercado-Inmobiliaria_1.png', 19, 8);
	else
		$pdf->Image('marca_adrianmercado-subasta_version1.png', 14, 8);
	$pdf->SetY(23);
	$pdf->SetX(15);
	$pdf->SetFont('Arial', 'BI', 8);
	$pdf->Cell(150, 10, utf8_decode('        Matriculado CUCICBA: Mercado Leandro Nicolás'));
	$pdf->SetY(26);
	$pdf->SetX(15);
	$pdf->Cell(150, 10, utf8_decode('                Matrícula N° 5604, Tomo 1, Folio 209'));
} else {
	//$pdf->SetY(23);
	$pdf->Cell(130);
	if ($inmob == 1)
		$pdf->Image('images/Adrian-Mercado-Inmobiliaria_1.png', 19, 8);
	else
		$pdf->Image('marca_adrianmercado-subasta_version1.png', 14, 8);
}
$pdf->SetFont('Arial', 'BI', 10);
$pdf->SetY(29);
$pdf->SetX(15);
$pdf->Cell(150, 10, '          ADRIAN MERCADO SUBASTAS S.A.');
$pdf->SetY(33);
$pdf->SetX(14);
$pdf->Cell(150, 10, '                 Olga Cossettini 731, Piso 3');
$pdf->SetY(37);
$pdf->SetX(15);
$pdf->Cell(150, 10, '      CABA  (C1107CDA) Tel: +54 11 3984-7400');
$pdf->SetY(41);
$pdf->SetX(15);
$pdf->Cell(150, 10, '                 IVA  Responsable  Inscripto');

$pdf->Image('images/tipo_a.gif', 99, 7.5);
$pdf->SetY(22);
$pdf->SetX(100);
$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(15, 8, $cod_cbte);
$pdf->SetFont('Arial', 'B', 10);

// Imprimo la cabecera
// Nota Debito
if ($ptcomp == 122) {
	$pdf->SetY(6);
	$pdf->SetFont('Arial', 'BI', 14);
	$nota = 'NOTA DE';
	$debito = ' DEBITO';
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->SetX(120);
	$pdf->Cell(70, 10, $nota . $debito, 0, 0, 'L');
	//$pdf->SetLineWidth(5);
	//$pdf->Line(145,10,170,10);
	//$pdf->SetX(174);
	//$pdf->Cell(70,10,$debito,0,0,'L');
}
if ($ptcomp == 119) {
	$pdf->SetY(6);
	$pdf->SetFont('Arial', 'BI', 14);
	$nota = 'NOTA DE ';
	$debito = 'CREDITO';
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->SetX(120);
	$pdf->Cell(70, 10, $nota . $debito, 0, 0, 'L');
	//$pdf->SetLineWidth(5);
	//$pdf->Line(145,10,170,10);
	//$pdf->SetX(174);
	//$pdf->Cell(70,10,$debito,0,0,'L');

}
$pdf->SetFont('Arial', 'B', 10);
//Movernos a la derecha
//Fecha
//$pdf->SetFont('Arial','',10);
$pdf->SetY(20); // $pdf->SetY(15);
$pdf->Cell(143);
$pdf->Cell(40, 10, $fecha, 0, 0, 'L');


// Datos del Cliente
$pdf->SetY(48); // $pdf->SetY(48);
$pdf->Cell(59);
$pdf->SetFont('Arial', '', 10);
if (isset($tip_ind_cliente)) {
	$pdf->Cell(70, 10, utf8_decode($nom_cliente), 0, 0, 'L');
	$pdf->SetFont('Arial', 'BI', 10);
	$pdf->SetY(48);
	$pdf->SetX(130);
	$pdf->Cell(20, 10, ' ', 0, 0, 'L');
	$pdf->SetY(48);
	$pdf->SetX(143);
	$pdf->SetFont('Arial', '', 10);
	//$pdf->Cell(70,10,$tip_ind_cliente,0,0,'L');		
} else {
	$pdf->Cell(70, 10, utf8_decode($nom_cliente), 0, 0, 'L');
}
$pdf->SetY(54); // $pdf->SetY(54);
$pdf->Cell(18);
if (isset($codp_cliente))
	$pdf->Cell(70, 10, utf8_decode($calle_cliente) . ' ' . $nro_cliente . ' (' . $codp_cliente . ') ' . utf8_decode($nomloc), 0, 0, 'L');
else
	$pdf->Cell(70, 10, utf8_decode($calle_cliente) . ' ' . $nro_cliente . '     ' . utf8_decode($nomloc), 0, 0, 'L');
// Datos del Remate
$pdf->SetFont('Arial', '', 9);
//$pdf->Ln(9);
$pdf->SetY(66); // $pdf->SetY(63);
$pdf->Cell(34);
$pdf->Cell(20, 10, $tip_iva_cliente, 0, 0, 'L');
$pdf->SetFont('Arial', 'BI', 10);
//$pdf->Ln(10);
$pdf->SetY(66); // $pdf->SetY(68);
$pdf->SetX(130);
//$pdf->Cell(50);
$pdf->Cell(20, 10, utf8_decode('Condición de Venta: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(15);
$pdf->Cell(20, 10, 'Contado', 0, 0, 'L');
$pdf->Cell(116);
$pdf->SetX(170);
//$pdf->Cell(30,10,$tel_cliente,0,0,'L');
$pdf->SetX(15);
$pdf->SetY(85); // $pdf->SetY(82);
if ($remate != "" && $remate != 0) {
	$pdf->SetFont('Arial', 'BI', 10);
	$pdf->SetX(15);
	$pdf->SetY(75); // $pdf->SetY(82);
	$pdf->Cell(20, 10, 'Por la subasta efectuada en: ', 0, 0, 'L');
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetX(15);
	$pdf->SetY(80); // $pdf->SetY(82);
	$pdf->Cell(30, 10, utf8_decode($remate_direc) . " ," . utf8_decode($nomlocrem) . " ," . utf8_decode($nomprovrem), 0, 0, 'L');
	//$pdf->Cell(130);
	$pdf->SetX(15);
	$pdf->SetY(85);
	$pdf->SetFont('Arial', 'BI', 10);
	$pdf->Cell(10, 10, 'De fecha: ', 0, 0, 'L');
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetY(85);
	$pdf->Cell(22);
	$pdf->Cell(15, 10, $remate_fecha, 0, 0, 'R');
	$pdf->Cell(2);
	//$pdf->SetY(85);
	$pdf->SetFont('Arial', 'BI', 10);
	$pdf->Cell(4, 10, 'ID: ', 0, 0, 'L');
	$pdf->SetFont('Arial', '', 10);
	//$pdf->SetY(85);
	$pdf->Cell(2);
	if ($pncomp == 1682)
		$pdf->Cell(15, 10, $remate . " Por cuenta y orden de BASF ARGENTINA S.A.", 0, 0, 'L');
	else
		$pdf->Cell(15, 10, $remate, 0, 0, 'L');
}
//Salto de línea
$pdf->SetY(93);
$pdf->SetX(5);
$pdf->SetFont('Arial', 'BI', 10);
if ($ptcomp == 115 || $ptcomp == 117)
	$pdf->Cell(20, 10, '       Lote                                                          Producto / Servicio                                                                           Subtotal', 0, 0, 'L');
else
	$pdf->Cell(20, 10, '                                                                        Producto / Servicio                                                                           Subtotal', 0, 0, 'L');

//Posición de los títulos de los renglones, en este caso no va
$Y_Fields_Name_position = 90;
//Posición del primer renglón 
$Y_Table_Position = 100; // $Y_Table_Position = 102;

//Los títulos de las columnas no los debo poner
//Aca van los datos de las columnas
$j = $Y_Table_Position;
$pdf->SetY($Y_Table_Position);

$pdf->SetFont('Arial', '', 8); // antes era 11
$pdf->SetY($j);
$pdf->SetX(15);
if ($ptcomp == 115 || $ptcomp == 117) {
	// Código interno de Lote
	$pdf->MultiCell(12, 6, $df_codintlote, 0, 'L');
}
$pdf->SetY($j);
$pdf->SetX(27);

// Descripción del lote en uno o dos O TRES renglones
//$df_descrip1 = str_replace('?', '', $df_descrip1);
if (isset($df_descrip3)) {
	$pdf->SetX(27);
	$pdf->MultiCell(150, 6, $df_descrip1, 0, 'L');
	$pdf->SetY($j + 3);
	$pdf->SetX(27);
	$pdf->MultiCell(150, 6, $df_descrip2, 0, 'L');
	$pdf->SetY($j + 6);
	$pdf->SetX(27);
	$pdf->MultiCell(150, 6, $df_descrip3, 0, 'L');
	$pdf->SetY($j);
	$df_descrip1 = "";
	$df_descrip2 = "";
	$df_descrip3 = "";
} else
	if (isset($df_descrip2)) {
	$pdf->SetX(27);
	$pdf->MultiCell(150, 6, $df_descrip1, 0, 'L');
	$pdf->SetY($j + 3);
	$pdf->SetX(27);
	$pdf->MultiCell(150, 6, $df_descrip2, 0, 'L');
	$pdf->SetY($j);
	$df_descrip1 = "";
	$df_descrip2 = "";
} else {
	$pdf->MultiCell(150, 6, $df_descrip1, 0, 'L');
	$pdf->SetY($j);
	$df_descrip1 = "";
	$df_descrip2 = "";
}
$pdf->SetX(158);
$pdf->MultiCell(44, 6, $df_importe, 0, 'R');

// ACA VA EL PIE
//Posición: a 64 mm cm del final
$pdf->SetY(-52); // $pdf->SetY(-60);
$pdf->SetFont('Arial', 'I', 8);
//$pdf->Cell(100,4,utf8_decode($leyenda1),0,0,'L');
$pdf->SetY(-48); // $pdf->SetY(-56);
//$pdf->Cell(100,4,utf8_decode($leyenda2),0,0,'L');
$pdf->SetY(-44); // $pdf->SetY(-52);
//$pdf->Cell(100,4,utf8_decode($leyenda3),0,0,'L');

if ($ptcomp != 117 && $ptcomp != 121 && $ptcomp != 127) {
	$pdf->SetY(-58); // $pdf->SetY(-25);
	//Arial italic 8
	$pdf->SetFont('Arial', 'I', 8);
	if ($pserie == 52) {
		$total = $tot_neto21 + $tot_neto105 + $tot_comision + $tot_iva21 + $tot_iva105 + $tot_resol + $tot_neto0;
		$tot_neto21   = number_format($tot_neto21, 2, ',', '.');
		$tot_neto105  = number_format($tot_neto105, 2, ',', '.');
		$tot_comision = number_format($tot_comision, 2, ',', '.');
		$tot_iva21    = number_format($tot_iva21, 2, ',', '.');
		$tot_iva105   = number_format($tot_iva105, 2, ',', '.');
		$tot_resol    = number_format($tot_resol, 2, ',', '.');
		$total        = number_format($total, 2, ',', '.');
		//Porcentajes
		// tipo de comprobante 
		// Factura A
		if ($ptcomp == 119 || ($ptcomp == 122 && $tot_iva21 == 0.00 && $tot_iva105 == 0.00))
			$pdf->Cell(0, 8, utf8_decode('  Neto 21 %                 Neto 10,5 %               Uso Plataforma                   IVA 21 %                         IVA 10,5 %                 Gs. Adm.             Importe Total'), 0, 0, 'L');
		else if ($ptcomp == 125 && $tot_neto0 != 0)
			$pdf->Cell(0, 8, utf8_decode('  Neto 21 %                  Neto 0 %                 Uso Plataforma                   IVA 21 %                         IVA 10,5 %                 Gs. Adm.             Importe Total'), 0, 0, 'L');
		else
			$pdf->Cell(0, 8, utf8_decode('  Neto 21 %                  Neto 10,5 %              Uso Plataforma                   IVA 21 %                         IVA 10,5 %                 Gs. Adm.             Importe Total'), 0, 0, 'L');
		$pdf->SetY(-51); // $pdf->SetY(-20);
		$pdf->SetFont('Arial', 'B', 10);

		// ACA VAN LOS CAMPOS UNO A UNO EN EL CASILLERO QUE LES CORRESPONDE
		if ($ptcomp == 125 && $tot_neto0 != 0) {
			$pdf->SetX(17);
			$pdf->Cell(15, 8, $tot_neto21, 0, 0, 'R');
			$pdf->SetX(46);
			$pdf->Cell(15, 8, $tot_neto0, 0, 0, 'R');
			$pdf->SetX(75);
			$pdf->Cell(15, 8, $tot_comision, 0, 0, 'R');
			$pdf->SetX(106);
			$pdf->Cell(15, 8, $tot_iva21, 0, 0, 'R');
			$pdf->SetX(139);
			$pdf->Cell(15, 8, $tot_iva105, 0, 0, 'R');
			$pdf->SetX(160);
			$pdf->Cell(15, 8, $tot_resol, 0, 0, 'R');
			$pdf->SetX(188);
			$pdf->Cell(15, 8, $total, 0, 0, 'R');
		} else {
			$pdf->SetX(17);
			$pdf->Cell(15, 8, $tot_neto21, 0, 0, 'R');
			$pdf->SetX(46);
			$pdf->Cell(15, 8, $tot_neto105, 0, 0, 'R');
			$pdf->SetX(75);
			$pdf->Cell(15, 8, $tot_comision, 0, 0, 'R');
			$pdf->SetX(106);
			$pdf->Cell(15, 8, $tot_iva21, 0, 0, 'R');
			$pdf->SetX(139);
			$pdf->Cell(15, 8, $tot_iva105, 0, 0, 'R');
			$pdf->SetX(160);
			$pdf->Cell(15, 8, $tot_resol, 0, 0, 'R');
			$pdf->SetX(188);
			$pdf->Cell(15, 8, $total, 0, 0, 'R');
		}
		$fecha_cierre = "2024-09-20";

		if ($fecha_qr < $fecha_cierre) {
			$pdf->SetFont('Arial', 'BI', 7);
			$pdf->SetY(-40);
			$pdf->SetX(40);
			$pdf->Cell(60, 4, "Opciones para cancelar esta factura:", 0, 0, 'L');
			$pdf->SetY(-36);
			$pdf->SetX(40);
			$pdf->Cell(85, 4, utf8_decode("Depósito/Transferencia Bancaria: Banco BBVA. Nro. de Cuenta: 123-009694/0 - CBU N°: 0170123020000000969406 - Alias: AM.SUBASTAS"), 0, 0, 'L');
			$pdf->SetY(-32);
			$pdf->SetX(40);
			$pdf->Cell(85, 4, utf8_decode("Los cheques deben ser propios, al día y a la orden de Adrián Mercado Subastas S.A."), 0, 0, 'L');
		} else {
			$pdf->SetFont('Arial', 'BI', 7);
			$pdf->SetY(-40);
			$pdf->SetX(40);
			$pdf->Cell(60, 4, "Opciones para cancelar esta factura:", 0, 0, 'L');
			$pdf->SetY(-36);
			$pdf->SetX(40);
			$pdf->Cell(85, 4, utf8_decode("Depósito/Transferencia Bancaria: Banco Galicia. Nro. de Cuenta: 0001590-7 358-4 - CBU N°: 0070358920000001590748"), 0, 0, 'L');
			$pdf->SetY(-32);
			$pdf->SetX(40);
			$pdf->Cell(85, 4, utf8_decode("Alias: AM.SUBASTAS.GALICIA"), 0, 0, 'L');
			$pdf->SetY(-28);
			$pdf->SetX(40);
			$pdf->Cell(85, 4, utf8_decode("Los cheques deben ser propios, al día y a la orden de Adrián Mercado Subastas S.A."), 0, 0, 'L');
		}
	} else {
		$totalneto21  = $tot_neto21 * 1.21;
		$totalneto105 = $tot_neto105 + $tot_iva105;
		$tot_comision = $tot_comision * 1.21;
		$total = $totalneto21 + $totalneto105 + $tot_comision;
		$totalneto21  = number_format($totalneto21, 2, ',', '.');
		$totalneto105 = number_format($totalneto105, 2, ',', '.');
		$tot_neto21   = number_format($tot_neto21, 2, ',', '.');
		$tot_neto105  = number_format($tot_neto105, 2, ',', '.');
		$tot_comision = number_format($tot_comision, 2, ',', '.');
		$tot_iva21    = number_format($tot_iva21, 2, ',', '.');
		$tot_iva105   = number_format($tot_iva105, 2, ',', '.');
		$tot_resol    = number_format($tot_resol, 2, ',', '.');
		$total        = number_format($total, 2, ',', '.');
		$pdf->Cell(0, 8, '                               21 %                                                         10,5 %                                                     10%                                                    ', 0, 0, 'L');
		$pdf->SetY(-51); // $pdf->SetY(-20);
		$pdf->SetFont('Arial', 'B', 10);

		// ACA VAN LOS CAMPOS UNO A UNO EN EL CASILLERO QUE LES CORRESPONDE
		$pdf->SetX(20);
		$pdf->Cell(15, 8, $totalneto21, 0, 0, 'R');
		$pdf->SetX(80);
		$pdf->Cell(15, 8, $totalneto105, 0, 0, 'R');
		$pdf->SetX(135);
		$pdf->Cell(15, 8, $tot_comision, 0, 0, 'R');
		$pdf->SetX(190);
		$pdf->Cell(15, 8, $total, 0, 0, 'R');
	}
} else {
	$pdf->SetY(-40); //$pdf->SetY(-40); // $pdf->SetY(-25);
	//Arial italic 8
	
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->SetX(40);
	$pdf->Cell(60, 4, utf8_decode("Opciones para cancelar esta factura: Depósito/Transferencia Bancaria:"), 0, 0, 'L');
	$fecha_cierre = "2024-09-26";

	if ($fecha_qr < $fecha_cierre) {
	$pdf->SetY(-37); //$pdf->SetY(-36);
	$pdf->SetX(40);
	$pdf->Cell(85, 4, utf8_decode("Banco BBVA. Nro. de Cuenta: 123-009694/0 - CBU N°: 0170123020000000969406 - Alias: AM.SUBASTAS"), 0, 0, 'L');
	$pdf->SetY(-34); //$pdf->SetY(-32);
	$pdf->SetX(40);
	} else {
	$pdf->SetY(-37); //$pdf->SetY(-36);
	$pdf->SetX(40);
	$pdf->Cell(85, 4, utf8_decode("Banco Galicia. Nro. de Cuenta: 0001590-7 358-4 - CBU N°: 0070358920000001590748 - Alias: AM.SUBASTAS.GALICIA"), 0, 0, 'L');
	$pdf->SetY(-34); //$pdf->SetY(-32);
	$pdf->SetX(40);
	}

	$pdf->Cell(85, 4, utf8_decode("Los cheques deben ser propios, al día y a la orden de Adrián Mercado Subastas S.A."), 0, 0, 'L');
	$pdf->SetY(-30); //$pdf->SetY(-50);
	$pdf->SetX(40);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Cell(188, 3, utf8_decode("Luego de su aceptación tácita  o  expresa,  esta Factura de  Crédito Electrónica MiPyMEs será transmitida a El Sistema de Circulación Abierta para Facturas de"), 0, 0, 'L');
	$pdf->SetY(-27); //$pdf->SetY(-47);
	$pdf->SetX(40);
	$pdf->Cell(188, 3, utf8_decode("Crédito Electrónicas MiPyMEs, para su circulación y negociación incluso en los Mercados de Valores, en este caso a través de un Agente de Depósito Colectivo"), 0, 0, 'L');
	$pdf->SetY(-24); //$pdf->SetY(-47);
	$pdf->SetX(40);
	$pdf->Cell(188, 3, utf8_decode("o agentes que cumplan similares funciones."), 0, 0, 'L');
	$pdf->SetY(-72);
	$pdf->SetFont('Arial', 'I', 8);
	 
	
	if ($pserie == 52 || $pserie == 54) {
		$total = $tot_neto21 + $tot_neto105 + $tot_comision + $tot_iva21 + $tot_iva105 + $tot_resol + $tot_neto0;
		$tot_neto21   = number_format($tot_neto21, 2, ',', '.');
		$tot_neto105  = number_format($tot_neto105, 2, ',', '.');
		$tot_comision = number_format($tot_comision, 2, ',', '.');
		$tot_iva21    = number_format($tot_iva21, 2, ',', '.');
		$tot_iva105   = number_format($tot_iva105, 2, ',', '.');
		$tot_resol    = number_format($tot_resol, 2, ',', '.');
		$total        = number_format($total, 2, ',', '.');
		//Porcentajes
		// tipo de comprobante 
		// FActura A
		if ($ptcomp == 119 || ($ptcomp == 122 && $tot_iva21 == 0.00 && $tot_iva105 == 0.00))
			$pdf->Cell(0, 8, utf8_decode('  Neto 0 %                  Neto 10,5 %             Uso Plataforma                   IVA 21 %                        IVA 10,5 %                  Gastos Adm.            Importe Total'), 0, 0, 'L');
		else if ($ptcomp == 125 && $tot_neto0 != 0) {

			$pdf->Cell(0, 8, utf8_decode('  Neto 21 %                  Neto 0 %             Uso Plataforma                   IVA 21 %                        IVA 10,5 %               Gastos Adm.            Importe Total'), 0, 0, 'L');
		} else {
			$pdf->SetY(-57);
			$pdf->Cell(0, 8, utf8_decode('  Neto 21 %                  Neto 10,5 %             Uso Plataforma                   IVA 21 %                        IVA 10,5 %               Gastos Adm.            Importe Total'), 0, 0, 'L');
		}
		$pdf->SetY(-50); //$pdf->SetY(-65); // $pdf->SetY(-20);
		$pdf->SetFont('Arial', 'B', 10);

		// ACA VAN LOS CAMPOS UNO A UNO EN EL CASILLERO QUE LES CORRESPONDE
		if ($ptcomp == 125 && $tot_neto0 != 0) {
			$pdf->SetX(17);
			$pdf->Cell(15, 8, $tot_neto21, 0, 0, 'R');
			$pdf->SetX(46);
			$pdf->Cell(15, 8, $tot_neto0, 0, 0, 'R');
			$pdf->SetX(75);
			$pdf->Cell(15, 8, $tot_comision, 0, 0, 'R');
			$pdf->SetX(106);
			$pdf->Cell(15, 8, $tot_iva21, 0, 0, 'R');
			$pdf->SetX(139);
			$pdf->Cell(15, 8, $tot_iva105, 0, 0, 'R');
			$pdf->SetX(160);
			$pdf->Cell(15, 8, $tot_resol, 0, 0, 'R');
			$pdf->SetX(188);
			$pdf->Cell(15, 8, $total, 0, 0, 'R');
		} else {
			$pdf->SetX(17);
			$pdf->Cell(15, 8, $tot_neto21, 0, 0, 'R');
			$pdf->SetX(46);
			$pdf->Cell(15, 8, $tot_neto105, 0, 0, 'R');
			$pdf->SetX(75);
			$pdf->Cell(15, 8, $tot_comision, 0, 0, 'R');
			$pdf->SetX(106);
			$pdf->Cell(15, 8, $tot_iva21, 0, 0, 'R');
			$pdf->SetX(139);
			$pdf->Cell(15, 8, $tot_iva105, 0, 0, 'R');
			$pdf->SetX(160);
			$pdf->Cell(15, 8, $tot_resol, 0, 0, 'R');
			$pdf->SetX(188);
			$pdf->Cell(15, 8, $total, 0, 0, 'R');
		}
	} else {
		$totalneto21  = $tot_neto21 * 1.21;
		$totalneto105 = $tot_neto105 + $tot_iva105;
		$tot_comision = $tot_comision * 1.21;
		$total = $totalneto21 + $totalneto105 + $tot_comision;
		$totalneto21  = number_format($totalneto21, 2, ',', '.');
		$totalneto105 = number_format($totalneto105, 2, ',', '.');
		$tot_neto21   = number_format($tot_neto21, 2, ',', '.');
		$tot_neto105  = number_format($tot_neto105, 2, ',', '.');
		$tot_comision = number_format($tot_comision, 2, ',', '.');
		$tot_iva21    = number_format($tot_iva21, 2, ',', '.');
		$tot_iva105   = number_format($tot_iva105, 2, ',', '.');
		$tot_resol    = number_format($tot_resol, 2, ',', '.');
		$total        = number_format($total, 2, ',', '.');
		$pdf->Cell(0, 8, '                               21 %                                                         10,5 %                                                     10%                                                    ', 0, 0, 'L');
		$pdf->SetY(-65); // $pdf->SetY(-20);
		$pdf->SetFont('Arial', 'B', 10);

		// ACA VAN LOS CAMPOS UNO A UNO EN EL CASILLERO QUE LES CORRESPONDE
		$pdf->SetX(20);
		$pdf->Cell(15, 8, $totalneto21, 0, 0, 'R');
		$pdf->SetX(80);
		$pdf->Cell(15, 8, $totalneto105, 0, 0, 'R');
		$pdf->SetX(135);
		$pdf->Cell(15, 8, $tot_comision, 0, 0, 'R');
		$pdf->SetX(190);
		$pdf->Cell(15, 8, $total, 0, 0, 'R');
	}
}
//$pdf->i25(10,275,$codigo);
$texto_afip = "";
if ($ptcomp == 127)
	$pdf->SetY(-32);
else
	$pdf->SetY(-40);
$pdf->SetX(12);
$pdf->Image('temp/' . $pncomp . '.png');
$pdf->SetY(-40);
$pdf->SetX(45);
//$pdf->Image('afip_logo.png');
if ($tipo_iva == 3) {
	$pdf->SetY(-32);
	$pdf->SetX(75);
	$pdf->SetFont('Arial', 'BI', 6);
	$pdf->Cell(70, 10, utf8_decode('El crédito fiscal discriminado en el presente comprobante, sólo '), 0, 0, 'L');
	$pdf->SetY(-29);
	$pdf->SetX(75);
	$pdf->Cell(70, 10, utf8_decode('podrá ser computado a efectos del Régimen de Sostenimiento e '), 0, 0, 'L');
	$pdf->SetY(-26);
	$pdf->SetX(75);
	$pdf->Cell(70, 10, utf8_decode('Inclusión Fiscal para Pequeños Contribuyentes de la Ley Nº 27.618'), 0, 0, 'L');
}
if ($ptcomp == 111 || $ptcomp == 112) {
}
$pdf->SetY(-25);
$pdf->SetX(39);
$pdf->SetFont('Arial', 'BI', 8);
//$pdf->Cell(50,10,'Comprobante autorizado  ',0,0,'L');
$pdf->SetY(-21);
$pdf->SetX(39);
$pdf->SetFont('Arial', 'BI', 6);
//$pdf->Cell(70,10,utf8_decode('Esta Administración Federal no se responsabiliza por los datos ingresados en el detalle de la operación '),0,0,'L');

$CAEFchVto2 = substr($CAEFchVto, 8, 2) . "/" . substr($CAEFchVto, 5, 2) . "/" . substr($CAEFchVto, 0, 4);
$pdf->SetY(-25); // $pdf->SetY(-20);
$pdf->SetX(150);
$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(30, 10, utf8_decode('     CAE N°:  '), 0, 0, 'L');
$pdf->SetX(170);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 10, $CAE, 0, 0, 'L');
$pdf->SetY(-20); // $pdf->SetY(-20);
$pdf->SetFont('Arial', 'BI', 10);
$pdf->SetX(140);
$pdf->Cell(30, 10, ' Fecha Vto. CAE: ', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->SetX(170);
$pdf->Cell(30, 10, $CAEFchVto2, 0, 0, 'L');
$pdf->Output();
// ob_end_clean();
