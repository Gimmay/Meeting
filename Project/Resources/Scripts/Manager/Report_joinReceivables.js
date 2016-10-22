/**
 * Created by 1195 on 2016-10-20.
 */
$(function(){
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	// 初始化表格头排序标识符
	(function(){
		var order_method            = url_object.getUrlParam('_sort');
		var order_column            = url_object.getUrlParam('_column');
		var char                    = order_method == 'asc' ? '▲' : '▼';
		order_column                = order_column == null ? 'name' : order_column; // 没有URL没有指定排序列名的参数时默认指定客户名称列
		var table_head_column       = document.querySelector('th[data-column='+order_column+']');
		table_head_column.innerText = table_head_column.innerText+' '+char;
	})();
	// 处理排序
	$('th[data-column]').on('click', function(){
		var column       = this.getAttribute('data-column');
		var order_method = url_object.getUrlParam('_sort');
		var order_column = url_object.getUrlParam('_column');
		if(!order_method) order_method = 'asc';
		switch(order_method.toLowerCase()){
			case 'desc':
				if(order_column == column){
					order_method = 'asc';
				}else{
					order_method = 'desc';
					order_column = column;
				}
				break;
			case 'asc':
				if(order_column == column){
					order_method = 'desc';
				}else{
					order_method = 'asc';
					order_column = column;
				}
				break;
		}
		url_object.setUrlParam('_column', order_column);
		var new_url = url_object.setUrlParam('_sort', order_method);
		location.replace(new_url);
	});
	// 勾选显示
	$('.screen_check').find('input[type=checkbox]').on('click', function(){
		var index      = $(this).parent('label').index();
		var $table_obj = $('#report_table');
		if($(this).prop('checked')){
			$table_obj.find('thead th').eq(index+1).removeClass('hide');
			$table_obj.find('tbody tr').each(function(){
				$(this).find('td').eq(index+1).removeClass('hide');
			});
		}else{
			$table_obj.find('thead th').eq(index+1).addClass('hide');
			$table_obj.find('tbody tr').each(function(){
				$(this).find('td').eq(index+1).addClass('hide');
			});
		}
	});
	// 显示结果数
	$('.table_length').find('select').on('change', function(){
		var number = $(this).find('option:selected').text();
		var url    = url_object.setUrlParam('_page_count', number);
		location.replace(url);
	});
	// 页面显示列表数下拉框默认值处理
	(function(){
		var page_count = url_object.getUrlParam('_page_count');
		$('.table_length').find('select option').each(function(){
			var num = $(this).text();
			if(page_count == num){
				$('.table_length').find('select option:selected').removeProp('selected').removeAttr('selected');
				$(this).prop('selected', true).attr('selected', true);
				return true;
			}
		});
	})();
});