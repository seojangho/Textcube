<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
define('NO_SESSION', true);
define('__TEXTCUBE_CUSTOM_HEADER__', true);
define('__TEXTCUBE_LOGIN__',true);
require ROOT . '/library/preprocessor.php';
requireStrictBlogURL();
$context = Model_Context::getInstance();
$author = $suri['value'];
$authorId = User::getUserIdByName($author);
if(empty($authorId)) exit;
$blogid = getBlogId();

$cache = pageCache::getInstance();
$cache->reset('authorATOM-'.$authorId);
if(!$cache->load()) {
	importlib("model.blog.feed");
	list($entries, $paging) = getEntriesWithPagingByAuthor($blogid, $author, 1, 1, 1);	
	if(empty($entries)) {
		header ("Location: ".$context->getProperty('uri.host').$context->getProperty('uri.blog')."/atom");
		exit;	
	}
	$result = getFeedWithEntries($blogid,$entries,_textf('%1 의 글 목록',$author),'atom');
	if($result !== false) {
		$cache->reset('authorATOM-'.$authorId);
		$cache->contents = $result;
		$cache->update();
	}
}
header('Content-Type: application/atom+xml; charset=utf-8');
fireEvent('FeedOBStart');
echo fireEvent('ViewAuthorATOM', $cache->contents);
fireEvent('FeedOBEnd');
?>
