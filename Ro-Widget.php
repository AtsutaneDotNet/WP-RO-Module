<?php

### Function: Init Ragnarok Widget
function ro_userpanel($args) {
	global $user_identity, $user_email, $post, $wpdb;
	extract($args);
	$title = htmlspecialchars("User Panel");
	echo $before_widget.$before_title.$title.$after_title;
	if ($user_identity) {
		echo '<p align="center">';
		echo $user_identity;
		echo '</p>';
		echo '<ul>';
			wp_register();
		echo '<li><a href="' . get_option('siteurl') . '/wp-login.php?action=logout&redirect_to=' . get_option('home') . '" title="Logout">Logout</a></li></ul>';
			wp_meta();
		echo '</ul><br /><br />';
	}
	else { 
		echo '
			<p align="center">
			<form name="loginform" id="loginform" action="' . get_option('siteurl') . '/wp-login.php" method="post">
			<div>User Name</div>
			<input  type="text" name="log" id="log" value="" size="20" tabindex="1" />
			<div>Password</div>
			<input type="password" name="pwd" id="pwd" value="" size="20" tabindex="2" />
			<div>Remember Me <input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="3" checked="checked" /><br /><input type="submit" name="submit" id="submit" value="Login" tabindex="4" /></div>
			<div><a href="' . get_option('siteurl') . '/wp-register.php">Register</a></div>
			<input type="hidden" name="redirect_to" value="' . get_option('home') . '" /></form></p>';
	}
	$todaydate = date('Y-m-d', time()) . '%';
	$todayc = $wpdb->get_results("SELECT * FROM `wp_users` WHERE `user_registered` LIKE '$todaydate'");
	$timestamp = strtotime("Yesterday");
	$ytddate = date('Y-m-d', $timestamp) . '%';
	$ytdc = $wpdb->get_results("SELECT * FROM `wp_users` WHERE `user_registered` LIKE '$ytddate'");
	$get_latestacc = $wpdb->get_row("SELECT * FROM `wp_users` ORDER BY `wp_users`.`ID` DESC LIMIT 1");
	$get_allwp = $wpdb->get_results("SELECT * FROM `wp_users` WHERE `wp_users`.`user_login` != 'admin'");
	echo '
		<h4>Membership</h4>
		<ul>
			<li><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/ur-moderator.gif" alt="membership" title="Membership" /> Latest: <strong>' . $get_latestacc->user_login . '</strong></li>
			<li><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/ur-author.gif" alt="membership" title="Membership" /> Today: <strong>' . count($todayc) . '</strong></li>
			<li><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/ur-admin.gif" alt="membership" title="Membership" /> Yesterday: <strong>' . count($ytdc) . '</strong></li>
			<li><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/ur-guest.gif" alt="membership" title="Membership" /> Overall: <strong>' . count($get_allwp) . '</strong></li>
		</ul>
	';
	echo $after_widget;
}

### Function: Register Login Widget
function ro_login_init() {
	if (function_exists('register_sidebar_widget')) {
		register_sidebar_widget('Login Widget','ro_userpanel');
	}
}

### Function: Add Ragnarok Widget
add_action('init', 'ro_login_init');

?>