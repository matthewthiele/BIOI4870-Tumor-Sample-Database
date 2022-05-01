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
	<i>Search for Samples by Cancer Type</i>
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
		$query = "SELECT * FROM biosample WHERE cancer_type = '$selection';";
	} else if ($_POST["search_type"] == 'primary') {
		$query = "SELECT * FROM biosample WHERE cancer_type = ANY (SELECT cancer_type FROM cancer_categories WHERE primary_site = '$selection');";
	}
	if ($selection == '') {
		$query = "SELECT * FROM biosample;";
	}
	$connect = mysqli_connect($server,$username,"",$database);
	if($connect->connect_error){
		echo "Connection error:" .$connect->connect_error;
	}
	$result = mysqli_query($connect, $query) 
		or trigger_error("Query Failed! SQL: $query - Error: "
		. mysqli_error($connect), E_USER_ERROR);
	if ($result = mysqli_query($connect, $query)) {
		?><table style="width: 100%; border: 1px solid;"><tr>
		<th>Biosample ID</th>
		<th>SRA ID</th>
		<th>Biomaterial Provider</th>
		<th>Sample Type</th>
		<th>Isolate</th>
		<th>Tissue</th>
		<th>Cell Type</th>
		<th>Disease Stage</th>
		<th>Cancer Type</th>
		<th>Phenotype</th>
		<th>Ethnicity</th>
		<th>Population</th>
		<th>Age</th>
		<th>Sex</th></tr>
		<?php
		while ($row = mysqli_fetch_row($result)) {
			?><tr><td><a href ="https://www.ncbi.nlm.nih.gov/biosample/<?php echo $row[0]; ?>">
			<?php echo $row[0]; ?>
			</a></td><td><a href ="https://www.ncbi.nlm.nih.gov/sra/<?php echo $row[1]; ?>">
			<?php echo $row[1]; ?>
			</a></td><td>
			<?php echo $row[2]; ?>
			</td><td>
			<?php echo $row[3]; ?>
			</td><td>
			<?php echo $row[4]; ?>
			</td><td>
			<?php echo $row[5]; ?>
			</td><td>
			<?php echo $row[6]; ?>
			</td><td>
			<?php echo $row[7]; ?>
			</td><td>
			<?php echo $row[8]; ?>
			</td><td>
			<?php echo $row[9]; ?>
			</td><td>
			<?php echo $row[10]; ?>
			</td><td>
			<?php echo $row[11]; ?>
			</td><td>
			<?php echo $row[12]; ?>
			</td><td>
			<?php echo $row[13]; ?>
			</td></tr><?php
		}
		mysqli_free_result($result);
	}
}
mysqli_close($connect);
?></table>
</body>
</html>