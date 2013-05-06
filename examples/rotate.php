<?php include_once("header.php"); ?>

<div class="span9">
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand">Rotate</a>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12 well">

<?php

$filename = "../assets/images/large-portrait.png";

echo "<p>";
echo "	<strong>Rotate with positive numbers</strong><br />";
echo "	<strong>Maximum Height:</strong> 150px<br />";
echo "	<strong>Maximum Height:</strong> 150px<br />";
echo "</p>";
echo "<img src=\"". ImageLite::inst($filename)->rotate(90)->resize(150, 150)->save()->getUri() ."\">\r\n";
echo "<img src=\"". ImageLite::inst($filename)->rotate(180)->resize(150, 150)->save()->getUri() ."\">\r\n";
echo "<img src=\"". ImageLite::inst($filename)->rotate(270)->resize(150, 150)->save()->getUri() ."\">\r\n";

?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12 well">
<?php

echo "<p>";
echo "	<strong>Rotate with negative numbers</strong><br />";
echo "	<strong>Maximum Height:</strong> 150px<br />";
echo "	<strong>Maximum Height:</strong> 150px<br />";
echo "</p>";
echo "<img src=\"". ImageLite::inst($filename)->rotate(-90)->resize(150, 150)->save()->getUri() ."\">\r\n";
echo "<img src=\"". ImageLite::inst($filename)->rotate(-180)->resize(150, 150)->save()->getUri() ."\">\r\n";
echo "<img src=\"". ImageLite::inst($filename)->rotate(-270)->resize(150, 150)->save()->getUri() ."\">\r\n";

?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12 well">
<?php

echo "<p>";
echo "	<strong>Rotate using Crop-to-fit</strong><br />";
echo "	<strong>Maximum Height:</strong> 150px<br />";
echo "	<strong>Maximum Height:</strong> 150px<br />";
echo "</p>";
echo "<img src=\"". ImageLite::inst($filename)->rotate(90)->resizeCropToFit(150, 150)->save()->getUri() ."\">\r\n";
echo "<img src=\"". ImageLite::inst($filename)->rotate(180)->resizeCropToFit(150, 150)->save()->getUri() ."\">\r\n";
echo "<img src=\"". ImageLite::inst($filename)->rotate(270)->resizeCropToFit(150, 150)->save()->getUri() ."\">\r\n";

?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12 well">
<?php

echo "<p>";
echo "	<strong>Rotate using Letterbox</strong><br />";
echo "	<strong>Maximum Height:</strong> 150px<br />";
echo "	<strong>Maximum Height:</strong> 150px<br />";
echo "</p>";
echo "<img src=\"". ImageLite::inst($filename)->rotate(90)->resizeLetterbox(150, 150)->save()->getUri() ."\">\r\n";
echo "<img src=\"". ImageLite::inst($filename)->rotate(180)->resizeLetterbox(150, 150)->save()->getUri() ."\">\r\n";
echo "<img src=\"". ImageLite::inst($filename)->rotate(270)->resizeLetterbox(150, 150)->save()->getUri() ."\">\r\n";


include_once("footer.php");
?>