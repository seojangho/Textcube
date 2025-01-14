<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
define('__NO_ADMINPANEL__', true);
$context->setProperty('import.library', array(
    'function.string', // Basics
    'function.time',
    'function.javascript',
    'function.html',
    'function.xml',
    'function.mail',
    'DEBUG : Basic functions loaded.',
    'auth',
    'blogskin',
    'DEBUG : Default library loaded.',
    'model.blog.service',                // Models
    'model.blog.archive',
    'model.blog.attachment',
    'model.blog.blogSetting',
    'model.blog.category',
    'model.blog.comment',
    'model.blog.entry',
    'model.blog.keyword',
    'model.blog.page',
    'model.blog.notice',
    'model.blog.link',
    'model.blog.locative',
    'model.blog.sidebar',
    'model.blog.remoteresponse',
    'model.blog.tag',
    'model.common.setting',
    'model.common.plugin',
    'model.common.module',
    'DEBUG : Models loaded.',
    'view.html',                        // Views
    'view.paging',
    'view.view',
    'DEBUG : Views loaded.'));
?>
