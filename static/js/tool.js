$.fn.disabled = function(x) {
	if(x) {
		this.attr('disabled', 'disabled');
	} else {
		this.removeAttr('disabled');
	}
};

/**
 * 将某奇怪日期自动溢出
 */
function _0535_transform(t) {
    var m = t[0];
    var d = t[1];
    if(m == 6 && 2 <= d && d <= 4) {
        m -= 1;
        d += 31;
    }
    return [m, d];
}

function prepend_str(str, len, prefix='0') {
	str = str.toString();
	while(str.length < len) {
		str = prefix + str;
	}
	return str;
}

function date_format_minutes(d) {
	return prepend_str('' + d.getHours(), 2, '0') + ':' + prepend_str('' + d.getMinutes(), 2, '0');
}

function date_format_seconds(d) {
	return date_format_minutes(d) + ':' + prepend_str('' + d.getSeconds(), 2, '0');
}

function date_format(d) {
	return date_format_fulldate(d) + ' ' + date_format_seconds(d);
}

function date_format_date(d) {
    var dateMd = _0535_transform([d.getMonth() + 1, d.getDate()]);
	return prepend_str('' + dateMd[0], 2, '0') + '月' + prepend_str('' + dateMd[1], 2, '0') + '日';
}

function date_format_fullmonth(d) {
	return prepend_str('' + d.getFullYear(), 4, '0') + '年' + prepend_str('' + (d.getMonth() + 1), 2, '0') + '月';
}

function date_format_fulldate(d) {
	return prepend_str('' + d.getFullYear(), 4, '0') + '年' + date_format_date(d);
}

function date_format_str(d) {
	return prepend_str('' + d.getFullYear(), 4, '0') + prepend_str('' + (d.getMonth() + 1), 2, '0') + prepend_str('' + d.getDate(), 2, '0');
}

function date_format_week(d) {
	return ['周日','周一','周二','周三','周四','周五','周六','周日'][d.getDay()];
}

function day_seconds_to_time(t) {
	return new Date(t * 1000 + (+new Date('1970/01/01')));
}

function day_seconds_minutes(t) {
	return date_format_minutes(day_seconds_to_time(t));
}

function date_from_datestr(t) {
	return new Date(t.substr(0,4) + '/' + t.substr(4,2) + '/' + t.substr(6,2));
}

function is_leap_year(t) {
    t = +t;
    return (t % 4 == 0 && t % 100 != 0) || (t % 400 == 0);
}

/**
 * 注意：date1 和 now 为 Date 对象
 */
function next_date_occurance(date1, now) {
    var md1 = date_format_str(date1).substr(4, 4);
    var md_now = date_format_str(now).substr(4, 4);
    var init_year = +now.getFullYear();
    if(md1 < md_now) {
        init_year += 1;
    }
    while(!is_leap_year(init_year) && md1 == '0229') {
        init_year += 1;
    }
    return new Date(init_year + '/' + (date1.getMonth() + 1) + '/' + date1.getDate());
}

Date.prototype.getSecondsOfDay = function() {
	var dayStart = +new Date('' + this.getFullYear() + '/' + (this.getMonth() + 1) + '/' + this.getDate());
	return (+this - dayStart) / 1000;
}
Date.prototype.getDateStr = function() {
	return prepend_str('' + this.getFullYear(), 4, '0')
	     + prepend_str('' + (this.getMonth() + 1), 2, '0')
		 + prepend_str('' + this.getDate(), 2, '0');
}

Array.prototype.findKV = function(k, v) {
	for(i in this) {
		if(this[i][k] == v) {
			return i;
		}
	}
	return -1;
};
Array.prototype.findKVEle = function(k, v) {
	var idx = this.findKV(k, v);
	if(idx == -1) return null;
	return this[idx];
};

function fetch_json(url) {
	return new Promise((resolve, reject) => {
		$.ajax({
			type: 'GET',
			dataType: 'json',
			url: url,
			timeout: 15000,
			error: (e) => {
				resolve({
					success: false,
					code: -1,
					data: e
				});
			},
			success: (t) => {
				resolve(t);
			}
		});
	});
}
function post_json(url, data) {
	return new Promise((resolve, reject) => {
		$.ajax({
			method: 'POST',
			data: data,
			dataType: 'json',
			url: url,
			timeout: 15000,
			error: (e) => {
				resolve({
					success: false,
					code: -1,
					data: e
				});
			},
			success: (t) => {
				resolve(t);
			}
		});
	});
}

async function fetch_json_retry(url, attempt = 3) {
	while(attempt--) {
		var data = await fetch_json(url);
		if(data.success == true) {
			return data;
		}
	}
	return data;
}

(() => {
	var last_date = date_format_str(new Date());
	setInterval(() => {
		if(last_date != date_format_str(new Date())) {
			$(document).trigger('data_daypass');
			last_date = date_format_str(new Date());
		}
	}, 1000);
})();

