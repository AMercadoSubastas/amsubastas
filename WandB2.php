<?php 
define('ND_B2','123');
define('SERIE_B2','53');
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors','Yes');
include_once "funcion_mysqli_result.php";
require_once('Connections/amercado.php');
include_once "FE_Pack_WSFE/config.php";
include_once "FE_Pack_WSFE/afip/AfipWsaa.php";
include_once "FE_Pack_WSFE/afip/AfipWsfev1.php";
require_once 'bejerman-xml/api-bejerman/BejermanClient.php';


$num_factura = "";
$fecha_hoy = date('d/m/Y');

$cod_usuario = $_SESSION['id'];
// LEO EL USUARIO


//echo "row_usuarios['codnum']  = ".$row_usuarios['codnum']." -  ";
//echo "row_usuarios['nombre']  = ".$row_usuarios['nombre']." -  ";
//echo "COD USUARIO = ".$cod_usuario." -- ";
mysqli_select_db($amercado, $database_amercado);
//$query_num_remates = "SELECT * FROM remates  ORDER BY `ncomp` desc";
$query_num_remates = "SELECT * FROM remates WHERE servicios IS NOT NULL AND gastos IS NOT NULL AND fecreal IS NOT NULL AND codcli != 940 ORDER BY `ncomp` desc";
//$query_num_remates = sprintf("SELECT * FROM `remates` WHERE `fecest` >= NOW() ORDER BY `ncomp` desc");
$num_remates = mysqli_query($amercado, $query_num_remates) or die("ERROR LEYENDO REMATES");
$row_num_remates = mysqli_fetch_assoc($num_remates);
$totalRows_num_remates = mysqli_num_rows($num_remates);


$sigo_y_grabo = 1;
$todo_ok = 1;

