<?php include_once('header.php'); ?>

<div class="span9">
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand">Constrain</a>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12 well">

<?php

$filename = '../assets/images/small-square.png';

echo "<p>";
echo "	<strong>Maximum Width:</strong> 400px<br />";
echo "	<strong>Maximum Height:</strong> 400px<br />";
echo "	<strong>Constrain:</strong> On<br />";
echo "	Image <strong>will not</strong> be enlarged beyond original dimensions";
echo "</p>";
echo "<img src=\"". ImageLite::inst($filename)->constrain(true)->resize(400,400)->save()->getUri() ."\">\r\n";

?>

	</div>
</div>
<div class="row-fluid">
	<div class="span12 well">

<?php

echo "<p>";
echo "	<strong>Maximum Width:</strong> 400px<br />";
echo "	<strong>Maximum Height:</strong> 400px<br />";
echo "	<strong>Constrain:</strong> Off<br />";
echo "	Image <strong>will</strong> be enlarged beyond original dimensions (notice the pixelation)";
echo "</p>";
echo "<img src=\"". ImageLite::inst($filename)->constrain(false)->resize(400,400)->save()->getUri() ."\">\r\n";

include_once('footer.php');
?>