function storeData(key='',val) {
	// 判断模式
	var mode = 'set';
	if(val === undefined) {
		mode = 'get';
	} else if(val === null) {
		mode = 'remove';
	}

	// 取出数据
	var data = localStorage[G.app_prefix + '-local-data'];
	try {
		data = JSON.parse(data);
	} catch(_e) {
		data = {};
	}
	if(typeof(data) != 'object') {
		data = {};
	}

	// 寻址
	key = key.split('.');
	if(key.length == 1 && key[0] == '') {
		if(mode == 'get') {
			return data;
		} else if(mode == 'set') {
			return null;
		} else {
			delete localStorage[G.app_prefix + '-local-data'];
			return undefined;
		}
	}
	var curr = data;
	for(let i=0;i<key.length-1;i++) {
		var nxt = curr[key[i]];
		if(typeof(nxt) != 'object' || nxt === null) {
			// 正在设置且此处无内容
			if(mode == 'set' && nxt === undefined) {
				// 创建，然后重新赋值
				curr[key[i]] = {};
				nxt = curr[key[i]];
			} else {
				// 否则失败
				return null;
			}
		}
		curr = nxt;
	}
	// 末端寻址
	if(mode == 'set') {
		curr[key[key.length-1]] = val;
		localStorage[G.app_prefix + '-local-data'] = JSON.stringify(data);
		return val;
	} else if(mode == 'get') {
		return curr[key[key.length-1]];
	} else if(mode == 'remove') {
		// 删除值
		delete curr[key[key.length-1]];
		localStorage[G.app_prefix + '-local-data'] = JSON.stringify(data);
		return undefined;
	}
}

function copy_text(str) {
	var ele = $('#singin-int-copy-text')[0];
	$(ele).css('display', 'block');
	ele.value = str;
	ele.select();
	document.execCommand('copy');
	$(ele).css('display', 'none');
}

function tool_mutation() {
	try {
		// 文本复制按钮
		$(() => {
			$('.singin-action-copy').off('click');
			$('.singin-action-copy').on('click', function() {
				var selector = $(this).attr('data-singin-target');
				var text = $(selector).text();
				copy_text(text);
				mdui.snackbar('已复制 ' + text.length + ' 个字符。', {timeout: 500});
			});
		});
		// 语音朗读按钮
		$(() => {
			$('.singin-action-read').off('click');
			$('.singin-action-read').on('click', function() {
				var selector = $(this).attr('data-singin-target');
				var text = $(selector).text();
				if($(selector)[0].tagName == 'INPUT' || $(selector)[0].tagName == 'TEXTAREA') {
					text = $(selector).val();
				}
				var lang = $(this).attr('data-singin-lang');
				Youdao.tts_speak(text, lang);
				$(document).off('data_ttserror');
				var issue_snk = mdui.snackbar('语音朗读已发起。', {timeout: 7000, buttonText: '撤销', onButtonClick: () => {Youdao.tts_stop();}});
				$(document).on('data_ttserror', () => {
					issue_snk.close();
					mdui.snackbar('语音朗读失败。', {timeout: 2000});
				});
			});
		});
		$(() => {
			$('.singin-action-dictvoice').off('click');
			$('.singin-action-dictvoice').on('click', function() {
				var param = $(this).attr('data-singin-dictvoice');
				Youdao.tts_dict(param);
				$(document).off('data_ttserror');
				var issue_snk = mdui.snackbar('语音朗读已发起。', {timeout: 2000, buttonText: '撤销', onButtonClick: () => {Youdao.tts_stop();}});
				$(document).on('data_ttserror', () => {
					issue_snk.close();
					mdui.snackbar('语音朗读失败。', {timeout: 2000});
				});
			});
		});
		// 发送
		$(() => {
			$('.singin-action-send').off('click');
			$('.singin-action-send').on('click', async function() {
				var data = $(this).attr('data-singin-data');
				if(undefined === data) {
					var selector = $(this).attr('data-singin-target');
					if($(selector)[0].tagName == 'INPUT' || $(selector)[0].tagName == 'TEXTAREA') {
						data = $(selector).val();
					} else {
						data = $(selector).text();
					}
				}
				var intent = $(this).attr('data-singin-intent');
				var app = $(this).attr('data-singin-app');

				var rid = Intent.send(app, intent, data);
				var echo = Echo.tick(app);

				if(rid != null) {
					var app_label = G.app_info.findKVEle('id', app).label;
					if(app != G.app_id) {
						mdui.snackbar('正在发送数据...', {timeout: 400});
						await new Promise((resolve, reject) => {setTimeout(() => resolve(), 1000)});
					}
					var resp = Echo.tock(app, echo);
					if(resp != null) {
						if(app != G.app_id) {
							mdui.snackbar(
								'已发送 ' + data.length + ' 个字符。<br />请切换至任意已打开的 ' + app_label + ' 窗口。',
								{timeout: 4000, buttonText: '打开新窗口', onButtonClick: () => {AppNav.open_app(app);}}
							);
						} else {
							mdui.snackbar(
								'已发送 ' + data.length + ' 个字符。',
								{timeout: 1000}
							);
						}
					} else {
						mdui.snackbar(
							'已发送 ' + data.length + ' 个字符。<br />正在打开新的 ' + app_label + ' 窗口。',
							{timeout: 2000}
						);
						setTimeout(() => AppNav.open_app(app), 100);
					}
				} else {
					mdui.snackbar('无法发送文本。', {timeout: 2000});
				}
			});
		});
		// 文本框 enter-target
		$('input[data-singin-enter-target]:not([data-singin-enter-target-ok]),textarea[data-singin-enter-target]:not([data-singin-enter-target-ok])')
			.attr('data-singin-enter-target-ok', '')
			.on('input', function() {
				var sel = $(this).attr('data-singin-enter-target');
				var $btn = $(sel);
				if(this.value.trim()) {
					$btn.removeAttr('disabled');
				} else {
					$btn.attr('disabled', '');
				}
			})
			.on('keyup', function(e) {
				if(e.keyCode == 13) {
					if(this.tagName == 'INPUT' || (this.tagName == 'TEXTAREA' && e.ctrlKey)) {
						var sel = $(this).attr('data-singin-enter-target');
						var $btn = $(sel);
						if(undefined === $btn.attr('disabled')) {
							$btn.trigger('click');
						}
					}
				}
			});
		var sample_tooltips = $('.singin-sample [singin-tooltip]');
		$('[singin-tooltip]:not([data-singin-tooltip-ok])').each(function() {
			var is_valid = true;
			var curr = this;
			sample_tooltips.each(function() {
				if(curr == this) {
					is_valid = false;
				}
			});
			if(!is_valid) return;
			$(this).attr('data-singin-tooltip-ok', '');
			new mdui.Tooltip(this, {delay: 350, content: $(this).attr('singin-tooltip')});
		});
	} catch(e) {
		alert(e);
	}
}

