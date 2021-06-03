<html>
<head>
<title>REPORTE DE OPCIONES Y USUARIOS PARA MAMBO</title>
<link rel="alternate" title="Transportes Pitic Light" href="https://www.tpitic.com.mx/plportal/index2.php?option=com_rss&no_html=1" type="application/rss+xml" />
<link rel="stylesheet" href="https://www.tpitic.com.mx/plportal/templates/247portal-broad/css/template_css.css" type="text/css"/>
</head>
<body>
<form name="reporte" id="reporte" action="" method="post">
<table>
<tr><td>Nombre de usuario</td><td><input type='text' name='username' id='username' /><br /></td></tr>
<tr><td>Portal a Buscar</td><td><select name="sufijo" id="sufijo">
<option value='pl'>Light</option>
<option value='web'>Empleados</option>
<option value='audi'>Auditoria</option>
<option value='cli'>Clientes</option>
<option value='cob'>Cobranza</option>
<option value='fin'>Finanzas</option>
<option value='man'>Manuales</option>
<option value='men'>Mensajeria</option>
<option value='rh'>RH</option>
<option value='tra'>Transporte</option>
<option value='ven'>Ventas</option>
</select></td></tr>
<tr><td>Tipo de reporte</td><td>
<select name='tipoReporte' id='tipoReporte'>
<option value="0">Seleccione un reporte </option>
<option value="1">Obtener Grupos a los que pertenece un Usuario</option>
<option value="2">Obtener Opciones de un Usuario</option>
<option value="3">Grupos a los que Pertenece una Opcion</option></select></td></tr>
<tr><td>Texto a Buscar<br />(para la tercera opcion)</td><td><input type='text' id='target' name='target' /></td></tr>
<tr><td><input type="submit" value="buscar" name="buscar" /></td></tr>
</table>
</form>
</body>
</html>
<?php
class Mambo{
	var $link;
	
	function __construct(){
		$this->link = mysql_connect('dbmsql.transportespitic.com', 'adminusertpitic', 'adminusertpitic') or die(mysql_error());
		if(!mysql_select_db('globaldb', $this->link)){
			echo "error al conectar";
		}
	}
	
	function getUserGroups($user, $suffix, $tipo_reporte){
		if($this->link){
			$query = "SELECT b.userid, a.username, b.groupid, c.name FROM globaldb.only_users a
						INNER JOIN globaldb.".$suffix."_graccess_usergroup b
						  ON a.id = b.userid 
							INNER JOIN globaldb.".$suffix."_graccess c
							  ON b.groupid = c.id
							  WHERE a.username = '".$user."'
							  order by c.name ASC";
			$rs = mysql_query($query);
			if($rs){
				$table = "GRUPOS A LOS QUE PERTENECE EL USUARIO <span style='color:red;'>'".$user."'</span><br /><br />";
				$table .= "<table border='1'><tr style='padding:5px;' ><td>ID USUARIO</td><td>USUARIO</td><td>ID GRUPO</td><td>NOMBRE GRUPO</td></tr>";
				while($obj = mysql_fetch_object($rs)){
					$table .= "<tr><td>".$obj->userid."</td><td>".$obj->username."</td><td>".$obj->groupid."</td><td>".$obj->name."</td></tr>";
				}
				echo $table .= "</table>";
				echo "<script>document.getElementById('username').value='".$user."'</script>";
				echo "<script>
					for (var i=0; i<document.reporte.sufijo.options.length; i++) {
						if (document.reporte.sufijo.options[i].value == '".$suffix."')
							document.reporte.sufijo.options[i].selected = true;
					}
					
					for (var i=0; i<document.reporte.tipoReporte.options.length; i++) {
						if (document.reporte.tipoReporte.options[i].value == '".$tipo_reporte."')
							document.reporte.tipoReporte.options[i].selected = true;
					}
				</script>";

			}
		}
	}
	
