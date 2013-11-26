<?php include_once('header.php'); ?>

<div class="span9">
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand">Sharpen</a>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12 well">

<?php

$filename = '../assets/images/large-landscape.jpg';

for ($i=100; $i>=0; $i-=10)
{
	try {
		
		echo "<h4>Sharpen: $i</h4>";
		$src = ImageLite::inst($filename)->sharpen($i)->resize(200)->save();
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