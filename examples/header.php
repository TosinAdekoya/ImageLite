<?php
// Errors are on for development purposes
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once('../src/ImageLite/ImageLite.php');

// Set-up ImageLite
//----------------------------------------------------------------------------------------
try {
	
	ImageLite::setCacheRootDirectory('./cache', true);
	ImageLite::setLifetime(60*60*4);

	// Optional Set-up
	// ImageLite::setMode(0775);
	// ImageLite::setCacheUri('/examples/cache');
	// ImageLite::setDocumentRoot('/home/website/public_html');
}
catch (Exception $e) { 
	echo $e->getMessage();
}	

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>ImageLite</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
			.sidebar-nav {
				padding: 9px 0;
			}
			@media (max-width: 980px) {
				.navbar-text.pull-right {
					float: none;
					padding-left: 5px;
					padding-right: 5px;
				}
			}
			img {
				margin:15px 0 0 15px;
			}
			.well-dark {
				background-color:#BAA6FF;
			}
			.img-block {				
				float:left;
				margin:15px 0 0 15px;
				padding:10px;
			}
		</style>

	</head>

	<body>

		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a class="brand" href="index.php">ImageLite: Basic Examples</a>
				</div>
			</div>
		</div>

		<div class="container-fluid">
			<div class="row-fluid">

				<div class="span3">
					<div class="well sidebar-nav">
						<ul class="nav nav-list">
							<li class="nav-header">Resize Options</li>
							<li><a href="index.php">Standard</a></li>
							<li><a href="crop-to-fit.php">Crop-to-fit</a></li>
							<li><a href="letterbox.php">Letterbox</a></li>
							<li><a href="percent.php">Percentage</a></li>
						</ul>
						<ul class="nav nav-list">
							<li class="nav-header">Manipulation</li>
							<li><a href="sharpen.php">Sharpen</a></li>
							<li><a href="rotate.php">Rotate</a></li>
							<li><a href="aspect.php">Aspect Ratio toggle</a></li>
							<li><a href="constrain.php">Contrain dimensions</a></li>
							<li><a href="quality.php">Quality/Compression</a></li>
						</ul>
					</div>
				</div>