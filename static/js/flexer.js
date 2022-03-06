$(() => {
	var top_ops_flex = () => {
		$('.singin-top-ops').each(function() {
			$(this).css('top', $('html')[0].scrollTop + 'px');
		});

		requestAnimationFrame(top_ops_flex);
	};
	requestAnimationFrame(top_ops_flex);
});
