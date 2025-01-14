<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)

define('NO_SESSION', true);
$IV = array(
	'GET' => array(
		'loginid' => array('email'),
		'key'     => array('string')
	)
);

require ROOT . '/library/preprocessor.php';

requireStrictBlogURL();
if(validateAPIKey($blogid, $_GET['loginid'], $_GET['key'])) {
	$userid = User::getUserIdByEmail($_GET['loginid']);
	if(in_array($blogid, User::getOwnedBlogs($userid))) { 
		if (file_exists(__TEXTCUBE_CACHE_DIR__."/backup/$blogid.xml")) {
			header('Content-Type: text/xml; charset=utf-8');
			$fileHandle = fopen(__TEXTCUBE_CACHE_DIR__."/backup/$blogid.xml", 'r');
			while(!feof($fileHandle)) {
				$buffer = fread($fileHandle, 4096);
				print $buffer;
			}
			fclose($fileHandle);
		}
	}
}
exit;
?>
