<?php include_once('header.php'); ?>

<div class="span9">
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand">Crop-to-fit</a>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12 well well-dark">
			<h4>Landscape</h4>
<?php

// Obtain assets images
//----------------------------------------------------------------------------------------
$images = glob('../assets/images/*.{jpg,jpeg,png,gif}', GLOB_BRACE);


// Resize each image
//----------------------------------------------------------------------------------------
foreach ($images as $filename)
{

	try {
		
		$src = ImageLite::inst($filename)->resizeCropToFit(300,150)->save();
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
		<h4>Portrait</h4>
<?php

// Resize each image
//----------------------------------------------------------------------------------------
foreach ($images as $filename)
{

	try {
		
		$src = ImageLite::inst($filename)->resizeCropToFit(150,300)->save();
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
		<h4>Square</h4>
<?php

// Resize each image
//----------------------------------------------------------------------------------------
foreach ($images as $filename)
{

	try {
		$src = ImageLite::inst($filename)->resizeCropToFit(200,200)->save();
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