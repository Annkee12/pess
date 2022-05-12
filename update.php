<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
<link href="contentStyle.css" rel="stylesheet" type="text/css">
</head>
<body>
<!-- Part 1 -->
<?php require_once 'nav.php'; ?>
<br><br>
<?php
if (!isset($_POST["btnSearch"])){
	?>
<!-- create a form to search for patrol car based on id -->
<form name="form1" method="post"
	  action-"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
	<div align="center">
	<table class="ContentStyle">
		<tr></tr>
		<tr>
			<td>Patrol Car ID :</td>
			<td><input type="text" name="patrolCarId" id="patrolCarId"></td>
			<td><input type="submit" name="btnSearch" id="btnSearch" value="Search"></td>
		</tr>
	</table>
	</div>
</form>
	
<!--part 2 -->
<?php
}	
/* if postback via clicking Search button */
else
{
	require_once 'db.php';
	
	//create database connection 
	$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	//check connection 
	if($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	//retrieve patrol car detail 
	$sql = "SELECT * FROM patrolcar WHERE patrolcar_id ='" .$_POST['patrolCarId']."'";
	$result = $conn->query($sql);
	
	//if the patrol car does not exist, redirect back to update.php
	if ($result->num_rows == 0 ) {
	?>
		<script type="text/javascript">window.location="./update.php";</script>
	<?php }
	//else if the patrol car found
	while ($row = $result->fetch_assoc()){
		$patrolCarId = $row['patrolcar_id'];
		$patrolCarStatusId = $row['patrolcar_status_id'];
	}
	//retrieve from patrolcar_status table for populating the combo box
	$sql = "SELECT * FROM patrolcar_status";
	$result = $conn->query($sql);
	if ($result -> num_rows > 0) {
		while ($row = $result->fetch_assoc()) 	{
			$patrolCarStatusArray[$row['patrolcar_status_id']] = $row['patrolcar_status_desc'];
		}
	}
	while ($row = $result-> fetch_assoc())	{
		$patrolCarStatusArray[$row['patrolcar_status_id']]= $row['patrolcar_status_desc'];
	}
$conn->close();
?>

<!-- display a form for operator to update status of patrol car -->
<form name="form2" method="post"
	  action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
	<div align="center">
<table class="ContentStyle">
	<tr></tr>
	<tr>
		<td>ID: </td>
		<td><?php echo $patrolCarId ?><input type="hidden" name="patrolCarId" id="patrolCarId" value="<?php echo $patrolCarId ?>">
		</td>
	</tr>
	<tr>
		<td>Status :</td>
		<td><select name="patrolCarStatus" id="patrolCarStatus"><?php foreach( $patrolCarStatusArray as $key =>$value) {?><option value="<?php echo $key ?>" <?php if ($key==$patrolCarStatusId){ ?> selected="selected" <?php }?> >
			<?php echo $value ?>
			</option>
		<?php } ?>
		</select></td>
	</tr>
	<tr>
		<td><input type="reset" name="btnCancel" id="btnCancel" value="Reset"></td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="btnUpdate" id="btnUpdate" value="Update"></td>
	</tr>
</table>
</form>
</div>
<!-- part 3-->
<?php 
}
/* if postback via clicking update button */ 
if (isset($_POST["btnUpdate"])) {
	require_once 'db.php';
	//create database connection
	$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	//check connection 
	if($conn->connect_error){
		die("Connection failed: " . $conn->connect_error);
	}
	// update patrol car status 
	$sql = "UPDATE patrolcar SET patrolcar_status_id = '".$_POST['patrolCarStatus']."' WHERE patrolcar_id ='".$_POST['patrolCarId']."'";
	if ($conn->query($sql)===FALSE){
		echo "Error: " . $sql . "<br>" . $conn->error; 
	}
	/* if patrol car status is Arrived (4) then capture the time of arrival */ 
	if ($_POST["patrolCarStatus"]== '4'){
		$sql = "UPDATE dispatch SET time_arrived = NOW() WHERE time_arrived is NULL AND patrolcar_id= '" .$_POST['patrolCarId']."'";
		if ($conn->query($sql)===FALSE)	{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}
	/* else if patrol car status is FREE(3) then capture the time of completion*/ 
	else if($_POST["patrolCarStatus"]=='3'){
		/* First, retrieve the incident ID from dispatch table handled bu that patrol car */
		$sql = "SELECT incident_id FROM dispatch WHERE time_completed is NULL AND patrolcar_id= '" .$_POST['patrolCarId']."'";
		$result = $conn->query ($sql);
		if($result->num_rows > 0 )	{
			while ($row = $result->fetch_assoc())	{
				$incidentId = $row['incident_id'];
			}
		}
		//next update dispatch table 
		$sql = "UPDATE dispatch SET time_completed = NOW() WHERE time_completed is NULL AND patrolcar_id= '" .$_POST['patrolCarId']."'";
		if($conn->query($sql)===FALSE)	{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		/* Update incident table to completed (3) all patrol car attended to it are FREE now */
		$sql = "UPDATE incident SET incident_status_id = '3' WHERE incident_id = '$incidentId' AND NOT EXITS (SELECT *FROM dispatch WHERE time_completed is NULL AND incident_id = 'incidentId')";
		if($conn->query($sql)===FALSE)	{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}
	$conn->close();
	?>
<script type="text/javascript">window.location="./logcall.php";</script>
	<?php } ?>
</body>