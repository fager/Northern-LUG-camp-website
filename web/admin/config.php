<?php

// Default DB
$DB_user = 'test';
$DB_pass = 'test';
$DB_name = 'test';
$DB_host = "localhost";

$CURRENT_EVENT_ID = 0;

$DB['DEFAULT']['user'] = $DB_user;
$DB['DEFAULT']['pass'] = $DB_pass;
$DB['DEFAULT']['name'] = $DB_name;
$DB['DEFAULT']['host'] = $DB_host;

define ('WEB_DATEFORMAT','%d.%m.%Y, %H:%m');
define ('WEB_NEWSPAGE','./news.php');
define ('WEB_NEWSTEASER_ANZAHL',3);
define ('DEBUG',0);

$DB_SCHEMA['news_cat']['name'] = 'News Kategorie';
$DB_SCHEMA['news_cat']['cols']['catid']['name'] = '';
$DB_SCHEMA['news_cat']['cols']['name']['name'] = 'Kategorie';
$DB_SCHEMA['news_cat']['cols']['pic']['name'] = 'Bild';

$DB_SCHEMA['news_eintrag']['name'] = 'News';

$DB_SCHEMA['news_eintrag']['cols']['eintragid']['name'] = '';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['detailview']['name'] = 'Details';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['detailview']['p'] = 'news';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['edit']['name'] = 'Editieren';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['edit']['p'] = 'news';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['delete']['name'] = 'L&ouml;schen';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['delete']['p'] = 'news';

$DB_SCHEMA['news_eintrag']['cols']['title']['name'] = 'Titel';
$DB_SCHEMA['news_eintrag']['cols']['short']['name'] = 'Teaser';
$DB_SCHEMA['news_eintrag']['cols']['txt']['name'] = 'Newstext';
$DB_SCHEMA['news_eintrag']['cols']['crdate']['name'] = 'Erstellt am';
$DB_SCHEMA['news_eintrag']['cols']['author']['name'] = 'Autor';

$DB_SCHMEA['event_lug']['cols']['lugid']['name'] = "ID";
$DB_SCHEMA['event_lug']['cols']['lugid']['cmd']['detailview']['name'] = 'Details';
$DB_SCHEMA['event_lug']['cols']['lugid']['cmd']['detailview']['p'] = 'lugs';
$DB_SCHEMA['event_lug']['cols']['lugid']['cmd']['edit']['name'] = 'Editieren';
$DB_SCHEMA['event_lug']['cols']['lugid']['cmd']['edit']['p'] = 'lugs';

$DB_SCHEMA['event_lug']['cols']['name']['name'] = 'Titel';
$DB_SCHEMA['event_lug']['cols']['abk']['name'] = 'Abk.';
$DB_SCHEMA['event_lug']['cols']['url']['name'] = 'URL';
$DB_SCHEMA['event_lug']['cols']['crdate']['name'] = 'Erstellt am';

$DB_SCHEMA['sponsoren']['cols']['name']['name'] = 'Name';
$DB_SCHEMA['sponsoren']['cols']['img']['name'] = 'Bild';
$DB_SCHEMA['sponsoren']['cols']['url']['name'] = 'URL';
$DB_SCHEMA['sponsoren']['cols']['crdate']['name'] = 'Erstellt am';

if( is_file('.htconfig.php') ) {
	include_once('.htconfig.php');
}
?>
