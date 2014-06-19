<?php 
if(!defined('ABSPATH')):
    die('Direct access of plugin file not allowed');
endif;	
	
$filename = getcwd().'/downloads/test.pdf'; // of course find the exact filename....        
if( file_exists ( $filename )):

echo 'found it';


else:

echo 'file does not exists';


endif;



header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false); // required for certain browsers 
header('Content-Type: application/pdf');

header('Content-Disposition: attachment; filename="'. basename($filename) . '";');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($filename));

readfile($filename);

exit; ?>