if (isset($_POST['concepto']) && GetSQLValueString($_POST['concepto'], "int")!="NULL") {

	if (!empty($_POST['tipoiva'] || $_POST['tipoiva'] == 0))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto1']) && GetSQLValueString($_POST['concepto1'], "int")!="NULL") {
	if (!empty($_POST['tipoiva1']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto2']) && GetSQLValueString($_POST['concepto2'], "int")!="NULL") {
	if (!empty($_POST['tipoiva2']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto3']) && GetSQLValueString($_POST['concepto3'], "int")!="NULL") {
	if (!empty($_POST['tipoiva3']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto4']) && GetSQLValueString($_POST['concepto4'], "int")!="NULL") {
	if (!empty($_POST['tipoiva4']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto5']) && GetSQLValueString($_POST['concepto5'], "int")!="NULL") {
	if (!empty($_POST['tipoiva5']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto6']) && GetSQLValueString($_POST['concepto6'], "int")!="NULL") {
	if (!empty($_POST['tipoiva6']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto7']) && GetSQLValueString($_POST['concepto7'], "int")!="NULL") {
	if (!empty($_POST['tipoiva7']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto8']) && GetSQLValueString($_POST['concepto8'], "int")!="NULL") {
	if (!empty($_POST['tipoiva8']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto9']) && GetSQLValueString($_POST['concepto9'], "int")!="NULL") {
	if (!empty($_POST['tipoiva9']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto10']) && GetSQLValueString($_POST['concepto10'], "int")!="NULL") {
	if (!empty($_POST['tipoiva10']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto11']) && GetSQLValueString($_POST['concepto11'], "int")!="NULL") {
	if (!empty($_POST['tipoiva11']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto12']) && GetSQLValueString($_POST['concepto12'], "int")!="NULL") {
	if (!empty($_POST['tipoiva12']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto13']) && GetSQLValueString($_POST['concepto13'], "int")!="NULL") {
	if (!empty($_POST['tipoiva13']))
		$oka = 1;
	else
		$todo_ok = 0;
}
if (isset($_POST['concepto14']) && GetSQLValueString($_POST['concepto14'], "int")!="NULL") {
	if (!empty($_POST['tipoiva14']))
		$oka = 1;
	else
		$todo_ok = 0;
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura") && $todo_ok == 1) {
	//echo "TODO_OK = ".$todo_ok;
	//========================================================================================
	//Datos Correspondientes a la factura
    /**
     * Para conocer que dato va en cada parametro se puede consultar el archivo:
     *
     * - fe/docs/Estructura de Datos del Request de WSFEv1.docx
     *
     *
     * Y para mas informacion se puede revisar el manual del WS
     * 
     * - fe/docs/manual_desarrollador_COMPG_v2.pdf 
     * 
     */
    
    //Cabecera
    $CbteTipo = 7; // N Deb B - Ver - AfipWsfev1::FEParamGetTiposCbte()
    $PtoVta = 2;

    //Requerimiento
    $Concepto = 3; //Productos y Servicios
    $DocTipo = 96; //DNI 80; //CUIT
	
	$totiva21 = intval($_POST['totiva21']);
	$totneto105 = intval($_POST['totneto105']);
	$totiva105 = intval($_POST['totiva105']);
	$totcomis = intval($_POST['totcomis']);
	$totneto21 = intval($_POST['totneto21']);
	$tot_general = intval($_POST['tot_general']);
	
	
	mysqli_select_db($amercado, $database_amercado);
	$query_cliente2 = sprintf("SELECT * FROM entidades WHERE cuit = %s",GetSQLValueString($_POST['cuit'],"text"));
	$cliente2 = mysqli_query($amercado, $query_cliente2) or die("ERROR EN LECTURA ENTIDADES 148");
	$row_cliente2 = mysqli_fetch_assoc($cliente2);

	$cod_cliente = $row_cliente2['codnum'];
	
	$cuit_enti2 = "";
    $cuit_enti = $row_cliente2['cuit'];
	if (isset($cuit_enti)) {
		$cuit_enti2 =substr($cuit_enti, 3, 8); // str_replace("-","",$cuit_enti);
		//echo " CUIT   -   ".$cuit_enti2."    -   ";
	}
	//echo " CUIT   -   ".$cuit_enti."    -   ";
	$DocNro = $cuit_enti2; //30661087753; // 30710183437; //30661087753;
    /**
     * Estos dos parametros representan el numero de factura desde/hasta pero deben ser iguales
     * Se obtienen mediante el metodo: $wsfe->FECompUltimoAutorizado($CbteTipo,$PtoVta);
     *
     * $CbteDesde = $wsfe->FECompUltimoAutorizado($CbteTipo,$PtoVta);;
     * $CbteHasta = $wsfe->FECompUltimoAutorizado($CbteTipo,$PtoVta);;
     *
     */
    //$CbteDesde = $wsfe->FECompUltimoAutorizado($CbteTipo,$PtoVta);//1;
	//$CbteHasta = $wsfe->FECompUltimoAutorizado($CbteTipo,$PtoVta);//1;

    $CbteFch      = intval(date('Ymd'));
    $ImpTotal     = $tot_general; //GetSQLValueString($_POST['tot_general'], "double"); //121.00;
    $ImpTotConc   = 0.00; // GetSQLValueString($_POST['totiva21_1'], "double"); // 0.00;
    $ImpNeto      = $totneto21 + $totneto105 + $totcomis; //GetSQLValueString($_POST['totneto21'], "double") + GetSQLValueString($_POST['totneto105'], "double"); //100.00; 
    $ImpOpEx      = 0.00;
    $ImpIVA       = $totiva21 + $totiva105;//GetSQLValueString($_POST['totiva21'], "double") + GetSQLValueString($_POST['totiva105'], "double"); // 21.00;
    $ImpTrib      = 0.00;
    $FchServDesde = intval(date('Ymd'));
    $FchServHasta = intval(date('Ymd'));
    $FchVtoPago   = intval(date('Ymd'));
    $MonId        = 'PES'; // Pesos (AR) - Ver - AfipWsfev1::FEParamGetTiposMonedas()
    $MonCotiz     = 1.00;

	//echo " IMPTOTAL = ".$ImpTotal."  IMPNETO  = ".$ImpNeto."  ImpIVA  = ".$ImpIVA."   ";

    //Informacion para agregar al array Tributos
    /**
     * Esto aplica si las facturas tienen tributos agregados
     */
    $tributoId = null; // Ver - AfipWsfev1::FEParamGetTiposTributos()
    $tributoDesc = null;
    $tributoBaseImp = null;
    $tributoAlic = null;
    $tributoImporte = null;

    //Informacion para agregar al array IVA
	if (isset($_POST['totiva105']) && $_POST['totiva105'] != 0.00 && isset($_POST['totiva21']) && $_POST['totiva21'] != 0.00) {
		$IvaAlicuotaId_1 = 4; // 10.5% Ver - AfipWsfev1::FEParamGetTiposIva()
		$IvaAlicuotaBaseImp_1 = $totneto105;
		$IvaAlicuotaImporte_1 = $totiva105;
	
		$IvaAlicuotaId_2 = 5; // 21% Ver - AfipWsfev1::FEParamGetTiposIva()
		$IvaAlicuotaBaseImp_2 = $totneto21  + $totcomis;
		$IvaAlicuotaImporte_2 = $totiva21;
		
		$Iva = array(
			'AlicIva' => array ( 
				array (
					'Id' => $IvaAlicuotaId_1,
					'BaseImp' => number_format(abs($IvaAlicuotaBaseImp_1),2,'.',''),
					'Importe' => number_format(abs($IvaAlicuotaImporte_1),2,'.','')
				),
				array (
					'Id' => $IvaAlicuotaId_2,
					'BaseImp' => number_format(abs($IvaAlicuotaBaseImp_2),2,'.',''),
					'Importe' => number_format(abs($IvaAlicuotaImporte_2),2,'.','')
				)
			)
		);
	 }
	else {
		if (isset($_POST['totiva21']) && $_POST['totiva21'] != 0.00) {
	    	$IvaAlicuotaId = 5; // 21% Ver - AfipWsfev1::FEParamGetTiposIva()
	    	$IvaAlicuotaBaseImp = $_POST['totneto21'] + $_POST['totcomis'];// 100.00;
    		$IvaAlicuotaImporte = $_POST['totiva21'] ;//21.00;
		}
		else {
			if (isset($_POST['totiva105']) && $_POST['totiva105'] != 0.00) {
				$IvaAlicuotaId = 4; // 10.5% Ver - AfipWsfev1::FEParamGetTiposIva()
	    		$IvaAlicuotaBaseImp = $_POST['totneto105'] ;// 100.00;
    			$IvaAlicuotaImporte = $_POST['totiva105'] ;//21.00;
			}
			else if($_POST['totneto_1'] != 0.00) {
				//echo "ESTOY EN IVA 3 ";
				$IvaAlicuotaId = 3; // 0 % Ver - AfipWsfev1::FEParamGetTiposIva() 1 = No gravado
				$IvaAlicuotaBaseImp = $_POST['totneto_1']; // 0.00;
				$IvaAlicuotaImporte = 0.00;
				$ImpTotConc = $_POST['tot_general'];
				$ImpTotal = $_POST['tot_general'];
				$ImpOpEx = 0.00;
			  }
		}
		if (isset($Iva) || isset($IvaAlicuotaBaseImp) || isset($IvaAlicuotaImporte))
        {
            if (empty($Iva))
            {
                $Iva = array(
                    'AlicIva' => array (
                        'Id' => $IvaAlicuotaId,
                        'BaseImp' => number_format(abs($IvaAlicuotaBaseImp),2,'.',''),
                        'Importe' => number_format(abs($IvaAlicuotaImporte),2,'.','')
                        )
                    );
					var_dump($Iva);
            }
		}
	}
	//=====================================================================================
	//LA MANDO AL WS
	
	$sigo_y_grabo = 1;
	


	/**
	 * En el archivo php.ini se deben habilitar las siguientes extensiones
	 *
	 * extension=php_openssl (.dll / .so)
	 * extension=php_soap    (.dll / .so)
	 *
	 */

	//error_reporting(E_ALL);
	//ini_set('display_errors','Yes');

	//Cargando archivo de configuracion
	include_once "FE_Pack_WSFE/config.php";
	include_once "FE_Pack_WSFE/library/functions.php";

	//Cargando modelos de conexion a WebService
	include_once MDL_PATH."AfipWsaa.php";
	include_once MDL_PATH."AfipWsfev1.php";


	//Datos correspondiente a la empresa que emite la factura
    //CUIT (Sin guiones)
    $empresaCuit  = '30718033612'; // '20233616126';
    //El alias debe estar mencionado en el nombre de los archivos de certificados y firmas digitales
    $empresaAlias = 'SubastasTesting'; // 'ldb'; // 'AMERCADO1';


	//Obtener los datos de la factura que se desea generar
    //Elegir uno de los include como para tener diferentes tipos de factura
    //include "FE_Pack_WSFE/test/data/TestRegistrarFeMultiIVA_ejemplo_Factura_tipo_A.php";
    //include "data/TestRegistrarFe_ejemplo_Factura_tipo_B.php";


	//WebService que utilizara la autenticacion
	$webService   = 'wsfe';
	//Creando el objeto WSAA (Web Service de Autenticaci�n y Autorizaci�n)
	$wsaa = new AfipWsaa($webService,$empresaAlias);

	//Creando el TA (Ticket de acceso)
	if ($ta = $wsaa->loginCms())
	{
    	$token      = $ta['token'];
    	$sign       = $ta['sign'];
    	$expiration = $ta['expiration'];
    	$uniqueid   = $ta['uniqueid'];
		
		//echo "  -  VOY A CONECTAR CON EL WS    -   ";

    	//Conectando al WebService de Factura electronica (WsFev1)
    	$wsfe = new AfipWsfev1($empresaCuit,$token,$sign);

    	//Obteniendo el ultimo numero de comprobante autorizado
    	$CompUltimoAutorizado = $wsfe->FECompUltimoAutorizado($PtoVta,$CbteTipo);
    	//echo "<h3>wsfe->FECompUltimoAutorizado(PtoVta,CbteTipo)</h3>";
    	//pr($CompUltimoAutorizado); //====================== COMENTADO ==========
    
    	/**
     	 * Aca se puede hacer una comparacion del Ultimo Comprobante Autorizado
     	 * y el ultimo comprobante que se registro en la base de datos.
    	 */

        
        $ncomp_asoc = $_POST['ncbterel'];
        $ptovta_asoc = 2;
        $tcomp_asoc = $_POST['tcbterel2'];
        
    	$CbteDesde = $CompUltimoAutorizado['CbteNro'] + 1;
    	$CbteHasta = $CbteDesde;
		$num_factura = $CbteDesde;
		$num_fac = $CbteDesde;
    	//Armando el array para el Request
    	//La estructura de este array esta dise�ada de acuerdo al registro XML del WebService y utiliza las variables antes declaradas.
        $FeCAEReq = array (
            'FeCAEReq' => array (
                'FeCabReq' => array (
                    'CantReg' => 1,
                    'CbteTipo' => $CbteTipo,
                    'PtoVta' => $PtoVta
                    ),
                'FeDetReq' => array (
                    'FECAEDetRequest' => array(
                        'Concepto' => $Concepto,
                        'DocTipo' => $DocTipo,
                        'DocNro' => $DocNro,
                        'CbteDesde' => $CbteDesde,
                        'CbteHasta' => $CbteHasta,
                        'CbteFch' => $CbteFch,
                        'FchServDesde' => $FchServDesde,
                        'FchServHasta' => $FchServHasta,
                        'FchVtoPago' => $FchVtoPago,
                        'ImpTotal' => number_format(abs($ImpTotal),2,'.',''),
                        'ImpTotConc' => number_format(abs($ImpTotConc),2,'.',''),
                        'ImpNeto' => number_format(abs($ImpNeto),2,'.',''),
                        'ImpOpEx' => number_format(abs($ImpOpEx),2,'.',''),
                        'ImpIVA' => number_format(abs($ImpIVA),2,'.',''),
                        'ImpTrib' => number_format(abs($ImpTrib),2,'.',''),
                        'MonId' => $MonId,
                        'MonCotiz' => $MonCotiz
                   	)
                )
            ),
        );


        //==========================================================================
        
        $cbte_asoc = array(
            'CbteAsoc' => array(
                'Tipo' => $tcomp_asoc,
                'PtoVta' => $ptovta_asoc, // Id del opcion
                'Nro'  => $ncomp_asoc, //intval($compro_asociado->observaciones),
                 ),
        );
        $FeCAEReq['FeCAEReq']['FeDetReq']['FECAEDetRequest']['CbtesAsoc'] = $cbte_asoc;
        //=========================================================================
        
        if ($tributoBaseImp || $tributoImporte)    	{
           	$Tributos = array(
               	'Tributo' => array (
                   	'Id' => $tributoId,
                   	'Desc' => $tributoDesc,
                   	'BaseImp' => number_format(abs(floatval($tributoBaseImp)),2,'.',''),
                   	'Alic' => number_format(abs(floatval($tributoAlic)),2,'.',''),
                   	'Importe' => number_format(abs(floatval($tributoImporte)),2,'.','')
               	)
           	);
           	$FeCAEReq['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Tributos'] = $Tributos;
       	}
		if (isset($Iva) || isset($IvaAlicuotaBaseImp) || isset($IvaAlicuotaImporte))
        {
            if (empty($Iva))
            {
                $Iva = array(
                    'AlicIva' => array (
                        'Id' => $IvaAlicuotaId,
                        'BaseImp' => number_format(abs($IvaAlicuotaBaseImp),2,'.',''),
                        'Importe' => number_format(abs($IvaAlicuotaImporte),2,'.','')
                        )
                    );
        	}
		}
       	$FeCAEReq['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Iva'] = $Iva;
/*
   		echo '
    		<table>
        	<caption>wsfe->FECAESolicitar(Request)</caption>
        	<tr>
            <th >Request</th>
            <th >Response</th>
        	</tr>
        	<tr>
            <td>
    		';
    		pr($FeCAEReq);

    		echo "
            </td>
            <td>
    		";
*/
   		//Registrando la factura electronica
   		$FeCAEResponse = $wsfe->FECAESolicitar($FeCAEReq);

    		/**
    		 * Tratamiento de errores
    		 */
        
       	if (!$FeCAEResponse)   	{
           	/* Procesando ERRORES */


           	echo '<h2 class="err">NO SE HA GENERADO EL CAE</h2>
               	  <h3 class="err">ERRORES DETECTADOS</h3>';

            	$errores = $wsfe->getErrLog();
           	if (isset($errores)) {
               	foreach ($errores as $v) {
                   	pr($v);
               	}
           	}
           	echo "<hr/><h3>Response</h3>";

       	}
       	elseif (!$FeCAEResponse['FeDetResp']['FECAEDetResponse']['CAE']) {
           	/* Procesando OBSERVACIONES */

           	echo '<h2 class="msg">NO SE HA GENERADO EL CAE</h2>
               	  <h3 class="msg">OBSERVACIONES INFORMADAS</h3>';

           	if (isset($FeCAEResponse['FeDetResp']['FECAEDetResponse']['Observaciones'])) {
               	foreach ($FeCAEResponse['FeDetResp']['FECAEDetResponse']['Observaciones'] as $v) {
                   	pr($v);
               	}
           	}
           	echo "<hr/><h3>Response</h3>";
       	}    
		else {
   			//pr($FeCAEResponse); //================ COMENTADO =======================
			$CAE       = $FeCAEResponse['FeDetResp']['FECAEDetResponse']['CAE'];
			$CAEFchVto = $FeCAEResponse['FeDetResp']['FECAEDetResponse']['CAEFchVto'];
			$Resultado = $FeCAEResponse['FeDetResp']['FECAEDetResponse']['Resultado'];
			$sigo_y_grabo = 1;
		}

	}
	else		{
   		echo '
  		<hr/>
   		<h3>Errores detectados al generar el Ticket de Acceso</h3>';
   		pr($wsaa->getErrLog());
		$sigo_y_grabo = 0;
	}

}

if ($sigo_y_grabo == 1 && $todo_ok ==1) {
	
	$renglones = 0;
	$primera_vez = 1;
	if (isset($_POST['concepto']) && GetSQLValueString($_POST['concepto'], "int")!="NULL") {
		
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {

			
		    $query_mascara2 = "SELECT * FROM series  WHERE series.codnum='53'";			
            $mascara2 = mysqli_query($amercado, $query_mascara2) or die("ERROR LEYENDO SERIES 2");
            $row_mascara2 = mysqli_fetch_assoc($mascara2);

            $totalRows_mascara2 = mysqli_num_rows($mascara2);
            $mascara2 = $row_mascara2['mascara'];
			

            if ($ncomp_asoc <10) {
                $mascara2=$mascara2."-"."0000000".$ncomp_asoc ;
            }

            if ($ncomp_asoc >9 && $ncomp_asoc <=99) {
                $mascara2=$mascara2."-"."000000".$ncomp_asoc;
            }

            if ($ncomp_asoc >99 && $ncomp_asoc <=999) {
                $mascara2=$mascara2."-"."00000".$ncomp_asoc;
            }

            if ($ncomp_asoc >999 && $ncomp_asoc <=9999) {
                $mascara2=$mascara2."-"."0000".$ncomp_asoc;
            }
            if ($ncomp_asoc >9999 && $ncomp_asoc <=99999) {
                $mascara2=$mascara2."-"."000".$ncomp_asoc;
            }
            if (GetSQLValueString($_POST['tcbterel'], "int") == 8)
                $mascara2 =substr($_POST['descripcion'],1,60).' Apl.Nc. '.$mascara2;
            else
                $mascara2 =substr($_POST['descripcion'],1,60).' Apl.Fc. '.$mascara2;
            $mascara2 = "'".$mascara2."'";
            //echo $mascara2;
			$mascara2 = preg_replace('/[^(\x20-\x7F)]*/','', $mascara2);
			var_dump($mascara2);

  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2, //GetSQLValueString($_POST['tcomp'], "int"),
                       SERIE_B2, //GetSQLValueString($_POST['serie'], "int"),
                       $num_fac,
						1,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto'], "int"),
                       GetSQLValueString($mascara2, 'text'),
                       GetSQLValueString($_POST['importe'], "double"),
					   GetSQLValueString($_POST['tipoiva'], "double"),
                       GetSQLValueString($_POST['comision'], "double"),
				       $cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 1;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			  
		}
	}
	if (isset($_POST['concepto1']) && GetSQLValueString($_POST['concepto1'], "int")!="NULL") {

		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2, //GetSQLValueString($_POST['tcomp'], "int"),
                       SERIE_B2, //GetSQLValueString($_POST['serie'], "int"),
                       $num_fac,
						2,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto1'], "int"),
					   GetSQLValueString($_POST['descripcion1'], "text"),
                       GetSQLValueString($_POST['importe1'], "double"),
					   GetSQLValueString($_POST['impuesto1'], "double"),
                       GetSQLValueString($_POST['comision1'], "double"),
						$cod_usuario);


  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 2;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto2']) && GetSQLValueString($_POST['concepto2'], "int")!="NULL") {

		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2, //GetSQLValueString($_POST['tcomp'], "int"),
                       SERIE_B2, //GetSQLValueString($_POST['serie'], "int"),
                       $num_fac,
						3,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto2'], "int"),
                       GetSQLValueString($_POST['descripcion2'], "text"),
                       GetSQLValueString($_POST['importe2'], "double"),
					   GetSQLValueString($_POST['impuesto2'], "double"),
                       GetSQLValueString($_POST['comision2'], "double"),
						$cod_usuario);


  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 3;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto3']) && GetSQLValueString($_POST['concepto3'], "int")!="NULL") {

		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						4,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto3'], "int"),
                       GetSQLValueString($_POST['descripcion3'], "text"),
                       GetSQLValueString($_POST['importe3'], "double"),
					   GetSQLValueString($_POST['impuesto3'], "double"),
                       GetSQLValueString($_POST['comision3'], "double"),
						$cod_usuario);


  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 4;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto4']) && GetSQLValueString($_POST['concepto4'], "int")!="NULL") {

		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						5,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto4'], "int"),
                       GetSQLValueString($_POST['descripcion4'], "text"),
					   GetSQLValueString($_POST['importe4'], "double"),
					   GetSQLValueString($_POST['impuesto4'], "double"),
                       GetSQLValueString($_POST['comision4'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 5;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto5']) && GetSQLValueString($_POST['concepto5'], "int")!="NULL") {

		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						6,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto5'], "int"),
                       GetSQLValueString($_POST['descripcion5'], "text"),
                       GetSQLValueString($_POST['importe5'], "double"),
					   GetSQLValueString($_POST['impuesto5'], "double"),
                       GetSQLValueString($_POST['comision5'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 6;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto6']) && GetSQLValueString($_POST['concepto6'], "int")!="NULL") {

		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						7,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto6'], "int"),
                       GetSQLValueString($_POST['descripcion6'], "text"),
                       GetSQLValueString($_POST['importe6'], "double"),
					   GetSQLValueString($_POST['impuesto6'], "double"),
                       GetSQLValueString($_POST['comision6'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 7;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto7']) && GetSQLValueString($_POST['concepto7'], "int")!="NULL") {

		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						8,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto7'], "int"),
                       GetSQLValueString($_POST['descripcion7'], "text"),
                       GetSQLValueString($_POST['importe7'], "double"),
					   GetSQLValueString($_POST['impuesto7'], "double"),
                       GetSQLValueString($_POST['comision7'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 8;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto8']) && GetSQLValueString($_POST['concepto8'], "int")!="NULL") {
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						9,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto8'], "int"),
                       GetSQLValueString($_POST['descripcion8'], "text"),
                       GetSQLValueString($_POST['importe8'], "double"),
					   GetSQLValueString($_POST['impuesto8'], "double"),
                       GetSQLValueString($_POST['comision8'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 9;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto9']) && GetSQLValueString($_POST['concepto9'], "int")!="NULL") {
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						10,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto9'], "int"),
                       GetSQLValueString($_POST['descripcion9'], "text"),
                       GetSQLValueString($_POST['importe9'], "double"),
					   GetSQLValueString($_POST['impuesto9'], "double"),
                       GetSQLValueString($_POST['comision9'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 10;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto10']) && GetSQLValueString($_POST['concepto10'], "int")!="NULL") {
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						11,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto10'], "int"),
                       GetSQLValueString($_POST['descripcion10'], "text"),
                       GetSQLValueString($_POST['importe10'], "double"),
					   GetSQLValueString($_POST['impuesto10'], "double"),
                       GetSQLValueString($_POST['comision10'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 11;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto11']) && GetSQLValueString($_POST['concepto11'], "int")!="NULL") {
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						12,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto11'], "int"),
                       GetSQLValueString($_POST['descripcion11'], "text"),
                       GetSQLValueString($_POST['importe11'], "double"),
					   GetSQLValueString($_POST['impuesto11'], "double"),
                       GetSQLValueString($_POST['comision11'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 12;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto12']) && GetSQLValueString($_POST['concepto12'], "int")!="NULL") {
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						13,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto12'], "int"),
                       GetSQLValueString($_POST['descripcion12'], "text"),
                       GetSQLValueString($_POST['importe12'], "double"),
					   GetSQLValueString($_POST['impuesto12'], "double"),
                       GetSQLValueString($_POST['comision12'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 13;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto13']) && GetSQLValueString($_POST['concepto13'], "int")!="NULL") {
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						14,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto13'], "int"),
                       GetSQLValueString($_POST['descripcion13'], "text"),
                       GetSQLValueString($_POST['importe13'], "double"),
					   GetSQLValueString($_POST['impuesto13'], "double"),
                       GetSQLValueString($_POST['comision13'], "double"),
						$cod_usuario);

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 14;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	if (isset($_POST['concepto14']) && GetSQLValueString($_POST['concepto14'], "int")!="NULL") {
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
  			$insertSQL = sprintf("INSERT INTO detfac (tcomp, serie, ncomp, nreng, codrem, concafac, descrip, neto, porciva, comcob, usuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
						15,
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['concepto14'], "int"),
                       GetSQLValueString($_POST['descripcion14'], "text"),
                       GetSQLValueString($_POST['importe14'], "double"),
					   GetSQLValueString($_POST['impuesto14'], "double"),
                       GetSQLValueString($_POST['comision14'], "double"),
						$cod_usuario);
					   

  			mysqli_select_db($amercado, $database_amercado);
  			$renglones = 15;
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
			
		}
	}
	//echo "   5  - ";
	if (isset($_POST['concepto']) && GetSQLValueString($_POST['concepto'], "int")!="NULL") {
  		/// Crea la mascara 

  		$tcomp = $_POST['tcomp'];
  		$serie = $_POST['serie'];
  		//$num_fac = $_POST['num_factura'];
  		$query_mascara = "SELECT * FROM series  WHERE  series.codnum='53'";//'$tcomp'  AND series.codnum='$serie'";
  		$mascara1 = mysqli_query($amercado, $query_mascara) or die(mysqli_error($amercado));
  		$row_mascara = mysqli_fetch_assoc($mascara1);
  		$totalRows_mascara = mysqli_num_rows($mascara1);
  		$mascara  = $row_mascara['mascara'];
 		//echo "MASCARA".$mascara;
		if ($num_fac <10) {
   			$mascara=$mascara."-"."0000000".$num_fac ;
   		}

		if ($num_fac >9 && $num_fac <=99) {
  			$mascara=$mascara."-"."000000".$num_fac;
   		}

		if ($num_fac >99 && $num_fac <=999) {
			$mascara=$mascara."-"."00000".$num_fac;
  		}
  
		if ($num_fac >999 && $num_fac <=9999) {
			$mascara=$mascara."-"."0000".$num_fac;
  		}
		if ($num_fac >9999 && $num_fac <=99999) {
			$mascara=$mascara."-"."000".$num_fac;
  		}
    
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "factura")) {
			$totimp = 0.0;
			$fecha_factura1 = $_POST['fecha_factura1'] ;
			$fecha_factura1 = substr($fecha_factura1,6,4)."-".substr($fecha_factura1,3,2)."-".substr($fecha_factura1,0,2);
			$insertSQL = sprintf("INSERT INTO cabfac (tcomp, serie, ncomp, fecval, fecdoc, fecreg, cliente, fecvenc, estado, emitido, codrem, totbruto, totiva105, totiva21, totimp, totcomis, totneto105, totneto21, nrengs, nrodoc , tieneresol, en_liquid, CAE, CAEFchVto, Resultado, usuario, usuarioultmod) VALUES (%s, %s, %s, '$fecha_factura1','$fecha_factura1', '$fecha_factura1', %s, '$fecha_factura1', %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,%s, '$CAE', '$CAEFchVto', '$Resultado', %s, %s)",
                       ND_B2,
                       SERIE_B2,
                       $num_fac,
                       GetSQLValueString($cod_cliente, "int"),
					   GetSQLValueString("P", "text"),
					   GetSQLValueString("0", "int"),
                       GetSQLValueString($_POST['remate_num'], "int"),
                       GetSQLValueString($_POST['tot_general'], "double"),
                       GetSQLValueString($_POST['totiva105'], "double"),
                       GetSQLValueString($_POST['totiva21'], "double"),
                       GetSQLValueString($totimp, "double"),
                       GetSQLValueString($_POST['totcomis'], "double"),
                       GetSQLValueString($_POST['totneto105'], "double"),
                       GetSQLValueString($_POST['totneto21'], "double"),
					   GetSQLValueString($renglones, "int"),
					   GetSQLValueString($mascara, "text"),
					   GetSQLValueString("0","int"),
					   GetSQLValueString($_POST['GrupoOpciones2'], "int"),
                       $cod_usuario,
                       $cod_usuario);

			echo $CAE;
  			mysqli_select_db($amercado, $database_amercado);
  			$Result1 = mysqli_query($amercado, $insertSQL) or die(mysqli_error($amercado));
  			
		}

		//======================================================================================


    // Cabecera Para BEJERMAN
    $cliente_query = sprintf("SELECT * FROM entidades WHERE cuit = %s",GetSQLValueString($_POST['cuit'],"text"));
    $cliente_array = mysqli_query($amercado, $cliente_query) or die(mysqli_error($amercado));
    $row_cliente = mysqli_fetch_assoc($cliente_array);
    
    // Cliente
    $now = new DateTime();
    $fechaEmision = $now->format('Y-m-d');
    $beCliente = str_pad($row_cliente['codnum'], 6, '0', STR_PAD_LEFT);
    $beRazsocial = $row_cliente['razsoc'];
    $beClienteTipoIva = $row_cliente['tipoiva'];
    $clienteNroDocumento = $row_cliente['cuit'];
    $beCuit = str_replace('-', '', $clienteNroDocumento);
    $beMail = $row_cliente['mailcont'];

    // UBICACION DEL CLIENTE
    $beDirec = $row_cliente['calle'].' '.$row_cliente['numero'];

    // Consulta string de Loc
    $bePostal = $row_cliente['codpost'];


	// Consultar provincia bejerman
	$prov_query = sprintf("SELECT * FROM provincias WHERE codnum = %s",GetSQLValueString($row_cliente['codprov'],"int"));
    $prov_array = mysqli_query($amercado, $prov_query) or die(mysqli_error($amercado));
    $row_prov = mysqli_fetch_assoc($prov_array);
    $beProv = $row_prov['codbejerman'];

    $loc_query = sprintf("SELECT descripcion FROM localidades WHERE codnum = %s",GetSQLValueString($row_cliente['codloc'],"int"));
    $loc_array = mysqli_query($amercado, $loc_query) or die(mysqli_error($amercado));
    $row_loc = mysqli_fetch_assoc($loc_array);

    $beLoc = $row_loc['descripcion'];

			$ivaConcepto = [];
			$ivaNeto =  [];
			$itemIvaNoInscript = [];
			$totalUnitario = [];
			$idLote = [];
			$descripcion = [];
			$importeTotal = [];
			$codigoArticulo = [];

			for ($i = 0; $i <= $renglones; $i++) {

			if ($i == 0) {
				$iName = 'importe';
				$iNameComis = 'comision';
				$iNameImpuesto = 'tipoiva';
				$iNameConcepto = 'concepto';
				$iNameDesc = 'descripcion';

			}	else {

				// Importes unitarios sin IVA
				$iName = 'importe' . $i;
				$iNameComis = 'comision' . $i;
				$iNameImpuesto = 'tipoiva' . $i;
				$iNameConcepto = 'concepto' . $i;
				$iNameDesc = 'descripcion' . $i;

			}
			
				// Importe total unitario
				$importeUniNeg = intval($_POST[$iName]);
				array_push($totalUnitario, $importeUniNeg);

				$comisionSolo = (intval($_POST[$iNameComis]) / 100) * intval($_POST[$iName]); // 4500 (10% de 45mil)
				$comisionSoloNeg = $comisionSolo; // neg
				$iva21 = ($comisionSolo * 0.21); // neg
				
				// importe de IVA
				$importeIva = (floatval($_POST[$iNameImpuesto]) / 100) * intval($_POST[$iName]);
				$importeIvaNeg = $importeIva; // neg

				// Calculos de importes unitarios solo IVA
				array_push($ivaNeto, $importeIvaNeg);

				// Uso de plataforma/ GS admin en importeIvaNoInscripto
				
				$gsMasUso = $comisionSolo - $iva21;
				array_push($itemIvaNoInscript, $gsMasUso);

				// tipo de impuesto
				array_push($ivaConcepto, $_POST[$iNameImpuesto]);

				// articulo cod
				array_push($codigoArticulo, $_POST[$iNameConcepto]);

				
				// Descripcion de los conceptos
				array_push($descripcion, $_POST[$iNameDesc]);



			}
			
			
						// DATOS DE FACTURA CABECERA
						$tcompconc = 126;
						$tcomplot = 116;
						$fc_b = 53;
						$ptoVenta = "00002";
						$beRemate = $_POST['remate_num'];
						$totNetoItem = $_POST['totneto21_1'];
						$tazaIvaTotal = $_POST['tot_general_1'];
						$tazaIva = $_POST['totiva21_1'];
						$numero_factura = str_pad($num_fac, 8, '0', STR_PAD_LEFT);
						$relacion_query = sprintf(
							"SELECT * FROM cabfac WHERE (tcomp = $tcompconc OR tcomp = $tcomplot) AND serie = %s AND ncomp = %s",
							GetSQLValueString($fc_b, "int"),
							GetSQLValueString($_POST['ncbterel'], "int")
						);
						$relacion_array = mysqli_query($amercado, $relacion_query) or die(mysqli_error($amercado));
						$row_relacion = mysqli_fetch_assoc($relacion_array);
						$fechaValasoc = $row_relacion['fecval'];
						$comprobanteAsoc = str_pad($_POST['ncbterel'], 8, '0', STR_PAD_LEFT);
			
						// CAE
						$beCae = $CAE;
						$dateTime = DateTime::createFromFormat('Ymd', $CAEFchVto);
						$beVenciCae = $dateTime->format('Y-m-d');
						
			
						$items = [];
			
						for ($i=0; $i < $renglones; $i++) {
			
							
							$item = [
								"Comprobante_Tipo" => "ND",
								"Comprobante_Letra" => "B",
								"Comprobante_PtoVenta" => $ptoVenta,
								"Comprobante_Numero" => $numero_factura,
								"Comprobante_LoteHasta" => " ",
								"Comprobante_FechaEmision" => $fechaEmision,
								"Cliente_Codigo" => $beCliente,
								"Item_Tipo" => "A",
								"Item_CodigoArticulo" => $codigoArticulo[$i],
								"Item_CantidadUM1" => 1,
								"Item_CantidadUM2" => 0,
								"Item_DescripArticulo" => $descripcion[$i],
								"Item_PrecioUnitario" => $totalUnitario[$i],
								"Item_TasaIVAInscrip" => $ivaConcepto[$i],
								"Item_TasaIVANoInscrip" => 0,
								"Item_ImporteIVAInscrip" => $ivaNeto[$i],
								"Item_ImporteIVANoInscrip" => 0,
								"Item_ImporteTotal" => $totalUnitario[$i],
								"Item_ImporteDescComercial" => 0,
								"Item_ImporteDescFinanciero" => 0,
								"Item_ImporteDescGeneral" => 0,
								"Item_ImporteIVANoGravado" => $itemIvaNoInscript[$i],
								"Item_TipoIVA" => "1",
								"Item_ImporteDescPorLinea" => 0,
								"Item_Deposito" => "00",
								"Item_Partida" => " ",
								"Item_TasaDescPorItem" => 0,
								"Item_Importe" => $totalUnitario[$i],
						];
			
						array_push($items, $item);
					}
					// el nmrodesde es de 8 digitos
					$comprobanteAsociados = [];
					
					$comprobanteAsociado = [
						"Comprobante_Tipo" => "ND",
						"Comprobante_Letra" => "B",
						"Comprobante_NCND_PtoVenta" => $ptoVenta,
						"Comprobante_NCND_NumeroDesde" => $numero_factura,
						"Comprobante_NCND_NumeroHasta" => "",
						"Comprobante_NCND_FechaEmision" => $fechaEmision,
						"Comprobante_NCND_CodigoCliente" => $beCliente,
						"Comprobante_Asociado_Tipo" => "FC",
						"Comprobante_Asociado_Letra" => "B",
						"Comprobante_Asociado_PtoVenta" => $ptoVenta,
						"Comprobante_Asociado_NumeroDesde" => $comprobanteAsoc,
						"Comprobante_Asociado_NumeroHasta" => "",
						 "Comprobante_Asociado_FechaEmision" => $fechaValasoc,
						 "Comprobante_Asociado_CodigoCliente" => $beCliente
					];
						
					echo json_decode($compro_asociado);
						array_push($comprobanteAsociados, $comprobanteAsociado);
			
			
						$parametros = [
							"Comprobante_Tipo" => "ND",
							"Comprobante_Letra" => "B",
							"Comprobante_PtoVenta" => $ptoVenta,
							"Comprobante_Numero" => $numero_factura,
							"Comprobante_LoteHasta" => " ",
							"Comprobante_FechaEmision" => $fechaEmision,
							"Cliente_Codigo" => $beCliente,
							"Cliente_RazonSocial" => $beRazsocial,
							"Cliente_SitIVA" => $beClienteTipoIva,
							"Cliente_TipoDocumento" => 1,
							"Cliente_NroDocumento" => $beCuit,
							"Cliente_Direccion" => $beDirec,
							"Cliente_Localidad" => $beLoc,
							"Cliente_CodigoPostal" => $bePostal,
							"Cliente_Provincia" => $beProv,
							"Cliente_Email" => $beMail,
							"Cliente_CodigoClase" => "",
							"Cliente_CodigoClase2" => "",
							"Vendedor_Codigo" => "0001",
							"Comprobante_CondVenta" => "02",
							"Comprobante_FechaVencimiento" => $fechaEmision,
							"Comprobante_ImporteTotal" => $tazaIvaTotal,
							"Comprobante_Moneda" => "1",
							"Comprobante_Proyecto" => $beRemate,
							"Comprobante_TipoCambio" => "UNI",
							"Comprobante_CotizacionCambio" => 1,
							"Comprobante_ListaPrecios" => "02",
							"Comprobante_NumeroCAI" => $beCae,
							"Comprobante_FechaVencimientoCAI" => $beVenciCae,
							"Comprobante_Items" => $items,
							"Comprobante_MediosPago" => null,
							"Comprobante_DatosAdicionales" => null,
							"Comprobante_Asociado" => $comprobanteAsociados,
							"Comprobante_RelacionComprobante" => null,
						];
				
				$jsonParametros = json_encode($parametros, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
				
				echo json_encode($parametros);
				$parametrosJson = [
				  json_encode($parametros),
				  "N",
				  "R"
				];
				
			
				$response = $bejermanClient->ejecutar('VENTAS', 'IngresarComprobanteJSON', $parametrosJson);
				  
				// Verificar respuesta
			if (strpos($response, 'OK') !== false) {
				$query_update = "UPDATE cabfac SET bejerman = 1 WHERE codnum = $codnum";
				mysqli_query($amercado, $query_update) or die(mysqli_error($amercado));
			} else {
				$bejerman = "No se cargó correctamente en Bejerman, Porfavor avisar a IT";
				$query_update = "UPDATE cabfac SET bejerman = 0 WHERE codnum = $codnum";
				mysqli_query($amercado, $query_update) or die(mysqli_error($amercado));
			}
		
		if (!empty($_POST['imprime'])) { 
			$facnum = $num_fac;
			$tipcomp = ND_B2;//GetSQLValueString($_POST['tcomp'], "int");
			$numserie = SERIE_B2; //GetSQLValueString($_POST['serie'], "int");

			  echo '<script type="text/javascript">';
			  echo 'window.location.href="rp_facncB.php?ftcomp=' . $tipcomp . '&fserie=' . $numserie . '&fncomp=' . $num_fac . '";';
			  echo '</script>';
		} else { 
			// Agregar el cambio que hice en el de pedidos, que lo env�a a un php que es contenedor del factura_ok, as� queda
			// el menu a la vista
  			$facnum = $num_fac;
			  echo '<script language="javascript">alertaConfirmado("factura='.$facnum.' Error: ' . $bejerman .'");</script>';
			}
	}

}

?>
<script language="javascript">
function agregarOpciones(form)
{
var selec = form.tipos.options;
    if (selec[0].selected == true)
    {
		var seleccionar = new Option("<-- esperando seleccion","","","");
    }

    if (selec[1].selected == true)
    {
		factura.serie.value = 53;
		factura.serie_texto.value = "SERIE DE N. DEB B0002";
		factura.tcomp.value = 122;
		<?php //echo $facturanum1 ?>;
	
    }

    
}
</script>
<?php
mysqli_select_db($amercado, $database_amercado);
$query_tipo_comprobante = "SELECT * FROM tipcomp WHERE esfactura='1'";
$tipo_comprobante = mysqli_query($amercado, $query_tipo_comprobante) or die(mysqli_error($amercado));
$row_tipo_comprobante = mysqli_fetch_assoc($tipo_comprobante);
$totalRows_tipo_comprobante = mysqli_num_rows($tipo_comprobante);

mysqli_select_db($amercado, $database_amercado);
$query_cliente = "SELECT * FROM entidades WHERE (tipoent='2' OR tipoent='1') AND activo = '1' AND tipoiva != '1' AND tipoiva != '3'  ORDER BY razsoc ASC";
$cliente = mysqli_query($amercado, $query_cliente) or die(mysqli_error($amercado));
$row_cliente = mysqli_fetch_assoc($cliente);
$totalRows_cliente = mysqli_num_rows($cliente);


mysqli_select_db($amercado, $database_amercado);
$query_conceptos_a_facturar = "SELECT * FROM concafactven WHERE impuesto = 2 ORDER BY nroconc  ASC";
$conceptos_a_facturar = mysqli_query($amercado, $query_conceptos_a_facturar) or die("ERROR LEYENDO CONCEPTOS");
$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
$totalRows_conceptos_a_facturar = mysqli_num_rows($conceptos_a_facturar);

    
$colname_serie = "53";
if (isset($_POST['tcomp'])) {
  $colname_serie = addslashes($_POST['tcomp']);
}

$nivel = 9 ;
mysqli_select_db($amercado, $database_amercado);
//$query_Recordset1 = sprintf("SELECT * FROM `remates` WHERE `fecest` >= NOW() ORDER BY `ncomp` desc");
$query_Recordset1 = "SELECT * FROM remates WHERE servicios IS NOT NULL AND gastos IS NOT NULL AND fecreal IS NOT NULL AND codcli != 940  ORDER BY `ncomp` desc";
//$query_Recordset1 = "SELECT * FROM `remates` ORDER BY `ncomp` desc";
$Recordset1 = mysqli_query($amercado, $query_Recordset1) or die(mysqli_error($amercado));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

$query_impuesto = "SELECT * FROM impuestos";
$impuesto = mysqli_query($amercado, $query_impuesto) or die(mysqli_error($amercado));
$row_impuesto = mysqli_fetch_assoc($impuesto);
$totalRows_impuesto = mysqli_num_rows($impuesto);
$iva_21_desc = "21 %"; //$impuesto->fetch_array()[2];
$iva_21_porcen = 21;//$impuesto->fetch_array()[1];


$impuesto->data_seek(1); // Mover el puntero del resultado a la segunda fila
$row = $impuesto->fetch_array(); // Obtener la segunda fila como un array
$iva_15_desc = $row[2];	// Obtener el valor de la tercera columna (índice 2)
$iva_15_porcen = $row[1];
	$iva_0_porcen = 0;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<?php 
 require_once('Connections/amercado.php');  ?>

<script src="cjl_cookie.js" type="text/javascript"></script>
<script language="javascript" src="cal2.js" type="text/javascript"> 
</script>
<script language="javascript" src="cal_conf2.js" type="text/javascript"></script>
<script language="JavaScript">
	

</script>   
<script language="javascript">
function agregarOpciones2(form)
{
var selec = form.tipos.options;
    if (selec[0].selected == true)
    {
		var seleccionar = new Option("<-- esperando seleccion","","","");
    }

    if (selec[1].selected == true)
    {
		factura.tcbterel.value = 6;
		factura.tcbterel_texto.value = "FACTURA B0002";
		
		<?php //echo $facturanum1 ?>;
	
    }

    if (selec[2].selected == true)
    {
	
			factura.tcbterel.value = 8;
	factura.tcbterel_texto.value = "N CRED B0002";
		
		<?php //echo $facturanum2 ?>;
	}
}
</script>
    <script language="javascript">
function validarFormulario(form)
{
	var series = form.serie.value  // serie 
	var monto  = form.importe.value; // Monto  primer concepto
	var monto1 = form.importe1.value; // Monto segundo concepto
	var monto2 = form.importe2.value; // Monto tercer concepto
	var monto3 = form.importe3.value; // Monto cuarto concepto
	var monto4 = form.importe4.value;  // Monto Quinto concepto
	var monto5 = form.importe5.value; // Monto Sexto concepto
	var monto6  = form.importe6.value; // Monto Septimo concepto
	var monto7  = form.importe7.value; // Monto Octavo concepto
	var monto8  = form.importe8.value; // Monto Noveno concepto
	var monto9  = form.importe9.value; // Monto D�cimo concepto
	var monto10 = form.importe10.value;  // Monto Onceavo concepto
	var monto11 = form.importe11.value; // Monto Doceavo concepto
	var monto12 = form.importe12.value; // Monto Treceavo concepto
	var monto13 = form.importe13.value; // Monto Catorceavo concepto
	var monto14 = form.importe14.value; // Monto Quinceavo concepto
	//alertaError(" 1 ");
	var comision  = form.comision.value; 
	var comision1 = form.comision1.value;
	var comision2 = form.comision2.value;
	var comision3 = form.comision3.value;
	var comision4 = form.comision4.value;
	var comision5 = form.comision5.value;
	var comision6  = form.comision6.value;
	var comision7  = form.comision7.value;
	var comision8  = form.comision8.value;
	var comision9  = form.comision9.value;
	var comision10 = form.comision10.value;
	var comision11 = form.comision11.value;
	var comision12 = form.comision12.value;
	var comision13 = form.comision13.value;
	var comision14 = form.comision14.value;
	//alertaError(" 2 ");
	var porciva  = form.tipoiva.value / 100; // %IVA  primer concepto
	var porciva1 = form.tipoiva1.value / 100; // %IVA segundo concepto
	var porciva2 = form.tipoiva2.value / 100; // %IVA tercer concepto
	var porciva3 = form.tipoiva3.value / 100; // %IVA cuarto concepto
	var porciva4 = form.tipoiva4.value / 100;  // %IVA Quinto concepto
	var porciva5 = form.tipoiva5.value / 100; // %IVA Sexto concepto
	var porciva6  = form.tipoiva6.value / 100; // %IVA Septimo concepto
	var porciva7  = form.tipoiva7.value / 100; // %IVA Octavo concepto
	var porciva8  = form.tipoiva8.value / 100; // %IVA Noveno concepto
	var porciva9  = form.tipoiva9.value / 100; // %IVA D�cimo concepto
	var porciva10 = form.tipoiva10.value / 100;  // %IVA Onceavo concepto
	var porciva11 = form.tipoiva11.value / 100; // %IVA Doceavo concepto
	var porciva12 = form.tipoiva12.value / 100; // %IVA Treceavo concepto
	var porciva13 = form.tipoiva13.value / 100; // %IVA Catorceavo concepto
	var porciva14 = form.tipoiva14.value / 100; // %IVA Quinceavo concepto
   	var tot_mon =  0 ;	var tot_mon_1 =  0 ; var tot_mon_2 =  0 ; var tot_mon_3 =  0 ;
	var tot_mon_4 =  0 ; var tot_mon_5 =  0 ; var tot_mon_6 =  0 ; var tot_mon_7 =  0 ;
	var tot_mon_8 =  0 ; var tot_mon_9 =  0 ; var tot_mon_10 =  0 ;	var tot_mon_11 =  0 ;
	var tot_mon_12 =  0 ; var tot_mon_13 =  0 ;	var tot_mon_14 =  0 ;
	//alertaError(" 3 ");
   	var imp_21 =   0 ; var imp_21_1 =  0 ; var imp_21_2 =  0 ; var imp_21_3 =  0 ;
	var imp_21_4 =  0 ;	var imp_21_5 =  0 ;	var imp_21_6 =  0 ;	var imp_21_7 =  0 ;
	var imp_21_8 =  0 ;	var imp_21_9 =  0 ;	var imp_21_10 = 0 ;	var imp_21_11 = 0 ;
	var imp_21_12 = 0 ;	var imp_21_13 = 0 ;	var imp_21_14 = 0 ;
	//alertaError(" 4 ");
	var imp_105 =   0 ;	var imp_105_1 =  0 ; var imp_105_2 =  0 ; var imp_105_3 =  0 ;
	var imp_105_4 =  0 ; var imp_105_5 =  0 ; var imp_105_6 =  0 ; var imp_105_7 =  0 ;
	var imp_105_8 =  0 ; var imp_105_9 =  0 ; var imp_105_10 = 0 ; var imp_105_11 = 0 ;
	var imp_105_12 = 0 ; var imp_105_13 = 0 ; var imp_105_14 = 0 ;		
    var tot_mon_ex   = 0;
	var tot_monto    = 0;
	var imp_tot_21   = 0;
	var imp_tot_105  = 0;
	var tot_general  = 0;
	var tot_comis    = 0;
	var iva21        = 0.21;
	// PRIMER CONCEPTO
	//alertaError(" 5 ");
    if (monto.length!=0) {

		if (eval(porciva)===0) {
			tot_mon_ex = eval(monto); 	
			tot_general = eval(monto);
			var tot_mon21   = 0;
			var imp_tot_21  = 0;
			var tot_mon105  = 0;
			var imp_tot_105 = 0;
			var comis = 0;
            //alertaError(" 6 ");
		}
		if (eval(porciva)===0.21) {
            if (eval(comision) != 0)
                var comis = eval(monto+('*')+comision+('/')+100.0);
            else
                comis = 0;
            //alertaError(" 7 ");
            var tot_mon21   = eval(monto);
            var neto21 = eval(monto+('+')+comis);
            var imp_tot_21  = eval(neto21+('*')+porciva);
            var tot_general = eval(monto+('+')+imp_tot_21+('+')+comis);
            var tot_mon_ex = 0;
            var imp_tot_105 = 0;
            var tot_mon105 = 0;
            //alertaError(" 7.1 ");
        }
	    if (eval(porciva)===0.105) {
            if (eval(comision) != 0) {
                var cien = 100;
                comis = eval(monto+('*')+comision+('/')+ cien);
                var imp_tot_21  = eval(comis+('*')+iva21);
                //alertaError(" 8 ");
            }
            else {
                comis = 0;
                //alertaError(" 9 ");
            }
            var tot_mon105 = eval(monto);
            var imp_tot_105 = eval(monto+('*')+porciva);
            var tot_general = eval(monto+('+')+imp_tot_105+('+')+comis+('+')+imp_tot_21);
            var tot_mon_ex  = 0;
            var tot_mon21   = 0;
            //alertaError(" 10 ");
        }
        //alertaError(" 11.1 ");
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
        //alertaError(" 11.2 ");
    } 
	// SEGUNDO CONCEPTO	
	if (monto1.length!=0) {
		
		if (eval(porciva1)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto1); 
			tot_general = eval(tot_general+('+')+monto1);
		}
		if (eval(porciva1)===0.21) {
           if (eval(comision1) != 0) {
                var comis1 = eval(monto1+('*')+comision1+('/')+100.0);
                comis = eval(comis+('+')+comis1);
            }
            else {
                comis1 = 0;
                comis = eval(comis+('+')+comis1);
            }
            var tot_mon21_1   = eval(monto1); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_1);
            var imp_tot_21_1  = eval(monto1+('+')+comis1); 
			imp_tot_21_1  = eval(imp_tot_21_1+('*')+porciva1);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_1);
            var tot_general = eval(tot_general+('+')+monto1+('+')+imp_tot_21_1+('+')+comis1);	
		}
	    if (eval(porciva1)===0.105) {
			if (eval(comision1) != 0) {
            	var cien = 100;
                comis1 = eval(monto1+('*')+comision1+('/')+ cien);
                var imp_tot_21_1  = eval(comis1+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_1);
            }
            else {
                comis1 = 0;
            }
            var tot_mon105_1 = eval(monto1);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_1);
            var imp_tot_105_1 = eval(monto1+('*')+porciva1);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_1);
            comis = eval(comis+('+')+comis1);
            tot_general = eval(tot_general+('+')+monto1+('+')+imp_tot_105_1+('+')+comis1+('+')+imp_tot_21_1);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 
	
	// TERCER CONCEPTO
	if (monto2.length!=0) {
		
		if (eval(porciva2)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto2); 
			tot_general = eval(tot_general+('+')+monto2);
		}
		if (eval(porciva2)===0.21) {
           if (eval(comision2) != 0) {
                var comis2 = eval(monto2+('*')+comision2+('/')+100.0);
                comis = eval(comis+('+')+comis2);
            }
            else {
                comis2 = 0;
                comis = eval(comis+('+')+comis2);
            }
            var tot_mon21_2   = eval(monto2); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_2);
            var imp_tot_21_2  = eval(monto2+('+')+comis2); 
			imp_tot_21_2  = eval(imp_tot_21_2+('*')+porciva2);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_2);
            var tot_general = eval(tot_general+('+')+monto2+('+')+imp_tot_21_2+('+')+comis2);	
		}
	    if (eval(porciva2)===0.105) {
			if (eval(comision2) != 0) {
            	var cien = 100;
                comis2 = eval(monto2+('*')+comision2+('/')+ cien);
                var imp_tot_21_2  = eval(comis2+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_2);
            }
            else {
                comis2 = 0;
            }
            var tot_mon105_2 = eval(monto2);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_2);
            var imp_tot_105_2 = eval(monto2+('*')+porciva2);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_2);
            comis = eval(comis+('+')+comis2);
            tot_general = eval(tot_general+('+')+monto2+('+')+imp_tot_105_2+('+')+comis2+('+')+imp_tot_21_2);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 
		
	// CUARTO CONCEPTO
	if (monto3.length!=0) {
		if (eval(porciva3)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto3);
			tot_general = eval(tot_general+('+')+monto3);
		}
		if (eval(porciva3)===0.21) {
           if (eval(comision3) != 0) {
                var comis3 = eval(monto3+('*')+comision3+('/')+100.0);
                comis = eval(comis+('+')+comis3);
            }
            else {
                comis3 = 0;
                comis = eval(comis+('+')+comis3);
            }
            var tot_mon21_3   = eval(monto3); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_3);
            var imp_tot_21_3  = eval(monto3+('+')+comis3); 
			imp_tot_21_3  = eval(imp_tot_21_3+('*')+porciva3);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_3);
            var tot_general = eval(tot_general+('+')+monto3+('+')+imp_tot_21_3+('+')+comis3);	
		}
	    if (eval(porciva3)===0.105) {
			if (eval(comision3) != 0) {
            	var cien = 100;
                comis3 = eval(monto3+('*')+comision3+('/')+ cien);
                var imp_tot_21_3  = eval(comis3+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_3);
            }
            else {
                comis3 = 0;
            }
            var tot_mon105_3 = eval(monto3);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_3);
            var imp_tot_105_3 = eval(monto3+('*')+porciva3);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_3);
            comis = eval(comis+('+')+comis3);
            tot_general = eval(tot_general+('+')+monto3+('+')+imp_tot_105_3+('+')+comis3+('+')+imp_tot_21_3);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 
		
	// QUINTO CONCEPTO
	if (monto4.length!=0) {
		
		if (eval(porciva4)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto4); 
			tot_general = eval(tot_general+('+')+monto4);
		}
		if (eval(porciva4)===0.21) {
           if (eval(comision4) != 0) {
                var comis4 = eval(monto4+('*')+comision4+('/')+100.0);
                comis = eval(comis+('+')+comis4);
            }
            else {
                comis4 = 0;
                comis = eval(comis+('+')+comis4);
            }
            var tot_mon21_4   = eval(monto4); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_4);
            var imp_tot_21_4  = eval(monto4+('+')+comis4); 
			imp_tot_21_4  = eval(imp_tot_21_4+('*')+porciva4);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_4);
            var tot_general = eval(tot_general+('+')+monto4+('+')+imp_tot_21_4+('+')+comis4);	
		}
	    if (eval(porciva4)===0.105) {
			if (eval(comision4) != 0) {
            	var cien = 100;
                comis4 = eval(monto4+('*')+comision4+('/')+ cien);
                var imp_tot_21_4  = eval(comis4+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_4);
            }
            else {
                comis4 = 0;
            }
            var tot_mon105_4 = eval(monto4);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_4);
            var imp_tot_105_4 = eval(monto4+('*')+porciva4);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_4);
            comis = eval(comis+('+')+comis4);
            tot_general = eval(tot_general+('+')+monto4+('+')+imp_tot_105_4+('+')+comis4+('+')+imp_tot_21_4);
      	}
			
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 

	// SEXTO CONCEPTO
	if (monto5.length!=0) {
		
		if (eval(porciva5)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto5);
			tot_general = eval(tot_general+('+')+monto5);
		}
		if (eval(porciva5)===0.21) {
           if (eval(comision5) != 0) {
                var comis5 = eval(monto5+('*')+comision5+('/')+100.0);
                comis = eval(comis+('+')+comis5);
            }
            else {
                comis5 = 0;
                comis = eval(comis+('+')+comis5);
            }
            var tot_mon21_5   = eval(monto5); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_5);
            var imp_tot_21_5  = eval(monto5+('+')+comis5); 
			imp_tot_21_5  = eval(imp_tot_21_5+('*')+porciva5);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_5);
            var tot_general = eval(tot_general+('+')+monto5+('+')+imp_tot_21_5+('+')+comis5);	
		}
	    if (eval(porciva5)===0.105) {
			if (eval(comision5) != 0) {
            	var cien = 100;
                comis5 = eval(monto5+('*')+comision5+('/')+ cien);
                var imp_tot_21_5  = eval(comis5+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_5);
            }
            else {
                comis5 = 0;
            }
            var tot_mon105_5 = eval(monto5);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_5);
            var imp_tot_105_5 = eval(monto5+('*')+porciva5);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_5);
            comis = eval(comis+('+')+comis5);
            tot_general = eval(tot_general+('+')+monto5+('+')+imp_tot_105_5+('+')+comis5+('+')+imp_tot_21_5);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 
	
	// SEPTIMO CONCEPTO
		if (monto6.length!=0) {
		
		if (eval(porciva6)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto6);
			tot_general = eval(tot_general+('+')+monto6);
		}
		if (eval(porciva6)===0.21) {
           if (eval(comision6) != 0) {
                var comis6 = eval(monto6+('*')+comision6+('/')+100.0);
                comis = eval(comis+('+')+comis6);
            }
            else {
                comis6 = 0;
                comis = eval(comis+('+')+comis6);
            }
            var tot_mon21_6   = eval(monto6); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_6);
            var imp_tot_21_6  = eval(monto6+('+')+comis6); 
			imp_tot_21_6  = eval(imp_tot_21_6+('*')+porciva6);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_6);
            var tot_general = eval(tot_general+('+')+monto6+('+')+imp_tot_21_6+('+')+comis6);	
		}
	    if (eval(porciva6)===0.105) {
			if (eval(comision6) != 0) {
            	var cien = 100;
                comis6 = eval(monto6+('*')+comision6+('/')+ cien);
                var imp_tot_21_6  = eval(comis6+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_6);
            }
            else {
                comis6 = 0;
            }
            var tot_mon105_6 = eval(monto6);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_6);
            var imp_tot_105_6 = eval(monto6+('*')+porciva6);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_6);
            comis = eval(comis+('+')+comis6);
            tot_general = eval(tot_general+('+')+monto6+('+')+imp_tot_105_6+('+')+comis6+('+')+imp_tot_21_6);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 

	// OCTAVO CONCEPTO
	if (monto7.length!=0) {
		
		if (eval(porciva7)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto7);
			tot_general = eval(tot_general+('+')+monto7);
		}
		if (eval(porciva7)===0.21) {
           if (eval(comision7) != 0) {
                var comis7 = eval(monto7+('*')+comision7+('/')+100.0);
                comis = eval(comis+('+')+comis7);
            }
            else {
                comis7 = 0;
                comis = eval(comis+('+')+comis7);
            }
            var tot_mon21_7   = eval(monto7); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_7);
            var imp_tot_21_7  = eval(monto7+('+')+comis7); 
			imp_tot_21_7  = eval(imp_tot_21_7+('*')+porciva7);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_7);
            var tot_general = eval(tot_general+('+')+monto7+('+')+imp_tot_21_7+('+')+comis7);	
		}
	    if (eval(porciva7)===0.105) {
			if (eval(comision7) != 0) {
            	var cien = 100;
                comis7 = eval(monto7+('*')+comision7+('/')+ cien);
                var imp_tot_21_7  = eval(comis7+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_7);
            }
            else {
                comis7 = 0;
            }
            var tot_mon105_7 = eval(monto7);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_7);
            var imp_tot_105_7 = eval(monto7+('*')+porciva7);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_7);
            comis = eval(comis+('+')+comis7);
            tot_general = eval(tot_general+('+')+monto7+('+')+imp_tot_105_7+('+')+comis7+('+')+imp_tot_21_7);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 
		

	// NOVENO CONCEPTO
		if (monto8.length!=0) {
		
		if (eval(porciva8)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto8);
			tot_general = eval(tot_general+('+')+monto8);
		}
		if (eval(porciva8)===0.21) {
           if (eval(comision8) != 0) {
                var comis8 = eval(monto8+('*')+comision8+('/')+100.0);
                comis = eval(comis+('+')+comis8);
            }
            else {
                comis8 = 0;
                comis = eval(comis+('+')+comis8);
            }
            var tot_mon21_8   = eval(monto8); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_8);
            var imp_tot_21_8  = eval(monto8+('+')+comis8); 
			imp_tot_21_8  = eval(imp_tot_21_8+('*')+porciva8);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_8);
            var tot_general = eval(tot_general+('+')+monto8+('+')+imp_tot_21_8+('+')+comis8);	
		}
	    if (eval(porciva8)===0.105) {
			if (eval(comision8) != 0) {
            	var cien = 100;
                comis8 = eval(monto8+('*')+comision8+('/')+ cien);
                var imp_tot_21_8  = eval(comis8+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_8);
            }
            else {
                comis8 = 0;
            }
            var tot_mon105_8 = eval(monto8);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_8);
            var imp_tot_105_8 = eval(monto8+('*')+porciva8);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_8);
            comis = eval(comis+('+')+comis8);
            tot_general = eval(tot_general+('+')+monto8+('+')+imp_tot_105_8+('+')+comis8+('+')+imp_tot_21_8);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 
		

	// DECIMO CONCEPTO
	if (monto9.length!=0) {
		
		if (eval(porciva9)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto9); 
			tot_general = eval(tot_general+('+')+monto9);
		}
		if (eval(porciva9)===0.21) {
           if (eval(comision9) != 0) {
                var comis9 = eval(monto9+('*')+comision9+('/')+100.0);
                comis = eval(comis+('+')+comis9);
            }
            else {
                comis9 = 0;
                comis = eval(comis+('+')+comis9);
            }
            var tot_mon21_9   = eval(monto9); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_9);
            var imp_tot_21_9  = eval(monto9+('+')+comis9); 
			imp_tot_21_9  = eval(imp_tot_21_9+('*')+porciva9);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_9);
            var tot_general = eval(tot_general+('+')+monto9+('+')+imp_tot_21_9+('+')+comis9);	
		}
	    if (eval(porciva9)===0.105) {
			if (eval(comision9) != 0) {
            	var cien = 100;
                comis9 = eval(monto9+('*')+comision9+('/')+ cien);
                var imp_tot_21_9  = eval(comis9+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_9);
            }
            else {
                comis9 = 0;
            }
            var tot_mon105_9 = eval(monto9);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_9);
            var imp_tot_105_9 = eval(monto9+('*')+porciva9);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_9);
            comis = eval(comis+('+')+comis9);
            tot_general = eval(tot_general+('+')+monto9+('+')+imp_tot_105_9+('+')+comis9+('+')+imp_tot_21_9);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 

	//  CONCEPTO ONCE
		if (monto10.length!=0) {
		
		if (eval(porciva10)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto10); 
			tot_general = eval(tot_general+('+')+monto10);
		}
		if (eval(porciva10)===0.21) {
           if (eval(comision10) != 0) {
                var comis10 = eval(monto10+('*')+comision10+('/')+100.0);
                comis = eval(comis+('+')+comis10);
            }
            else {
                comis10 = 0;
                comis = eval(comis+('+')+comis10);
            }
            var tot_mon21_10   = eval(monto10); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_10);
            var imp_tot_21_10  = eval(monto10+('+')+comis10); 
			imp_tot_21_10  = eval(imp_tot_21_10+('*')+porciva10);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_10);
            var tot_general = eval(tot_general+('+')+monto10+('+')+imp_tot_21_10+('+')+comis10);	
		}
	    if (eval(porciva10)===0.105) {
			if (eval(comision10) != 0) {
            	var cien = 100;
                comis10 = eval(monto10+('*')+comision10+('/')+ cien);
                var imp_tot_21_10 = eval(comis10+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_10);
            }
            else {
                comis10 = 0;
            }
            var tot_mon105_10 = eval(monto10);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_10);
            var imp_tot_105_10 = eval(monto10+('*')+porciva10);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_10);
            comis = eval(comis+('+')+comis10);
            tot_general = eval(tot_general+('+')+monto10+('+')+imp_tot_105_10+('+')+comis10+('+')+imp_tot_21_10);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 

	//  CONCEPTO DOCE
		if (monto11.length!=0) {
		
		if (eval(porciva11)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto11); 
			tot_general = eval(tot_general+('+')+monto11);
		}
		if (eval(porciva11)===0.21) {
           if (eval(comision11) != 0) {
                var comis11 = eval(monto11+('*')+comision11+('/')+100.0);
                comis = eval(comis+('+')+comis11);
            }
            else {
                comis11 = 0;
                comis = eval(comis+('+')+comis11);
            }
            var tot_mon21_11   = eval(monto11); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_11);
            var imp_tot_21_11  = eval(monto11+('+')+comis11); 
			imp_tot_21_11  = eval(imp_tot_21_11+('*')+porciva11);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_11);
            var tot_general = eval(tot_general+('+')+monto11+('+')+imp_tot_21_11+('+')+comis11);	
		}
	    if (eval(porciva11)===0.105) {
			if (eval(comision11) != 0) {
            	var cien = 100;
                comis11 = eval(monto11+('*')+comision11+('/')+ cien);
                var imp_tot_21_11  = eval(comis11+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_11);
            }
            else {
                comis11 = 0;
            }
            var tot_mon105_11 = eval(monto11);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_11);
            var imp_tot_105_11 = eval(monto11+('*')+porciva11);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_11);
            comis = eval(comis+('+')+comis11);
            tot_general = eval(tot_general+('+')+monto11+('+')+imp_tot_105_11+('+')+comis11+('+')+imp_tot_21_11);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 
		

	//  CONCEPTO TRECE
	if (monto12.length!=0) {
		
		if (eval(porciva12)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto12);
			tot_general = eval(tot_general+('+')+monto12);
		}
		if (eval(porciva12)===0.21) {
           if (eval(comision12) != 0) {
                var comis12 = eval(monto12+('*')+comision12+('/')+100.0);
                comis = eval(comis+('+')+comis12);
            }
            else {
                comis12 = 0;
                comis = eval(comis+('+')+comis12);
            }
            var tot_mon21_12   = eval(monto12); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_12);
            var imp_tot_21_12  = eval(monto12+('+')+comis12); 
			imp_tot_21_12  = eval(imp_tot_21_12+('*')+porciva12);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_12);
            var tot_general = eval(tot_general+('+')+monto12+('+')+imp_tot_21_12+('+')+comis12);	
		}
	    if (eval(porciva12)===0.105) {
			if (eval(comision12) != 0) {
            	var cien = 100;
                comis12 = eval(monto12+('*')+comision12+('/')+ cien);
                var imp_tot_21_12  = eval(comis12+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_12);
            }
            else {
                comis12 = 0;
            }
            var tot_mon105_12 = eval(monto12);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_12);
            var imp_tot_105_12 = eval(monto12+('*')+porciva12);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_12);
            comis = eval(comis+('+')+comis12);
            tot_general = eval(tot_general+('+')+monto12+('+')+imp_tot_105_12+('+')+comis12+('+')+imp_tot_21_12);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 
		

	//  CONCEPTO CATORCE
		if (monto13.length!=0) {
		
		if (eval(porciva13)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto13); 
			tot_general = eval(tot_general+('+')+monto13);
		}
		if (eval(porciva13)===0.21) {
           if (eval(comision13) != 0) {
                var comis13 = eval(monto13+('*')+comision13+('/')+100.0);
                comis = eval(comis+('+')+comis13);
            }
            else {
                comis13 = 0;
                comis = eval(comis+('+')+comis13);
            }
            var tot_mon21_13   = eval(monto13); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_13);
            var imp_tot_21_13 = eval(monto13+('+')+comis13); 
			imp_tot_21_13  = eval(imp_tot_21_13+('*')+porciva13);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_13);
            var tot_general = eval(tot_general+('+')+monto13+('+')+imp_tot_21_13+('+')+comis13);	
		}
	    if (eval(porciva13)===0.105) {
			if (eval(comision13) != 0) {
            	var cien = 100;
                comis13 = eval(monto13+('*')+comision13+('/')+ cien);
                var imp_tot_21_13  = eval(comis13+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_13);
            }
            else {
                comis13 = 0;
            }
            var tot_mon105_13 = eval(monto13);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_13);
            var imp_tot_105_13 = eval(monto13+('*')+porciva13);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_13);
            comis = eval(comis+('+')+comis13);
            tot_general = eval(tot_general+('+')+monto13+('+')+imp_tot_105_13+('+')+comis13+('+')+imp_tot_21_13);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 

	//  CONCEPTO QUINCE
    if (monto14.length!=0) {
		
		if (eval(porciva14)===0) {
			tot_mon_ex = eval(tot_mon_ex+('+')+monto14);
			tot_general = eval(tot_general+('+')+monto14);
		}
		if (eval(porciva14)===0.21) {
           if (eval(comision14) != 0) {
                var comis14 = eval(monto14+('*')+comision14+('/')+100.0);
                comis = eval(comis+('+')+comis14);
            }
            else {
                comis14 = 0;
                comis = eval(comis+('+')+comis14);
            }
            var tot_mon21_14   = eval(monto14); 
            tot_mon21 = eval(tot_mon21+('+')+tot_mon21_14);
            var imp_tot_21_14  = eval(monto14+('+')+comis14); 
			imp_tot_21_14  = eval(imp_tot_21_14+('*')+porciva14);
            imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_14);
            var tot_general = eval(tot_general+('+')+monto14+('+')+imp_tot_21_14+('+')+comis14);	
		}
	    if (eval(porciva14)===0.105) {
			if (eval(comision14) != 0) {
            	var cien = 100;
                comis14 = eval(monto14+('*')+comision14+('/')+ cien);
                var imp_tot_21_14  = eval(comis14+('*')+iva21);
                imp_tot_21 = eval(imp_tot_21+('+')+imp_tot_21_14);
            }
            else {
                comis14 = 0;
            }
            var tot_mon105_14 = eval(monto14);
            tot_mon105 = eval(tot_mon105+('+')+tot_mon105_14);
            var imp_tot_105_14 = eval(monto14+('*')+porciva14);
            imp_tot_105 = eval(imp_tot_105+('+')+imp_tot_105_14);
            comis = eval(comis+('+')+comis14);
            tot_general = eval(tot_general+('+')+monto14+('+')+imp_tot_105_14+('+')+comis14+('+')+imp_tot_21_14);
      	}
		
		form.totneto21.value = tot_mon21.toFixed(2) ;
		form.totneto105.value = tot_mon105.toFixed(2) ;
		form.totcomis.value = comis.toFixed(2) ;
		form.totiva21.value = imp_tot_21.toFixed(2) ;
		form.totiva105.value = imp_tot_105.toFixed(2) ;
		form.tot_general.value = tot_general.toFixed(2) ;
	} 
  
    // Oculto   
    //alertaError(" 12 ");
		
    form.totneto21.value = tot_mon21.toFixed(2) ;	
	form.totneto105.value = tot_mon105.toFixed(2) ;	
    form.totiva21.value = imp_tot_21.toFixed(2) ;
	form.totcomis.value = comis.toFixed(2) ;
	form.totiva105.value = imp_tot_105.toFixed(2) ;
    form.tot_general.value = tot_general.toFixed(2) ;
    // Visible
	
    form.totneto21_1.value = tot_mon21.toFixed(2) ;	
	form.totneto105_1.value = tot_mon105.toFixed(2) ;
    form.totiva21_1.value = imp_tot_21.toFixed(2) ;
	form.totcomis_1.value = comis.toFixed(2) ;
	form.totiva105_1.value = imp_tot_105.toFixed(2) ;
    form.tot_general_1.value = tot_general.toFixed(2) ;
	//alertaError(" 13 ");
}	
</script> 
<script language="javascript">
// Concepto 1
	function getprov(form) {
	var seleccion =  form.concepto.options;
	var cantidad  =  form.concepto.options.length;
	var cant = (cantidad+1) ;
	var contador = 0;
	strAlerta = "seleccion " + seleccion + "\n" + "cantidad " + cantidad + "\n" + "cant " + cant + "\n" + "contador " + contador + "\n" + "seleccion[0].selected" + seleccion[0].selected + "\n" + "seleccion[contador].selected" + "\n" + seleccion[contador].selected;
	//alertaError(strAlerta);
	for ( contador ; contador < cant ; contador++) {
   		if (seleccion[0].selected == true) { 
	  
	  		alertaError("Debe seleccionar una opcion");
	  		form.descripcion.value = "" ;
	  		form.descripcion.disabled = true ;
      		form.importe.disabled = true ;
	  		break ;	
    	}

    	if (seleccion[contador].selected) { 
	    	var opcion = new String (seleccion[contador].text);
			let posicion = opcion.indexOf("|");
			if (posicion !== -1)
    			opcion = opcion.substr(2,posicion - 2);
	  		form.descripcion.value = opcion+" ";
			//form.lote.value = opcion+" ";
			var opcion2 = new String (seleccion[contador].text);
	  		var opcion2 = opcion2.substring(posicion + 2,posicion + 6);
			form.tipoiva.value = opcion2+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision.value = 10;
			else
				form.comision.value = 0;
	  		form.importe.focus();

	   	}
		strAlerta = "seleccion " + seleccion + "\n" + "cantidad " + cantidad + "\n" + "cant " + cant + "\n" + "contador " + contador + "\n" + "seleccion[0].selected" + seleccion[0].selected + "\n" + "seleccion[contador].selected" + "\n" + seleccion[contador].selected;
		//alertaError(strAlerta);
	}
	strAlerta2 = "seleccion " + seleccion + "\n" + "cantidad " + cantidad + "\n" + "cant " + cant + "\n" + "contador " + contador + "\n" + "seleccion[0].selected" + seleccion[0].selected + "\n" + "seleccion[contador].selected" + "\n" + seleccion[contador].selected;
	//alertaError(strAlerta2);
}  
</script> 
<script language="javascript">
// Concepto 2
function getprov1(form) {

	var seleccion1 = form.concepto1.options;
	var cantidad1 =  form.concepto1.options.length;
	var cant1 = (cantidad1+1) ;
	var contador1 = 0;
  
	for (contador1; contador1<cantidad1 ; contador1++) {
   		if (seleccion1[0].selected == true)  { 
	  		alertaError("Debe seleccionar una opcion")
	  		form.descripcion1.value = "" ;
	  		form.descripcion1.disabled = true ;
      		form.importe1.disabled = true ;
	  		break ;	
    	}

    	if (seleccion1[contador1].selected == true)  { 
	  		var opcion1 = new String (seleccion1[contador1].text);
			let posicion1 = opcion1.indexOf("|");
			if (posicion1 !== -1)
    			opcion1 = opcion1.substr(2,posicion1 - 2);
	  		form.descripcion1.value = opcion1+" ";
			//form.lote.value = opcion+" ";
			var opcion2_1 = new String (seleccion1[contador1].text);
	  		var opcion2_1 = opcion2_1.substring(posicion1 + 2,posicion1 + 6);
			form.tipoiva1.value = opcion2_1+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision1.value = 10;
			else
				form.comision1.value = 0;
	  		form.importe1.focus();
   		}
   	}
}   
</script> 
<script language="javascript">
// TERCER CONCEPTO
function getprov2(form) {

	var seleccion2 = form.concepto2.options;
	var cantidad2  =  form.concepto2.options.length;
	var cant2      = (cantidad2+1) ;
	var contador2  = 0;
  
	for (contador2; contador2<cantidad2 ; contador2++) {
   		if (seleccion2[0].selected == true) { 
	  		alertaError("Debe seleccionar una opcion");
	  		form.descripcion2.value = "" ;
	  		form.descripcion2.disabled = true ;
      		form.importe2.disabled = true ;
	  		break ;	
    	}

    	if (seleccion2[contador2].selected == true)  { 
	  		var opcion2 = new String (seleccion2[contador2].text);
			let posicion2 = opcion2.indexOf("|");
			if (posicion2 !== -1)
    			opcion2 = opcion2.substr(2,posicion2 - 2);
	  		form.descripcion2.value = opcion2+" ";
			//form.lote.value = opcion+" ";
			var opcion2_2 = new String (seleccion2[contador2].text);
	  		var opcion2_2 = opcion2_2.substring(posicion2 + 2,posicion2 + 6);
			form.tipoiva2.value = opcion2_2+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision2.value = 10;
			else
				form.comision2.value = 0;
	  		form.importe2.focus();
		}
   	}
}   
</script>
<script language="javascript">
 
