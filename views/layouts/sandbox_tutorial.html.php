<?php
/**
 * This template is meant to provide a clean look for tutorials.
 * The "admin" template could be used, but that involves a whole system with admin routing, a special
 * menu class, and other things that could be confusing and get in the way.
 * 
 * This template's styles take on Lithium branding colors and keeps everything else out of the way
 * to provide a less confusing experience.
*/
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<?php 
		echo $this->html->charset() . "\n";
		$title = $this->title() ? $this->title() . ' :: Lithium Sandbox Tutorial':'Lithium Sandbox Tutorial';
		echo "\t" . '<title>' . $title . '</title>' . "\n\n";
	?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script>!window.jQuery && document.write('<script src="/js/jquery/jquery-1.7.2.min.js"><\/script>')</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
	<script>!window.jQuery.ui && document.write('<script src="/js/jquery/jquery-ui-1.8.20.custom.min.js"><\/script>')</script>
	<?php
		echo $this->html->script(array('jquery/jquery.tipsy.js'), array('inline' => false));
		echo $this->html->style(array('reset', 'text', '960', 'jquery/themes/smoothness/jquery-ui-1.8.6.custom.css', 'sandbox', 'jquery/tipsy.css'), array('inline' => false)) . "\n";
		
		echo $this->scripts();
		echo $this->styles();
		echo "\t" . '<!--[if IE 6]>'.$this->html->style('ie6').'<![endif]-->' . "\n";
		echo "\t" . '<!--[if IE 7]>'.$this->html->style('ie').'<![endif]-->' . "\n\n";
		echo "\t" . $this->html->link('Icon', null, array('type' => 'icon')) . "\n";
	?>
</head>
<body class="app">
	<div class="container_16">
		<div id="header">
			<h1><?php echo $this->title() ? $this->title():'A Lithium Sandbox Tutorial'; ?></h1>
		</div>
		<div id="content">
			<?php echo $this->content(); ?>
		</div>
		<div id="footer">
			<p>
				Powered by <?php echo $this->html->link('Lithium', 'http://lithify.me/', array('target' => '_blank')); ?>.
			</p>
		</div>
	</div>
</body>
</html>