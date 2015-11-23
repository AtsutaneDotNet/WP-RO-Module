<?php

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function char_manage() {
	global $wpdb, $userdata, $jobname;
?>
<div class="wrap">
<?php
	if ($_POST['Submit']) {
		$pchar_id = $_POST['charid'];
		$pacc_id = $_POST['accid'];
		$psave_map = $_POST['savemap'];
		$pxmap = $_POST['xmap'];
		$pymap = $_POST['ymap'];
		if ($_POST['type'] == "reset_eq") {
			$update_eq = $wpdb->query("UPDATE `char` SET `weapon` = '0', `shield` = '0', `head_top` = '0', `head_mid` = '0', `head_bottom` = '0' WHERE `char_id` = '$pchar_id' AND `account_id` = '$pacc_id'");
			$update_inven = $wpdb->query("UPDATE `inventory` SET `equip` = '0' WHERE `char_id` = '$pchar_id'");
			$mmsg = 'Equipment for char id ' . $pchar_id . ' has been reset.';
		}
		elseif ($_POST['type'] == "reset_hair") {
			$update_hc = $wpdb->query("UPDATE `char` SET `hair_color` = '0' WHERE `char_id` = '$pchar_id' AND `account_id` = '$pacc_id'");
			$update_ht = $wpdb->query("UPDATE `char` SET `hair` = '0' WHERE `char_id` = '$pchar_id' AND `account_id` = '$pacc_id'");
			$mmsg = 'Hair type and color for char id ' . $pchar_id . ' has been reset.';
		}
		elseif ($_POST['type'] == "reset_cloth") {
			$update_cc = $wpdb->query("UPDATE `char` SET `clothes_color` = '0' WHERE `char_id` = '$pchar_id' AND `account_id` = '$pacc_id'");
			$mmsg = 'Color cloth for char id ' . $pchar_id . ' has been reset.';
		}
		elseif ($_POST['type'] == "reset_map") {
			$update_sm = $wpdb->query("UPDATE `char` SET `last_map` = '$psave_map', `last_x` = '$pxmap', `last_y` = '$pymap' WHERE `char_id` = '$pchar_id'");
			$mmsg = 'Save point for char id ' . $pchar_id . ' has been reset.';
		}
	}
	if ($mmsg) { echo '<div id="message" class="updated fade"><p>' . $mmsg . '</p></div>'; }
	if ($userdata) {
		$check_aid = $wpdb->get_row("SELECT * FROM login WHERE userid = '$userdata->user_login'");
		if ($check_aid) {
			$check_char = $wpdb->get_results("SELECT * FROM `char` WHERE `account_id` = '$check_aid->account_id'");
			if ($check_char) {
				foreach ($check_char as $char) {
					echo '
						<h2>Manage - ' . $char->name . ' ('.$char->char_id.')</h2>
					';
					$guild_data = $wpdb->get_row("SELECT * FROM guild WHERE guild_id = '$char->guild_id'");
					$emblems = $guild_data->emblem_data;
					if ($emblems) {
						$guild_emblem = '<img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Emblem.php?emblems='.$emblems.'" alt="Emblem" title="Emblem" />';
					}
					$get_opt = get_option('ro_panel_opt');
					if ($get_opt['jail_map'] == $char->last_map) { echo '<div id="akismet-warning" class="updated fade-ff0000"><p>Your Char Is In Jail. So U Cant Manage It.</p></div>'; }
					elseif ($char->online == 0) {
					echo '
						<table align="center" width="100%">
							<tr>
								<th rowspan="9" width="20%"><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/' . $check_aid->sex . '/' . $char->class . '.gif" alt="'.$jobname[$char->class].'" title="'.$jobname[$char->class].'" /></th>
								<th width="20%">Char Name</th><td>:</td><td width="20%">' . $char->name . '</td>
								<th width="20%">Job Class</th><td>:</td><td width="20%">' . $jobname[$char->class] . '</td>
							</tr>
							<tr>
								<th>Base Level</th><td>:</td><td>' . $char->base_level . '</td>
								<th>Job Level</th><td>:</td><td>' . $char->job_level . '</td>
							</tr>
							<tr>
								<th>HP/Max HP</th><td>:</td><td>' . $char->hp . '/' . $char->max_hp . '</td>
								<th>SP/Max SP</th><td>:</td><td>' . $char->sp . '/' . $char->max_sp . '</td>
							</tr>
							<tr>
								<th>STR</th><td>:</td><td>' . $char->str . '</td>
								<th>INT</th><td>:</td><td>' . $char->int . '</td>
							</tr>
							<tr>
								<th>AGI</th><td>:</td><td>' . $char->agi . '</td>
								<th>DEX</th><td>:</td><td>' . $char->dex . '</td>
							</tr>
							<tr>
								<th>VIT</th><td>:</td><td>' . $char->vit . '</td>
								<th>LUK</th><td>:</td><td>' . $char->luk . '</td>
							</tr>
							<tr>
								<th>Char Zeny</th><td>:</td><td>' . $char->zeny . '</td>
								<th>Guild Name</th><td>:</td><td>'.$guild_emblem.' <a href="' . $_SERVER['SCRIPT_NAME'] . '?page=Ro-Guild.php&detail='.$guild_data->guild_id.'">'.$guild_data->name.'</a></td>
							</tr>
							<tr>
								<th>Last Map</th><td>:</td><td>' . $char->last_map . ' (' . $char->last_x . ',' . $char->last_y . ')</td>
								<th>Save Map</th><td>:</td><td>' . $char->save_map . ' (' . $char->save_x . ',' . $char->save_y . ')</td>
							</tr>
							<tr>
								<th>Management</th>
								<td>:</td>
								<td><form method="POST">
									<input type="hidden" name="charid" value="' . $char->char_id . '" />
									<input type="hidden" name="accid" value="' . $char->account_id . '" />
									<input type="hidden" name="savemap" value="' . $char->save_map . '" />
									<input type="hidden" name="xmap" value="' . $char->save_x . '" />
									<input type="hidden" name="ymap" value="' . $char->save_y . '" />
									<select name="type">
										<option selected value="reset_eq">Reset Equip
										<option value="reset_hair">Reset Hair
										<option value="reset_cloth">Reset Cloth Color
										<option value="reset_map">Reset Save Point
									</select>
								</td><td></td><td></td><td>
									<p class="submit"><input type="submit" name="Submit" value="Manage My Char &raquo;" /></p>
								</form></td>
							</tr>
						</table>
					';
					}
					else { echo '<div id="akismet-warning" class="updated fade-ff0000"><p>Char Is Online. Please logout first to manage it.</p></div>'; }
					$guild_emblem = '';
				}
			}
			else {
				echo '<div id="akismet-warning" class="updated fade-ff0000"><p>Sorry I cant found your char. Please make sure you create one already.</p></div>';
			}
		}
	}
	echo '</div>';
}

?>