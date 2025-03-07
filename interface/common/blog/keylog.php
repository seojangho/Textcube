<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)

// $keylog : $keylog (explanation for specific keyword)
// $entries : Posts that contain specific keyword.

$skin = new KeylogSkin($skinSetting['keylogSkin']);
$out = str_replace("[##_SKIN_head_end_##]", '<script type="text/javascript">//<![CDATA' . CRLF . 'var servicePath = "' . $context->getProperty('service.path') . '"; var blogURL = "' . $context->getProperty('uri.blog') . '"; var adminSkin = "' . $context->getProperty('panel.skin','canon') . '";//]]></script><script type="text/javascript" src="' . $service['resourcepath'] . '/script/common3.min.js"></script><script type="text/javascript" src="' . $service['resourcepath'] . '/script/gallery.min.js"></script>' . $skin->skin, $skin->outter);
$keylogView = $skin->keylog;
$itemsView = '';
$contentContainer = array();
foreach ($entries as $item) {
	$itemView = $skin->keylogItem;
	dress('blog_rep_link', $context->getProperty('uri.blog')."/{$item['id']}", $itemView);
	dress('blog_rep_title', htmlspecialchars($item['title']), $itemView);
	dress('blog_rep_regdate', Timestamp::format3($item['published']), $itemView);
	if ($item['comments'] > 0)
		dress('blog_rep_rp_cnt', "({$item['comments']})", $itemView);
	$itemsView .= $itemView;
}
dress('blog_rep', $itemsView, $keylogView);
$contentContainer["keyword_{$keylog['id']}"] = getEntryContentView($blogid, $keylog['id'], $keylog['content'], $keylog['contentformatter'], array(), 'Keyword');
dress('blog_desc', setTempTag("keyword_{$keylog['id']}"), $keylogView);
dress('blog_conform', htmlspecialchars($keylog['title']), $keylogView);
dress('blog', $keylogView, $out);
dress('blog_word', htmlspecialchars($keylog['title']), $out);
dress('body_id',"tt-body-keylog",$out);
$out = revertTempTags(removeAllTags($out));
fireEvent('OBStart');
print $out;
fireEvent('OBEnd');
?>
