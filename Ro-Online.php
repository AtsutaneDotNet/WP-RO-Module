<?php

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function online_data() {
	global $wpdb, $jobname;
	if ($_GET['map']) {
		$lastmap = $_GET['map'];
		$charlist = $wpdb->get_results("SELECT * FROM `char` WHERE `char`.`online` = 1 AND `char`.`last_map` = '$lastmap' ORDER BY `char`.`last_map` DESC");
	}
	else {
		$charlist = $wpdb->get_results("SELECT * FROM `char` WHERE `char`.`online` = 1 ORDER BY `char`.`last_map` DESC");
	}
	$total = count($charlist);
	$blno = 0;
?>
<div class="wrap">
<h2>Who Is Online</h2>
<p>A total of <strong><?php echo $total; ?></strong> player currently online right now.</p>
<table width="100%" class="widefat"><tr class="thead"><th align="center" width="3%">No.</th><th>Char Name</th><th colspan="2">Guild</th><th align="center">Class</th><th align="center">BLvl</th><th align="center">JLvl</th><th align="center">Location</th></tr>
<?php
	foreach ($charlist as $char) {
		$get_charlvl = $wpdb->get_row("SELECT * FROM login WHERE account_id = '$char->account_id'");
		if ($get_charlvl->level == 0) {
			$ip_table[$get_charlvl->last_ip] = $char->name;
			$blno = $blno + 1;
			$guild_data = $wpdb->get_row("SELECT * FROM guild WHERE guild_id = '$char->guild_id'");
			$emblems = $guild_data->emblem_data;
			if ($emblems) {
				$guild_emblem = '<img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/Ro-Emblem.php?emblems='.$emblems.'" alt="Emblem" title="Emblem" />';
			}
			echo '<tr><td align="center">' . $blno . '</td><td>' . $char->name . ' <!-- ID: ' . $get_charlvl->userid . ', IP: ' . $get_charlvl->last_ip . ' --></td><td align="center" width="3%">'.$guild_emblem.'</td><td><a href="' . $_SERVER['SCRIPT_NAME'] . '?page=Ro-Guild.php&detail='.$guild_data->guild_id.'">'.$guild_data->name.'</a></td><td align="center">' . $jobname[$char->class] . '</td><td align="center">' . $char->base_level . '</td><td align="center">' . $char->job_level . '</td>';
?>
	<td align="center"><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=Ro-Online.php&map=<?php echo $char->last_map ?>"><?php echo $char->last_map ?></a></td></tr>
<?php
		}
		$guild_emblem = '';
	}
?>
</table>
</div>
<?php
}

?>