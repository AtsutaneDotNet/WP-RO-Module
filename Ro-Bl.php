<?php

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function bl_item() {
	global $wpdb;
?>
<div class="wrap">
	<h2>Blacklisted Database</h2>
<?php
	if ($_GET['detail']) {
		$blaid = $_GET['detail'];
		$bl_detail = $wpdb->get_results("SELECT * FROM `char` WHERE `char`.`account_id` = '$blaid'");
		foreach ($bl_detail as $bld) {
			echo $bld->name . '<br />';
		}
	}
	else {
		$blacklisted = $wpdb->get_results("SELECT * FROM login WHERE state > 1 OR ban_until != 0 ORDER BY lastlogin DESC");
		$total = count($blacklisted);
		$blno = 0;
		echo "<p>A total of <strong>$total</strong> account has been ban/block for various reason such as Botting, Disrespect other Players or GM's, Use a swear word to insult a player or GM, Harass other players or GMs and ETC.</p>";
?>
		<table width="100%" class="widefat"><tr class="thead"><th align="center">No.</th><th>Username</th><th>Sex</th><th align="center">Last Login</th><th align="center">Ban Untill</th></tr>
<?php
		foreach ($blacklisted as $bl) {
			$blno = $blno + 1;
			$bltime = $bl->ban_until;
			if ($bl->state == 5) { $blstats = '<font color="red">Permanent Ban</font>'; }
			else { $trcolor = ''; $blstats = date("d/m/y h:i:s A",$bltime); }
			echo '<tr><td align="center">' . $blno . '</td><td>' . $bl->userid . '</td><td align="center">' . $bl->sex . '</td><td align="center">' . $bl->lastlogin  . '</td><td align="center">' . $blstats . '</td></tr>';
		}
?>
		</table>
</div>
<?php
	}
}

?>