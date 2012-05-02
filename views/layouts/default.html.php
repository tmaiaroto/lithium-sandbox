<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<?php echo $this->html->charset();?>
	<?php $title = $this->title() ? $this->title():''; ?>
	<title><?=$title ?></title>
	<?php 
		echo $this->html->style(array('reset', 'text', '960', 'layout', 'nav', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css', 'front_end', 'jquery/tipsy.css'), array('inline' => false));	
		echo '<!--[if IE 6]>'.$this->html->style('ie6').'<![endif]-->';
		echo '<!--[if IE 7]>'.$this->html->style('ie').'<![endif]-->';
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
	<script>!window.jQuery && document.write('<script src="/js/jquery/jquery-1.4.4.min.js"><\/script>')</script>
	<?php
		echo $this->html->script(array('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js', 'jquery/jquery.tipsy.js', 'tiny_mce/jquery.tinymce.js'), array('inline' => false));
	?>
	<?php
		echo $this->scripts();
		echo $this->styles();
	?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="app">
	<div id="container">
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