<?php

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function char_inventory() {
	global $wpdb, $userdata, $itemtype;
?>
	<div class="wrap">
		<h2>Manage Inventory</h2>
		<p>You can only view character that are not online.</p>
<?php
	if ($_POST['del_item']) {
		check_admin_referer('rocp-inventory_delete');
		$data_count = $_POST['data_count'];
		$n_data = 0;
		while ($n_data < $data_count) {
			$n_data = $n_data + 1;
			if ($_POST['invent'.$n_data]) {
				$invent_post_data = $_POST['invent'.$n_data];
				$data_id = $_POST['data_cid'];
				$get_itemname = $wpdb->get_row("SELECT * FROM `item_db` WHERE `id` = '$invent_post_data'");
				$del_item = $wpdb->query("DELETE FROM `inventory` WHERE `inventory`.`nameid` = '$invent_post_data' AND `inventory`.`char_id` = '$data_id' LIMIT 1");
				echo "<div id='message' class='updated fade'><p><strong>".$get_itemname->name_japanese."</strong> (".$invent_post_data.") has been delete from ".$_POST['data_name']." inventory ".$del_item."</p></div>";
			}
		}
	}
	if ($userdata) {
		$check_aid = $wpdb->get_row("SELECT * FROM login WHERE userid = '$userdata->user_login'");
		if ($check_aid) {
			$check_char = $wpdb->get_results("SELECT * FROM `char` WHERE `account_id` = '$check_aid->account_id' AND `online` = '0'");
			if ($check_char) {
?>
	<form method="POST"><ul style="list-style:none;">
<?php
				if ( function_exists('wp_nonce_field') ) {
					wp_nonce_field('rocp-inventory_show');
				}
				foreach ($check_char as $char) {
					echo '
						<input type="hidden" name="' . md5($char->name) . '" value="'.$char->char_id.'" />
						<li><label><input name="charinven" type="radio" value="' . $char->name . '" tabindex="90" /> ' . $char->name . ' Inventory</label></li>
					';
				}
			}
?>
		<p class="submit">
			<input type="submit" name="char_inven" id="char_inven" value="<?php _e('Manage Inventory &raquo;'); ?>">
		</p>
	</ul></form>
<?php
		}
		if ($_POST['charinven']) {
			check_admin_referer('rocp-inventory_show');
			$invent_charid = $_POST[md5($_POST['charinven'])];
			echo '<h2>'.$_POST['charinven'].' Inventory</h2>';
			$check_invent = $wpdb->get_results("SELECT * FROM `inventory` WHERE `char_id` = '$invent_charid' AND `equip` = 0");
			if ($check_invent) {
?>
<form method="POST">
<?php
	if ( function_exists('wp_nonce_field') ) {
		wp_nonce_field('rocp-inventory_delete');
	}
?>
	<p class="submit">
		<input type="submit" name="del_item" id="del_item" value="<?php _e('Delete Item &raquo;'); ?>">
	</p>
	<table width="100%" class="widefat">
		<tr class="thead">
			<th width="6%">ID</th>
			<th colspan="2">Item Name</th>
			<th width="5%">Type</th>
			<th width="5%">Amount</th>
		</tr>
<?php
				$item_count = 0;
				foreach ($check_invent as $invent) {
					$get_itemname = $wpdb->get_row("SELECT * FROM `item_db` WHERE `id` = '$invent->nameid'");
					$item_count = $item_count + 1;
					if ($get_itemname->slots != 0) { $slots = '['.$get_itemname->slots.']'; }
					else { $slots = ''; }
					echo '
						<tr>
							<td><label><input name="invent'.$item_count.'" type="checkbox" id="invent" value="'.$invent->nameid.'" /> '.$get_itemname->id.'</label></td>
							<td align="center" width="3%"><img src="' . get_option('siteurl') . '/wp-content/plugins/Ro-Module/images/item/'.$get_itemname->id.'.gif" alt="'.$get_itemname->id.'" title="'.$get_itemname->name_japanese.'" /></td>
							<td>'.$get_itemname->name_japanese.' '.$slots.'</td>
							<td>'.$itemtype[$get_itemname->type].'</td>
							<td align="center">'.$invent->amount.'</td>
						</tr>
					';
				}
?>
	</table>
	<input type="hidden" name="data_count" value="<?php echo $item_count; ?>" />
	<input type="hidden" name="data_cid" value="<?php echo $invent_charid; ?>" />
	<input type="hidden" name="data_name" value="<?php echo $_POST['charinven']; ?>" />
	<p class="submit">
		<input type="submit" name="del_item" id="del_item" value="<?php _e('Delete Item &raquo;'); ?>">
	</p>
</form>
<?php
			}
		}
	}
?>
	</div>
<?php
}

?>