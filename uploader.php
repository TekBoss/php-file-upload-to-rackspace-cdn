<?php

/*
 * Uploads a file directly to the CDN container of your choice.
 */

// Turn on error reporting so we can see if there are any problems
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

echo '<br>';

/**
 * Functions
 */
function step($msg,$p1=NULL,$p2=NULL,$p3=NULL) {
    global $STEPCOUNTER;
    printf("Step %d. %s", ++$STEPCOUNTER, sprintf($msg,$p1,$p2,$p3));
	echo "<br><br>";
}

function info($msg,$p1=NULL,$p2=NULL,$p3=NULL,$p4=NULL,$p5=NULL) {
    printf("  %s", sprintf($msg,$p1,$p2,$p3,$p4,$p5));
	echo "<br><br>";
}

function Guess_Content_Type($handle) {
/*
 * Could probably replace with something smarter. - Woody
 */

$handle = strtolower($handle);

$ext = ".".end(explode(".", $handle));
switch($ext)
	{
	case '.jpg': $content_type = "image/jpeg"; break;
	case '.gif': $content_type = "image/gif"; break;
	case '.png': $content_type = "image/png"; break;
	case '.css': $content_type = "text/css"; break;
	case '.js':  $content_type = "application/javascript"; break;
	default: $content_type = "image/jpeg"; break;
	}

return $content_type;

}

/**
 * Includes and definitions
 */

step('Get Inputs and Constants');

require_once('_inc_cdn_ini.php'); // Contains our secret codes. Put someplace safe!

# NOTE: You have the option of replacing the include fiel above with form inputs currently commented out.
#	$username       = isset($_POST['username'])        ? $_POST['username']        : '' ;
#	$key            = isset($_POST['key'])             ? $_POST['key']             : '' ;
	$container_name = isset($_POST['container_name'])  ? $_POST['container_name']  : '' ;
//	echo "Username:  $username<br/>";
//	echo "Username:  $key<br/>";
	echo "Container: $container_name<br/><br>";

require_once('lib/rackspace.php'); // Cloudspace API for Rackspace


/**
 * Get Input Data.
 */
step('Get File Input');
$localfile = $_FILES['upload']['tmp_name']; // Local copy of file uploaded to some system temp folder .
$filename  = urlencode($_FILES['upload']['name']); // File name of uploaded file.

// Abort on error.
if ($filename == '')       { echo 'No FILE to upload.<br><br><a href="javascript:history.back();">BACK</a>'; exit; } // Print error message then quit.
if ($container_name == '') { echo 'We must have a container name to upload file(s).<br><br><a href="javascript:history.back();">BACK</a>'; exit; } // Print error message then quit.

// Print some information about the file
//echo "Local: $localfile<br/>";
echo "File : $filename<br/><br/>";

/**
 * Authenticate
 */
step('Authenticate');
$rackspace = new OpenCloud\Rackspace(AUTHURL, array( 'username' => USERNAME, 'apiKey' => APIKEY )); // Uses OpenCloud namespace.
$rackspace->AppendUserAgent('(My File Uploader)'); // Replace text with whatever you want or comment this out entirely.

/**
 * Cloud Files
 */
step('Connect to Cloud Files');
$cloudfiles = $rackspace->ObjectStore('cloudFiles', MYREGION);

step('Open Container');
$container = $cloudfiles->Container($container_name);

step('Get File List for Duplicate Check');
$list = $container->ObjectList();

/**
 * Process image (create correct sizes and rename them.
 */
step('Upload File');
// Create Object from this file
$object = $container->DataObject();
//$resp = $object->Create(array('name'=>$_FILES['upload']['name'],'content_type'=>Guess_Content_Type($_FILES['upload']['name'])), $_FILES['upload']['tmp_name']);
$resp = $object->Create(array('name'=>$filename,'content_type'=>Guess_Content_Type($filename)), $localfile);
// Publish File to CDN
$container->PublishToCDN(600); // 600-second TTL

//	Show or don't show whatever info you desire.
//	info('CDN URL:              %s', $container->CDNUrl());
//	info('Public URL:           %s', $container->PublicURL());
	info('Object Public URL:    %s', $object->PublicURL());
//	info('Object SSL URL:       %s', $object->PublicURL('SSL'));
//	info('Object Streaming URL: %s', $object->PublicURL('Streaming'));

echo 'Your URL is: <a href="'.$object->PublicURL().'" target="_blank">httpd://'.$container_name.'/'.$filename.'</a> <-- USE THIS LINK !<br><br>';

echo 'NOTE: Ignore the URL in the Address Bar of your browser. It is only used for debugging.<br><br>';

echo '<a href="javascript:window.close();">CLOSE WINDOW</a><br><br>';

echo '<a href="javascript:history.back();">BACK</a><br><br>';


exit;

// end of line

/*
 * Other available options (there are more than these).
 */

/*
step('List Containers');
$list = $cloudfiles->ContainerList();
while($c = $list->Next())
	{
    info('Container: %s', $c->name);
	}

step('List Objects in Container %s', $container->name);
$list = $container->ObjectList();
while($o = $list->Next())
	{
    info('Object: %s', $o->name);
	}

step('Disable Container CDN');
$container->DisableCDN();

step('Delete Object');
$list = $container->ObjectList();
while($o = $list->Next())
	{
    info('Deleting: %s', $o->name);
    $o->Delete();
	}
*/

?>