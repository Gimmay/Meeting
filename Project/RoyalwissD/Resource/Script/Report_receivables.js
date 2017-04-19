/**
 * Created by 1195 on 2017-4-5.
 */

$(function(){
	var quasar_script     = document.getElementById('quasar_script');
	var url_object        = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	var $control_form_obj = $('#control_form');
	$control_form_obj.find('.btn-save').on('click', function(){
		var url = location.href;
		$control_form_obj.find('.form-control').each(function(){
			var name  = $(this).attr('name');
			var value = $(this).val();
			url       = url_object.delUrlParam('p', url);
			url       = url_object.delUrlParam(name, url);
			if(value != ''){
				var val = encodeURI(encodeURI(value));
				url     = url_object.setUrlParam(name, val, url);
			}
		});
		location.replace(url);
	});
	/**
	 * 回车搜索
	 */
	$control_form_obj.find('.keyword').on('keydown', function(e){
		if(e.keyCode == 13){
			var url = location.href;
			$control_form_obj.find('.form-control').each(function(){
				var name  = $(this).attr('name');
				var value = $(this).val();
				url       = url_object.delUrlParam('p', url);
				url       = url_object.delUrlParam(name, url);
				if(value != ''){
					var val = encodeURI(encodeURI(value));
					url     = url_object.setUrlParam(name, val, url);
				}
			});
			location.replace(url);
		}
	});

	//排序
	var cur_order_column = $('#default_order_column').val();
	var cur_order_method = $('#default_order_method').val();
	$('#tableExcel').find('th[data-column]').on('click', function(){
		var order_column = $(this).attr('data-column');
		var order_method = url_object.getUrlParam('orderMethod');
		var new_url      = url_object.setUrlParam('orderColumn', order_column);
		if(cur_order_column == order_column){
			if(order_method == 'desc') order_method = 'asc';
			else if(order_method == 'asc') order_method = 'desc';
			else order_method = 'desc';
		}
		else order_method = 'asc';
		new_url = url_object.setUrlParam('orderMethod', order_method, new_url);
		location.replace(new_url);
	}).each(function(){
		var order_column = $(this).attr('data-column');
		if(order_column == cur_order_column){
			var column_word = $(this).text();
			var method_word = cur_order_method == 'desc' ? '▼' : '▲';
			$(this).css('background', '#EFEFEF').text(column_word+method_word);
		}
	});
});
