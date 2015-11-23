<?php

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function ro_log_read() {
	global $wpdb, $logtype;
	echo '<div class="wrap">';
	if ($_GET['data'] || $_GET['date']) {
		$char_id = $_GET['data'];
		if ($char_id) { $query_log = "`char_id` = '$char_id' AND"; }
		$data_log = $_GET['date'] . '%';
		$check_char = $wpdb->get_row("SELECT * FROM `char` WHERE `char_id` = '$char_id'");
		$check_log = $wpdb->get_results("SELECT * FROM `picklog` WHERE ".$query_log." `time` LIKE '$data_log' ORDER BY `picklog`.`time` DESC LIMIT 0 , 200");
		echo '<h2>'.$check_char->name.' - '.count($check_log).'</h2>';
		if ($check_log) {
			echo '
				<table width="100%" class="widefat">
					<tr class="thead">
						<th width="15%">Date/Time</th>
						<th width="5%">Type</th>
						<th colspan="2">Item Name</th>
						<th width="5%">Amount</th>
						<th width="5%">Location</th>
					</tr>
			';
			foreach ($check_log as $loggy) {
				$get_itemname = $wpdb->get_row("SELECT * FROM `item_db` WHERE `id` = '$loggy->nameid'");
				$get_charname = $wpdb->get_row("SELECT * FROM `char` WHERE `char_id` = '$loggy->char_id'");
				echo '
					<tr>
						<td>'.$loggy->time.'</td>
						<td>'.$logtype[$loggy->type].'</td>
						<td align="center" width="3%"><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/item/'.$get_itemname->id.'.gif" alt="'.$get_itemname->id.'" title="'.$get_itemname->name_japanese.'" /></td>
						<td>'.$get_itemname->name_japanese.' '.$slots.' '.$get_charname->name.'</td>
						<td align="center">'.$loggy->amount.'</td>
						<td>'.$loggy->map.'</td>
					</tr>
				';
			}
			echo '</table>';
		}
	}
	echo '</div>';
}

$logtype = array(
'P' => "Drop/Picked",
'V' => "Vending",
'T' => "Trades",
);

?>