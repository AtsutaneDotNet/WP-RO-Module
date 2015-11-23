<?php

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

### Function: Add Option Menu
add_action('admin_menu', 'bt_add_pages');
function bt_add_pages() {
	add_management_page(__("ROCP"), __('ROCP'), 8, __FILE__, 'ro_manage');
	add_submenu_page('profile.php', 'Blacklisted Database', 'Blacklisted', 0, 'Ro-Bl.php', 'bl_item');
	add_submenu_page('profile.php', 'Manage Character', 'Manage Character', 0, 'Ro-Manage.php', 'char_manage');
	add_submenu_page('profile.php', 'Character Ranking', 'Character Ranking', 0, 'Ro-Ranking.php', 'stand_item');
	add_submenu_page('profile.php', 'Guild Ranking', 'Guild Ranking', 0, 'Ro-Guild.php', 'stand_guild');
	add_submenu_page('profile.php', 'Who Is Online', 'Who Is Online', 0, 'Ro-Online.php', 'online_data');
	add_submenu_page('profile.php', 'Manage Inventory', 'Manage Inventory', 0, 'Ro-Inventory.php', 'char_inventory');
	add_submenu_page('profile.php', 'Server Statistic', 'Statistic', 8, 'Ro-Stats.php', 'server_stats');
	add_submenu_page('profile.php', 'RO Log', 'RO Log', 8, 'Ro-Log.php', 'ro_log_read');
}

### Function: Admin Page
function ro_manage() {
	global $wpdb;
	if ($_POST['ro_opt']){
		check_admin_referer('rocp-update_option');
		$server_opt = array(
			'server_name' => $_POST['server_name'],
			'server_mode' => $_POST['server_mode'],
			'jail_map' => $_POST['jail_map'],
		);
		update_option('ro_panel_opt', $server_opt);
		$update_msg = "<div id='message' class='updated fade'><p>".$_POST['server_name']." options saved successfully.</p></div>";
	}
	$get_opt = get_option('ro_panel_opt');
?>
	<div class="wrap">
		<h2><?php echo $get_opt['server_name']; ?> Control Panel</h2>
		<?php
			if ($get_opt['server_mode']) {
				echo "<div id='akismet-warning' class='updated fade-ff0000'><p><strong>ALERT!</strong> Server is in Maintenance Mode</p></div>";
			}
		?>
		<?php if ($update_msg) { _e("$update_msg"); } ?>
		<p><strong>Debug:</strong></p>
<?php
	if ($_POST['acc_sys']){
		check_admin_referer('rocp-update_account');
		echo '
			<div id="message" class="updated fade">
			<blockquote>
		';
		if ($_POST['delete'] == "inactive") {
			chknouseacc();
		}
		else {
			delete_older();
		}
		echo '
			</blockquote>
			</div>
		';
	}
?>
		<p>InActive Account: <?php get_inactive(); ?> InActive Account.</p>
		<p>3 Month Old Account: <?php get_oldacc(); ?> Old Account.</p>
		<p>Missing WP2RO Account: <?php get_wp2ro(); ?>  Account.</p>
		<form method="POST">
			<?php
			if ( function_exists('wp_nonce_field') ) {
				wp_nonce_field('rocp-update_account');
			}
			?>
			<h2><?php echo $get_opt['server_name']; ?> Account Option</h2>
			<ul style="list-style:none;">
				<li><label><input name="delete" type="radio" value="inactive" tabindex="90" /> Delete InActive Account</label></li>
    		<li><label><input name="delete" type="radio" value="old" tabindex="90" /> Delete Old Account</label></li>
    	</ul>
    	<p class="submit">
				<input type="submit" name="acc_sys" id="acc_sys" value="<?php _e('Manage Account &raquo;'); ?>">
			</p>
		</form>
		<form method="POST">
			<?php
			if ( function_exists('wp_nonce_field') ) {
				wp_nonce_field('rocp-update_option');
			}
			?>
			<h2><?php echo $get_opt['server_name']; ?> Server Setting</h2>
			<p class="submit">
				<input type="submit" name="ro_opt" id="ro_opt" value="<?php _e('Update Option &raquo;'); ?>">
			</p>
			<table class="optiontable">
				<tr valign="top"> 
					<th scope="row"><?php _e('Server Name:'); ?></th> 
					<td><input name="server_name" type="text" id="server_name" value="<?php echo $get_opt['server_name']; ?>" size="40" /></td> 
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Maintenance Mode:'); ?></th>
					<td><label for="server_mode"><input name="server_mode" type="checkbox" id="server_mode" value="server_mode" <?php if ($get_opt['server_mode']) { echo('checked="checked"'); } ?> /> <?php _e('Put this server into Maintenance Mode'); ?></label></td>
				</tr>
				<tr valign="top"> 
					<th scope="row"><?php _e('Jail Map:'); ?></th> 
					<td><input name="jail_map" type="text" id="jail_map" value="<?php echo $get_opt['jail_map']; ?>" size="20" /></td> 
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="ro_opt" id="ro_opt" value="<?php _e('Update Option &raquo;'); ?>">
			</p>
		</form>
	</div>
<?php
}

