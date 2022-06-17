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
		'group_label' => LNG('nav.cate.functions')
	],
	[
		'id' => 'classroom-dk',
		'icon' => 'done',
		'label' => LNG('nav.item.classroom'),
		// 'selected' => true,
	],
	[
		'id' => 'tradition',
		'icon' => 'date_range',
		'label' => LNG('nav.item.tradition'),
		'hidden_target' => 'run-dk',
		'selected' => true
	],
	[
		'id' => 'run-dk',
		'icon' => 'directions_run',
		'label' => LNG('nav.item.run'),
		'hidden' => true,
		// 'selected' => true
	],
	[
		'group_label' => LNG('nav.cate.system')
	],
	[
		'id' => 'status',
		'icon' => 'settings',
		'label' => LNG('nav.item.status')
	],
	[
		'id' => 'logs',
		'icon' => 'bug_report',
		'label' => LNG('nav.item.logs')
	],
	[
		'group_label' => LNG('nav.cate.about')
	],
	[
		'id' => 'about',
		'icon' => 'info_outline',
		'label' => LNG('nav.item.about')
	]
]) ?>

<div class="mdui-container singin-page-container singin-page-fluid" data-singin-page="classroom-dk">
	<div class="singin-classroom-loading mdui-typo">
		<h2><?php LNGe('cls.loadinglesson') ?></h2>
		<p>
			<div class="mdui-progress">
				<div class="mdui-progress-indeterminate"></div>
			</div>
		</p>
	</div>
    <div class="singin-classroom-empty mdui-typo" style="display: none;">
        <h2><?php LNGe('cls.nolesson') ?></h2>
		<p><?php LNGe('cls.nolesson.1') ?></p>
		<p><?php LNGe('cls.nolesson.2') ?></p>
    </div>
    <div class="singin-classroom-active mdui-typo" style="display: none;">
        <h2 class="mdui-m-b-3"><?php LNGe('cls.currlesson') ?>（<span class="singin-lesson-name">undefined</span>）</h2>
		<p>
			<?php LNGe('cls.filter') ?>
			<select class="mdui-select singin-classroom-filter" mdui-select>
				<option value="both"><?php LNGe('cls.filter.both') ?></option>
				<option value="no"><?php LNGe('cls.filter.no') ?></option>
				<option value="yes"><?php LNGe('cls.filter.yes') ?></option>
			</select>
		</p>
        <div style="margin-bottom: 160px;" class="mdui-row singin-student-list">
            <div class="mdui-col-xs-12"><?php LNGe('cls.nostudent') ?></div>
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
	<div class="mdui-dialog-title"><?php LNGe('cls.submit.title') ?></div>
	<div class="mdui-dialog-content">
		<?php LNGe('cls.submit.progress') ?><span class="singin-classroom-send-process-x">2</span>/<span class="singin-classroom-send-process-y">100</span><br />
		<?php LNGe('cls.submit.currname') ?><span class="singin-classroom-send-name"></span><br />
		<p>
			<div class="mdui-progress">
				<div class="mdui-progress-determinate singin-classroom-send-process-d" style="width: 0%;"></div>
			</div>
		</p>
		<?php LNGe('cls.submit.dontclose') ?>
	</div>
	<div class="mdui-dialog-actions">
		<button class="mdui-btn mdui-ripple singin-classroom-send-break"><?php LNGe('ui.abort') ?></button>
	</div>
</div>

