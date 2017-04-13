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
});
