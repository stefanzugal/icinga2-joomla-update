<?php
//restrict access to this script
$allowed_ips = array(
        '127.0.0.1'
);

//if your Joomla installation is behind a proxy like Nginx or Apache, use 'HTTP_X_FORWARDED_FOR'
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
        $remote_ip = $_SERVER['REMOTE_ADDR'];
}

if (!in_array($remote_ip, $allowed_ips)){
        echo "CRITICAL#IP $remote_ip not allowed.";
        exit;
}

//access is allowed - set up the Joomla platform
define('_JEXEC', 1);
include_once __DIR__ . '/defines.php';
define('JPATH_BASE', __DIR__);
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

//retrieve and compare the Joomla version
$joomla_version = new JVersion;
$installed_version = $joomla_version->getShortVersion();

// get remote Joomla version (last entry of "extension" list)
$xml = simplexml_load_file('http://update.joomla.org/core/list.xml');
$available_version = $xml->extension[$xml->extension->count()-1]['version'];

//compare installed and availale version
$status = '';
$text = '';
if ($installed_version < $available_version) {
  $status = 'CRITICAL';
  $text = 'A newer version of Joomla is available - please update you Joomla installation';
} else if ($installed_version == $available_version) {
  $status = 'OK';
  $text = 'Joomla is up to date';
} else {
  $status = 'WARNING';
  $text = 'It seems that the installed version of Joomla is newer than the current version - looks like something went wrong here (either the check or the Joomla installation)';
}

$text = $text . ': installed version: ' . $installed_version . ', newest version: ' . $available_version;
print $status . '#' . $text;
?>