function get_inactive() {
	global $wpdb;
	$get_allwp = $wpdb->get_results("SELECT *FROM `login` WHERE `state` = '0' AND `ban_until` = '0' AND `sex` != 'S' AND `level` = '0' AND `logincount` = '0'");
	echo count($get_allwp);
}

function get_oldacc() {
	global $wpdb;
	$get_old = $wpdb->get_results("SELECT *FROM `login` WHERE `sex` != 'S' AND `level` = '0' AND `logincount` != '0' ORDER BY `login`.`lastlogin` ASC");
	$count_old = 0;
	if ($get_old) {
		foreach ($get_old as $go) {
			$time = strtotime($go->lastlogin);
			$check_old = (time() - $time);
			if ($check_old > 7776000) {
				$count_old = $count_old + 1;
			}
		}
		echo $count_old;
	}
}

function get_wp2ro() {
	global $wpdb;
	//Get Wordpress Acc
	$get_allwp = $wpdb->get_results("SELECT * FROM `wp_users` WHERE `wp_users`.`user_login` != 'admin'");
	$cemptyacc = 0;
	if ($get_allwp) {
		foreach ($get_allwp as $gwp) {
			$user_id = $gwp->user_login;
			$get_wpuid = $gwp->ID;
			//Check if the acc valid or not
			$wp2ro = $wpdb->get_row("SELECT * FROM `login` WHERE `login`.`userid` = '$user_id'");
			if (!$wp2ro) {
				$cemptyacc = $cemptyacc + 1;
			}
		}
		echo $cemptyacc;
	}
}

