<?php require_once('Connections/amercado.php'); 
include_once "funcion_mysqli_result.php";
if (isset($_POST['cuit'])) {
	$cliente = $_POST['cuit'];
	echo "Nro.Cliente = ".$cliente;
}
mysqli_select_db($amercado, $database_amercado);

$query_cliente = "SELECT entidades.codnum, entidades.razsoc , entidades.calle , entidades.numero ,  entidades.codloc , entidades.codprov FROM entidades WHERE cuit='$cliente' and activo=1";
$selec_cliente = mysqli_query($amercado, $query_cliente) or die("ERROR LEYENDO CLIENTE ".$cliente." ");
$row_cliente = mysqli_fetch_assoc($selec_cliente);

$cliente = $row_cliente['codnum'];
$localidad = $row_cliente['codloc'];
$provincia = $row_cliente['codprov'];
$query_localidad = "SELECT localidades.descripcion FROM localidades WHERE localidades.codnum='$localidad' AND localidades.codprov='$provincia'";
$localidad1 = mysqli_query($amercado, $query_localidad) or die("ERROR LEYENDO LOCALIDADES ".$provincia." - ".$localidad." - ");
$row_loc = mysqli_fetch_assoc($localidad1);

$query_prov = "SELECT provincias.descripcion FROM provincias WHERE provincias.codnum='$provincia'";
$provincia1 = mysqli_query($amercado, $query_prov) or die("ERROR LEYENDO PROVINCIAS ".$provincia." - ");
$row_prov = mysqli_fetch_assoc($provincia1);

$query_Recordset1 = "SELECT cabfac.nrodoc , cabfac.fecval, cabfac.totbruto , cabfac.tcomp , cabfac.codnum FROM cabfac WHERE cabfac.cliente='$cliente'  AND cabfac.estado ='P' AND cabfac.tcomp NOT IN (98,99) ORDER BY cabfac.fecreg DESC, cabfac.tcomp , cabfac.ncomp";
$Recordset1 = mysqli_query($amercado, $query_Recordset1) or die("ERROR LEYENDO CABFAC ");
//$query_Recordset1 = "SELECT cabfac.nrodoc , cabfac.fecval, cabfac.totbruto , cabfac.tcomp , cabfac.codnum FROM cabfac WHERE cabfac.cliente='$cliente'  AND cabfac.tcomp NOT IN (98,99) ORDER BY cabfac.fecreg DESC, cabfac.tcomp , cabfac.ncomp";
//$Recordset1 = mysqli_query($amercado, $query_Recordset1) or die("ERROR LEYENDO CABFAC ");

?>
<script language="javascript">
function carga_cheque(form) {
	var cantcheques = deposito_list.idcheque.length

	if (cantcheques==null) {
 		if(deposito_list.idcheque.checked == true)
	 	{ 
	   		var monto  = deposito_list.monto0.value ;
	   		var monto_cheque = eval(monto) ;
			deposito_list.total_deposito.value = monto_cheque ;
    		deposito_list.cod_num.value = deposito_list.idcheque.value ;;
	 	}
	} else {

		var monto_total = new Array(cantcheques)
		var i = 0 ;
		var pesos_total = 0
		var lista = "";
		for (i ; i < cantcheques ; i++)
		{
   			if(deposito_list.idcheque[i].checked == true)
	 		{
	   			var monto = "deposito_list.monto"+eval(i)+".value" ;
	   			var lista  = lista + deposito_list.idcheque[i].value+",";
	   			var monto = "deposito_list.monto"+eval(i)+".value" ;
	   			var monto_cheque = eval(monto) ;
	   			monto_total[i] = monto_cheque;
	   			pesos_total = eval(pesos_total+('+')+monto_cheque) ;
			}
		}
		cant_cheques = eval(lista.length-1) ;
		cheques_can = lista.substr(0,cant_cheques);
		deposito_list.total_deposito.value = pesos_total ;
		deposito_list.cod_num.value = cheques_can;
	}
  	// total_deposito
}
</script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="estilo_factura.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #ECE9D8}
-->
.seleccion {
	color: #000066; /* text color */
	font-family: Verdana; /* font name */
	font-size:13px;
	
	
}
</style>

</head>

<body>
<table class="container" width="600" border="1" cellpadding="1" cellspacing="1">
 
  <tr>
    <td colspan="5" align="center" bgcolor="#000066" class="ewAstSelListItem">Datos del Cliente </td>
  </tr>
 
  
  <form name="deposito_list" method='post' action='recibos.php'>
   <tr bgcolor="#003366">
    <td class="ewTableHeader" >Cliente&nbsp;&nbsp;&nbsp;</td>
     <td colspan="3"><input type="text"  value="<?php echo $row_cliente['razsoc'] ?>" /></td>
	</tr><input type="hidden" name="cli" value="<?php echo $cliente ?>" />
   
	<tr bgcolor="#003366">
    <td class="ewTableHeader" >Direccion&nbsp;&nbsp;&nbsp;</td>
     <td colspan="3"><input type="text"  value="<?php echo $row_cliente['calle']."  ".$row_cliente['numero'] ?>" size="35" /></td>
	
    </tr>
	<tr bgcolor="#003366">
    <td class="ewTableHeader" >Localidad&nbsp;&nbsp;&nbsp;</td>
     <td colspan="3"><input type="text"  value="<?php echo $row_loc['descripcion'] ?>" /></td>
	
    </tr>
	<tr bgcolor="#003366">
    <td class="ewTableHeader" >Provincia&nbsp;&nbsp;&nbsp;</td>
     <td colspan="3"><input type="text"  value="<?php echo $row_prov['descripcion'] ?>" /></td>
	
    </tr>
	
	<tr>
    
    <td>&nbsp;</td>
    <td class="ewMultiPagePager">Numero de Compobante</td>
    <td class="ewMultiPagePager">Fecha del Comprobante</td>
    <td class="ewMultiPagePager">Monto del Comprobante</td>
  </tr>
 <?php $i=0;
//   $total_general= 0;
  while ($registro1 = mysqli_fetch_array($Recordset1) AND $i<='30'){
  echo "<tr>"; ?>
<td width="15" bgcolor='993333'><input type='checkbox' name='idcheque' value='<?php echo $registro1['codnum'] ?>' onclick="carga_cheque(this.form)"/></td> <?php echo "<td width=\"250\" bgcolor=\"993333\"><input name=\'banco$i\' type=\'text\' class=\"seleccion\" size=\"35\" value='$registro1[0]'/></td>" ;
echo "<td bgcolor=\"993333\"><input name=\"fecha_venc$i\" type=\"text\" class=\"seleccion\"  value='$registro1[1] '/></td>" ;
echo "<td bgcolor=\"993333\"><input name=\"monto$i\" type=\"text\" class=\"seleccion\"  value='$registro1[2]'/></td>" ;    
echo "</tr>";
$i=$i+1;

		} ?>
  
    <tr>
    <td><input name="cod_num" type="hidden" class="seleccion" size="20" /></td>
    
	<td colspan="2" align="right">Total en Recibo&nbsp;&nbsp;&nbsp;</td>
    <td><input name="total_deposito" type="text" class="seleccion" size="15" /></td>
    </tr>
 <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="hidden"   name="MM_update" value="deposito_list"></td>
    <td> <input type="submit" name="Submit" value="Enviar" /></td>
    
  </tr>
  </form>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    
  </tr>
 
</table>
</body>
</html>