<!DOCTYPE html>
<html>
<body>
<h1><b>HuTTS</b></h1>
<p>
	<i><b>Hu</b>man <b>T</b>umor <b>T</b>issue <b>S</b>ample Database<br></i>
</p>

<br><form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	Select Type of Query: <select name = "dropdown">
	<option value = "none" selected>---Select---</option>
	<option value = "cancer">Search Cancer Types</option>
	<option value = "genes">Retrieve List of Canonical Cancer Drivers for Cancer Type</option>
	<option value = "sample">Search Samples by Cancer Type</option>
	</select>
	<input type="submit">
</form>
<?php
	$selection = $_POST["dropdown"];
	if ($selection == "cancer") {
		header('Location: http://odin.unomaha.edu/~matthewthiele/cancer.php');
	} else if ($selection == "genes") {
		header('Location: http://odin.unomaha.edu/~matthewthiele/genes.php');
	} else if ($selection == "sample") {
		header('Location: http://odin.unomaha.edu/~matthewthiele/sample.php');
	}
?>
</body>
</html>