function getprov3(form) { 
	var seleccion3 = form.concepto3.options;
	var cantidad3 =  form.concepto3.options.length
	var cant3 = (cantidad3+1) ;
	var contador3 = 0;
  
	for (contador3; contador3<cantidad3 ; contador3++) {
		if (seleccion3[0].selected == true){ 
	  		alertaError("Debe seleccionar una opcion")
	  		form.descripcion3.value = "" ;
	  		form.descripcion3.disabled = true ;
      		form.importe3.disabled = true ;
	  		break ;	
    	}

    	if (seleccion3[contador3].selected == true) { 
		  	var opcion3 = new String (seleccion3[contador3].text);
			let posicion3 = opcion3.indexOf("|");
			if (posicion3 !== -1)
    			opcion3 = opcion3.substr(2,posicion3 - 2);
	  		form.descripcion3.value = opcion3+" ";
			//form.lote.value = opcion+" ";
			var opcion2_3 = new String (seleccion3[contador3].text);
	  		var opcion2_3 = opcion2_3.substring(posicion3 + 2,posicion3 + 6);
			form.tipoiva3.value = opcion2_3+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision3.value = 10;
			else
				form.comision3.value = 0;
	  		form.importe3.focus();
		}
   	}
}    
</script>
<script language="javascript">

