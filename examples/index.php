<?php include_once("header.php"); ?>

<div class="span9">
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand">Standard/Proportional</a>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12 well well-dark">
			<p>
				<strong>Maximum Width:</strong> 200px<br />
				<strong>Maximum Height:</strong> 200px
			</p>
<?php

// Obtain assets images
//----------------------------------------------------------------------------------------
$images = glob("../assets/images/*.{jpg,jpeg,png,gif}", GLOB_BRACE);


// Resize each image
//----------------------------------------------------------------------------------------
foreach ($images as $filename)
{

	try {

		$src = ImageLite::inst($filename)->constrain(false)->resize(200,200)->save();
		echo "<img src=\"". $src->getUri() ."\">\r\n";
		
	}
	catch (Exception $e) { 

		echo "<div class=\"well\">";
		echo "<strong>" . $e->getMessage() . "\r\n</strong><br />";
		echo $e->getFile() . " : " . $e->getLine() . "\r\n<br />";
		echo "</div>";
	}

}

?>

	</div>
</div>
<div class="row-fluid">
	<div class="span12 well well-dark">
			<p>
				<strong>Maximum Width:</strong> 200px<br />
				<strong>Maximum Height:</strong> 150px
			</p>

<?php

// Resize each image
//----------------------------------------------------------------------------------------
foreach ($images as $filename)
{

	try {

		$src = ImageLite::inst($filename)->constrain(false)->resize(200,150)->save();
		echo "<img src=\"". $src->getUri() ."\">\r\n";
		
	}
	catch (Exception $e) { 

		echo "<div class=\"well\">";
		echo "<strong>" . $e->getMessage() . "\r\n</strong><br />";
		echo $e->getFile() . " : " . $e->getLine() . "\r\n<br />";
		echo "</div>";
	}

}

?>

	</div>
</div>
<div class="row-fluid">
	<div class="span12 well well-dark">
			<p>
				<strong>Maximum Width:</strong> 300px<br />
				<strong>Maximum Height:</strong> 200px
			</p>

<?php

// Resize each image
//----------------------------------------------------------------------------------------
foreach ($images as $filename)
{

	try {

		$src = ImageLite::inst($filename)->constrain(false)->resize(300,200)->save();
		echo "<img src=\"". $src->getUri() ."\">\r\n";
		
	}
	catch (Exception $e) { 

		echo "<div class=\"well\">";
		echo "<strong>" . $e->getMessage() . "\r\n</strong><br />";
		echo $e->getFile() . " : " . $e->getLine() . "\r\n<br />";
		echo "</div>";
	}

}


include_once("footer.php");
?>