### Function: Delete Old Account
function delete_older() {
	global $wpdb;
	//Find Old Id
	$get_old = $wpdb->get_results("SELECT *FROM `login` WHERE `sex` != 'S' AND `level` = '0' AND `logincount` != '0' ORDER BY `login`.`lastlogin` ASC");
	$count_old = 0;
	if ($get_old) {
		foreach ($get_old as $go) {
			$time = strtotime($go->lastlogin);
			$check_old = (time() - $time);
			if ($check_old > 7776000) {
				$count_old = $count_old + 1;
				$accound_id = $go->account_id;
				$user_id = $go->userid;
				echo '<p>'.$accound_id.': ';
				//Check Char On Tat Id
				$check_char = $wpdb->get_results("SELECT * FROM `char` WHERE `account_id` = '$accound_id'");
				echo 'Found ' . count($check_char) . ' Char</p>';
				if ($check_char) {
					foreach ($check_char as $cc) {
						$char_id = $cc->char_id;
						$homun_id = $cc->homun_id;
						$party_id = $cc->party_id;
						$guild_id = $cc->guild_id;
						echo '<p>Process: '.$char_id.'. Progress: ';
						//Delete Fren
						$del_fren = $wpdb->query("DELETE FROM `friends` WHERE `friends`.`char_id` = '$char_id'");
						//Delete Global Reg
						$del_greg = $wpdb->query("DELETE FROM `global_reg_value` WHERE `global_reg_value`.`char_id` = '$char_id' AND `global_reg_value`.`account_id` = '$accound_id'");
						//Delete Inventory
						$del_inven = $wpdb->query("DELETE FROM `inventory` WHERE `inventory`.`char_id` = '$char_id'");
						//Delete Memo
						$del_memo = $wpdb->query("DELETE FROM `memo` WHERE `memo`.`char_id` = '$char_id'");
						if ($party_id != 0) {			
							//Delete Party
							$del_party = $wpdb->query("DELETE FROM `party` WHERE `party`.`party_id` = '$party_id'");
						}
						//Delete Pet
						$del_pet = $wpdb->query("DELETE FROM `pet` WHERE `pet`.`account_id` = '$accound_id' AND `pet`.`char_id` = '$char_id'");
						//Delete Skill
						$del_skill = $wpdb->query("DELETE FROM `skill` WHERE `skill`.`char_id` = '$char_id'");
						//Delete Cart
						$del_cart = $wpdb->query("DELETE FROM `cart_inventory` WHERE `cart_inventory`.`char_id` = '$char_id'");
						if ($homun_id != 0) {
							//Delete Homun
							$del_homun = $wpdb->query("DELETE FROM `homunculus` WHERE `homunculus`.`homun_id` = '$homun_id' AND `homunculus`.`char_id` = '$char_id'");
							//Delete Homun Skill
							$del_homunsk = $wpdb->query("DELETE FROM `skill_homunculus` WHERE `skill_homunculus`.`homun_id` = '$homun_id'");
						}
						if ($guild_id != 0) {
							//Delete From Member
							$del_gmem = $wpdb->query("DELETE FROM `guild_member` WHERE `guild_member`.`guild_id` = '$guild_id' AND `guild_member`.`char_id` = '$char_id'");
							//Check Guild Leader
							$get_gleader = $wpdb->get_row("SELECT * FROM `guild` WHERE `guild`.`guild_id` = '$guild_id' AND `guild`.`char_id` = '$char_id'");
							if ($get_gleader) {
								//Delete Storage
								$del_gstor = $wpdb->query("DELETE FROM `guild_storage` WHERE `guild_storage`.`guild_id` = '$guild_id'");
								//Delete Skill
								$del_gskill = $wpdb->query("DELETE FROM `guild_skill` WHERE `guild_skill`.`guild_id` = '$guild_id'");
								//Delete Expulsion
								$del_gexpel = $wpdb->query("DELETE FROM `guild_expulsion` WHERE `guild_expulsion`.`guild_id` = '$guild_id'");
								//Delete Member
								$del_gmem = $wpdb->query("DELETE FROM `guild_member` WHERE `guild_member`.`guild_id` = '$guild_id'");
								//Delete Position
								$del_gpos = $wpdb->query("DELETE FROM `guild_position` WHERE `guild_position`.`guild_id` = '$guild_id'");
								//Delete Castle
								$del_gcas = $wpdb->query("DELETE FROM `guild_castle` WHERE `guild_castle`.`guild_id` = '$guild_id'");
								//Delete Ally
								$del_gally = $wpdb->query("DELETE FROM `guild_alliance` WHERE `guild_alliance`.`guild_id` = '$guild_id'");
								//Delete Guild
								$del_guild = $wpdb->query("DELETE FROM `guild` WHERE `guild`.`guild_id` = '$guild_id'");
							}
						}
						echo 'DONE</p>';
					}
				}
				//Delete Charlog
				$del_charlog = $wpdb->query("DELETE FROM `charlog` WHERE `charlog`.`account_id` = '$accound_id'");
				//Delete Loginlog
				$del_charlog = $wpdb->query("DELETE FROM `loginlog` WHERE `loginlog`.`user` = '$user_id'");
				//Delete Storage
				$del_storage = $wpdb->query("DELETE FROM `storage` WHERE `storage`.`account_id` = '$accound_id'");
				//Delete Char
				$del_char = $wpdb->query("DELETE FROM `char` WHERE `char`.`account_id` = '$accound_id'");
				//Delete Login
				$del_login = $wpdb->query("DELETE FROM `login` WHERE `login`.`account_id` = '$accound_id'");
				//Get WP UID
				$get_wpid = $wpdb->get_row("SELECT * FROM `wp_users` WHERE `wp_users`.`user_login` = '$user_id'");
				if ($get_wpid) {
					$get_wpuid = $get_wpid->ID;
					//Delete WP UID
					$del_wpid = $wpdb->query("DELETE FROM `wp_users` WHERE `wp_users`.`user_login` = '$user_id' AND `wp_users`.`ID` = '$get_wpuid'");
					//Delete WP UMETA
					$del_wpid = $wpdb->query("DELETE FROM `wp_usermeta` WHERE `wp_usermeta`.`umeta_id` = '$get_wpuid'");
				}
				//echo $go->account_id . ' OLD ' . date(j,$check_old) . ' - ' . $go->lastlogin . '<br />';
				echo '<p>Finish delete '.$accound_id.'</p>';
			}
		}
		//echo $count_old . '<br />';
	}
}

