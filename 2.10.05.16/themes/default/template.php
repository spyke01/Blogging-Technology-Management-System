<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
	<head>
		<title><? echo $bts_config['ftsbts_site_title'] . " - " . $page->printTemplateVar("PageTitle");  ?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="content-language" content="en-us" />
		<!--Stylesheets Begin-->
			<link rel="stylesheet" type="text/css" href="themes/lightbox.css" />
			<link rel="stylesheet" type="text/css" href="themes/<?= $bts_config['ftsbts_theme']; ?>/main.css" />
			<!--[if lt IE 7]>
				<style>
					#blogname { padding-top: 12px; }
					#sidebar li ul { padding-left: 20px; }
				</style>
			<![endif]-->			
		<!--Stylesheets End-->
	<!--Javascripts Begin-->
		<script type="text/javascript" src="javascripts/confirm.js"></script>
		<script type="text/javascript" src="javascripts/scriptaculous1.8.2.js"></script>
		<script type="text/javascript" src="javascripts/lightbox.js"></script>
		<script type="text/javascript" src="javascripts/validation.js"></script>
		<script type="text/javascript" src="javascripts/tiny_mce/tiny_mce.js"></script>	
		<!--[if lt IE 7]>
			<script src="javascripts/pngfix.js" defer type="text/javascript"></script>
		<![endif]-->	
	<!--Javascripts End-->
	</head>
	<body<?= $page->printTemplateVar("ftsbts_body_on_load_event"); ?>>
		<div id="header">
			<div id="logo">
				<h1 id="blogname"><a href="index.php"><?= $bts_config['ftsbts_site_title']; ?></a></h1>
				<div class="description">
					<?= $bts_config['ftsbts_site_tagline']; ?>
				</div>
			</div>
			<div id="navigation">
				<?= $page->printMenu("top", "ul", "", "", "", ""); ?>
			</div>
		</div>		
		<div id="wrap">
			<div id="content">
				<?= $page->printTemplateVar('PageContent'); ?>	
			</div>
			<?= $page->printSidebar("sidebar", ""); ?>
			<div id="footer">
				<div style="float:right; padding-right: 5px;">
					Powered By: <a href="http://www.fasttracksites.com/">Fast Track Sites Blogging Technology System</a>
				</div>					
				Copyright &copy; 2006 Fast Track Sites
			</div>
		</div>
	</body>
</html>
