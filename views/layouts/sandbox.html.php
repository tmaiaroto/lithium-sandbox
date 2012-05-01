<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<?php 
		echo $this->html->charset() . "\n";
		$title = $this->title();
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
	<div id="container">
		<div id="header">
			<h1>The Lithium Sandbox</h1>
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