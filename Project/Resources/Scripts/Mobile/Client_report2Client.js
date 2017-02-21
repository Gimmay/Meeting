/**
 * Created by 0967 on 2017-2-8.
 */
$(function(){
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(0);
	var cur_order_column= $('#default_order_column').val();
	var cur_order_method= $('#default_order_method').val();
	$('table th[data-column]').on('click', function(){
		var order_column= $(this).attr('data-column');
		var order_method = url_object.getUrlParam('_orderMethod');
		var new_url = url_object.setUrlParam('_orderColumn', order_column);
		if(cur_order_column==order_column){
			if(order_method == 'desc') order_method = 'asc';
			else if(order_method == 'asc') order_method = 'desc';
		}
		else order_method = 'asc';
		new_url = url_object.setUrlParam('_orderMethod', order_method, new_url);
		location.replace(new_url);
	}).each(function(){
		var order_column= $(this).attr('data-column');
		if(order_column==cur_order_column){
			var column_word = $(this).text();
			var method_word = cur_order_method=='desc'?'▼':'▲';
			$(this).css('background', '#EFEFEF').text(column_word+method_word);
		}
	});
});