	function getUserOptions($user, $suffix, $tipo_reporte){
		if($this->link){
			$query = "SELECT b.userid, a.username, d.id, d.name FROM globaldb.only_users a
					  INNER JOIN globaldb.".$suffix."_graccess_usergroup b
						ON a.id = b.userid 
						  INNER JOIN globaldb.".$suffix."_graccess_groupmenu c
							ON b.groupid = c.groupid
							  INNER JOIN globaldb.".$suffix."_menu d
								ON c.menuid = d.id
							  WHERE a.username = '".$user."'
							  order by d.name ASC";
							  
			$rs = mysql_query($query);
			if($rs){
				$table = "GRUPOS A LOS QUE PERTENECE EL USUARIO <span style='color:red;'>'".$user."'</span><br /><br />";
				$table .= "<table border='1'><tr style='padding:5px;' ><td>ID USUARIO</td><td>USUARIO</td><td>ID OPCION</td><td>NOMBRE OPCION</td></tr>";
				while($obj = mysql_fetch_object($rs)){
					$table .= "<tr><td>".$obj->userid."</td><td>".$obj->username."</td><td>".$obj->id."</td><td>".$obj->name."</td></tr>";
				}
				echo $table .= "</table>";
				echo "<script>document.getElementById('username').value='".$user."'</script>";
				echo "<script>
					for (var i=0; i<document.reporte.sufijo.options.length; i++) {
						if (document.reporte.sufijo.options[i].value == '".$suffix."')
							document.reporte.sufijo.options[i].selected = true;
					}
					
					for (var i=0; i<document.reporte.tipoReporte.options.length; i++) {
						if (document.reporte.tipoReporte.options[i].value == '".$tipo_reporte."')
							document.reporte.tipoReporte.options[i].selected = true;
					}
				</script>";
			}
		}
	}
	
	function gpoAlQuePerteneceOpcion($suffix, $target, $tipo_reporte){
		if($this->link){
			$query = "SELECT c.id as ID_GRUPO, c.name AS GRUPO, a.id AS ID_OPCION, a.name AS NOMBRE_OPCION FROM globaldb.".$suffix."_menu a 
					  INNER JOIN globaldb.".$suffix."_graccess_groupmenu b
						ON a.id = b.menuid
						  INNER JOIN globaldb.".$suffix."_graccess c
							ON b.groupid = c.id
					  WHERE a.name LIKE '%".$target."%'
					  ORDER BY NOMBRE_OPCION";
							  
			$rs = mysql_query($query);
			if($rs){
				$table = "GRUPOS QUE TIENEN OPCIONES QUE COINCIDEN CON LA BUSQUEDA DE <span style='color:red;'>'".$target."'</span><br /><br />";
				$table .= "<table border='1'><tr style='padding:5px;' ><td>ID USUARIO</td><td>GRUPO</td><td>ID OPCION</td><td>NOMBRE OPCION</td></tr>";
				while($obj = mysql_fetch_object($rs)){
					$table .= "<tr><td>".$obj->ID_GRUPO."</td><td>".$obj->GRUPO."</td><td>".$obj->ID_OPCION."</td><td>".$obj->NOMBRE_OPCION."</td></tr>";
				}
				echo $table .= "</table>";
				echo "<script>document.getElementById('target').value='".$target."'</script>";
				echo "<script>
					for (var i=0; i<document.reporte.sufijo.options.length; i++) {
						if (document.reporte.sufijo.options[i].value == '".$suffix."')
							document.reporte.sufijo.options[i].selected = true;
					}
					
					for (var i=0; i<document.reporte.tipoReporte.options.length; i++) {
						if (document.reporte.tipoReporte.options[i].value == '".$tipo_reporte."')
							document.reporte.tipoReporte.options[i].selected = true;
					}
				</script>";
			}
		}
	}
}

$objMambo = new Mambo();
$first_time = isset($_POST['first_time']) ? $_POST['first_time'] : 0;
$llamarFuncion = isset($_POST['tipoReporte']) ? $_POST['tipoReporte'] : 1;

if($first_time != 1){
	if($llamarFuncion != 0){
		switch($llamarFuncion){
			case 1: $objMambo->getUserGroups($_POST['username'], $_POST['sufijo'], $_POST['tipoReporte']); break;
			case 2: $objMambo->getUserOptions($_POST['username'], $_POST['sufijo'], $_POST['tipoReporte']); break;
			case 3: $objMambo->gpoAlQuePerteneceOpcion($_POST['sufijo'], $_POST['target'], $_POST['tipoReporte']); break;
		}
	}else{
		echo "<script>alert('debes seleccionar un tipo de reporte')</script>";
	}
}
/*$objMambo->getUserGroups('cmburboa', 'cob');
$objMambo->getUserOptions('cmburboa', 'cob');
$objMambo->gpoAlQuePerteneceOpcion('cob', 'Solicitud');*/
?>