<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
define('NO_SESSION', true);
define('__TEXTCUBE_CUSTOM_HEADER__', true);
define('__TEXTCUBE_LOGIN__',true);

require ROOT . '/library/preprocessor.php';
importlib("model.blog.tag");

requireStrictBlogURL();
$blogid = getBlogId();
$children = array();
$cache = pageCache::getInstance();
$context = Model_Context::getInstance();
if(!empty($suri['id'])) {
	$tagId = $suri['id'];
	$tagTitle = getTagById($blogid, $tagId);
} else if (!empty($suri['value'])) {
 	$tagId = getTagId($blogid, $suri['value']);
	$tagTitle = $suri['value'];
} else { 	// If no tag is mentioned, redirect it to total atom.
	header ("Location: ".$context->getProperty('uri.host').$context->getProperty('uri.blog')."/atom");
	exit;
}

$cache->reset('tagATOM-'.$tagId);
if(!$cache->load()) {
	importlib("model.blog.feed");
	$result = getTagFeedByTagId(getBlogId(),$tagId,'atom',$tagTitle);
	if($result !== false) {
		$cache->reset('tagATOM-'.$tagId);
		$cache->contents = $result;
		$cache->update();
	}
}
header('Content-Type: application/atom+xml; charset=utf-8');
fireEvent('FeedOBStart');
echo fireEvent('ViewTagATOM', $cache->contents);
fireEvent('FeedOBEnd');
?>
