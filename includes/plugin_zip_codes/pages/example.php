<?php
error_reporting(E_ALL); 
ini_set("display_errors", 1);

if($_POST)
{
	include('includes/clsDatabase.php');
	include('includes/clsZipCodeRange.php');

	$objDatabase = new clsDatabase("mysql", "localhost", "lawnetic_zipcode", "lawnetic_zipcode", "gmortgage78", "errors.log");

	$sSearchZipCode = $_POST['zipcode'];
	$iRadius = $_POST['radius'];

	$objZipCodeRange = new ZipCodesRange($sSearchZipCode, $iRadius);
	$sQuery = "$sSearchZipCode";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<body>
	<h1> US Zipcode distance search</h1>
	<p>Class to find zip codes within an approximate distance of another zip code. This can be useful when trying to find retailers within a certain number of miles to a customer.</p>
	<div class="panel">
		<div class="panel-header"><h3>Begin US Zipcode Search</h3></div>
		<div class="panel-content">
			<form action="<?php echo $_SERVER['PHP_SELF'];?>" method='POST'>
				<label for='zipcode'>Enter zipcode (US)</label><input type='text' name='zipcode' id='zipcode' <?php if(isset($sSearchZipCode)){ echo "value= " . $sSearchZipCode; }?>>
				<div class="helptext">eg. 20007,20306</div>
				<label for='radius'>Radius</label>
				<select name='radius' id='radius'>
					<option value='5'>5 miles</option>
					<option value='10'>10 miles</option>
					<option value='15'>15 miles</option>
					<option value='20'>20 miles</option>
					<option value='25'>25 miles</option>
					<option value='30'>30 miles</option>
					<option value='35'>35 miles</option>
					<option value='40'>40 miles</option>
				</select>
				<div id="actions">
					<input type='submit' value='Search' class='clear'>
				</div>
			</form>	
		</div>
		
	</div>
	<div id="results">
		<?php 
		if(isset($objZipCodeRange)){
			$totalZipcodesFound = count($objZipCodeRange->zipCodes);
			if($totalZipcodesFound > 0){
				echo "<h1>Total zipcode found : $totalZipcodesFound within $iRadius miles of $sSearchZipCode</h1>";
				echo "<ul id='result-list'>";
				foreach ($objZipCodeRange->zipCodes as $ZipData){ ?>
					<li>
						<div class="row">
							<strong>Zipcode</strong> : <?php echo $ZipData['ZIPCODE'];?><br>
							<strong>City</strong> : <?php echo $ZipData['CITY'];?><br>
							<strong>State</strong> : <?php echo $ZipData['STATE'];?><br>
							<strong>Distance</strong> : <?php echo $ZipData['DISTANCE'];?> miles<br>
						</div>
					</li>
				<?php }
				echo "</ul>";
			}else{
				echo "<p class='error rc5'>No results found.</p>";
			}
		}
		?>
	</div>
</body>
</html>

