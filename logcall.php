
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
<link href="header_style.css" rel="stylesheet" type="text/css">
<link href="content_style.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
	function validateForm()
		{
			var x=document.forms["frmLogCall"]["callerName"].value;
			if (x==null || x=="")
				{
					alert("Caller Name is required.");
					return false;
				}
			var y=document.forms ["frmLogCall"]["contactNo"].value;
			if (y==null || y=="")
				{
					alert("Contact Number is required.");
					return false;
				}
			var z=document.forms ["frmLogCall"]["location"].value;
			if (z==null || z=="")
				{
					alert("Location is required.");
					return false;
				}
		}
	
	</script>
</head>

<body>
	<?php	//import nav.php
	require_once 'nav.php';
	?>
	<?php 	//import db.php
	require_once 'db.php';
	
	//create connection 
	$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	//create connection
	if ($conn->connect_error)	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	$sql = "SELECT * FROM incident_type";
	
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0 )	{
		while ($row = $result ->fetch_assoc())	{
			/* create an assocative array of $incidentType [incident_type_id, incident _type_desc ]*/
			$incidentType[$row['incident_type_id']] = $row['incident_type_desc'];
		}
	}
	
	$conn->close();
	?>
	<form name="frmLogCall" method="post"
		  onSubmit="return validateForm()" action="dispatch.php">
	
	<table class="ContentStyle">
		<tr>
			<td colspan="2">Log Call Panel</td>
		</tr>	
		
		<tr>
			<td>Caller's Name :</td>
			<td><input text="text" name="callerName" id="callerName"></td>
		</tr>
		<tr>
			<td>Contact Number :</td>
			<td><input type="text" name="contactNo" id="contactNo"></td>
		</tr>
		<tr>
			<td>Location :</td>
			<td><input type="text" name="location" id="location"></td>
		</tr>
		<tr>
			<td>Incident Type :</td>
			<td><select name="incidentType" id="incidentType">
				<?php 	//populate a combo box with $incidentType
				foreach ( $incidentType as $key => $value)	{
				?>
					<option value="<?php echo $key ?>">
						<?php echo $value ?>
				</option>
				<?php
				}
				?>
				</select></td>
		</tr>
		<tr>
			<td>Desciption : </td>
			<td><textarea name="incidentDesc" id="incidentDesc" cols="45" rows="5"></textarea></td>
		</tr>
		<tr>
			<td><input type="reset" name="btnCancel" id="btnCancel" value="Reset"></td>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="btnProcessCall" id="btnProcessCall" value="Process Call..."></td>
		</tr>
	</table>
</form>
</body>
</html>