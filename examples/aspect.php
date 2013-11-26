<?php include_once('header.php'); ?>

<div class="span9">
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand">Aspect Ratio</a>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12 well well-dark">
			<p>
				<strong>Maximum Width:</strong> 150px<br />
				<strong>Maximum Height:</strong> 150px<br />
				<strong>Aspect Ratio:</strong> On<br />
				<strong>Resize:</strong> Standard
			</p>
<?php

// Obtain assets images
//----------------------------------------------------------------------------------------
$images = glob('../assets/images/*.{jpg,jpeg,png,gif}', GLOB_BRACE);


// Resize each image
//----------------------------------------------------------------------------------------
foreach ($images as $filename)
{

	try {
		$src = ImageLite::inst($filename)->aspectRatio(true)->resize(150,150)->save();
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
			<strong>Maximum Width:</strong> 150px<br />
			<strong>Maximum Height:</strong> 150px<br />
			<strong>Aspect Ratio:</strong> Off<br />
			<strong>Resize:</strong> Standard
		</p>		

<?php

// Resize each image
//----------------------------------------------------------------------------------------
foreach ($images as $filename)
{

	try {
		$src = ImageLite::inst($filename)->aspectRatio(false)->resize(150,150)->save();
		echo "<img src=\"". $src->getUri() ."\">\r\n";
	}
	catch (Exception $e) { 

		echo "<div class=\"well\">";
		echo "<strong>" . $e->getMessage() . "\r\n</strong><br />";
		echo $e->getFile() . " : " . $e->getLine() . "\r\n<br />";
		echo "</div>";
	}

}

include_once('footer.php');
?>