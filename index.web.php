<?php
include_once 'lib/adianti/util/TAdiantiLoader.class.php';
spl_autoload_register(array('TAdiantiLoader', 'autoload_web'));
define('APPLICATION_NAME', 'framework');

$uri = 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
new TSession;

if (isset($_GET['template']))
{
    TSession::setValue('template', $_GET['template']);
}
else
{
    $template = TSession::getValue('template') ?
                TSession::getValue('template') : 'theme1';
}

if (TSession::getValue('started'))
{
    date_default_timezone_set(TSession::getValue('timezone'));
    $lang = TSession::getValue('language');
}
else
{
    $ini  = parse_ini_file('application.ini');
    $lang = $ini['language'];
    date_default_timezone_set($ini['timezone']);
    TSession::setValue('timezone', $ini['timezone']);
    TSession::setValue('language', $ini['language']);
    TSession::setValue('started', TRUE);
}
TAdiantiCoreTranslator::setLanguage($lang);
TApplicationTranslator::setLanguage($lang);

ob_start();
$menu = TMenuBar::newFromXML('menu.xml');
$menu->show();
$menu_string = ob_get_contents();
ob_end_clean();

$content  = file_get_contents("app/templates/{$template}/layout.html");
$content  = TApplicationTranslator::translateTemplate($content);
$content  = str_replace('{URI}', $uri, $content);
$content  = str_replace('{template}', $template, $content);
$content  = str_replace('{MENU}', $menu_string, $content);
$css      = TPage::getLoadedCSS();
$js       = TPage::getLoadedJS();
$content  = str_replace('{HEAD}', $css.$js, $content);

if (isset($_REQUEST['class']))
{
    $url = http_build_query($_REQUEST);
    $content = str_replace('//#javascript_placeholder#',
                           "__adianti_load_page('engine.php?{$url}');", $content);
}
echo $content;
?>
