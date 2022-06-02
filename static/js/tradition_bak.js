$(async () => {
	var selectInst = null;
	var $selectClass = $('.singin-tradition-class');
	var countdown_end = -1;

	var updateClasses = async () => {
		$('.singin-tradition-loading').show();
		$('.singin-tradition-content').hide();
		$('.singin-tradition-fail').hide();

		var data = await fetch_json(G.basic_url + 'api/hz2zrun/student/classes');
		if(!data || !data.success) {
			$('.singin-tradition-loading').hide();
			$('.singin-tradition-content').hide();
			$('.singin-tradition-fail').show();
			return;
		}

		$('.singin-tradition-loading').hide();
		$('.singin-tradition-content').show();
		$('.singin-tradition-fail').hide();

		// 更新班级选择菜单
		$('.singin-tradition-class option').remove();
		data = data.data;
		console.log(data);
		let depart_id_check = [];
		for(let grade_id in data) {
			let grade = data[grade_id];
			if(grade.name === null && !G.public_config.tradition_allow_teacher) continue;
			let depart_list = [];
			for(let depart_id in grade.departs) {
				let depart = grade.departs[depart_id];
				depart.id = depart_id;
				depart_list.push(depart);
			}
			depart_list.sort((a, b) => {
				if(a.name < b.name) return -1;
				if(a.name > b.name) return 1;
				return 0;
			});
			for(let depart of depart_list) {
				let depart_id = depart.id;
				if(depart_id != G.public_config.tradition_here && !G.public_config.tradition_allow_other) continue;
				$selectClass.append(
					$('<option></option>')
						.attr('value', depart_id)
						.text(grade.name ? grade.name + ' ' + depart.name : depart.name)
				);
				depart_id_check.push(depart_id);
			}
		}

		if(depart_id_check.length === 0) {
			$('.singin-tradition-loading').hide();
			$('.singin-tradition-content').hide();
			$('.singin-tradition-fail').show();
			return;
		}

		if(!selectInst) {
			selectInst = new mdui.Select('.singin-tradition-class', {position: 'bottom'});
		}

		if(depart_id_check.indexOf(G.public_config.tradition_here) != -1) {
			$selectClass.val(G.public_config.tradition_here);
		}

		selectInst.handleUpdate();

		loadStudents($selectClass.val());
	};

	updateClasses();

	$('.singin-tradition-reload').on('click', () => updateClasses());
	$selectClass.on('change', () => loadStudents($selectClass.val()));

	var loadStudents = async (class_id) => {
		$('.singin-tradition-data-loading').show();
		$('.singin-tradition-data').hide();

		var data = await fetch_json(G.basic_url + 'api/hz2zrun/student/students?depart=' + encodeURIComponent(class_id));

		if(!data || !data.success) {
			return;
		}

		data = data.data;
		$('.singin-tradition-data-loading').hide();
		$('.singin-tradition-data').show();

		// 解析数据
		var students = [];
		for(stu_id in data) {
			let stu = data[stu_id];
			if(stu.BirthDay) {
    			stu.BirthDate = stu.BirthDay.substr(4, 8);
    			students.push(stu);
			}
		}

		// 排序数值
		var currDate = new Date();
		var currDateStr = date_format_str(currDate).substr(4, 4);
		students.sort((a, b) => {
			date0 = currDateStr;
			date1 = a.BirthDate;
			date2 = b.BirthDate;

			var p1 = (date1 < date0);
			var p2 = (date2 < date0);

			if(p1 < p2) return -1;
			if(p1 > p2) return 1;
			if(date1 < date2) return -1;
			if(date1 > date2) return 1;
			if(a.name < b.name) return -1;
			if(a.name > b.name) return 1;
			return 0;
		});

		// console.log(students);

		// 显示下一个
		let len = students.length;
		let start_ptr = 1;
		while(start_ptr < len && students[start_ptr].BirthDate == students[start_ptr - 1].BirthDate) {
			start_ptr += 1;
		}

		let start_names = [];
		let start_date_repr = '';
		let now_year_str = prepend_str(currDate.getFullYear(), 4, '0');
		let next_year_str = prepend_str(currDate.getFullYear() + 1, 4, '0');
		for(let i = 0; i < start_ptr; i++) {
			let stu = students[i];
			let target_date = date_from_datestr(now_year_str + stu.BirthDate);
			if(stu.BirthDate < currDateStr) {
				target_date = date_from_datestr(next_year_str + stu.BirthDate);
			}
			countdown_end = +target_date;
			start_date_repr = (target_date.getMonth() + 1).toString() + '.' + target_date.getDate();
			start_names.push(stu.UserName);
		}

		$('.singin-tradition-attendant').remove();
		for(let start_name of start_names) {
			$('.singin-tradition-attendants').append(
				$('<p></p>')
				.addClass('singin-tradition-attendant')
				.text(start_name)
			);
		}
		$('.singin-tradition-date').text(start_date_repr);

		// 显示接下来
		var displayNext = (name, date_repr, is_more) => {
			var $line = $('<tr></tr>')
			.addClass('singin-tradition-next')
			.append(
				$('<td></td>').text(name)
			)
			.append(
				$('<td></td>').text(date_repr)
			);
			$('.singin-tradition-nexts').append(
				$line	
			);
			if(is_more) $line.addClass('singin-tradition-next-more');
		};

		let next_ptr = Math.min(len, start_ptr + G.public_config.tradition_nexts);
		while(next_ptr < len && students[next_ptr].BirthDate == students[next_ptr - 1].BirthDate) {
			next_ptr += 1;
		}
		$('.singin-tradition-next-sec')[next_ptr - start_ptr > 0 ? 'show' : 'hide']();
		$('.singin-tradition-next').remove();
		for(let i = start_ptr; i < next_ptr; i++) {
			let stu = students[i];
			let name = stu.UserName;
			let date_repr = date_format_date(date_from_datestr(stu.BirthDay));
			displayNext(name, date_repr, false);
		}

		// 显示剩余
		$('.singin-tradition-show-more')
		.text(LNG('tradition.show_more'))
		.removeClass('singin-toggle-active')
		.disabled(len - next_ptr == 0);
		$('.singin-tradition-show-more')[G.public_config.tradition_allow_show_all ? 'show' : 'hide']();
		for(let i = next_ptr; i < len; i++) {
			let stu = students[i];
			let name = stu.UserName;
			let date_repr = date_format_date(date_from_datestr(stu.BirthDay));
			displayNext(name, date_repr, true);
		}
		$('.singin-tradition-next-more').hide();
	};

	$(document).on('data_daypass', () => loadStudents($selectClass.val()));

	$('.singin-tradition-show-more').on('click', function() {
		if($(this).hasClass('singin-toggle-active')) {
			$(this)
			.removeClass('singin-toggle-active')
			.text(LNG('tradition.show_more'));
			$('.singin-tradition-next-more').hide();
		} else {
			$(this)
			.addClass('singin-toggle-active')
			.text(LNG('tradition.show_less'));
			$('.singin-tradition-next-more').show();
		}
	});

	var updateCountdown = () => {
		var t = '';
		var currDate = new Date();
		if(+currDate <= countdown_end) {
			t = LNG('tradition.remain.years', ((countdown_end - currDate) / 1000 / 3600 / 24 / 365).toFixed(8));
		} else {
			t = LNG('tradition.now');
		}
		$('.singin-tradition-countdown').text(t);

		requestAnimationFrame(updateCountdown);
	};
	requestAnimationFrame(updateCountdown);
});
