<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
define('NO_SESSION', true);
define('__TEXTCUBE_LOGIN__',true);
define('__TEXTCUBE_CUSTOM_HEADER__', true);

require ROOT . '/library/preprocessor.php';
importlib("model.blog.feed");
importlib("model.blog.entry");

requireStrictBlogURL();
$cache = pageCache::getInstance();
if(!empty($suri['id'])) {
	$cache->reset('responseRSS-'.$suri['id']);
	if(!$cache->load()) {
		$result = getResponseFeedByEntryId(getBlogId(),$suri['id']);
		if($result !== false) {
			$cache->reset('responseRSS-'.$suri['id']);
			$cache->contents = $result;
			$cache->update();
		}
	}
} else {
	$cache->reset('responseRSS');
	if(!$cache->load()) {
		$result = getResponseFeedTotal(getBlogId());
		if($result !== false) {
			$cache->reset('responseRSS');
			$cache->contents = $result;
			$cache->update();
		}
	}
}
header('Content-Type: application/rss+xml; charset=utf-8');
fireEvent('FeedOBStart');
echo fireEvent('ViewResponseRSS', $cache->contents);
fireEvent('FeedOBEnd');
?>
