<?php
/// Copyright (c) 2004-2011, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
define('__TEXTCUBE_IPHONE__', true);
require ROOT . '/library/preprocessor.php';
requireView('iphoneView');
$context = Model_Context::getInstance();
printMobileHTMLHeader();

if(empty($suri['id']) && empty($suri['value'])) {
	printMobileHTMLMenu('','list');
	$blog['entriesOnList'] = 8;
	if(!$listWithPaging = getEntriesWithPaging($blogid, $suri['page'], $blog['entriesOnList']))
		$listWithPaging = array(array(), array('total' => 0));
	$list = array('title' => (empty($suri['value']) ? getCategoryLabelById($blogid, 0) : $suri['value']), 'items' => $listWithPaging[0], 'count' => $listWithPaging[1]['total']);
	$paging = $listWithPaging[1];
?>
	<ul data-role="listview" class="posts" id="blog_posts_<?php echo $suri['page'];?>" title="<?php echo _text('글목록');?>" selected="false">
<?php
	$itemsView = '<li class="group ui-bar ui-bar-e">'.CRLF;
	$itemsView .= '	<span class="left">'._text('글목록').'('.$list['count'].')</span>'.CRLF;
	$itemsView .= '	<span class="right">Page <span class="now_page">' . $paging['page'] . '</span> / '.$paging['pages'].'</span>'.CRLF;
	$itemsView .= '</li>'.CRLF;
	foreach ($list['items'] as $item) {	
		$author = User::getName($item['userid']);
		if($imageName = printMobileAttachmentExtract($item['content'])){
			$imageSrc = printMobileImageResizer($blogid, $imageName, 28);
		}else{
			$imageSrc = $service['path'] . '/resources/style/iphone/image/noPostThumb.png';
		}
		$itemsView .= '<li class="post_item">'.CRLF;
		$itemsView .= '	<span class="image"><img src="' . $imageSrc . '" width="36px" height="36px" /></span>'.CRLF;
		$itemsView .= '	<a href="' . $blogURL . '/entry/' . $item['id'] . '" class="link">'.CRLF;
		$itemsView .= '		<div class="post">'.CRLF;
		$itemsView .= '			<span class="title">' . fireEvent('ViewListTitle', htmlspecialchars($item['title'])) . '</span>'.CRLF;
		$itemsView .= '			<span class="description">' . Timestamp::format5($item['published']) . ', ' . _text('댓글').'(' . ($item['comments'] > 0 ? $item['comments'] : 0) . ')' . '</span>'.CRLF;
		$itemsView .= '		</div>'.CRLF;
		$itemsView .= '	</a>'.CRLF;
		$itemsView .= '</li>'.CRLF;
	}
	$itemsView .= '</ul>'.CRLF;

	$itemsView .= '<div data-role="navbar" data-theme="c">'.CRLF;
	$itemsView .= '<ul>'.CRLF;
	if(isset($paging['prev'])){
		$itemsView .= '<li><a data-role="button" data-theme="d" data-icon="arrow-l" href="' .$blogURL . '/entry?page=' . $paging['prev'] . '" class="previous">'._textf('%1 페이지',$paging['prev']) . '</a></li>'.CRLF;
	}
	if (isset($paging['next'])) {
		$itemsView .= '<li><a data-role="button" data-theme="d" data-icon="arrow-r"  href="' .$blogURL . '/entry?page=' . $paging['next'] . '" class="next">'._textf('%1 페이지',$paging['next']) . '</a></li>'.CRLF;
	}
	if ($suri['page'] > 1 && $suri['page'] != $paging['pages']) {
		$itemsView .= '<strong>' . $suri['page'] . '</strong>'.CRLF;
	}
	$itemsView .= '</ul>'.CRLF;
	$itemsView .= '</div>'.CRLF;

	print $itemsView;
?>
	</ul>
<?php
} else {
	if(!empty($suri['id'])) {
		list($entries, $paging) = getEntryWithPaging($blogid, $suri['id']);
	} else if(!empty($suri['value'])) {
		$entryPrint = true;
		list($entries, $paging) = getEntryWithPagingBySlogan($blogid, $suri['value']);
	//	printMobileHTMLHeader();
	}
	printMobileHTMLMenu('','list');
	
	$entry = $entries ? $entries[0] : null;
?>
	<div id="post_<?php echo $entry['id'];?>" title="<?php echo htmlspecialchars($entry['title']);?>" class="panel"<?php echo (!empty($entryPrint) ? 'selected="true"' : '');?>>
		<div class="entry_info">
			<h2><?php echo htmlspecialchars($entry['title']);?></h2>
			<h3 class="noBorderLine"><?php echo Timestamp::format5($entry['published']);?></h3>
		</div>
		<div class="content"><?php printMobileEntryContentView($blogid, $entry, null); ?></div>
<?php 
	$entryTags = getTags($entry['blogid'], $entry['id']);
	if (sizeof($entryTags) > 0) {
?>
		<div class="entry_tags" data-role="content" data-theme="c">
		<h3 class="tags_title">Tags</h3>
<?php
		$tags = array();
		$relTag = Setting::getBlogSettingGlobal('useMicroformat', 3)>1 && (count($entries) == 1 || !empty($skin->hentryExisted) );
		foreach ($entryTags as $entryTag) {
			$tags[$entryTag['name']] = "<a href=\"$blogURL/tag/" . $entryTag['id'] . '">' . htmlspecialchars($entryTag['name']) . '</a>';
		}
		echo implode(",\r\n", array_values($tags));
?>
		</div>
	</div>
<?php
	}
	if(doesHaveOwnership() || ($entry['visibility'] >= 2) || (isset($_COOKIE['GUEST_PASSWORD']) && (trim($_COOKIE['GUEST_PASSWORD']) == trim($entry['password'])))) {
		printMobileNavigation($entry, true, true, $paging);
	} else {
		printMobileNavigation($entry, false, false, $paging);
	}
?>
<?php
	if(!empty($entryPrint)) {
?>
		</div>
<?php
	}
}

printMobileHTMLFooter();
?>
