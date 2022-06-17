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
					text = (text ? LNG('ui.yes') : LNG('ui.no'));
				}
				text = map(text);
				$('.singin-status').append(
					$('<tr></tr>')
					.addClass('singin-status-item')
					.append($('<td></td>').text(name))
					.append($('<td></td>').addClass('singin-status-value').text(text))
				);
			};
			add_status_line(LNG('status.item.server_os'), 'server_os');
			add_status_line(LNG('status.item.php_ver'), 'php_version');
			add_status_line(LNG('status.item.namespace'), 'namespace');
			add_status_line(LNG('status.item.port'), 'server_port');
			add_status_line(LNG('status.item.ver'), 'version');
			add_status_line(LNG('status.item.webroot'), 'document_root');
			add_status_line(LNG('status.item.db_path'), 'db_path');
			add_status_line(LNG('status.item.lesson'), 'singing_lesson');
			add_status_line(LNG('status.item.proc_name'), 'proc_name');
			add_status_line(LNG('status.item.proc_path'), 'proc_path');
			add_status_line(LNG('status.item.terminal_state'), 'terminal_running');
			add_status_line(LNG('status.item.launcher_state'), 'desklaunch_daemon');
			add_status_line(LNG('status.item.time'), 'system_time', (x) => date_format(new Date(x * 1000)));

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
		var data = await post_json(G.basic_url + 'api/system/terminal/stop');
		if(data === null) {
		    mdui.snackbar(LNG('status.toast.except'), {timeout: 2000});
		    return;
		}
		if(data.code == 200) {
			mdui.snackbar(LNG('status.toast.stop'), {timeout: 2000});
		} else if(data.code == 201) {
			mdui.snackbar(LNG('status.toast.stopped'), {timeout: 2000});
		} else if(data.code == 500) {
			mdui.snackbar(LNG('status.toast.launcher'), {timeout: 2000});
		}
		if(rid != stop_terminal_t) return;
		$('.singin-stop-terminal, .singin-start-terminal').disabled(false);
		reload_system_status();
	};
	$('.singin-stop-terminal').on('click', () => stop_terminal());
	window.start_terminal = async () => {
		$('.singin-start-terminal, .singin-stop-terminal').disabled(true);
		var rid = stop_terminal_t = random_ele_id();
		var data = await post_json(G.basic_url + 'api/system/terminal/start');
		if(data === null) {
		    mdui.snackbar(LNG('status.toast.except'), {timeout: 2000});
		    return;
		}
		if(data.code == 200) {
			mdui.snackbar(LNG('status.toast.start'), {timeout: 2000});
		} else if(data.code == 201) {
			mdui.snackbar(LNG('status.toast.started'), {timeout: 2000});
		} else if(data.code == 500) {
			mdui.snackbar(LNG('status.toast.launcher'), {timeout: 2000});
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
		    show_result(LNG('log.fail'));
		    return;
		}
		if(data.code == 400) {
			show_result(LNG('log.bad'));
		} else {
			var text = data.data.join("\n").trim();
			if(text == '') {
				show_result(LNG('log.no', datestr));
			} else {
				show_result(LNG('log.below', datestr) + "\n" + text);
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
	    var state = await mdui.confirm_async(LNG('status.reboot.1'), LNG('status.reboot.title'));
	    if(!state) return;
	    state = await mdui.confirm_async(LNG('status.reboot.2'), LNG('status.reboot.title'));
	    if(!state) return;
	    mdui.snackbar(LNG('status.toast.reboot'))
	    post_json(G.basic_url + 'api/system/reboot');
	});
});
