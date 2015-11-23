<?php

function server_stats() {
	global $wpdb, $job_data, $jobname;
?>
<div class="wrap">
	<h2>Server Statistic</h2>
<?php
	$char_data = $wpdb->get_results("SELECT * FROM `char` GROUP BY `char`.`class`");
	if ($char_data) {
		foreach ($char_data as $char) {
			$char_count = $wpdb->get_results("SELECT * FROM `char` WHERE `char`.`class` = '$char->class'");
			//$job_data[$char->class] = count($char_count);
			$job_stats = $job_stats.'
					<tr valign="top">
						<th align="right" scope="row">'.$jobname[$char->class].':</th><td>'.count($char_count).' Characters</td>
					</tr>
			';
			$total_char = $total_char + count($char_count);
			foreach ($char_count as $char_count) {
				$zeny_data = $zeny_data + $char_count->zeny;
			}
		}
	}
	$account_data = $wpdb->get_results("SELECT * FROM `login` WHERE `sex` != 'S'");
	foreach ($account_data as $adata) {
		if ($adata->sex == 'M') { $male = $male + 1; }
		else { $female = $female + 1; }
	}
	echo '
		<table width="100%" class="optiontable">
			<tr valign="top">
				<th align="right" scope="row">Total Account:</th><td>'.count($account_data).' Accounts</td>
			</tr>
			<tr valign="top">
				<th align="right" scope="row">Total Male:</th><td>'.$male.' Players</td>
			</tr>
			<tr valign="top">
				<th align="right" scope="row">Total Female:</th><td>'.$female.' Players</td>
			</tr>
			<tr valign="top">
				<th align="right" scope="row">Total Character:</th><td>'.$total_char.' Characters</td>
			</tr>
			<tr valign="top">
				<th align="right" scope="row">Total Zeny:</th><td>'.$zeny_data.' Zenys</td>
			</tr>
		</table>
		<h2>Character Statistic</h2>
		<table width="100%" class="optiontable">
		'.$job_stats.'
		</table>
	';
?>
</div>
<?php
}

?>
