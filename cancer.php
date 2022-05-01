<!DOCTYPE html>
<?php
$server="localhost";
$username="matthewthiele";
$password="";
$database="matthewthiele";
?>

<html>
<body>
<h1><a href ="http://odin.unomaha.edu/~matthewthiele/HuTTS.php"><b>HuTTS</b></a></h1>
<p>
	<i>Cancer Type Lookup</i>
</p>
<br>
<form id="organ" name="organ" method="post" action="<?php echo $PHP_SELF;?>">
	Organ System List: <select name='ORGAN'>
	<option value = "">---Select---</option>
	<?php
		$connect = mysqli_connect($server,$username,"",$database);
		if($connect->connect_error){
			echo "Connection error:" .$connect->connect_error;
		}
		$query = "SELECT DISTINCT organ_system FROM cancer_categories;";
		$result = mysqli_query($connect, $query) 
	  		or trigger_error("Query Failed! SQL: $query - Error: "
	  		. mysqli_error($connect), E_USER_ERROR);
		if ($result = mysqli_query($connect, $query)) {
			while ($row = mysqli_fetch_row($result)) {
				?>
				<option value="<?php echo $row[0]; ?>">
					<?php echo $row[0]; ?>
				</option>
<?php			}
			mysqli_free_result($result);
		}
	?>
	</select>
	<input type = "submit">
</form>
<br>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$selection = $_POST["ORGAN"];
	$query = "SELECT DISTINCT primary_site FROM cancer_categories WHERE organ_system = '$selection';";
	$result = mysqli_query($connect, $query) 
  		or trigger_error("Query Failed! SQL: $query - Error: "
  		. mysqli_error($connect), E_USER_ERROR);
	if ($result = mysqli_query($connect, $query)) {
		?>Primary Sites in <b>bold</b><br><br> <?php
		while ($row = mysqli_fetch_row($result)) {
			?> <b><?php echo $row[0]; ?> </b><br> <?php
			$deepquery = "SELECT DISTINCT cancer_type FROM cancer_categories WHERE organ_system = '$selection' AND primary_site = '$row[0]';";
			$deepresult = mysqli_query($connect, $deepquery)
				or trigger_error("Query Failed! SQL: $query - Error: "
				. mysqli_error($connect), E_USER_ERROR);
			if ($deepresult = mysqli_query($connect, $deepquery)) {
				while ($deeprow = mysqli_fetch_row($deepresult)) {
					echo $deeprow[0]; ?> <br>
					<?php
				}
				mysqli_free_result($deepresult);
			}
		}
		mysqli_free_result($result);
	}
}
mysqli_close($connect);
?>
</body>
</html>