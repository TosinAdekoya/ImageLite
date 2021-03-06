<?php include_once('header.php'); ?>

<div class="span9">
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand">Percent</a>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12 well well-dark">
			<h4>Resize to 7% of original sizes</h4>
<?php

// Obtain assets images
//----------------------------------------------------------------------------------------
$images = glob('../assets/images/*.{jpg,jpeg,png,gif}', GLOB_BRACE);


// Resize each image
//----------------------------------------------------------------------------------------
foreach ($images as $filename)
{

	try {
		
		$src = ImageLite::inst($filename)->resizePercent(7)->save();
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