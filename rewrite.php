<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
define('ROOT',dirname(__FILE__));
require_once(ROOT.'/framework/id/textcube/Dispatcher.php');
/** Dispatching Interface request via URI */
$dispatcher = Dispatcher::getInstance();
/** Interface Loading */
if (empty($dispatcher->service['debugmode'])) {@include_once $dispatcher->interfacePath;}
else {include_once $dispatcher->interfacePath;}
?>
