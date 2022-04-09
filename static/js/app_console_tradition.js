$(async () => {
	var selectInst = new mdui.Select('.singin-tradition-class');

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
	};

	updateClasses();

	$('.singin-tradition-reload').on('click', () => updateClasses());
});