<div class="mdui-container singin-page-container" data-singin-page="tradition">
	<div class="mdui-row">
		<div class="mdui-col-xs-12 mdui-typo">
			<div class="singin-tradition-loading">
				<h2><?php LNGe('tradition.loading') ?></h2>
				<p>
					<div class="mdui-progress">
						<div class="mdui-progress-indeterminate"></div>
					</div>
				</p>
			</div>
			<div class="singin-tradition-fail">
				<h2><?php LNGe('tradition.fail') ?></h2>
				<p>
					<?php echo LNG('tradition.fail.tips') ?>
				</p>
				<p>
					<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent singin-tradition-reload"><?php LNGe('tradition.reload') ?></button>
				</p>
			</div>
			<div class="singin-tradition-content" style="display: none;">
				<p style="text-align:right">
					<span style="text-align:left">
						<select class="mdui-select singin-tradition-class"></select>
					</span>
				</p>
				<div class="singin-tradition-data" style="display:none;">
					<p class="singin-tradition-date"></p>
					<div class="singin-tradition-attendants"></div>
					<h3 style="text-align:center" class="singin-tradition-countdown"><?php LNGe('tradition.remain.years', 'NaN') ?></h3>
					<div class="singin-tradition-next-sec">
						<hr class="mdui-m-t-5" />
						<h3 class="mdui-m-t-3"><?php LNGe('tradition.next') ?></h3>
						<table class="mdui-table singin-tradition-nexts">
							<tr>
								<th><?php LNGe('tradition.nexts.name') ?></th>
								<th><?php LNGe('tradition.nexts.date') ?></th>
							</tr>
						</table>
						<p style="text-align: right;">
							<button class="mdui-btn mdui-ripple singin-tradition-show-more">
								<?php LNGe('tradition.show_more') ?>
							</button>
						</p>
					</div>
				</div>
				<div class="singin-tradition-data-loading">
					<div class="mdui-progress">
						<div class="mdui-progress-indeterminate"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="mdui-container singin-page-container" data-singin-page="run-dk">
	<div class="mdui-row">
		<div class="mdui-col-xs-12 mdui-typo">
	        <?php LNGe('ui.developing') ?>
	    </div>
	</div>
</div>

<div class="mdui-container singin-page-container" data-singin-page="status">
	<div class="mdui-row">
		<div class="mdui-col-xs-12 mdui-typo">
			<h2><?php LNGe('status.title') ?></h2>
			<div class="mdui-table-fluid">
				<table class="mdui-table singin-status">
					<tr>
						<th><?php LNGe('status.key') ?></th>
						<th><?php LNGe('status.val') ?></th>
					</tr>
				</table>
			</div>
			<p>
				<!--<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent singin-status-refresh">刷新</button>&nbsp;-->
				<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent singin-stop-terminal">
					<?php LNGe('status.term.stop') ?>
				</button>&nbsp;
				<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent singin-start-terminal">
					<?php LNGe('status.term.start') ?>
				</button>&nbsp;
				<button class="mdui-btn mdui-btn-raised mdui-color-deep-orange-a400 singin-reboot">
					<?php LNGe('status.reboot') ?>
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
					<label class="mdui-textfield-label"><?php LNGe('log.date') ?></label>
					<input class="mdui-textfield-input singin-log-date" style="width: calc(100% - 128px); display: inline-block; margin-right: 15.05px" type="text" data-singin-enter-target=".singin-log-query" />
					<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent singin-log-query mdui-float-right">
					<?php LNGe('log.query') ?>
					</button>
				</div>
			</div>
			<p>
				<pre style="border: none;user-select: text;"><code class="singin-log-display"><?php LNGe('log.empty') ?></code></pre>
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
				<?php LNGe('about.text') ?>
			</p>
			<p><?php LNGe('about.version') ?><span class="singin-about-version"></span></p>
			<p><?php LNGe('about.author') ?></p>
			<p><a href="https://github.com/HEZOpen-2020/singin2-punctuality" target="_blank">在 Github 上查看</a></p>
			<p>
				<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent" onclick="switchLanguage('zh_cn')">
					中文模式
				</button>&nbsp;
				<button class="mdui-btn mdui-btn-raised mdui-color-theme-accent" onclick="switchLanguage('en_us')">
					English Mode
				</button>&nbsp;
			</p>
			<p>
				<button class="mdui-btn mdui-btn-raised mdui-color-orange-a400" onclick="switchLanguage('ky_cd')">
					i18n.switch.empty
				</button>&nbsp;
			</p>
		</div>
	</div>
</div>

<?php
	tpl('common/js');
	load_js('js/app_common');
	load_js('js/app_console');
	load_js('js/app_console_classroom');
	load_js('js/app_console_tradition');
	load_js('js/app_console_hz2zrun');
	show_app_title('console');
	show_theme_color('indigo', 'pink');
?>
<script>$('.singin-about-name').text(G.app_name);$('.singin-about-version').text(G.version)</script>
<?php tpl('common/meta-foot'); ?>
