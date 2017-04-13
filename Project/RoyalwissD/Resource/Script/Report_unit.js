/**
 * Created by 1195 on 2017-4-5.
 */

$(function(){
	var quasar_script = document.getElementById('quasar_script');
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	$('#control_form .btn-save').on('click', function(){
		$('#control_form .form-control').each(function(){
			if($(this).val() != ''){
				var name = $(this).attr('name');
				var val  = encodeURI(encodeURI($(this).val()));
				var url  = url_object.setUrlParam(name, val);
				url      = url_object.delUrlParam('p', url);
				location.replace(url);
			}
		})
	});
	/**
	 * 回车搜索
	 */
	$('.keyword').on('keydown', function(e){
		if(e.keyCode == 13){
			console.log($(this));
			var name = $(this).attr('name');
			var val  = encodeURI(encodeURI($(this).val()));
			var url  = url_object.setUrlParam(name, val);
			url      = url_object.delUrlParam('p', url);
			location.replace(url);
		}
	});
});
