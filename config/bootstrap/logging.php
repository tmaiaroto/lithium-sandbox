<?php
use lithium\analysis\Logger;

Logger::config(array(
    'default' => array('adapter' => 'File', 'priority' => array('debug', 'alert', 'error')),
    /*'badnews' => array(
        'adapter' => 'File',
        'priority' => array('emergency', 'alert', 'critical', 'error')
    )*/
));
?>
