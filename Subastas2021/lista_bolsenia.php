<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Documento sin t&iacute;tulo</title>

<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>

<?php

require_once('Connections/amercado.php'); 
require_once('funcion_mysqli_result.php'); 
mysqli_select_db($amercado, $database_amercado);
$query_Recordset1 = "SELECT * FROM `remates` ORDER BY `ncomp` desc";
$Recordset1 = mysqli_query($amercado, $query_Recordset1) or die(mysqli_error($amercado));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
//echo $totalRows_Recordset1;
 ?>

<body>
<form id="form1" class="container" name="form1" method="post" action="rp_bolsenia.php">
  <table width="706" height="304" border="0" align="left" cellpadding="1" cellspacing="1">
    <tr>
    </tr>
    <tr>
      <td width="234">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
    <td width="34">Remate N&uacute;mero : </td>
      <td height="27" class="ewTableHeader"><select name="remate_num" id="remate_num">
        <option value="">Remate</option>
        <?php
				do {  
			?>
        <option value="<?php echo $row_Recordset1['ncomp']?>"><?php echo $row_Recordset1['ncomp']?><?php echo " - "?><?php echo $row_Recordset1['direccion']?></option>
        <?php
				} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  				$rows = mysqli_num_rows($Recordset1);
  				if($rows > 0) {
      				mysqli_data_seek($Recordset1, 0);
	  				$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  				}
			?>
      </select></td>
          <td>&nbsp;</td>
      
    </tr>
	  <tr>
              <td width="48%" height="27" bgcolor="#CFCFCF">&nbsp;<span class="ewTableHeader">ORIGINAL</span></td>
              <td width="52%" bgcolor="#CFCFCF"><input name="GrupoOpciones2" type="radio" value=1 checked="checked"  /></td>
            </tr>
            <tr>
              <td height="27" bgcolor="#CFCFCF">&nbsp;<span class="ewTableHeader">DUPLICADO</span></td>
              <td bgcolor="#CFCFCF"><input name="GrupoOpciones2" type="radio" value=0   /></td>
            </tr>
      <td width="420"><input type="submit" style='bottom: 0;' name="Submit" value="Generar PDF"/></td>
    </tr>
  </table>
</form>
</body>
</html>