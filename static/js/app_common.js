$(() => {
	var show_page = (page_id) => {
		if($('.singin-page-select[data-singin-page=' + page_id + ']').length == 0) {
			return false;
		}
		$('.singin-page-select').removeAttr('data-singin-page-selected')
							.removeClass('mdui-list-item-active')
							.removeClass('mdui-color-theme-100')
							.removeClass('mdui-text-color-theme-900');
		$('.singin-page-select[data-singin-page=' + page_id + ']').attr('data-singin-page-selected', '')
			 											  .addClass('mdui-list-item-active')
			 											  .addClass('mdui-color-theme-100')
			 											  .addClass('mdui-text-color-theme-900');
		$('.singin-page-container').css('display', 'none');
		$('.singin-page-container').css('opacity', '0');
		var $page_container = $('.singin-page-container[data-singin-page=' + page_id + ']');
		$page_container.css('display', 'block');
		setTimeout(() => {
			$page_container.css('opacity', '1');
		}, 3);
		if(undefined === $page_container.attr('data-singin-page-loaded')) {
			$page_container.trigger('data_pageloaded');
			$page_container.attr('data-singin-page-loaded', '');
		}
		$page_container.trigger('data_pageopened');
		return true;
	};
	$('.singin-page-select').on('click', function() {
		var page_id = $(this).attr('data-singin-page');
		show_page(page_id);
	});
	setTimeout(() => {
		show_page($('.singin-page-select[data-singin-page-selected]').attr('data-singin-page'));
	}, 50);
});
