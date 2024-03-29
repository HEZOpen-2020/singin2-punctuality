<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

/**
 * 获取所有应用信息
 */
function app_get_all() {
	return json5_decode(file_get_contents(LIB . 'app_info.json5'), true);
}

/**
 * 获取应用信息
 */
function app_get($id) {
	foreach(app_get_all() as &$v) {
		if($v['id'] == $id) {
			return $v;
		}
	}
}

/**
 * 页面显示应用标题和图标
 */
function show_app_title($id) {
	show_icon(BASIC_URL . 'static/ico/app_' . $id . '.png');
	show_title(LNG(app_get($id)['label']));
	echo '<script>G.app_id = "' . jsspecial($id) . '";</script>' . "\n";
	echo '<script>G.app_instance_id = random_ele_id();</script>' . "\n";
}

/**
 * 应用页页头
 */
function app_header($id, $buttons = []) {
	?>
	<div class="mdui-appbar mdui-appbar-fixed">
		<div class="mdui-toolbar mdui-color-theme-600 singin-appbar">
			<a href="javascript:;" mdui-drawer="{target: '.singin-drawer'}" class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white singin-appbar-menu">
				<i class="mdui-icon material-icons">menu</i>
			</a>
			<a href="<?php echo BASIC_URL ?>" class="mdui-typo-headline mdui-hidden-xs-down"><?php echo APP_NAME ?></a>
			<span class="singin-appbar-delimiter mdui-hidden-xs-down">/</span>
			<a href="javascript:;" class="mdui-typo-title"><?php echo LNG(app_get($id)['label']) ?></a>
			<div class="mdui-toolbar-spacer"></div>
			<?php
				foreach($buttons as $btn) {
					?>
					<a href="javascript:;" class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white singin-appbar-btn-<?php echo $btn['class'] ?>">
						<i class="mdui-icon material-icons"><?php echo $btn['icon'] ?></i>
					</a>
					<?php
				}
			?>
		</div>
	</div>
	<?php
}

/**
 * 应用页导航菜单
 */
function app_nav($id, $pages) {
	?>
	<div class="mdui-drawer singin-drawer" id="drawer">
		<ul class="mdui-list">
			<?php
				foreach($pages as $page) {
					if(isset($page['group_label'])) {
						?>
						<li class="mdui-subheader"><?php echo htmlspecial($page['group_label']) ?></li>
						<?php
					} else {
						?>
						<li class="mdui-list-item mdui-ripple mdui-page-select singin-page-select <?php if($page['selected'] ?? false) echo 'mdui-list-item-active mdui-color-theme-100 mdui-text-color-theme-900' ?>" data-singin-page="<?php echo htmlspecial($page['id']) ?>" <?php if($page['selected'] ?? false) echo 'data-singin-page-selected' ?> <?php if($page['hidden'] ?? false) echo 'data-singin-page-hidden style="display: none;"' ?> <?php if($page['hidden_target'] ?? null) echo 'data-singin-hidden-target="' . htmlspecial($page['hidden_target']) . '"' ?>>
							<i class="mdui-list-item-icon mdui-icon material-icons"><?php echo htmlspecial($page['icon']) ?></i>
							<div class="mdui-list-item-content"><?php echo htmlspecial($page['label']) ?></div>
						</li>
						<?php
					}
				}
			?>
		</ul>
	</div>
	<?php
}
