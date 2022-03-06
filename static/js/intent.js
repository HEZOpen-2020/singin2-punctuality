// Echo Service
$(() => {
	var read_echo = (app) => {
		var k = G.app_prefix + '-echo-' + app;
		if(!localStorage[k]) {
			return {};
		}
		return JSON.parse(localStorage[k]);
	};
	var write_echo = (app, data) => {
		var k = G.app_prefix + '-echo-' + app;
		localStorage[k] = JSON.stringify(data);
	};
	var add_echo = (app, id, responsed = null) => {
		var arr = read_echo(app);
		for(i in arr) {
			if(arr[i].echo) {
				if(+new Date() - arr[i].created > 2000) {
					delete arr[i];
				}
			}
		}
		arr[id] = {
			echo: true,
			responsed: responsed,
			created: +new Date()
		};
		write_echo(app, arr);
	};
	var query_echo = (app, id) => {
		var arr = read_echo(app);
		if(arr[id]) {
			return arr[id].responsed;
		}
		return null;
	}
	
	window.Echo = {};
	window.Echo.tick = (app) => {
		if(G.app_info.findKV('id', app) == -1) {
			return null;
		}
		var rid = random_ele_id();
		add_echo(app, rid, app == G.app_id ? G.app_instance_id : null);
		return rid;
	};
	window.Echo.tock = (app, id) => {
		if(G.app_info.findKV('id', app) == -1) {
			return null;
		}
		return query_echo(app, id);
	};

	var last_response_all = null;
	var response_all = async () => {
		last_response_all = +new Date();
		var arr = read_echo(G.app_id);
		for(i in arr) {
			if(!arr[i].responded) arr[i].responsed = G.app_instance_id;
		}
		write_echo(G.app_id, arr);
	};
	setInterval(response_all, 200);
});

// Intent Receiver
$(() => {
	var receive_delay = 100;
	var life = 300000; // 5 mins

	var read_intent = (app) => {
		var k = G.app_prefix + '-intent-' + app;
		if(!localStorage[k]) {
			return {};
		}
		return JSON.parse(localStorage[k]);
	};
	var write_intent = (app, data) => {
		var k = G.app_prefix + '-intent-' + app;
		localStorage[k] = JSON.stringify(data);
	};
	var add_intent = (app, id, intent, data, allow_dupe = true) => {
		var arr = read_intent(app);
		
		for(i in arr) {
			if(arr[i].intent) {
				if(!allow_dupe || arr[i].received && +new Date() - arr[i].received > receive_delay || +new Date() - arr[i].created > life) {
					delete arr[i];
				}
			}
		}
		arr[id] = {
			intent: intent,
			received: null,
			created: +new Date(),
			data: data
		};
		write_intent(app, arr);
	};

	window.Intent = {};
	window.Intent.send = (app, intent, data) => {
		var app_data = G.app_info.findKVEle('id', app);
		if(!app_data) {
			return null;
		}
		if(!app_data.intent || !app_data.intent[intent]) {
			return null;
		}
		var allow_dupe = app_data.intent[intent].allow_dupe ?? true;
		var rid = random_ele_id();
		add_intent(app, rid, intent, data, allow_dupe);
		return rid;
	};

	var last_receive = +new Date();
	var k = 0;
	var receive = () => {
		if(!document.hidden && +new Date() - last_receive >= 200) {
			last_receive = +new Date();
			var arr = read_intent(G.app_id);
			for(i in arr) {
				if(arr[i].intent) {
					if((!arr[i].received || +new Date() - arr[i].received <= receive_delay) && +new Date() - arr[i].created <= life) {
						if(!arr[i].received) arr[i].received = +new Date();
						$(document).trigger('intent', {intent: arr[i].intent, data: arr[i].data});
					}
				}
			}
			write_intent(G.app_id, arr);
		}

		requestAnimationFrame(receive);
	};
	requestAnimationFrame(receive);
});
