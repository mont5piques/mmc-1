<?php
/**
 * (c) 2004-2007 Linbox / Free&ALter Soft, http://linbox.com
 * (c) 2007-2008 Mandriva, http://www.mandriva.com
 *
 * $Id$
 *
 * This file is part of Mandriva Management Console (MMC).
 *
 * MMC is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * MMC is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MMC; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


require("graph/navbar.inc.php");
require("localSidebar.php");

$p = new PageGenerator(_T("Packages list", 'pkgs'));
$p->setSideMenu($sidemenu);
$p->display();


require_once("modules/pkgs/includes/xmlrpc.php");

$res = getUserPackageApi();
$list = array();
$list_val = array();
if (!isset($_SESSION['PACKAGEAPI'])) {
    $_SESSION['PACKAGEAPI'] = array();
}
$err = array();
foreach ($res as $mirror) {
    if (isset($mirror['ERR']) && $mirror['ERR'] == 'PULSE2ERROR_GETUSERPACKAGEAPI') {
        $err[] = sprintf(_T("MMC failed to contact package server %s.", "pkgs"), $mirror['mirror']);
    } else {
        $list_val[$mirror['uuid']] = base64_encode($mirror['uuid']);
        $list[$mirror['uuid']] = $mirror['mountpoint'];
        $_SESSION['PACKAGEAPI'][$mirror['uuid']] = $mirror;
    }
}
if ($err) {
    new NotifyWidgetFailure(implode('<br/>', array_merge($err, array(_T("Please contact your administrator.", "pkgs")))));
}

$ajax = new AjaxFilterLocation(urlStrRedirect("pkgs/pkgs/ajaxPackageList"));
if (isset($_GET['location'])) {
    $ajax->setSelected($list_val[base64_decode($_GET['location'])]);
}
elseif (isset($_SESSION['pkgs_selected'])) {
    $ajax->setSelected($list_val[$_SESSION['pkgs_selected']]);
}
$ajax->setElements($list);
$ajax->setElementsVal($list_val);
$ajax->display();

$ajax->displayDivToUpdate();

?>

<style>
    .noborder { border:0px solid blue; }
</style>
