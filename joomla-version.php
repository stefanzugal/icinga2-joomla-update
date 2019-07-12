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

//access is allowed - set up the joomla platform
define('_JEXEC', 1);
include_once __DIR__ . '/defines.php';
define('JPATH_BASE', __DIR__);
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

//check for joomla core updates
//retrieve and compare the joomla version
$joomla_version = new JVersion;
$installed_joomla_version = $joomla_version->getShortVersion();

// get remote Joomla Versio (last entry of "extension" list)
$xml = simplexml_load_file('http://update.joomla.org/core/list.xml');
$available_joomla_version = $xml->extension[$xml->extension->count()-1]['version'];

//check for joomla extensions updates
Joomla\CMS\Factory::getApplication('site');
$updater = new JUpdater;
//parameters: check all sites, force reload of cache, 4 = stable only, do not include the current version in the results
$updater->findUpdates(0, 0, 4, false);

//query the database for extension updates
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('COUNT(*)');
$query->from($db->quoteName('#__updates'));
$query->where($db->quoteName('extension_id') . '!= 0');
$db->setQuery($query);
$extension_updates = $db->loadResult();

// converts the version x.y.z to a number, assuming that neither y nor z will be larger than 99.
function versionAsNumber($version) {
  $tokens = preg_split('/\\./', $version);
  return $tokens[0] * 10000 + $tokens[1] * 100 + $tokens[2];
}

//compare installed and available version
$status = '';
$text = '';
if (versionAsNumber($installed_joomla_version) < versionAsNumber($available_joomla_version)) {
  $status = 'CRITICAL';
  $text = 'A newer version of Joomla is available - please update you Joomla installation';
} else if ($installed_joomla_version == $available_joomla_version) {
  $status = 'OK';
  $text = 'Joomla is up to date';
} else {
  $status = 'WARNING';
  $text = 'It seems that the installed version of Joomla is newer than the current version - looks like something went wrong here (either the check or the Joomla installation)';
}

//adapt the status according to the available extensions; use WARNING for extensions
if ($extension_updates > 0 && $status == 'OK') {
  $status = 'WARNING';
}

//assemble the information for icinga
$text = $text . ': installed version: ' . $installed_joomla_version . ', newest version: ' . $available_joomla_version . '<br>Available extension updates: ' . $extension_updates;
print $status . '#' . $text;
?>