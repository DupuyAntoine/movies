<?php
session_name('movies_session');
session_start();

define('FACEBOOK_SDK_ROOT_PATH', 'facebook');

define('FACEBOOK_SDK_V4_SRC_DIR', 'inc/'.FACEBOOK_SDK_ROOT_PATH.'/src/Facebook/');
require __DIR__ . '/'.FACEBOOK_SDK_ROOT_PATH.'/autoload.php';

define('FB_APP_ID', '');
define('FB_APP_SECRET', '');

global $protocol, $domain, $current_dir, $root_path, $root_dir;
$protocol = (@$_SERVER['HTTPS'] == 'on' ? 'https' : 'http');
$domain = $_SERVER['HTTP_HOST'];
$current_dir = dirname($_SERVER['PHP_SELF']);
$current_path = $protocol.'://'.$domain.$current_dir;
$root_dir = str_replace(array('\\', 'inc'), array('/', ''), __DIR__);
$root_path = $protocol.'://'.$domain.'/'.str_replace($_SERVER['DOCUMENT_ROOT'], '', $root_dir);

$current_year = date('Y');

$current_page = basename($_SERVER['PHP_SELF']);

$pages = array(
	'index.php' => 'Accueil',
	'random.php' => 'Film au hasard',
	'news.php' => 'Actualités',
	'search.php' => 'Recherche',
	'contact.php' => 'Contact'
);

$admin_pages = array(
	'navbar' => array(
		'index.php' => 'Tableau de bord',
		'settings.php' => 'Paramètres',
		'profile.php' => 'Profile',
		'../index.php' => 'Site',
		'logout.php' => 'Déconnexion',
	),
	'sidebar' => array(
		'index.php' => 'Tableau de bord',
		'stats.php' => 'Statistiques',
		'reports.php' => 'Rapports',
		'movies.php' => 'Films',
		'users.php' => 'Utilisateurs',
		'comments.php' => 'Commentaires',
		'messages.php' => 'Messages',
	)
);


// On récupère la page en cours, $_SERVER['PHP_SELF'] renvoie le chemin en entier, basename permet de garder seulement le nom du fichier
$current_page = basename($_SERVER['PHP_SELF']);