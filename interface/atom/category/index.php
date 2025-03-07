<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
define('NO_SESSION', true);
define('__TEXTCUBE_LOGIN__',true);
define('__TEXTCUBE_CUSTOM_HEADER__', true);

require ROOT . '/library/preprocessor.php';
importlib("model.blog.category");

requireStrictBlogURL();

$children = array();
$cache = pageCache::getInstance();
if(!empty($suri['id'])) {
	$categoryId = $suri['id'];
	if(in_array($categoryId, getCategoryVisibilityList($blogid, 'private'))) return false;
	$categotyTitle = getCategoryNameById($categoryId);
} else if (!empty($suri['value'])) {
 	$categoryId = getCategoryIdByLabel(getBlogId(), $suri['value']);
	if(in_array($categoryId, getCategoryVisibilityList($blogid, 'private'))) return false;
 	$categoryTitle = $suri['value'];
} else { 	// If no category is mentioned, redirect it to total atom.
	header ("Location: ".$context->getProperty('uri.host').$context->getProperty('uri.blog')."/atom");
	exit;
}

$cache->reset('categoryATOM-'.$categoryId);
if(!$cache->load()) {
	$categoryIds = array($categoryId);
	$parent = getParentCategoryId(getBlogId(),$categoryId);
	if($parent === null) {	// It's parent. let's find childs.
		$children = getChildCategoryId(getBlogId(),$categoryId);
		if(!empty($children)){
			$categoryIds = array_merge($categoryIds, $children);
		}
	}
	importlib("model.blog.feed");
	$result = getCategoryFeedByCategoryId(getBlogId(),$categoryIds,'atom',$categoryTitle);
	if($result !== false) {
		$cache->reset('categoryATOM-'.$categoryId);
		$cache->contents = $result;
		$cache->update();
	}
}
header('Content-Type: application/atom+xml; charset=utf-8');
fireEvent('FeedOBStart');
echo fireEvent('ViewCategoryATOM', $cache->contents);
fireEvent('FeedOBEnd');
?>
