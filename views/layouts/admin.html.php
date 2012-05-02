<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->html->charset();?>
	<?php $title = $this->title() ? $this->title() . ' :: Admin':'Admin'; ?>
	<title><?=$title; ?></title>
	<?php //echo $this->html->style(array('debug', 'lithium')); ?>	
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	<?php	// Flip between "960" and "grid" style sheets for fluid vs. fixed 960gs
		echo $this->html->style(array('reset', 'text', '960', 'layout', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css', 'admin', 'jquery/tipsy.css'), array('inline' => false));	
		echo '<!--[if IE 6]>'.$this->html->style('ie6').'<![endif]-->';
		echo '<!--[if IE 7]>'.$this->html->style('ie').'<![endif]-->';
		echo $this->html->script(array('https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js', 'jquery/jquery.tipsy.js', 'tiny_mce/jquery.tinymce.js'), array('inline' => false));
	?>
		
	<?php
		echo $this->scripts();
		echo $this->styles();
	?>
</head>
<body>
	<div class="container_16">
		<div class="grid_16" id="admin_header">
			<h1 id="branding">
				<a href="/admin">Admin Dashboard</a>
			</h1>
			<?php echo $this->menu->static_menu('admin', array('menu_class' => 'nav main', 'menu_id' => 'nav')); ?>
		</div>
		<div class="clear" style="height: 10px;"></div>
		
		<?php echo $this->content(); ?>		
		
		<div class="clear"></div>
		<div id="footer" class="grid_16">
			Powered by <?php echo $this->html->link('Lithium', 'http://li3.rad-dev.org'); ?>.
		</div>
	</div>
	<?php // echo $this->flashMessage->output(); ?>
	<script type="text/javascript">
		$(function() {
			$('textarea.tinymce').tinymce({
				// Location of TinyMCE script
				script_url : '/js/tiny_mce/tiny_mce.js',
				// General options
				theme : "advanced",
				plugins : "style,table,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,xhtmlxtras",
				// Theme options
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,|,code,fullscreen",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
				theme_advanced_resize_horizontal : false,
				
				width: '675',
				
				// Example content CSS (should be your site CSS)
				content_css : "css/content.css,/css/content.css"
			});
		});
	</script>
	<?=$this->html->flash(); ?>
</body>
</html>