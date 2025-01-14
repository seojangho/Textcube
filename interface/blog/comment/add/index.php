<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)

require ROOT . '/library/preprocessor.php';
$context = Model_Context::getInstance();
$entryId = $suri['id'];
$IV = array(
	'POST' => array(
		'key' => array('string', 32, 32),
		"comment_type" => array(array('idpwd', ''), 'default' => '', 'mandatory' => false),
		"name" => array('string', 'default' => ''),
		"password" => array('string', 'default' => ''),
		"secret" => array(array('1', 'on'), 'mandatory' => false),
		"homepage" => array('url', 'default' => 'http://'),
		"comment" => array('string', 'default' => '')
	)
);
$customIV = fireEvent('ManipulateIVRules',$IV,$context->getProperty('uri.interfaceRoute'));
Validator::addRule($customIV);
if(!Validator::isValid())
	Respond::PrintResult(array('error' => 1, 'description' => 'Illegal parameters'));
requireStrictRoute();
header('Content-Type: text/xml; charset=utf-8');
if (!isset($_POST['key']) || ($_POST['key'] != md5(filemtime(ROOT . '/config.php'))) || !Setting::getBlogSettingGlobal('acceptComments',0)) {
	Respond::PrintResult(array('error' => 0, 'commentBlock' => '', 'recentCommentBlock' => ''));
	exit;
}
$userName = isset($_POST["name"]) ? trim($_POST["name"]) : '';
$userPassword = isset($_POST["password"]) ? $_POST["password"] : '';
$userSecret = isset($_POST["secret"]) ? 1 : 0;
$userHomepage = isset($_POST["homepage"]) ? trim($_POST["homepage"]) : '';
$userComment = isset($_POST["comment"]) ? trim($_POST["comment"]) : '';
if (!doesHaveMembership() && !doesHaveOwnership() && $userName == '') {
	Respond::PrintResult(array('error' => 2, 'description' => _text('이름을 입력해 주십시오.')));
	exit;
} else if ($userComment == '') {
	Respond::PrintResult(array('error' => 2, 'description' => _text('본문을 입력해 주십시오.')));
	exit;
} else {
	if (!empty($userName)) {
		setcookie('guestName', $userName, time() + 2592000, $context->getProperty('uri.blog')."/");
	}
	if (!empty($userHomepage) && ($userHomepage != 'http://')) {
		if (strpos($userHomepage, 'http://') !== 0)
			$userHomepage = "http://$userHomepage";
		setcookie('guestHomepage', $userHomepage, time() + 2592000, $context->getProperty('uri.blog')."/");
	}
	if( Acl::getIdentity( 'openid' ) ) {
		OpenIDConsumer::updateUserInfo( $userName, $userHomepage );
	}
	$comment = array();
	$comment['entry'] = $entryId;
	$comment['parent'] = null;
	$comment['name'] = $userName;
	$comment['password'] = $userPassword;
	$comment['homepage'] = ($userHomepage == '' || $userHomepage == 'http://') ? '' : $userHomepage;
	$comment['secret'] = $userSecret;
	$comment['comment'] = $userComment;
	$comment['ip'] = $_SERVER['REMOTE_ADDR'];

	$result = addComment($blogid, $comment);
	if (in_array($result, array("ip", "name", "homepage", "comment", "openidonly", "etc"))) {
		switch ($result) {
			case "name":
				$errorString = _text('차단된 이름을 사용하고 계시므로 댓글을 남기실 수 없습니다.');
				break;
			case "ip":
				$errorString = _text('차단된 IP를 사용하고 계시므로 댓글을 남기실 수 없습니다.');
				break;
			case "homepage":
				$errorString = _text('차단된 홈페이지 주소를 사용하고 계시므로 댓글을 남기실 수 없습니다.');
				break;
			case "comment":
				$errorString = _text('금칙어를 사용하고 계시므로 댓글을 남기실 수 없습니다.');
				break;
			case "openidonly":
				$errorString = _text('관리자 설정에 의해 오픈아이디로만 댓글을 남길 수 있습니다.');
				break;
			case "etc":
				$errorString = _text('귀하는 차단되었으므로 사용하실 수 없습니다.');
				break;
		}
		Respond::PrintResult(array('error' => 1, 'description' => $errorString));
		exit;

	} else if ($result === false) {
		Respond::PrintResult(array('error' => 2, 'description' => _text('댓글을 달 수 없습니다.')));
		exit;
	} else {
		$entry = array();
		$entry['id'] = $entryId;
		$entry['slogan'] = getSloganById($blogid, $entryId);
		if(!$comment['secret']) {
			$pool = DBModel::getInstance();
			$pool->reset('Entries');
			$pool->setQualifier('blogid','equals',$blogid);
			$pool->setQualifier('id','equals',$entryId);
			$pool->setQualifier('draft','equals',0);
			$pool->setQualifier('visibility','equals',3);
			$pool->setQualifier('acceptcomment','equals',1);
			$row = $pool->getAll('*');
			if(!empty($row))
				sendCommentPing($entryId, $context->getProperty('uri.default')."/".($context->getProperty('blog.useSloganOnPost') ? "entry/{$row['slogan']}": $entryId), is_null($user) ? $comment['name'] : $user['name'], is_null($user) ? $comment['homepage'] : $user['homepage']);
		}
		importlib('model.blog.skin');
		$skin = new Skin($context->getProperty('skin.skin'));
		if ($entryId > 0) {
			$commentBlock = getCommentView($entry, $skin);
			dress('article_rep_id', $entryId, $commentBlock);
			$commentBlock = escapeCData(revertTempTags(removeAllTags($commentBlock)));
			$recentCommentBlock = escapeCData(revertTempTags(getRecentCommentsView(getRecentComments($blogid), null, $skin->recentCommentItem)));
			$commentCount = getCommentCount($blogid, $entryId);
			$commentCount = ($commentCount > 0) ? $commentCount : 0;
			list($tempTag, $commentView) = getCommentCountPart($commentCount, $skin);
		} else {
			$commentView = '';
			$commentBlock = getCommentView($entry, $skin);
			dress('article_rep_id', $entryId, $commentBlock);
			$commentBlock = escapeCData(revertTempTags(removeAllTags($commentBlock)));
			$commentCount = 0;
			$recentCommentBlock = escapeCData(revertTempTags(getRecentCommentsView(getRecentComments($blogid), $skin->recentComment, $skin->recentCommentItem)));
		}
		Respond::PrintResult(array('error' => 0,
			'commentView'        => $commentView,
			'commentCount'       => $commentCount,
			'commentBlock'       => $commentBlock,
			'recentCommentBlock' => $recentCommentBlock));
		exit;
	}
}
?>