function getprov4(form) {
	var seleccion4 = form.concepto4.options;
	var cantidad4 =  form.concepto4.options.length
	var cant4 = (cantidad4+1) ;
	var contador4 = 0;
  
	for (contador4; contador4<cantidad4 ; contador4++) {
   		if (seleccion4[0].selected == true) { 
	    	alertaError("Debe seleccionar una opcion")
	  		form.descripcion4.value = "" ;
	  		form.descripcion4.disabled = true ;
      		form.importe4.disabled = true ;
	  		break ;	
    	}

    	if (seleccion4[contador4].selected == true) { 
		  	var opcion4 = new String (seleccion4[contador4].text);
			let posicion4 = opcion4.indexOf("|");
			if (posicion4 !== -1)
    			opcion4 = opcion4.substr(2,posicion4 - 2);
	  		form.descripcion4.value = opcion4+" ";
			//form.lote.value = opcion+" ";
			var opcion2_4 = new String (seleccion4[contador4].text);
	  		var opcion2_4 = opcion2_4.substring(posicion4 + 2,posicion4 + 6);
			form.tipoiva4.value = opcion2_4+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision4.value = 10;
			else
				form.comision4.value = 0;
	  		form.importe4.focus();
	  	}
	}	
}  
</script>
<script language="javascript">
 
