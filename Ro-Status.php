<?php

function get_server_status() {
	global $wpdb;
	if (get_option('ro-status') == '') {
		$server_stats = array(
			'rologin' => '<font color="red">OFFLINE</font>',
			'rochar' => '<font color="red">OFFLINE</font>',
			'romap' => '<font color="red">OFFLINE</font>',
			'rouser' => 0,
		);
		update_option('ro-status', $server_stats);
	}
	$get_opt = get_option('ro-status');
	$serv_opt = get_option('ro_panel_opt');
	$server_name = $serv_opt['server_name'];
	$diff = time() - $get_opt['rostats_time'];
	if ($serv_opt['server_mode']) {
		$login_server = '<font color="red">Service</font>';
		$char_server = '<font color="red">Service</font>';
		$map_server = '<font color="red">Service</font>';
		$user_online = '<font color="red">Service</font>';
	} else {
		if ($diff > 300) {
			$on9char = $wpdb->get_results("SELECT * FROM `char` WHERE `char`.`online` = '1'");
			$user_online = count($on9char);
			$acc = @fsockopen ('localhost', '6900', $errno, $errstr, 1);
			$char = @fsockopen ('localhost', '6121', $errno, $errstr, 1);
			$map = @fsockopen ('localhost', '5121', $errno, $errstr, 1);
			if ($acc) { $login_server = '<font color="green">ONLINE</font>'; }
			else { $login_server = '<font color="red">OFFLINE</font>'; }
			if ($char) { $char_server = '<font color="green">ONLINE</font>'; }
			else { $char_server = '<font color="red">OFFLINE</font>'; }
			if ($map) { $map_server = '<font color="green">ONLINE</font>'; }
			else { $map_server = '<font color="red">OFFLINE</font>'; }
			$server_stats = array(
				'rologin' => $login_server,
				'rochar' => $char_server,
				'romap' => $map_server,
				'rouser' => $user_online,
				'rostats_time' => time(),
			);
			update_option('ro-status', $server_stats);
		} else {
			$login_server = $get_opt['rologin'];
			$char_server = $get_opt['rochar'];
			$map_server = $get_opt['romap'];
			$user_online = $get_opt['rouser'];
		}
	}
	show_server_status($server_name, $login_server, $char_server, $map_server, $user_online, $diff);
}

function show_server_status($server_name, $login_server, $char_server, $map_server, $user_online, $diff) {
?>
<br />
<p class="description">
	<img src="<?php bloginfo('template_url'); ?>/images/Kafra_3.gif" class="kafra" alt="kafra" title="kafra" />
	<div class="alert">
		<table>
			<tr>
				<th colspan="3"><?php echo $server_name; ?></th>
			</tr>
			<tr>
				<th>Login Server</th><td>:</td><td><?php echo $login_server; ?></td>
			</tr>
			<tr>
				<th>Char Server</th><td>:</td><td><?php echo $char_server; ?></td>
			</tr>
			<tr>
				<th>Map Server</th><td>:</td><td><?php echo $map_server; ?></td>
			</tr>
			<tr>
				<th>User Online</th><td>:</td><td><?php echo $user_online; ?></td>
			</tr>
		</table>
		<!-- <?php echo $diff; ?> Seconds -->
	</div>
</p>
<?php
}

?>