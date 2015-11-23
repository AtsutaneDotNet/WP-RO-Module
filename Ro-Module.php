<?php

/*
Plugin Name: WPRO Module
Plugin URI: http://www.atsutane.net
Description: Ragnarok Online Module For Wordpress.
Author: Atsutane Shirane
Version: 1.0
Author URI: http://www.atsutane.net
*/

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

$plugin_name = 'WPRO Module';
$plugin_url = 'http://www.atsutane.net';
$plugin_author = 'Atsutane Shirane';
$plugin_version = '1.0';

require_once("Ro-Bl.php");
require_once("Ro-Ranking.php");
require_once("Ro-Manage.php");
require_once("Ro-Online.php");
require_once("Ro-Stats.php");
require_once("Ro-Widget.php");
require_once("Ro-Admin.php");
require_once("Ro-Status.php");
require_once("Ro-Guild.php");
require_once("Ro-Inventory.php");
require_once("Ro-Log.php");

function rologin_redirect($link) {
	if(!current_user_can('level_10')){
		$link = preg_replace("/Site Admin/", "Control Panel", $link);
		$link = preg_replace("/\/wp-admin\//","/wp-admin/profile.php",$link);
		}
	return $link;
}

add_filter('register', 'rologin_redirect');
add_action('login_head', 'rologin_head');
add_action('admin_head', 'roadmin_head');
add_action('admin_footer', 'roadmin_foot');
add_action('wp_head', 'ro_head');

function roadmin_foot() {
	global $plugin_name, $plugin_url, $plugin_author, $plugin_version;
	echo '
		<div>
			<p align="center"><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/Ro-Emblem.png" alt="Ro-Emblem" title="Ro-Emblem" /> <strong>'.$plugin_name.'</strong> Version <strong>'.$plugin_version.'</strong> By <a href="'.$plugin_url.'"><strong>'.$plugin_author.'</strong></a></p>
		</div>
	';
}

function roadmin_head() {
	global $submenu, $menu, $wpdb;
	echo '<link rel="stylesheet" href="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Style-Admin.css" type="text/css" />';
	if(!current_user_can('level_10')){
		unset($menu[0]); // hide dashboard
		unset($menu[35]); // hide Users or Profile
		//unset($submenu['profile.php']);
	}
}

function rologin_head() {
	echo '<link rel="stylesheet" href="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Style.css" type="text/css" />';
}

function ro_head() {
	echo '<link rel="stylesheet" href="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Style.css" type="text/css" />';
}

//Update Profile Data
add_action('profile_update', 'parse_user_profile');
function parse_user_profile($data) {
	global $wpdb;
	$profile_data = get_userdata($data);
	$update_user = $wpdb->query("UPDATE login SET user_pass = '$profile_data->user_pass', email = '$profile_data->user_email' WHERE userid = '$profile_data->user_login'");
}


add_action('user_register', 'parse_user_regis');
//add_action('password_reset', 'parse_user_password');

function parse_user_password($data) {
	global $wpdb, $message;
	$new_pass = substr( md5( uniqid( microtime() ) ), 0, 7);
	$update_ropass = $wpdb->query("UPDATE login SET user_pass = MD5('$new_pass') WHERE userid = '$data'");
	$message .= sprintf(__('RO Password: %s'), $new_pass) . "\r\n";
}

function parse_user_regis($data) {
	global $wpdb, $gender, $ip_add;
	$reg_data = get_userdata($data);
	$regis_user = $wpdb->query("INSERT INTO login (userid, user_pass, sex, email) VALUES ('$reg_data->user_login', '$reg_data->user_pass', '$gender', '$reg_data->user_email')");
	//update_usermeta($data,'gender','$gender');
	rocontrol_regis($ip_add);
}

function rocontrol_regis($ip_add) {
	global $wpdb, $ip_add;
	$check_ip = $wpdb->get_row("SELECT * FROM `wp_roregis` WHERE `wp_roregis`.`IP` = '$ip_add'");
	if ($check_ip) {
		if ($check_ip->Count < 2) {
			$regis_count = $check_ip->Count + 1;
			$update_regis = $wpdb->query("UPDATE `wp_roregis` SET `Count` = '$regis_count' WHERE `IP` = '$ip_add'");
		}
	}
	else {
		$insert_ip = $wpdb->query("INSERT INTO `wp_roregis` (IP, Count) VALUES ('$ip_add', '1')");
	}
}

add_action('register_form', 'roreg_form');
add_action('register_post', 'roreg_post');

function roreg_form() {
    echo '
    	<p>Select your gender.</p>
    	<p><label>Male: <input name="gender" type="radio" value="M" tabindex="90" /></label>&nbsp;&nbsp;
    	<label>Female: <input name="gender" type="radio" value="F" tabindex="90" /></label></p>
    	<input type="hidden" name="userip" value="' . $_SERVER['REMOTE_ADDR'] . '" />
    ';
}