function getprov5(form) { 
	var seleccion5 = form.concepto5.options;
	var cantidad5 =  form.concepto5.options.length
	var cant5 = (cantidad5+1) ;
	var contador5 = 0;
  
	for (contador5; contador5<cantidad5 ; contador5++) {
		if (seleccion5[0].selected == true) { 
	    	alertaError("Debe seleccionar una opcion")
	  		form.descripcion5.value = "" ;
	  		form.descripcion5.disabled = true ;
      		form.importe5.disabled = true ;
	  		break ;	
    	}

    	if (seleccion5[contador5].selected == true)  { 
		  	var opcion5 = new String (seleccion5[contador5].text);
			let posicion5 = opcion5.indexOf("|");
			if (posicion5 !== -1)
    			opcion5 = opcion5.substr(2,posicion5 - 2);
	  		form.descripcion5.value = opcion5+" ";
			//form.lote.value = opcion+" ";
			var opcion2_5 = new String (seleccion5[contador5].text);
	  		var opcion2_5 = opcion2_5.substring(posicion5 + 2,posicion5 + 6);
			form.tipoiva5.value = opcion2_5+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision5.value = 10;
			else
				form.comision5.value = 0;
	  		form.importe5.focus();
		}
   	}
}   
</script>
<script language="javascript">
 
