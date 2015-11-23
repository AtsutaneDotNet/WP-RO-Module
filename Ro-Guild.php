<?php

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function stand_guild() {
	global $wpdb, $jobname, $casname;
?>
<div class="wrap">
<?php
	if ($_GET['detail']) {
		$guild_id = $_GET['detail'];
		$guild_info = $wpdb->get_row("SELECT * FROM `guild` WHERE `guild_id` = '$guild_id'");
		if ($guild_info) {
			$emblems = $guild_info->emblem_data;
			if ($emblems) {
				$guild_emblem = '<img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Emblem.php?emblems='.$emblems.'" alt="Emblem" title="Emblem" />';
			}
			echo '
				<h2>'.$guild_emblem.' '.$guild_info->name.' - Guild Info</h2>
				<table align="center" width="100%">
					<tr>
						<th rowspan="3">'.$guild_emblem.'</th>
						<th width="20%">Guild Name</th><td>:</td><td width="20%">' . $guild_info->name . '</td>
						<th width="20%">Guild Level</th><td>:</td><td width="20%">' . $guild_info->guild_lv . '</td>
					</tr>
					<tr>
						<th>Guild Master</th><td>:</td><td>' . $guild_info->master . '</td>
						<th>Average Level</th><td>:</td><td>' . $guild_info->average_lv . '</td>
					</tr>
					<tr>
						<th>Max Member</th><td>:</td><td>' . $guild_info->max_member . '</td>
						<th>Guild Exp</th><td>:</td><td>' . $guild_info->exp . '</td>
					</tr>
				</table>
			';
			$guild_ally = $wpdb->get_results("SELECT * FROM `guild_alliance` WHERE `guild_id` = '$guild_id'");
			if ($guild_ally) {
				echo '
					<h2>'.$guild_emblem.' '.$guild_info->name.' - Guild Alliance</h2>
					<table width="100%" class="widefat"><tr class="thead"><th align="center" width="3%">No.</th><th colspan="2">Guild Name</th><th>Guild Master</th><th align="center">Guild Lvl</th><th align="center">Average Lvl</th></tr>
				';
				$blno = 0;
				foreach ($guild_ally as $ally) {
					$guild_name = $wpdb->get_row("SELECT * FROM `guild` WHERE `guild_id` = '$ally->alliance_id'");
					$blno = $blno + 1;
					$aemblems = $guild_name->emblem_data;
					if ($aemblems) {
						$guild_aemblem = '<img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Emblem.php?emblems='.$aemblems.'" alt="Emblem" title="Emblem" />';
					}
					echo '<tr><td align="center">' . $blno . '</td><td align="center" width="3%">'.$guild_aemblem.'</td><td><a href="' . $_SERVER['SCRIPT_NAME'] . '?page=Ro-Guild.php&detail='.$ally->alliance_id.'">' . $ally->name . '</a></td><td>' . $guild_name->master . '</td><td align="center">' . $guild_name->guild_lv . '</td><td align="center">' . $guild_name->average_lv . '</td></tr>';
					$guild_aemblem = '';
				}
				echo '</table>';
			}
			echo '
				<h2>'.$guild_emblem.' '.$guild_info->name.' - Guild Member</h2>
				<table width="100%" class="widefat"><tr class="thead"><th width="3%" align="center">No.</th><th>Char Name</th><th align="center">Class</th><th align="center">BLvl</th><th align="center">Position Name</th><th align="center">Status</th></tr>
			';
			$guild_member = $wpdb->get_results("SELECT * FROM `guild_member` WHERE `guild_id` = '$guild_id' ORDER BY `guild_member`.`position` ASC");
			$blno = 0;
			foreach ($guild_member as $g_mem) {
				$blno = $blno + 1;
				if ($g_mem->online == 1) { $charstats = '<font color="green">Online</font>'; }
				else { $charstats = '<font color="red">Offline</font>'; }
				$guild_pos = $wpdb->get_row("SELECT * FROM `guild_position` WHERE `guild_id` = '$guild_id' AND `position` = '$g_mem->position'");
				echo '<tr><td align="center">' . $blno . '</td><td>'.$g_mem->name.'</td><td align="center">' . $jobname[$g_mem->class] . '</td><td align="center">' . $g_mem->lv . '</td><td align="center">' . $guild_pos->name . '</td><td align="center">' . $charstats . '</td></tr>';
			}
			echo '</table>';
		}
		else { echo 'No Guild'; }
	}
	else {
?>
<h2>Castle Ranking</h2>
<table width="100%" class="widefat"><tr class="thead"><th>Castle Name</th><th colspan="2">Current Guild Name</th><th>Current Guild Master</th><th colspan="2">Last Guild Name</th><th>Last Guild Master</th></tr>
<?php
	$caslist = $wpdb->get_results("SELECT * FROM `guild_castle` ORDER BY `guild_castle`.`castle_id` ASC");
	foreach ($caslist as $cl) {
		$guild_name = $wpdb->get_row("SELECT * FROM `guild` WHERE `guild_id` = '$cl->guild_id'");
		$emblems = $guild_name->emblem_data;
		if ($emblems) {
			$guild_emblem = '<img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Emblem.php?emblems='.$emblems.'" alt="Emblem" title="Emblem" />';
		}
		echo '<tr><td>' . $casname[$cl->castle_id] . '</td><td align="center" width="3%">'.$guild_emblem.'</td><td><a href="' . $_SERVER['SCRIPT_NAME'] . '?page=Ro-Guild.php&detail='.$guild_name->guild_id.'">' . $guild_name->name . '</a></td><td>' . $guild_name->master . '</td><td></td><td></td><td></td></tr>';
		$guild_emblem = '';
	}
	echo '</table>';
?>
<h2>Guilds Ranking</h2>
<table width="100%" class="widefat"><tr class="thead"><th align="center">No.</th><th colspan="2">Guild Name</th><th>Guild Master</th><th align="center">Guild Lvl</th><th align="center">Average Lvl</th><th align="center">Guild Member</th></tr>
<?php
	$guildlist = $wpdb->get_results("SELECT * FROM `guild` WHERE `guild_id` !=1 ORDER BY `guild`.`exp` DESC LIMIT 0 , 100");
	$blno = 0;
	foreach ($guildlist as $gl) {
		$count_member = $wpdb->get_results("SELECT * FROM `guild_member` WHERE `guild_id` = '$gl->guild_id'");
		$blno = $blno + 1;
		$emblems = $gl->emblem_data;
		if ($emblems) {
			$guild_emblem = '<img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Emblem.php?emblems='.$emblems.'" alt="Emblem" title="Emblem" />';
		}
		echo '<tr><td align="center">' . $blno . '</td><td align="center" width="3%">'.$guild_emblem.'</td><td><a href="' . $_SERVER['SCRIPT_NAME'] . '?page=Ro-Guild.php&detail='.$gl->guild_id.'">' . $gl->name . '</a></td><td>' . $gl->master . '</td><td align="center">' . $gl->guild_lv . '</td><td align="center">' . $gl->average_lv . '</td><td align="center">'.count($count_member).'</td></tr>';
		$guild_emblem = '';
	}
?>
</table>
<?php
	}
?>
</div>
<?php
}

?>