<?php
/*
*
*
*     Author: Alex Scott
*      Email: alex@cgi-central.net
*        Web: http://www.cgi-central.net
*    Details: Admin Info / PHP
*    FileName $RCSfile$
*    Release: 6.3.20 ($Revision$)
*
* Please direct bug reports,suggestions or feedback to the cgi-central forums.
* http://www.cgi-central.net/forum/
*
* aMember PRO is a commercial software. Any distribution is strictly prohibited.
*
*/

class AdminInfoController extends Am_Mvc_Controller
{
    public function checkAdminPermissions(Admin $admin)
    {
        return $admin->hasPermission(Am_Auth_Admin::PERM_SYSTEM_INFO);
    }

    function getUpgrade()
    {
        $load = $this->getDi()->store->getBlob('upgrades-list');
        if (!empty($load))
        {
            $upgrades = unserialize($load);
        } else {
            $upgrades = ['_loaded' => null, '_dismissed' => null, 'items' => []];
        }
        if ($upgrades['_loaded'] < (time() - 3600*24))
        {
            $c = new AdminUpgradeController($this->getRequest(), $this->getResponse(), ['di' => $this->getDi()]);
            ob_start();
            $result = $c->loadUpgradesList(false);
            ob_end_clean();
            if ($result !== false) {
                $upgrades['items'] = $result;
                $upgrades['_loaded'] = time();
                $this->getDi()->store->setBlob('upgrades-list', serialize($upgrades));
            }
        }

        foreach ($upgrades['items'] as $upgrade)
        {
            if (version_compare($upgrade->version, AM_VERSION) <= 0) continue;
            return $upgrade;
        }
    }

    function getUpgradeAction()
    {
        if ($upgrade = $this->getUpgrade()) {
            echo sprintf(<<<CUT
<div style="display: inline-block; background:#fff9c4; padding:.5em">Upgrade to <strong>%s</strong> is available &middot; <a href="%s" class="ajax-link" title="Changelog">changelog</a> &middot; <a href="%s">update</a></div>
CUT
                ,
                $upgrade->version,
                $this->getDi()->url('admin-info/change-log'),
                $this->getDi()->url('admin-upgrade')
            );
        }
    }

    function changeLogAction()
    {
        if ($upgrade = $this->getUpgrade()) {
            echo <<<CUT
<pre>
{$upgrade->text}
</pre>
CUT;
        }
    }

