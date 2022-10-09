<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link id="page-icon" rel="shortcut icon" href="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php LNGe('ui.loading') ?> - <?php echo htmlspecial(APP_NAME) ?></title>
	<?php
		load_css('mdui/css/mdui');
		load_css('css/common');
		load_css('font/skf/part-1');
		load_js_e(BASIC_URL . 'i18n?v=' . VERSION);
	?>
	<script>
		<?php
			$public_values = [];
			$public_keys = _C_public_values();
			foreach($public_keys as $key) {
				$public_values[$key] = _C($key);
			}
		?>
		var G = <?php echo html_json_encode([
			'basic_url' => BASIC_URL,
			'version' => VERSION,
			'app_name' => APP_NAME,
			'app_prefix' => APP_PREFIX,
			'app_info' => app_get_all(),
			'csrf' => [
				'sess' => $GLOBALS['sess'],
				'token' => $GLOBALS['token']
			],
			'public_config' => $public_values
		]) ?>;
	</script>
</head>