### Function: Delete Empty Wordpress Account
function chkfalseacc() {
	global $wpdb;
	//Get Wordpress Acc
	$get_allwp = $wpdb->get_results("SELECT * FROM `wp_users` WHERE `wp_users`.`user_login` != 'admin'");
	//echo '<p>Found ' . count($get_allwp) . ' Account</p>';
	$cemptyacc = 0;
	if ($get_allwp) {
		foreach ($get_allwp as $gwp) {
			$user_id = $gwp->user_login;
			$get_wpuid = $gwp->ID;
			//Check if the acc valid or not
			$wp2ro = $wpdb->get_row("SELECT * FROM `login` WHERE `login`.`userid` = '$user_id'");
			if (!$wp2ro) {
				$cemptyacc = $cemptyacc + 1;
				//echo "<p>Acc Not Found. WP ID: $user_id <br />Start Delete Procedure ... ";
				//Delete WP UID
				$del_wpid = $wpdb->query("DELETE FROM `wp_users` WHERE `wp_users`.`user_login` = '$user_id' AND `wp_users`.`ID` = '$get_wpuid'");
				//Delete WP UMETA
				$del_wpid = $wpdb->query("DELETE FROM `wp_usermeta` WHERE `wp_usermeta`.`umeta_id` = '$get_wpuid'");
				//echo '<font color="green">DONE</font></p>';
			}
		}
		//echo "<p>Found $cemptyacc Empty Account</p>";
	}
}

### Function: Delete Unuse Account
function chknouseacc() {
	global $wpdb;
	//Get RO Acc
	$get_allwp = $wpdb->get_results("SELECT *FROM `login` WHERE `state` = '0' AND `ban_until` = '0' AND `sex` != 'S' AND `level` = '0' AND `logincount` = '0'");
	echo '<p>Found ' . count($get_allwp) . ' Account</p>';
	$cemptyacc = 0;
	if ($get_allwp) {
		foreach ($get_allwp as $gwp) {
			$user_id = $gwp->userid;
			$accound_id = $gwp->account_id;
			$cemptyacc = $cemptyacc + 1;
			echo "<p>Unuse Acc Found. ID: $user_id <br />Start Delete Procedure ";
			//Get WP UID
			$get_wpid = $wpdb->get_row("SELECT * FROM `wp_users` WHERE `wp_users`.`user_login` = '$user_id'");
			if ($get_wpid) {
				$get_wpuid = $get_wpid->ID;
				//Delete WP UID
				$del_wpid = $wpdb->query("DELETE FROM `wp_users` WHERE `wp_users`.`user_login` = '$user_id' AND `wp_users`.`ID` = '$get_wpuid'");
				echo '.';
				//Delete WP UMETA
				$del_wpid = $wpdb->query("DELETE FROM `wp_usermeta` WHERE `wp_usermeta`.`umeta_id` = '$get_wpuid'");
				echo '.';
			}
			//Delete Login
			$del_login = $wpdb->query("DELETE FROM `login` WHERE `login`.`account_id` = '$accound_id'");
			echo '. ';
			echo '<font color="green">DONE</font></p>';
		}
		echo "<p>Found $cemptyacc Unuse Account</p>";
	}
}

?>