function roreg_post() {
    global $wpdb, $errors, $gender, $ip_add;
    $ip_add = $_POST['userip'];
    $todaydate = date('Y-m-d', time()) . '%';
    $check_regcount = $wpdb->get_row("SELECT * FROM `wp_roregis` WHERE `wp_roregis`.`IP` = '$ip_add' AND `wp_roregis`.`Count` = '2' AND `wp_roregis`.`Time` LIKE '$todaydate'");
		if ($check_regcount) {
			$time = strtotime($check_regcount->Time);
			$check_old = (time() - $time);
			if ($check_old < 604800) {
				$errors['REGIP'] = 'ERROR: 1 IP Only Allowed To Register 2 Id Per Week.!';
			}
		}
    if ( empty( $_POST['gender'] ) ) $errors['gender'] = 'ERROR: You need to fill in the gender field!';
    else {
    	$gender = $_POST['gender'];
    }
}

add_filter('login_headerurl', 'ro_change_url');
add_filter('login_headertitle', 'ro_change_title');

function ro_change_url($content) {
	$content = get_option('home');
	return $content;
}

function ro_change_title($content) {
	$content = get_option('blogdescription');
	return $content;
}

$itemtype = array(
'0' => "Healing item",
'2' => "Usable item",
'3' => "Etc item",
'4' => "Weapon",
'5' => "Armor/Garment/Boots/Headgear",
'6' => "Card",
'7' => "Pet egg",
'8' => "Pet equipment",
'10' => "Ammo (Arrows/Bullets/etc)",
'11' => "Usable item",
);

$jobname = array(
'0' => "Novice",
'1' => "Swordman",
'2' => "Mage",
'3' => "Archer",
'4' => "Acolyte",
'5' => "Merchant",
'6' => "Thief",
'7' => "Knight",
'8' => "Priest",
'9' => "Wizard",
'10' => "Blacksmith",
'11' => "Hunter",
'12' => "Assassin",
'13' => "Kinght Peco",
'14' => "Crusader",
'15' => "Monk",
'16' => "Sage",
'17' => "Rogue",
'18' => "Alchemist",
'19' => "Bard",
'20' => "Dancer",
'21' => "Crusader Peco",
'22' => "Wedding",
'23' => "Super Novice",
'24' => "Gunslinger",
'25' => "Ninja",
'4001' => "Novice High",
'4002' => "Swordman High",
'4003' => "Mage High",
'4004' => "Archer High",
'4005' => "Acolyte High",
'4006' => "Merchant High",
'4007' => "Thief High",
'4008' => "Lord Knight",
'4009' => "High Priest",
'4010' => "High Wizard",
'4011' => "Mastersmith",
'4012' => "Sniper",
'4013' => "Assassin Cross",
'4014' => "Lord Knigh Peco",
'4015' => "Paladin",
'4016' => "Champion",
'4017' => "Scholar",
'4018' => "Stalker",
'4019' => "Biochemist",
'4020' => "Minstrel",
'4021' => "Gypsy",
'4022' => "Paladin Peco",
'4023' => "Baby",
'4024' => "Baby Swordman",
'4025' => "Baby Mage",
'4026' => "Baby Archer",
'4027' => "Baby Acolyte",
'4028' => "Baby Merchant",
'4029' => "Baby Thief",
'4030' => "Baby Knight",
'4031' => "Baby Priest",
'4032' => "Baby Wizard",
'4033' => "Baby Blacksmith",
'4034' => "Baby Hunter",
'4035' => "Baby Assassin",
'4036' => "Baby Knight Peco",
'4037' => "Baby Crusader",
'4038' => "Baby Monk",
'4039' => "Baby Sage",
'4040' => "Baby Rogue",
'4041' => "Baby Alchemist",
'4042' => "Baby Bard",
'4043' => "Baby Dancer",
'4044' => "Baby Crusader Peco",
'4045' => "Super Baby",
'4046' => "Taekwondo",
'4047' => "Star Gladiator",
'4048' => "Star Gladiator",
'4049' => "Soul Linker",
);

$casname = array(
'0' => "Aldebaran - Neuschwanstein",
'1' => "Aldebaran - Hohenschwangau",
'2' => "Aldebaran - Nuenberg",
'3' => "Aldebaran - Wuerzburg",
'4' => "Aldebaran - Rothenburg",
'5' => "Geffen - Repherion",
'6' => "Geffen - Eeyolbriggar",
'7' => "Geffen - Yesnelph",
'8' => "Geffen - Bergel",
'9' => "Geffen - Mersetzdeitz",
'10' => "Payon - Bright Arbor",
'11' => "Payon - Scarlet Palace",
'12' => "Payon - Holy Shadow",
'13' => "Payon - Sacred Altar",
'14' => "Payon - Bamboo Grove Hill",
'15' => "Prontera - Kriemhild",
'16' => "Prontera - Swanhild",
'17' => "Prontera - Fadhgridh",
'18' => "Prontera - Skoegul",
'19' => "Prontera - Gondul",
);

?>