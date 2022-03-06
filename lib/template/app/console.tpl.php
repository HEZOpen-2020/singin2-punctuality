<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php
	tpl('common/meta-head-app');
?>
<?php
	load_css('css/app_common');
	load_css('css/app_console');
?>
<?php app_header('console') ?>
<?php app_nav('console', [
	[
		'group_label' => '功能'
	],
	[
		'id' => 'classroom-dk',
		'icon' => 'done',
		'label' => '教室考勤',
		'selected' => true
	],
	// [
	// 	'id' => 'run-dk',
	// 	'icon' => 'directions_run',
	// 	'label' => '晨跑考勤',
	// ],
	[
		'group_label' => '系统'
	],
	[
		'id' => 'status',
		'icon' => 'settings',
		'label' => '系统状态',
	],
	[
		'id' => 'logs',
		'icon' => 'bug_report',
		'label' => '日志数据',
	],
	[
		'group_label' => '关于'
	],
	[
		'id' => 'about',
		'icon' => 'info_outline',
		'label' => '关于此项目'
	]
]) ?>

<div class="mdui-container singin-page-container singin-page-fluid" data-singin-page="classroom-dk">
    <div class="singin-classroom-empty mdui-typo">
        <h2>没有正在考勤的课程</h2>
		<p>请在考勤开始后检查此页面。</p>
		<p>若检测存在问题，请检查服务端配置和时区设置。</p>
    </div>
    <div class="singin-classroom-active mdui-typo" style="display: none;">
        <h2 class="mdui-m-b-3">当前课程（<span class="singin-lesson-name">undefined</span>）</h2>
        <div style="margin-bottom: 160px;" class="mdui-row singin-student-list">
            <div class="mdui-col-xs-12">未发现任何考勤人员</div>
        </div>
		<div class="mdui-fab-wrapper" mdui-fab>
			<button class="mdui-fab mdui-ripple mdui-color-theme-accent">
				<i class="mdui-icon material-icons">add</i>

				<i class="mdui-icon mdui-fab-opened material-icons">clear</i>
			</button>
			<div class="mdui-fab-dial">
				<button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-red singin-classroom-action-clear">
					<i class="mdui-icon material-icons">clear_all</i>
				</button>
				<button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-green singin-classroom-action-markall">
					<i class="mdui-icon material-icons">done_all</i>
				</button>
				<button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-orange singin-classroom-action-send">
					<i class="mdui-icon material-icons">send</i>
				</button>
				<button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-indigo singin-classroom-action-refresh">
					<i class="mdui-icon material-icons">sync</i>
				</button>
			</div>
		</div>
    </div>
</div>

<div class="mdui-dialog singin-classroom-send-dialog">
	<div class="mdui-dialog-title">正在提交更改</div>
	<div class="mdui-dialog-content">
		[教室考勤] 正在提交更改... <span class="singin-classroom-send-process-x">2</span>/<span class="singin-classroom-send-process-y">100</span><br />
		当前姓名：<span class="singin-classroom-send-name">班有荣</span><br />
		请勿关闭页面，这将导致操作中断。
	</div>
	<div class="mdui-dialog-actions">
		<button class="mdui-btn mdui-ripple singin-classroom-send-break">中止</button>
	</div>
</div>

<div class="mdui-container singin-page-container" data-singin-page="status">
	<div class="mdui-row">
		<div class="mdui-col-xs-12 mdui-typo">
			<h2>系统状态</h2>
			<div class="mdui-table-fluid">
				<table class="mdui-table singin-status">
					<tr>
						<th>项目</th>
						<th>值</th>
					</tr>
				</table>
			</div>
			<p>
				<!--<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent singin-status-refresh">刷新</button>&nbsp;-->
				<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent singin-stop-terminal">
					关掉终端程序
				</button>&nbsp;
				<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent singin-start-terminal">
					打开终端程序
				</button>&nbsp;
				<button class="mdui-btn mdui-btn-raised mdui-color-deep-orange-a400 singin-reboot">
					爆破服务器
				</button>&nbsp;
			</p>
		</div>
	</div>
</div>

<div class="mdui-container singin-page-container" data-singin-page="logs">
	<div class="mdui-row">
		<div class="mdui-col-xs-12 mdui-typo">
			<div class="mdui-shadow-8 singin-top-ops">
				<div class="mdui-textfield">
					<label class="mdui-textfield-label">日志日期</label>
					<input class="mdui-textfield-input singin-log-date" style="width: calc(100% - 106px); display: inline-block; margin-right: 15.05px" type="text" data-singin-enter-target=".singin-log-query" />
					<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent singin-log-query mdui-float-right">
						查询日志
					</button>
				</div>
			</div>
			<p>
				<pre style="border: none;user-select: text;"><code class="singin-log-display">请点击上方按钮查询</code></pre>
			</p>
		</div>
	</div>
</div>

<div class="mdui-container singin-page-container" data-singin-page="about">
	<div class="mdui-row">
		<div class="mdui-col-xs-12 mdui-typo">
			<h1 class="singin-about-name">undefined</h1>
			<p style="opacity: .6;margin-top:-12px;">singin2-punctuality</p>
			<p>
				基于 PHP/Apache 的 Web 应用程序，用于同步查看和管理智能班牌上的考勤信息。
			</p>
			<p>版本：<span class="singin-about-version"></span></p>
			<p>祈雨project · Singin2 项目组出品</p>
			<p><a href="https://github.com/yezhiyi9670/singin2-punctuality" target="_blank">在 Github 上查看</a></p>
		</div>
	</div>
</div>

<?php
	tpl('common/js');
	load_js('js/app_common');
	load_js('js/app_console');
	load_js('js/app_console_classroom');
	show_app_title('console');
	show_theme_color('blue-grey', 'blue');
?>
<script>$('.singin-about-name').text(G.app_name);$('.singin-about-version').text(G.version)</script>
<?php tpl('common/meta-foot'); ?>
