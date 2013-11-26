<?php include_once('header.php'); ?>

<div class="span9">
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand">Quality / Compression</a>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12 well">

<?php

$filename = '../assets/images/large-landscape.jpg';

for ($i=0; $i<=100; $i+=10)
{
	try {
		echo "<p><strong>Quality:</strong> $i</p>";
		$src = ImageLite::inst($filename)->quality($i)->resize(200)->save();
		echo "<p><img src=\"". $src->getUri() ."\"></p>\r\n";

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