function getprov6(form) { 
	var seleccion6 = form.concepto6.options;
	var cantidad6 =  form.concepto6.options.length
	var cant6 = (cantidad6+1) ;
	var contador6 = 0;
  
	for (contador6; contador6<cantidad6 ; contador6++){ 
   		if (seleccion6[0].selected == true) { 
	    	alertaError("Debe seleccionar una opcion")
	  		form.descripcion6.value = "" ;
	  		form.descripcion6.disabled = true ;
      		form.importe6.disabled = true ;
	  		break ;	
    	}

    	if (seleccion6[contador6].selected == true) { 
		  	var opcion6 = new String (seleccion6[contador6].text);
			let posicion6 = opcion6.indexOf("|");
			if (posicion6 !== -1)
    			opcion6 = opcion6.substr(2,posicion6 - 2);
	  		form.descripcion6.value = opcion6+" ";
			//form.lote.value = opcion+" ";
			var opcion2_6 = new String (seleccion6[contador6].text);
	  		var opcion2_6 = opcion2_6.substring(posicion6 + 2,posicion6 + 6);
			form.tipoiva6.value = opcion2_6+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision6.value = 10;
			else
				form.comision6.value = 0;
	  		form.importe6.focus();
	   }
   	}
}   
</script>
<script language="javascript">

function getprov7(form) {
 	var seleccion7 = form.concepto7.options;
	var cantidad7 =  form.concepto7.options.length
	var cant7 = (cantidad7+1) ;
	var contador7 = 0;
  
	for (contador7; contador7<cantidad7 ; contador7++) {   
		if (seleccion7[0].selected == true) { 
	    	alertaError("Debe seleccionar una opcion")
	  		form.descripcion7.value = "" ;
	  		form.descripcion7.disabled = true ;
      		form.importe7.disabled = true ;
	  		break ;	
    	}

    	if (seleccion7[contador7].selected == true) { 
		  	var opcion7 = new String (seleccion7[contador7].text);
			let posicion7 = opcion7.indexOf("|");
			if (posicion7 !== -1)
    			opcion7 = opcion7.substr(2,posicion7 - 2);
	  		form.descripcion7.value = opcion7+" ";
			//form.lote.value = opcion+" ";
			var opcion2_7 = new String (seleccion7[contador7].text);
	  		var opcion2_7 = opcion2_7.substring(posicion7 + 2,posicion7 + 6);
			form.tipoiva7.value = opcion2_7+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision7.value = 10;
			else
				form.comision7.value = 0;
	  		form.importe7.focus();
   		}
   	}
}   
</script>
<script language="javascript">

function getprov8(form) {
 	var seleccion8 = form.concepto8.options;
	var cantidad8 =  form.concepto8.options.length
	var cant8 = (cantidad8+1) ;
	var contador8 = 0;
  
	for (contador8; contador8<cantidad8 ; contador8++) { 
   		if (seleccion8[0].selected == true) { 
	  	  	alertaError("Debe seleccionar una opcion")
	  		form.descripcion8.value = "" ;
	  		form.descripcion8.disabled = true ;
      		form.importe8.disabled = true ;
	  		break ;	
    	}

    	if (seleccion8[contador8].selected == true) { 
		  	var opcion8 = new String (seleccion8[contador8].text);
			let posicion8 = opcion8.indexOf("|");
			if (posicion8 !== -1)
    			opcion8 = opcion8.substr(2,posicion8 - 2);
	  		form.descripcion8.value = opcion8+" ";
			//form.lote.value = opcion+" ";
			var opcion2_8 = new String (seleccion8[contador8].text);
	  		var opcion2_8 = opcion2_8.substring(posicion8 + 2,posicion8 + 6);
			form.tipoiva8.value = opcion2_8+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision8.value = 10;
			else
				form.comision8.value = 0;
	  		form.importe8.focus();
	   }
	}
}   
</script>
<script language="javascript">

