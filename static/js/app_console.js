// System status loading
$(async () => {
	G.system_status = null;
	var reload_system_status_t = '';
	window.reload_system_status = async (firstState) => {
		$('.singin-status-refresh').disabled(true);
		var rid = reload_system_status_t = random_ele_id();
		var data = await fetch_json(G.basic_url + 'api/system/status');
		if(rid != reload_system_status_t) return;
		if(data && data.success) {
			G.system_status = data.data;

			$('.singin-status .singin-status-item').remove();
			var add_status_line = function(name, key, map = (x) => x) {
				var text = G.system_status[key];
				if(text === true || text === false) {
					text = (text ? '是' : '否');
				}
				text = map(text);
				$('.singin-status').append(
					$('<tr></tr>')
					.addClass('singin-status-item')
					.append($('<td></td>').text(name))
					.append($('<td></td>').addClass('singin-status-value').text(text))
				);
			};
			add_status_line('服务端系统', 'server_os');
			add_status_line('PHP 版本', 'php_version');
			add_status_line('命名空间', 'namespace');
			add_status_line('服务端口', 'server_port');
			add_status_line('服务版本', 'version');
			add_status_line('Web 根目录', 'document_root');
			add_status_line('目标数据库', 'db_path');
			add_status_line('正在考勤的课程', 'singing_lesson');
			add_status_line('终端进程名', 'proc_name');
			add_status_line('终端程序', 'proc_path');
			add_status_line('终端程序运行中', 'terminal_running');
			add_status_line('桌面启动器运行中', 'desklaunch_daemon');
			add_status_line('刷新时间', 'system_time', (x) => date_format(new Date(x * 1000)));

			// $('.singin-stop-terminal, .singin-start-terminal').disabled(!G.system_status['desklaunch_daemon']);
			if(firstState) {
			    $(document).trigger('data_statusinit');
			}
		} else {
			if(document.visibilityState == 'visible') mdui.snackbar('系统状态获取失败！', {timeout: 500});
		}
		$('.singin-status-refresh').disabled(false);
	};

	var stop_terminal_t = '';
	window.stop_terminal = async () => {
		$('.singin-stop-terminal, .singin-start-terminal').disabled(true);
		var rid = stop_terminal_t = random_ele_id();
		var data = await fetch_json(G.basic_url + 'api/system/terminal/stop');
		if(data === null) {
		    mdui.snackbar('异常无法操作。', {timeout: 2000});
		    return;
		}
		if(data.code == 200) {
			mdui.snackbar('已关闭终端程序。', {timeout: 2000});
		} else if(data.code == 201) {
			mdui.snackbar('终端程序不在运行。', {timeout: 2000});
		} else if(data.code == 500) {
			mdui.snackbar('桌面启动器不在运行，因而无法操作。', {timeout: 2000});
		}
		if(rid != stop_terminal_t) return;
		$('.singin-stop-terminal, .singin-start-terminal').disabled(false);
		reload_system_status();
	};
	$('.singin-stop-terminal').on('click', () => stop_terminal());
	window.start_terminal = async () => {
		$('.singin-start-terminal, .singin-stop-terminal').disabled(true);
		var rid = stop_terminal_t = random_ele_id();
		var data = await fetch_json(G.basic_url + 'api/system/terminal/start');
		if(data === null) {
		    mdui.snackbar('异常无法操作。', {timeout: 2000});
		    return;
		}
		if(data.code == 200) {
			mdui.snackbar('已启动终端程序。', {timeout: 2000});
		} else if(data.code == 201) {
			mdui.snackbar('终端程序已经在运行。', {timeout: 2000});
		} else if(data.code == 500) {
			mdui.snackbar('桌面启动器不在运行，因而无法操作。', {timeout: 2000});
		}
		if(rid != stop_terminal_t) return;
		$('.singin-start-terminal, .singin-stop-terminal').disabled(false);
		reload_system_status();
	};
	$('.singin-start-terminal').on('click', () => start_terminal());

	reload_system_status(true);
	setInterval(reload_system_status, 7000);
});
$(() => {
	var query_log = async () => {
		$('.singin-log-query').disabled(true);
		var datestr = $('.singin-log-date').val();
		var data = await fetch_json(G.basic_url + 'api/system/log?date=' + datestr);
		var show_result = (str) => {
			$('.singin-log-display').text(str);
		};
		if(data === null) {
		    show_result('加载失败');
		    return;
		}
		if(data.code == 400) {
			show_result('日期格式不正确。');
		} else {
			var text = data.data.join("\n").trim();
			if(text == '') {
				show_result(datestr + ' 没有日志数据。');
			} else {
				show_result(datestr + ' 的日志数据如下：' + "\n" + text);
			}
		}
		$('.singin-log-query').disabled(false);
	};
	$('.singin-log-query').on('click', query_log);
	$('[data-singin-page=logs]').on('data_pageloaded', () => {
		$('.singin-log-date').val((new Date()).getDateStr());
		tool_updateTextFields();
	}).on('data_pageopened', () => {
		if(undefined === $('.singin-log-query').attr('disabled')) $('.singin-log-query').trigger('click');
	});
	
	$('.singin-reboot').on('click', async () => {
	    var state = await mdui.confirm_async('即将重启服务器！<br>请注意服务器重启需要亿点时间。不要在打卡即将结束时重启，否则可能导致数据未上传。', '重启服务器');
	    if(!state) return;
	    state = await mdui.confirm_async('确定重启？', '重启服务器');
	    if(!state) return;
	    mdui.snackbar('重启指令已发送，请在重启完成后刷新页面。')
	    fetch_json(G.basic_url + 'api/system/reboot');
	});
});