    function indexAction()
    {
        check_demo();

        $this->view->title = ___('Version Info');
        $trial = "";
        if ('==TRIAL==' != '=='.'TRIAL==')
        {
            $trial = "Trial Version (expires ==TRIAL_EXPIRES==)";
        }
        if ('==LITE==' != '==' . 'LITE==')
        {
            $trial = "<b>LITE Version</b>";
        }
        $am_version = AM_VERSION;
        $zend_version = Zend_Version::VERSION;
        $cron_last_run = ($_ = $this->getDi()->cron->getLastRun()) ?
            sprintf('<time datetime="%s" title="%s">%s</time>',
                date("c", $_), $this->view->getElapsedTime($_), amDatetime($_)) :
            ___('Never');
        $cron_last_run_title = ___('Cron Last Run');
        $now = amDatetime('now');
        $now_title = ___('Current Server Date and Time');

        $timezone = date_default_timezone_get();
        $timezone_title = ___('Server Timezone');

        $phpversion = phpversion() . " (".php_sapi_name().")";
        $os=substr(php_uname(),0,28);
        if (strlen($os)==28) $os="$os&hellip;";
        $mysql = $this->getDi()->db->selectCell("SELECT VERSION()");

        $db = $this->getDi()->getParameter('db');
        $dsn = sprintf("mysql://%s@%s:%d/%s.%s", $db['mysql']['user'],
            $db['mysql']['host'], @$db['mysql']['port'] ? $db['mysql']['port'] : 3306,
            $db['mysql']['db'], $db['mysql']['prefix']);
        $root  = $this->getDi()->root_dir;
        $root_title = ___('Root Folder');

        $modules = [];
        foreach ($this->getDi()->modules->getEnabled() as $m)
        {
            $fn = AM_APPLICATION_PATH . '/' . $m . '/module.xml';
            if (!file_exists($fn)) continue;
            $xml = simplexml_load_file($fn);
            if (!$xml) continue;

            $version = "(" . $xml->version . ")";
            $modules[] = "$m $version";
        }
        $modules = implode("<br />", $modules);
        $modules_title = ___('Modules');

        $plugins = "";
        foreach (array_merge(
            $this->getDi()->plugins_payment->loadEnabled()->getAllEnabled(),
            $this->getDi()->plugins_protect->loadEnabled()->getAllEnabled()) as $p) {
            $rClass = new ReflectionClass(get_class($p));
            $plugins .= sprintf("%s (%s - %s) <br />\n",
                $p->getId(),
                preg_replace('/\$'.'Revision: (\d+).*/', '$1', $rClass->getConstant('PLUGIN_REVISION')),
                preg_replace('/\$'.'Date: (.+?)\s+.+/', '$1',  $rClass->getConstant('PLUGIN_DATE')));

        }
        $plugins_title = ___('Plugins');

        $_ = explode('_', get_class($this->getDi()->cacheBackend));
        $cacheBackend = array_pop($_);
        $cacheBackend_title = ___('Cache Backend');

        $upgrade_url = json_encode($this->getDi()->url('admin-info/get-upgrade', false));
        $amInfo = <<<CUT
<script type="text/javascript">
jQuery(function(){
    jQuery.get($upgrade_url, function(r){
        jQuery('#am-version-cell').append(r);
    });
});
</script>
<div class="am-grid-container">
<table class="am-grid grid-no-highlight">
<tr class="am-grid-row">
    <td align="right">$now_title</td>
    <td><strong>$now</strong></td>
</tr>
<tr class="am-grid-row odd">
    <td align="right">$timezone_title</td>
    <td><strong>$timezone</strong></td>
</tr>
<tr class="am-grid-row">
    <td align="right" style="vertical-align:middle">aMember</td>
    <td id="am-version-cell"><strong>$am_version</strong>
    $trial
    <div style="display:inline-block; padding:.5em 0">&nbsp;</div>
    </td>
</tr>
<tr class="am-grid-row odd">
    <td align="right">PHP</td>
    <td><strong>$phpversion</strong></td>
</tr>
<tr class="am-grid-row">
    <td align="right">OS</td>
    <td><strong>$os</strong></td>
</tr>
<tr class="am-grid-row odd">
    <td align="right">MySQL</td>
    <td><strong>$mysql</strong><br />
        <strong>$dsn</strong></td>
</tr>
<tr class="am-grid-row">
    <td align="right">$cacheBackend_title</td>
    <td><strong>$cacheBackend</strong></td>
</tr>
<tr class="am-grid-row odd">
    <td align="right">$root_title</td>
    <td><strong>$root</strong></td>
</tr>
<tr class="am-grid-row">
    <td align="right">$cron_last_run_title</td>
    <td><strong>$cron_last_run</strong></td>
</tr>
<tr class="am-grid-row odd">
    <td align="right">$modules_title</td>
    <td>$modules</td>
</tr>
<tr class="am-grid-row">
    <td align="right">$plugins_title</td>
    <td>$plugins</td>
</tr>
</table>
</div>
CUT;
        $content = $amInfo;

        if ($this->getDi()->authAdmin->getUser()->isSuper()) {
            ob_start();
            phpinfo(1|4|8|16|32);
            $phpInfo = ob_get_clean();

            $phpStyles = <<<CUT
#phpinfo {background-color: #ffffff; color: #000000;}
#phpinfo td, #phpinfo th, #phpinfo h1, #phpinfo h2 {font-family: sans-serif;}
#phpinfo pre {margin: 0px; font-family: monospace;}
#phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
#phpinfo a:hover {text-decoration: underline;}
#phpinfo table {border-collapse: collapse;}
#phpinfo .center {text-align: center;}
#phpinfo .center table { margin-left: auto; margin-right: auto; text-align: left;}
#phpinfo .center th { text-align: center !important; }
#phpinfo td, #phpinfo th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
#phpinfo h1 {font-size: 150%;}
#phpinfo h2 {font-size: 125%;}
#phpinfo .p {text-align: left;}
#phpinfo .e {background-color: #ccccff; font-weight: bold; color: #000000;}
#phpinfo .h {background-color: #9999cc; font-weight: bold; color: #000000;}
#phpinfo .v {background-color: #cccccc; color: #000000; word-break: break-word; word-wrap: break-word}
#phpinfo .vr {background-color: #cccccc; text-align: right; color: #000000;}
#phpinfo img {float: right; border: 0px;}
#phpinfo hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
CUT;

            preg_match('/<body>(.*)<\/body>/s', $phpInfo, $matches);
            $phpInfo = $matches[1];

            $content .= sprintf('<style type="text/css">%s</style><h1>PHP Info</h1><div id="phpinfo" class="am-grid-container"><br />%s</div>',
                $phpStyles, $phpInfo);

        }

        $this->view->assign('content', $content);
        $this->view->display("admin/layout.phtml");
    }
}