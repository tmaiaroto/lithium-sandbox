<?=$this->html->script('sandbox-widgets'); ?>
<p>
	Welcome to the sandbox! Here you can browse through a variety of tutorials and links to more information about Lithium related coding. There are a lot of sites out there and I also hope this application can help wrangle them. This is a local application, but you will likely want an internet connection because you will need one to read various sites linked here and to watch the screencasts.
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
	<div class="widget-box widget-box-3 widget-articles" title="Latest Articles" rel="A listing of blog posts with tutorials for and information about Lithium."></div>
	
	<div class="widget-box widget-box-3 widget-links" title="Links" rel="A listing of useful links with more information about Lithium."></div>
	
	<div class="widget-box widget-box-3-last widget-screencasts" title="Latest Screencasts" rel="A listing of the latest screencasts."></div>
</div>
<div class="grid-8"></div>

<div class="clear"></div>

<div class="grid-16">
	<h2>Donate?</h2>
	<p>
		Please keep in mind that creating tutorials is very time consuming and I'm a normal person like everyone else...I have bills just the same. So I can only spend so much spare time working on this project. In order to be able to increase the amount of time I can spend on this effort, this crusade, of edumacation...I would need it to help cover these normal life expenses. I'm not putting together a tutorial site/platform so that I can drive around a fancy car. I'm doing it so I can spread the good word of Lithium and help people. I may work this out a bit better and offer "premium" content, but I'm just not sure yet. Please feel free to donate if you want to help support my ability to spend more time working on this application or education framework...I still don't know what to call it.
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="unstyled" target="_blank">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
	</p>
	
	<p>
		In the future, I would like to setup a system where anyone can contribute tutorials supported by screencasts, blog posts, etc. and then have the ability to make money for their hard work as well. However, I never see this as something overpriced. I believe these bits of information are typically short, but can be very helpful. My thinking is that certain things may cost a few bucks to obtain while others are completely free. My belief is that most information should be free. I benefitted from free information for years and I would never want to prevent anyone else from doing the same. However, I also believe that incentive to creative tutorials and fair payment for time spent is essential to keeping this alive. Ultimately, I think someone should be able to load up this application for free and then spend $30 or so on advanced training, which is maybe a few tutorials, to walk away with at least the value of a good book. The other very important thing to note is that these tutorials would be coming with code examples. In fact, since this is a free public repository on Github, all code examples are free. So you should definitely be getting far more value from this than any book you could reach for on the shelf. Also note that there are not really any books for Lithium yet anyway.
	</p>
	<p>
		Keep in mind that anyone can fork this application's repository. If you are interested in what I'm doing here and are interested in creating tutorials for the sandbox yourself, then please get in touch. Additionally, if you have any suggestions for tutorials or if there is something you would like to see, please feel free to ask. I will likely create a wish list component to this application with voting, etc. You can e-mail me at: <?=$this->html->link('tom@union-of-rad.com', 'mailto:tom@union-of-rad.com', array('target' => '_blank')); ?>
	</p>
	<p>
		Thanks,<br />
		Tom
	</p>
</div>