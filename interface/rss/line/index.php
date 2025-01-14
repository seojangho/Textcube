<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
define('NO_SESSION', true);
define('__TEXTCUBE_LOGIN__',true);
define('__TEXTCUBE_CUSTOM_HEADER__', true);

require ROOT . '/library/preprocessor.php';
importlib("model.blog.feed");

requireStrictBlogURL();

$children = array();
$cache = pageCache::getInstance();

$cache->reset('linesRSS');
if(!$cache->load()) {
	$result = getLinesFeed(getBlogId(),'public','rss');
	if($result !== false) {
		$cache->reset('linesRSS');
		$cache->contents = $result;
		$cache->update();
	}
}
header('Content-Type: application/rss+xml; charset=utf-8');
fireEvent('FeedOBStart');
echo fireEvent('ViewLinesRSS', $cache->contents);
fireEvent('FeedOBEnd');
?>
