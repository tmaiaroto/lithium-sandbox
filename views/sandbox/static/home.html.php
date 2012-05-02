<?=$this->html->script('sandbox-widgets'); ?>
<p>
	Welcome to the sandbox! This application is intended to be used for learning and experimenting with the ability to easily share and collaborate. Here you can browse through a variety of tutorials and links to more information about Lithium related coding. This is a local application, but all content (including screencasts) is out on the internet.
</p>

<?php 
/**
 * NOT IMPLMENTED YET...
 * 
<div class="timeline-date-range">
	<?=$this->html->image('icons/calendar.png', array('class' => 'timeline-date-range-icon')); ?>
	<a href="#" class="timeline-date-submit greenButton" id="timeline-update-date-range">Generate Report</a>
	<span class="timeline-date-label">from</span>
	<input class="timeline-date timeline-date-from" value="" type="text" id="timeline-from" name="from"/>
	<span class="timeline-date-label">to</span>
	<input class="timeline-date timeline-date-to" value="" type="text" id="timeline-to" name="to"/>
</div>
*/
?>

<div class="grid-8">
	<div class="widget-box widget-box-3 widget-screencasts" title="Latest Tutorials"></div>
	
	<div class="widget-box widget-box-3 widget-articles" title="Latest Articles" rel="A listing of blog posts with tutorials for and information about Lithium."></div>
	
	<?php /* <div class="widget-box widget-box-3 widget-links" title="Links" rel="A listing of useful links with more information about Lithium."></div> */ ?>
	
	<div class="widget-box widget-box-3-last widget-presentations" title="Latest Videos & Slides" rel="A listing of the latest presentations from events and meetups."></div>
</div>
<div class="grid-8"></div>

<div class="clear"></div>

<div class="grid-16">
	<br style="clear: left;" />
	<p>
		Please be sure to keep this application up to date. If you are reading this, it means you are looking at an extremely early version of this project and there will be frequent updates.
	</p>
	<p>
		For information about how you can help contribute or donate to make this project better, please <?=$this->html->link('click here', array('controller' => 'sandbox', 'action' => 'page', 'args' => array('donate'))); ?>.
	</p>
</div>