function getprov9(form) {
 	var seleccion9 = form.concepto9.options;
	var cantidad9 =  form.concepto9.options.length
	var cant9 = (cantidad9+1) ;
	var contador9 = 0;
  	
	for (contador9; contador9<cantidad9 ; contador9++) {
   		if (seleccion9[0].selected == true) { 
	  	  	alertaError("Debe seleccionar una opcion")
	  		form.descripcion9.value = "" ;
	  		form.descripcion9.disabled = true ;
      		form.importe9.disabled = true ;
	  		break ;	
    	}

    	if (seleccion9[contador9].selected == true) { 
		  	var opcion9 = new String (seleccion9[contador9].text);
			let posicion9 = opcion9.indexOf("|");
			if (posicion9 !== -1)
    			opcion9 = opcion9.substr(2,posicion9 - 2);
	  		form.descripcion9.value = opcion9+" ";
			//form.lote.value = opcion+" ";
			var opcion2_9 = new String (seleccion9[contador9].text);
	  		var opcion2_9 = opcion2_9.substring(posicion9 + 2,posicion9 + 6);
			form.tipoiva9.value = opcion2_9+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision9.value = 10;
			else
				form.comision9.value = 0;
	  		form.importe9.focus();
	
   		}
   	}
}   
</script>
<script language="javascript">

function getprov10(form) { 
	var seleccion10 = form.concepto10.options;
	var cantidad10 =  form.concepto10.options.length
	var cant10 = (cantidad10+1) ;
	var contador10 = 0;
  
	for (contador10; contador10<cantidad10 ; contador10++) {
   		if (seleccion10[0].selected == true) { 
	  		alertaError("Debe seleccionar una opcion")
	  		form.descripcion10.value = "" ;
	  		form.descripcion10.disabled = true ;
      		form.importe10.disabled = true ;
	  		break ;	
    	}

    	if (seleccion10[contador10].selected == true)    { 
		  	var opcion10 = new String (seleccion10[contador10].text);
			let posicion10 = opcion10.indexOf("|");
			if (posicion10 !== -1)
    			opcion10 = opcion10.substr(2,posicion10 - 2);
	  		form.descripcion10.value = opcion10+" ";
			//form.lote.value = opcion+" ";
			var opcion2_10 = new String (seleccion10[contador10].text);
	  		var opcion2_10 = opcion2_10.substring(posicion10 + 2,posicion10 + 6);
			form.tipoiva10.value = opcion2_10+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision10.value = 10;
			else
				form.comision10.value = 0;
	  		form.importe10.focus();
	   	}
   	}
}   
</script>
<script language="javascript">
 
function getprov11(form) { type="text/javascript"
	var seleccion11 = form.concepto11.options;
	var cantidad11 =  form.concepto11.options.length
	var cant11 = (cantidad11+1) ;
	var contador11 = 0;
  
	for (contador11; contador11<cantidad11 ; contador11++) { 
   		if (seleccion11[0].selected == true) { 
	  		alertaError("Debe seleccionar una opcion")
	  		form.descripcion11.value = "" ;
	  		form.descripcion11.disabled = true ;
      		form.importe11.disabled = true ;
	  		break ;	
    	}

    	if (seleccion11[contador11].selected == true) { 
		  	var opcion11 = new String (seleccion11[contador11].text);
			let posicion11 = opcion11.indexOf("|");
			if (posicion11 !== -1)
    			opcion11 = opcion11.substr(2,posicion11 - 2);
	  		form.descripcion11.value = opcion11+" ";
			//form.lote.value = opcion+" ";
			var opcion2_11 = new String (seleccion11[contador11].text);
	  		var opcion2_11 = opcion2_11.substring(posicion11 + 2,posicion11 + 6);
			form.tipoiva11.value = opcion2_11+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision11.value = 10;
			else
				form.comision11.value = 0;
	  		form.importe11.focus();
		}
   	}
}   
</script>
<script language="javascript">

function getprov12(form) { 
	var seleccion12 = form.concepto12.options;
	var cantidad12 =  form.concepto12.options.length
	var cant12 = (cantidad12+1) ;
	var contador12 = 0;
  
	for (contador12; contador12<cantidad12 ; contador12++) {
   		if (seleccion12[0].selected == true) { 
	    	alertaError("Debe seleccionar una opcion")
	  		form.descripcion12.value = "" ;
	  		form.descripcion12.disabled = true ;
      		form.importe12.disabled = true ;
	  		break ;	
    	}

    	if (seleccion12[contador12].selected == true)  { 
		  	var opcion12 = new String (seleccion12[contador12].text);
			let posicion12 = opcion12.indexOf("|");
			if (posicion12 !== -1)
    			opcion12 = opcion12.substr(2,posicion12 - 2);
	  		form.descripcion12.value = opcion12+" ";
			//form.lote.value = opcion+" ";
			var opcion2_12 = new String (seleccion12[contador12].text);
	  		var opcion2_12 = opcion2_12.substring(posicion12 + 2,posicion12 + 6);
			form.tipoiva12.value = opcion2_12+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision12.value = 10;
			else
				form.comision12.value = 0;
	  		form.importe12.focus();
	   	}
   	}
}
</script>
<script language="javascript">
 function getprov13(form) {
 	var seleccion13 = form.concepto13.options;
	var cantidad13 =  form.concepto13.options.length
	var cant13 = (cantidad13+1) ;
	var contador13 = 0;
  
	for (contador13; contador13<cantidad13 ; contador13++) { //alertaError();
   		if (seleccion13[0].selected == true) { 
	    	alertaError("Debe seleccionar una opcion")
	  		form.descripcion13.value = "" ;
	  		form.descripcion13.disabled = true ;
      		form.importe13.disabled = true ;
	  		break ;	
    	}
    	if (seleccion13[contador13].selected == true) { 
		  	var opcion13 = new String (seleccion13[contador13].text);
			let posicion13 = opcion13.indexOf("|");
			if (posicion13 !== -1)
    			opcion13 = opcion13.substr(2,posicion13 - 2);
	  		form.descripcion13.value = opcion13+" ";
			//form.lote.value = opcion+" ";
			var opcion2_13 = new String (seleccion13[contador13].text);
	  		var opcion2_13 = opcion2_13.substring(posicion13 + 2,posicion13 + 6);
			form.tipoiva13.value = opcion2_13+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision13.value = 10;
			else
				form.comision13.value = 0;
	  		form.importe13.focus();
	   	}
   	}
}  
</script>
<script language="javascript">
 
function getprov14(form) { type="text/javascript"
	var seleccion14 = form.concepto14.options;
	var cantidad14 =  form.concepto14.options.length
	var cant14 = (cantidad14+1) ;
	var contador14 = 0;
  	
	for (contador14; contador14<cantidad14 ; contador14++) { //alertaError();
   		if (seleccion14[0].selected == true) { 
	    	alertaError("Debe seleccionar una opcion")
	  		form.descripcion14.value = "" ;
	  		form.descripcion14.disabled = true ;
      		form.importe14.disabled = true ;
	  		break ;	
    	}
    	if (seleccion14[contador14].selected == true) { 
		  	var opcion14 = new String (seleccion14[contador14].text);
			let posicion14 = opcion14.indexOf("|");
			if (posicion14 !== -1)
    			opcion14 = opcion14.substr(2,posicion14 - 2);
	  		form.descripcion14.value = opcion14+" ";
			//form.lote.value = opcion+" ";
			var opcion2_14 = new String (seleccion14[contador14].text);
	  		var opcion2_14 = opcion2_14.substring(posicion14 + 2,posicion14 + 6);
			form.tipoiva14.value = opcion2_14+" ";
			if (form.concepto.value === "10" || form.concepto.value === "11" )
				form.comision14.value = 10;
			else
				form.comision14.value = 0;
	  		form.importe14.focus();
	  	}
   	}
}   	
</script>

<script language="javascript">
function sin_lotes(form)
{
 alertaError("Debe ingresar al menos un lote para facturar");
}

	function neto(form) {
			//alertaError("en MM_findObj    ");
  		var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    	d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  		if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  		for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  		if(!x && d.getElementById) x=d.getElementById(n); return x;
	}

	function MM_validateForm() { //v4.0
		//alertaError("en MM_validateForm    ");
  		var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  		for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    	if (val) { nm=val.name; if ((val=val.value)!="") {
      	if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      	} else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='El importe debe contener un n�mero.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
        min=test.substring(8,p); max=test.substring(p+1);
        if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    	} } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  		} if (errors) alertaError('ERROR \n'+errors);
  		document.MM_returnValue = (errors == '');
	}
	//-->

</script>
<script language="javascript">
// Remate
function getRemate(form) {
	var seleccion =  form.remate_num.options;
	var cantidad  =  form.remate_num.options.length;
	var cant = (cantidad+1) ;
	var contador = 0;
	
	for ( contador ; contador < cant ; contador++) {
   		if (seleccion[0].selected == true) { 
	  
	  		//alertaError("Debe seleccionar una opcion");
	  		form.remate_num.value = "" ;
	  		form.lugar_remate.disabled = true ;
      		form.fecha_remate.disabled = true ;
	  		break ;	
    	}

    	if (seleccion[contador].selected) { 
	    	var todo = new String (seleccion[contador].text);
			let pos1 = todo.indexOf("|");
			if (pos1 !== -1) {
    			var lugar = new String (todo.substr(pos1 +1, 150));
				let pos2 = lugar.indexOf("|");
				if (pos2 !== -1) {
					lugar = lugar.substr(pos1 - 3 , pos2 - 1);
					var fecha = todo.substr(pos2 + 7,  150);
				
				}
			}
	  		//form.remate_num.value = opcion+" ";
			form.lugar_remate.value = lugar+" ";
			form.fecha_remate.value = fecha+" ";
	  		form.codnum.focus();
			strAlerta = "lugar" + lugar + "\n" + "fecha " + fecha + "\n" + "cant " + cant + "\n" + "contador " + contador + "\n";
			//alertaError(strAlerta);
	   	}
		
	}
}
</script> 
</head>
<body>

<form id="factura" class="container" name="factura" method="POST" action="VAndB2">
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?=  $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?=  $TokenValue ?>"><!-- CSRF token value -->
  <table width="640" border="1" align="left" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">

    <tr>
      <td colspan="3" ><div align="center"><img src="images/not_deb_aut.gif" width="358" height="30" /></div></td>
    </tr>
    <tr>
      <td colspan="3" valign="top" bgcolor="#FFFFFF"><table width="100%" border="1" cellspacing="1" cellpadding="1">
        <tr>
      <td width="15%" height="20" bgcolor="#FFFFFF"><span class="ewTableHeader">Tipo de Comprobante</span></td>
          <td width="40%"><select name="tipos" onchange="agregarOpciones(this.form)">
                                      <option value="122">NOTA DE DEBITO B0002</option>
                                     
                           </select>
		  <input name="tcomp" id="tcomp"  type="hidden" />
		         </td>
          
          <td width="12%" class="ewTableHeader">Serie</td>
          <td width="33%"><input name="serie_texto" type="text"  value="SERIE DE N DEB B0002" size="25" />
		  <input name="serie" type="hidden" value="53" size="25"/>
            </td>
        </tr>
        <tr>
          <td he53ht="20" class="ewTableHea2er">N° Factura </td>
          <td><input name="num_factura" type="hidden" class="phpmakerlist" id="ncomp"  width="25" /></td>
          
          <td class="ewTableHeader">Fecha Factura</td>
		  <input name="fecha_factura" type="hidden" id="fecha_factura"  />
          <td><div class="input-container">
                        <input name="fecha_factura1" type="text" id="fecha_factura1" size="25" value="<?php echo $fecha_hoy; ?>" />
                        <a href="javascript:showCal('Calendar4')"><img src="calendar/img.gif" width="22" height="14" border="0"/></a>
                    </div></td>
        </tr>
        
        <tr>
          <td height="20" class="ewTableHeader">N° Remate </td>
          <td><?php if ($cod_usuario == 25 || $cod_usuario == 26)  { //echo "estoy en cod_usuario = ".$cod_usuario?>
              <select name="remate_num" id="remate_num" onchange="getRemate(this.form)">
            <option value="">Remate</option>
		
            
          </select>
             <?php } else  { ?>
              <select name="remate_num" id="remate_num"  required="required" onchange="getRemate(this.form)">
            <option value="">Remate</option>
		
            <?php
				do {  
			?>
            		<option value="<?php echo $row_num_remates['ncomp']?>"><?php echo $row_num_remates['ncomp']?><?php echo " | "?><?php echo substr($row_num_remates['direccion'],0,31)?><?php echo " | "?><?php echo $row_num_remates['fecreal']?></option>
            <?php 
				} while ($row_num_remates = mysqli_fetch_assoc($num_remates));
  				$rows = mysqli_num_rows($num_remates);
  				if($rows > 0) {
      				mysqli_data_seek($num_remates, 0);
	  				$row_num_remates = mysqli_fetch_assoc($num_remates);
					
  				}
			?>
          </select>
              <?php } ?>
