<!DOCTYPE html>
<?php
$server="localhost";
$username="matthewthiele";
$password="";
$database="matthewthiele";
?>

<html>
<body>
<style>
table, th, td {
	border: 1px solid black;
}
tr:nth-child(even) {
	background-color: #D6EEEE;
}
</style>
<h1><a href ="http://odin.unomaha.edu/~matthewthiele/HuTTS.php"><b>HuTTS</b></a></h1>
<p>
	<i>Search Cancer Types for Canonical Drivers</i>
</p>
<br>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="radio" id="cancer" name="search_type" value="cancer" checked>
	<label for="cancer">Cancer Type</label>
	<input type="radio" id="primary" name="search_type" value="primary">
	<label for="primary">Primary Site</label><br>
	Enter Cancer Type/Primary Site: <input type="text" name="input">
	<input type="submit">
</form>
<br>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$selection = $_POST["input"];
	if ($_POST["search_type"] == 'cancer') {
		$query = "SELECT symbol FROM cancer_genes WHERE cancer_type = '$selection';";
	} else if ($_POST["search_type"] == 'primary') {
		$query = "SELECT DISTINCT symbol FROM cancer_genes WHERE cancer_type = ANY (SELECT cancer_type FROM cancer_categories WHERE primary_site = '$selection');";
	}
	if ($selection == '') {
		$query = "SELECT DISTINCT symbol FROM cancer_genes;";
	}
	$connect = mysqli_connect($server,$username,"",$database);
	if($connect->connect_error){
		echo "Connection error:" .$connect->connect_error;
	}
	$result = mysqli_query($connect, $query) 
		or trigger_error("Query Failed! SQL: $query - Error: "
		. mysqli_error($connect), E_USER_ERROR);
	if ($result = mysqli_query($connect, $query)) {
		?><table style="width: 80%; border: 1px solid;"><?php
		$colnum = 1;
		while ($row = mysqli_fetch_row($result)) {
			if ($colnum == 1) {
				$colnum += 1;
				?><tr><td><?php
				echo $row[0]; ?> </td> <?php
			} else if ($colnum < 10) {
				$colnum += 1;
				?><td><?php
				echo $row[0]; ?> </td> <?php
			} else if ($colnum == 10) {
				$colnum = 1;
				?><td><?php
				echo $row[0]; ?> </td></tr><?php
			}
		}
		mysqli_free_result($result);
	}
}
mysqli_close($connect);
?></table>
</body>
</html>