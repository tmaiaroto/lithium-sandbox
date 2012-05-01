<div id="center-box-bg">
	<div id="center-box-content">
		<p>This is the main application's static "home" page. This is subject to change based on the branch you are on.
			The main sandbox page, which contains all the information you need, is kept separate and is always located at: /sandbox/page</p>
		<p>You can <?=$this->html->link('click here', array('controller' => 'sandbox', 'action' => 'page', 'args' => array('home'))); ?> to go to the sandbox main page.</p>
	</div>
</div>