</td>
         
          <td colspan="4" rowspan="5" valign="top" bgcolor="#FFFFFF" ><table width="100%" border="1" cellpadding="1" cellspacing="1" bgcolor="#003366">
            
         <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="images/cond_pago.gif" width="150" height="30" /></td>
           </tr>
            <tr>
              
            </tr>
             <tr>
              <td width="56%" height="39" bgcolor="#FFFFFF">&nbsp;<span class="ewTableHeader">Afecta Liquidacion</span></td>
              <td width="44%" bgcolor="#FFFFFF"><input name="GrupoOpciones2" type="radio" value='1' onclick="liquidacion(this.form)"  /></td>
          </tr>
            <tr>
              <td width="56%" height="44" bgcolor="#FFFFFF">&nbsp;<span class="ewTableHeader">No afecta Liquidacion</span></td>
              <td width="44%" bgcolor="#FFFFFF"><input type="radio" name="GrupoOpciones2" value='0' checked="checked"onclick="liquidacion(this.form)"/></td>
              </tr>
          </table></td>
          </tr>
        <tr>
          <td height="9" class="ewTableHeader">Lugar del remate </td>
          <td><input name="lugar_remate" type="text" size="25" id="lugar_remate" /></td>
          
        </tr>
		<tr>
         <td height="20" class="ewTableHeader">Fecha de remate</td>
          <td><input name="fecha_remate" type="text" size="25" /></td>
           </tr>
		   <tr>
          <td height="10" class="ewTableHeader">Cliente </td>
          <td>
			<div class="search-box-B">
        		<input id="B-search-field" type="text" autocomplete="off" name="cliente" required placeholder="Buscar..." />
        		<div class="result"></div>
    		</div>
		   </td>
          <td>&nbsp;</td>
    </tr>
	<tr hidden>
          <td height="10" class="ewTableHeader">CUIT</td>
          <td>
			<div class="search-box-cuit">
        		<input id="B-CUIT" name="cuit" type="text" />
        		<div class="result"></div>
    		</div>
		  </td>
          <td>&nbsp;</td>
     </tr>

	 <tr hidden>
          <td height="10" class="ewTableHeader">Razon Social</td>
          <td>
			<div class="search-box-razan-social">
        		<input id="B-razon-social" type="text" hidden />
        		<div class="result"></div>
    		</div>
		  </td>
          <td>&nbsp;</td>
     </tr>
     </table></td>
    </tr>
    
    <tr>
        <td colspan="3" valign="top" bgcolor="#FFFFFF"><table width="100%" border="1" cellspacing="1" cellpadding="1">
      	<td width="8%" height="20" bgcolor="#FFFFFF">&nbsp;<span class="ewTableHeader">Tipo de Cbte Relac</span></td>
          <td width="20%"><select name="tcbterel2" onchange="agregarOpciones2(this.form)">
                              <option value="6">FACTURA B0002</option>
                              <?php  if($_SESSION['id'] == 1 || $_SESSION['id'] == 49 || $_SESSION['id'] == 58)
                              echo "<option value='8'>N CRED B0002</option>";
							  ?>  
                                     
                          </select>
		  <input name="tcbt2rel" id="tcbterel"  type="hidden" />
              <input name="tcbterel_texto" id="tcbterel_texto"  type="hidden" />
            </td>
        <td width="33%" height="20" bgcolor="#FFFFFF">&nbsp;<span class="ewTableHeader">Nro. de Cbte Relacionado 
            <input name="ncbterel" type="text" class="phpmaker" id="ncbterel"  required="required" size="10" />
        </span></td>
            </table></td>
        
    </tr>
    
    <tr>
      
    </tr>
    <tr>
      <td colspan="3">
	  
	   
	  <table width="100%" border="1" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
        <tr>
          <td width="75" rowspan="2" class="ewTableHeader"  background="images/fonod_lote.jpg" > <div align="center">CONCEPTO</div></td>
          <td width="1"  rowspan="2" background="images/fonod_lote.jpg" class="ewTableHeader">
            <div  align="center">   % IVA   </div></td>
            <td width="55" rowspan="2" class="ewTableHeader"  background="images/fonod_lote.jpg"> <div align="center">DESCRIPCION</div></td>
          <td width="40" rowspan="2" class="ewTableHeader"  background="images/fonod_lote.jpg"> <div align="center">COMISION</div></td>
          <td width="68" rowspan="2" class="ewTableHeader"  background="images/fonod_lote.jpg"> <div align="center">IMPORTE</div></td>
		 	  
        </tr>
        <tr>
          
        </tr>
        
		<tr>
          <td bgcolor="#FFFFFF"><select name="concepto"  required="required" onchange="getprov(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do { 
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
			?>
					<input name="imp" type="hidden" class="phpmaker"  size="10"  value="<?php echo $row_conceptos_a_facturar['porcentaje'];?>"/>
   			<?php
  				}
			?>
          	</select></td>
            <td bgcolor="#FFFFFF"><input name="tipoiva" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion" type="text" class="phpmaker" id="descripcion" size="75" />		  </td>
         <td bgcolor="#FFFFFF"><input name="comision" type="text" id="comision" size="3" /></td> 
          <td bgcolor="#FFFFFF"><input name="importe" type="text" id="importe"  required="required" onchange="validarFormulario(this.form)"  size="20"   /></td>
          
		<input name="secuencia0" type="hidden" class="phpmaker" id="secuencia0" size="65" />
          
		 </tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto1" onchange="getprov1(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				   	} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  					$rows = mysqli_num_rows($conceptos_a_facturar);
  					if($rows > 0) {
      					mysqli_data_seek($conceptos_a_facturar, 0);
	  					$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  					}
			?>
           	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva1" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion1" type="text" class="phpmaker" id="descripcion1" size="75" /></td>
         <td bgcolor="#FFFFFF"><input name="comision1" type="text" id="comision1" size="3" /></td>
    <td bgcolor="#FFFFFF"><input name="importe1" type="text" id="importe1"  onchange="validarFormulario(this.form)"  size="20"  /></td>
	
    
      <input name="secuencia1" type="hidden" class="phpmaker" id="secuencia1" size="65" />
	    </tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto2" onchange="getprov2(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva2" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion2" type="text" class="phpmaker" id="descripcion2" size="75" /></td>
         <td bgcolor="#FFFFFF"><input name="comision2" type="text" id="comision2" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe2" type="text" id="importe2"  onchange="validarFormulario(this.form)"  size="20" /></td>
		  
         
          <input name="secuencia2" type="hidden" class="phpmaker" id="secuencia2" size="65" /> 
		</tr>
		  <tr>
          <td bgcolor="#FFFFFF"><select name="concepto3" onchange="getprov3(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <<option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
          <td bgcolor="#FFFFFF"><input name="tipoiva3" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion3" type="text" class="phpmaker" id="descripcion3" size="75" /></td>
          <td bgcolor="#FFFFFF"><input name="comision3" type="text" id="comision3" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe3" type="text" id="importe3" onchange="validarFormulario(this.form)"  size="20" /></td>
		  
          
           <input name="secuencia3" type="hidden" class="phpmaker" id="secuencia3" size="65" />
		</tr>
		  <tr>
          <td bgcolor="#FFFFFF"><select name="concepto4" onchange="getprov4(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
              <td bgcolor="#FFFFFF"><input name="tipoiva4" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion4" type="text" class="phpmaker" id="descripcion4" size="75" /></td>
           <td bgcolor="#FFFFFF"><input name="comision4" type="text" id="comision4" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe4" type="text" id="importe4" onchange="validarFormulario(this.form)"  size="20" /></td>
		   
        
          <input name="secuencia4" type="hidden" class="phpmaker" id="secuencia4" size="65" />
		</tr>
		  <tr>
          <td bgcolor="#FFFFFF"><select name="concepto5" onchange="getprov5(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
              <td bgcolor="#FFFFFF"><input name="tipoiva5" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion5" type="text" class="phpmaker" id="descripcion5" size="75" /></td>
          <td bgcolor="#FFFFFF"><input name="comision5" type="text" id="comision5" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe5" type="text" id="importe5" onchange="validarFormulario(this.form)"  size="20" /></td>
		  
          
          <input name="secuencia5" type="hidden" class="phpmaker" id="secuencia5" size="65" />
		</tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto6" onchange="getprov6(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva6" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion6" type="text" class="phpmaker" id="descripcion6" size="75" /></td>
          <td bgcolor="#FFFFFF"><input name="comision6" type="text" id="comision6" size="3" /></td> 
		   <td bgcolor="#FFFFFF"><input name="importe6" type="text" id="importe6"  onchange="validarFormulario(this.form)"  size="20" /></td>
           
         
           <input name="secuencia6" type="hidden" class="phpmaker" id="secuencia6" size="65" />
		</tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto7" onchange="getprov7(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva7" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion7" type="text" class="phpmaker" id="descripcion7" size="75" /></td>
          <td bgcolor="#FFFFFF"><input name="comision7" type="text" id="comision7" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe7" type="text" id="importe7"  onchange="validarFormulario(this.form)"  size="20" /></td>
		 
         
           <input name="secuencia7" type="hidden" class="phpmaker" id="secuencia7" size="65" />
		</tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto8" onchange="getprov8(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
				}
			?>
          	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva8" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion8" type="text" class="phpmaker" id="descripcion8" size="75" /></td>
          <td bgcolor="#FFFFFF"><input name="comision8" type="text" id="comision8" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe8" type="text" id="importe8" onchange="validarFormulario(this.form)"  size="20" /></td>
		  
         
          <input name="secuencia8" type="hidden" class="phpmaker" id="secuencia8" size="65" />
		</tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto9" onchange="getprov9(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva9" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion9" type="text" class="phpmaker" id="descripcion9" size="75" /></td>
          <td bgcolor="#FFFFFF"><input name="comision9" type="text" id="comision9" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe9" type="text" id="importe9"  onchange="validarFormulario(this.form)"  size="20" /></td>
		  
         
           <input name="secuencia9" type="hidden" class="phpmaker" id="secuencia9" size="65" />
		</tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto10" onchange="getprov10(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva10" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion10" type="text" class="phpmaker" id="descripcion10" size="75" /></td>
         <td bgcolor="#FFFFFF"><input name="comision10" type="text" id="comision10" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe10" type="text" id="importe10"  onchange="validarFormulario(this.form)"  size="20" /></td>
		  
        
          <input name="secuencia10" type="hidden" class="phpmaker" id="secuencia10" size="65" />
		</tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto11" onchange="getprov11(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva11" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion11" type="text" class="phpmaker" id="descripcion11" size="75" /></td>
          <td bgcolor="#FFFFFF"><input name="comision11" type="text" id="comision11" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe11" type="text" id="importe11" onchange="validarFormulario(this.form)"  size="20" /></td>
		  
          
           <input name="secuencia11" type="hidden" class="phpmaker" id="secuencia11" size="65" />
		</tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto12" onchange="getprov12(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva12" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion12" type="text" class="phpmaker" id="descripcion12" size="75" /></td>
          <td bgcolor="#FFFFFF"><input name="comision12" type="text" id="comision12" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe12" type="text" id="importe12"  onchange="validarFormulario(this.form)"  size="20" /></td>
		  
        
           <input name="secuencia12" type="hidden" class="phpmaker" id="secuencia12" size="65" />
		</tr>
		 <tr>
          <td bgcolor="#FFFFFF"><select name="concepto13" onchange="getprov13(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
             <td bgcolor="#FFFFFF"><input name="tipoiva13" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion13" type="text" class="phpmaker" id="descripcion13" size="75" /></td>
          <td bgcolor="#FFFFFF"><input name="comision13" type="text" id="comision13" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe13" type="text" id="importe13"  onchange="validarFormulario(this.form)"  size="20" /></td>
		           
             <input name="secuencia13" type="hidden" class="phpmaker" id="secuencia13" size="65" />
		</tr> <tr>
          <td bgcolor="#FFFFFF"><select name="concepto14" onchange="getprov14(this.form)">
		  	<option value="">[Tipo de concepto]</option>
            <?php
				do {  
			?>
            <option value="<?php echo $row_conceptos_a_facturar['nroconc'];?>"><?php echo $row_conceptos_a_facturar['nroconc'];?>&nbsp;<?php echo ($row_conceptos_a_facturar['descrip']);?><?php echo" | ";?><?php echo $row_conceptos_a_facturar['porcentaje'];?></option>
            <?php
				} while ($row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar));
  				$rows = mysqli_num_rows($conceptos_a_facturar);
  				if($rows > 0) {
      				mysqli_data_seek($conceptos_a_facturar, 0);
	  				$row_conceptos_a_facturar = mysqli_fetch_assoc($conceptos_a_facturar);
  				}
			?>
          	</select></td>
        <td bgcolor="#FFFFFF"><input name="tipoiva14" type="text" size="10" onchange="getprov(this.form)">
		  	</td>
          <td bgcolor="#FFFFFF"><input name="descripcion14" type="text" class="phpmaker" id="descripcion14" size="75" /></td>
       <td bgcolor="#FFFFFF"><input name="comision14" type="text" id="comision14" size="3" /></td>
          <td bgcolor="#FFFFFF"><input name="importe14" type="text" id="importe14"  onchange="validarFormulario(this.form)"  size="20" /></td>
		 
         
          <input name="secuencia14" type="hidden" class="phpmaker" id="secuencia14" size="65" />
		</tr>
      </table>      </td>
    </tr>
    <tr bgcolor="#FFFFFF"><td bgcolor="#FFFFFF"><table width="900" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF"><tr>
      
    </tr>
	<tr><td class="ewTableHeader">&nbsp;Imprimir </td><td><input type="checkbox" name="imprime" value="1" />
</td>
	</tr>
	 </table></td>
      
  
  
    <tr>
      <td colspan="3" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
        <tr>
          <td height="20" background="images/fonod_lote.jpg" class="ewTableHeader"><div align="center">Neto <?php  echo $iva_15_porcen ?> %</div></td>
          <td height="20" background="images/fonod_lote.jpg" class="ewTableHeader"><div align="center">Neto <?php  echo $iva_21_porcen ?> %</div></td>
          <td height="20" background="images/fonod_lote.jpg" class="ewTableHeader"><div align="center">Comision </div></td>
          <td height="20" background="images/fonod_lote.jpg" class="ewTableHeader"><div align="center">Iva <?php  echo $iva_15_porcen ?> %</div></td>
          <td height="20" background="images/fonod_lote.jpg" class="ewTableHeader"><div align="center">Iva <?php  echo $iva_21_porcen ?> %</div></td>
          
          <td height="20" background="images/fonod_lote.jpg" class="ewTableHeader"><div align="center">Total </div></td>
        </tr>
        
        <tr>
		<td><input name="totneto105_1"  type="hidden"  size="20" /></td>
          <td><input name="totneto21_1"   type="hidden"  size="20" /></td>
          <td><input name="totcomis_1"    type="hidden"  size="20" /></td>
          <td><input name="totiva105_1"   type="hidden"  size="20" /></td>
          <td><input name="totiva21_1"    type="hidden"  size="20" /></td>
          
          <td><input name="tot_general_1" type="hidden"  size="25" /></td>
        </tr>
		 <tr>
          <td><input name="totneto105"  type="text"  size="20" /></td>
          <td><input name="totneto21"   type="text"  size="20" /></td>
          <td><input name="totcomis"    type="text"  size="20" /></td>
          <td><input name="totiva105"   type="text"  size="20" /></td>
          <td><input name="totiva21"    type="text"  size="20" /></td>
          
          <td><input name="tot_general" type="text"  required="required" size="25" /></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td colspan="3" bgcolor="#FFFFFF">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" bgcolor="#FFFFFF"><table width="100%" border="1" cellspacing="1" cellpadding="1">
        <tr>
          <td><div align="center">
            
          </div></td>
          <td><div align="center">
            
            <input type="hidden" value="Quehago" id="pageOperation" name="pageOperation" />
			<input type="submit" value="Enviar a AFIP" id="evento_eliminar" name="evento_eliminar"  onclick="preguntame(this.form)"/>
			<input type="reset" value="Limpiar Formulario">
          </div></td>
          <td><div align="center">
            
          </div></td>
        </tr>
      </table></td>
    </tr>
  </table>
    <input type="hidden" name="MM_insert" value="factura">
</form>


