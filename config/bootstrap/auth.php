<?php
use lithium\security\Auth;

Auth::config(array(
	'user' => array(
	    'adapter' => 'Form',
	    'model'  => 'User',
	    'fields' => array('email', 'password'),
	    'scope'  => array('active' => true),
	    /*'filters' => array(
		//'password' => 'app\models\User::hashPassword'
	    ),*/
	    'session' => array(
	    	'options' => array('name' => 'default')
	    )
	)
));

?>