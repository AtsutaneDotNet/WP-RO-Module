<?php

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function stand_item() {
	global $wpdb, $jobname, $casname;
?>
<div class="wrap">
<?php
	if ($_GET['detail']) {
		$check_aid = $wpdb->get_row("SELECT * FROM login WHERE userid = '".$_GET['detail']."'");
		if ($check_aid->state != 0) { $acc_status = 'Blocked'; }
		elseif ($check_aid->ban_until != 0) { $acc_status = 'Ban'; }
		else { $acc_status = 'Normal'; }
		echo '
			<h2>'.$_GET['detail'].' - Account Detail ('.$check_aid->account_id.')</h2>
			<table align="center" width="100%">
				<tr>
					<th width="20%">Account Name</th><td>:</td><td width="20%">'.$_GET['detail'].'</td>
					<th width="20%">Account Level</th><td>:</td><td width="20%">' . $check_aid->level . '</td>
				</tr>
				<tr>
					<th>Last Login</th><td>:</td><td>' . $check_aid->lastlogin . '</td>
					<th>Login Count</th><td>:</td><td>' . $check_aid->logincount . '</td>
				</tr>
				<tr>
					<th>Last IP</th><td>:</td><td>' . $check_aid->last_ip . '</td>
					<th>Status</th><td>:</td><td>'.$acc_status.'</td>
				</tr>
			</table>
		';
		$charlist = $wpdb->get_results("SELECT * FROM `char` WHERE `account_id` = '$check_aid->account_id'");
		foreach ($charlist as $char) {
			$blno = $blno + 1;
			if ($check_aid->level == 0 && $blno < 101) {
				if ($char->online == 1) { $charstats = '<font color="green">Online</font>'; }
				else { $charstats = '<font color="red">Offline</font>'; }
				$guild_data = $wpdb->get_row("SELECT * FROM guild WHERE guild_id = '$char->guild_id'");
				$emblems = $guild_data->emblem_data;
				if ($emblems) {
					$guild_emblem = '<img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Emblem.php?emblems='.$emblems.'" alt="Emblem" title="Emblem" />';
				}
				echo '
					<h2>'.$blno.'. ' . $char->name . ' - '.$charstats.'</h2>
					<table align="center" width="100%">
						<tr>
							<th rowspan="4" width="20%"><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/' . $check_aid->sex . '/' . $char->class . '.gif" alt="'.$jobname[$char->class].'" title="'.$jobname[$char->class].'" /></th>
							<th width="20%">Char Name</th><td>:</td><td width="20%">' . $char->name . ' ('.$char->char_id.')</td>
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
							<th>Char Zeny</th><td>:</td><td>' . $char->zeny . '</td>
							<th>Guild Name</th><td>:</td><td>'.$guild_emblem.' <a href="' . $_SERVER['SCRIPT_NAME'] . '?page=Ro-Guild.php&detail='.$guild_data->guild_id.'">'.$guild_data->name.'</a></td>
						</tr>
					</table>
				';
			}
			$guild_emblem = '';
		}
	}
	else {
		$order = "base_level";
		if ($_POST['ro_rank']) {
			$name = $_POST['char_name'];
			$class = $_POST['char_class'];
			if ($class) {
				$classq = "AND `class` = '$class'";
			}
			$order = $_POST['order_type'];
		}
		$charlist = $wpdb->get_results("SELECT * FROM `char` WHERE `name` LIKE '%$name%' $classq ORDER BY `char`.`$order` DESC LIMIT 0 , 105");
		$total = count($charlist);
		echo '<h2>Top 100 Player Ranking</h2>';
?>
<form method="POST">
	<table class="optiontable">
		<tr valign="top"> 
			<th scope="row"><?php _e('Char Name:'); ?></th> 
			<td><input name="char_name" type="text" id="char_name" value="" size="40" /></td> 
		</tr>
		<tr valign="top"> 
			<th scope="row"><?php _e('Job Class:'); ?></th> 
			<td>
				<select name="char_class">
					<option selected value="">All
					<option value="0">Novice
					<option value="1">Swordman
					<option value="2">Mage
					<option value="3">Archer
					<option value="4">Acolyte
					<option value="5">Merchant
					<option value="6">Thief
					<option value="7">Knight
					<option value="8">Priest
					<option value="9">Wizard
					<option value="10">Blacksmith
					<option value="11">Hunter
					<option value="12">Assassin
					<option value="13">Knight Peco
					<option value="14">Crusader
					<option value="15">Monk
					<option value="16">Sage
					<option value="17">Rouge
					<option value="18">Alchemist
					<option value="19">Bard
					<option value="20">Dancer
					<option value="21">Crusader Peco
					<option value="22">Wedding
					<option value="23">Super Novice
					<option value="24">Gunslinger
					<option value="25">Ninja
					<option value="4001">Novice High
					<option value="4002">Swordman High
					<option value="4003">Mage High
					<option value="4004">Archer High
					<option value="4005">Acolyte High
					<option value="4006">Merchant High
					<option value="4007">Thief High
					<option value="4008">Lord Knight
					<option value="4009">High Priest
					<option value="4010">High Wizard
					<option value="4011">Mastersmith
					<option value="4012">Sniper
					<option value="4013">Assassin Cross
					<option value="4014">Lord Knigh Peco
					<option value="4015">Paladin
					<option value="4016">Champion
					<option value="4017">Scholar
					<option value="4018">Stalker
					<option value="4019">Biochemist
					<option value="4020">Minstrel
					<option value="4021">Gypsy
					<option value="4022">Paladin Peco
				</select>
			</td> 
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Sort By:'); ?></th>
			<td>
				<select name="order_type">
					<option selected value="base_level">Base Level
					<option value="job_level">Job Level
					<option value="zeny">Zeny
				</select>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="ro_rank" id="ro_rank" value="<?php _e('Search Player &raquo;'); ?>">
	</p>
</form>
<?php
		foreach ($charlist as $char) {
			$check_aid = $wpdb->get_row("SELECT * FROM login WHERE account_id = '$char->account_id'");
			$blno = $blno + 1;
			if ($check_aid->level == 0 && $blno < 101) {
				if ($char->online == 1) { $charstats = '<font color="green">Online</font>'; }
				else { $charstats = '<font color="red">Offline</font>'; }
				$guild_data = $wpdb->get_row("SELECT * FROM guild WHERE guild_id = '$char->guild_id'");
				$emblems = $guild_data->emblem_data;
				if ($emblems) {
					$guild_emblem = '<img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Emblem.php?emblems='.$emblems.'" alt="Emblem" title="Emblem" />';
				}
				echo '
					<h2>'.$blno.'. ' . $char->name . ' - '.$charstats.'</h2>
					<table align="center" width="100%">
						<tr>
							<th rowspan="4" width="20%"><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/' . $check_aid->sex . '/' . $char->class . '.gif" alt="'.$jobname[$char->class].'" title="'.$jobname[$char->class].'" /></th>
							<th width="20%">Char Name</th><td>:</td><td width="20%">' . $char->name . ' ('.$char->char_id.')</td>
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
							<th>Char Zeny</th><td>:</td><td>' . $char->zeny . '</td>
							<th>Guild Name</th><td>:</td><td>'.$guild_emblem.' <a href="' . get_option('siteurl') . '/wp-admin/users.php?page=Ro-Guild.php&detail='.$guild_data->guild_id.'">'.$guild_data->name.'</a></td>
						</tr>
					</table>
				';
			}
			$guild_emblem = '';
		}
	}
?>
	</div>
<?php
}

?>