// 替换XML
function escapeXml(unsafe) {
	if(undefined === unsafe) unsafe = 'undefined';
	return unsafe.toString().replace(/[<>&'"]/g, function (c) {
		switch (c) {
			case '<': return '&lt;';
			case '>': return '&gt;';
			case '&': return '&amp;';
			case '\'': return '&apos;';
			case '"': return '&quot;';
		}
	});
}

window.ElementProps = {
	is_visible: (e) => {
		return $(e).attr('disabled') === undefined && $(e).css('display') != 'none' && !$(e).hasClass('mdui-fab-hide');
	}
};

tool_mutation();
var tool_updateTextFields = () => {
	$('input,textarea').each(function() {
		$(this).trigger('input');
	});
};
setTimeout(() => tool_updateTextFields(), 1);

$('body').on('keydown', (e) => {
	if(e.keyCode == 27) {
		$('[data-singin-esc]').each(function() {
			var isVisible = true;
			var k = this;
			while(k.tagName != 'BODY') {
				if(!ElementProps.is_visible(k)) {
					isVisible = false;
					break;
				}
				k = k.parentElement;
			}
			if(isVisible) {
				$(this).trigger('click');
			}
		});
	}
});

function random_ele_id() {
	var ch = '0123456789QWERTYUIOPASDFGHJKLZXCVBNM';
	var ret = 'singin-rid-';
	for(let i=0;i<40;i++) {
		ret += ch[Math.floor(Math.random() * ch.length)];
	}
	return ret;
}

window.StringType = {
	latin_count: (str) => {
		var cnt = 0;
		for(let i=0;i<str.length;i++) {
			if(str.charCodeAt(i) < 256) {
				cnt += 1;
			}
		}
		return cnt;
	},
	latin_ratio: (str) => {
		return StringType.latin_count(str) / str.length;
	},
	str_language: (str) => {
		if(StringType.latin_ratio(str) < 0.85) {
			return 'zh';
		} else {
			return 'en';
		}
	},
	word_not_sentence: (str) => {
		if(StringType.str_language(str) == 'zh') {
			return str.length <= 4;
		} else {
			for(let i=0;i<str.length;i++) {
				if(str.charAt(i).trim().length == 0) {
					return false;
				}
			}
			return true;
		}
	}
};

$(() => {
	tool_updateTextFields();
});

mdui.confirm_async = async (text, title, options) => {
    return new Promise((resolve) => {
        mdui.confirm(text, title, () => resolve(true), () => resolve(false), {modal: true, closeOnEsc: false});
    });
};
