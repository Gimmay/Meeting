/**
 * Created by qyqy on 2016-12-9.
 */
$(function(){
	// js object
	var quasar_script = document.getElementById('quasar_script');
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
	// jquery dom object
	var $search_input = $('input#search_input');
	(function(){
		$search_input.focus();
		$search_input.select();
		if(url_object.isTheUrlParamExist('keyword')){
			// todo sign
		}
	})();
	$('#search_input').keyup(function(e){
		if(e.keyCode == 13){
			var keyword = $('#search_input').val();
			keyword     = encodeURI(keyword);
			keyword     = encodeURI(keyword);
			var new_url = url_object.setUrlParam('keyword', keyword);
			location.replace(new_url);
		}
	});
	$('form#form').submit(function(){
		var keyword = $search_input.val();
		var new_url = url_object.setUrlParam('keyword', keyword);
		location.replace(new_url);
		return false;
	});
	$('.btn-gift').click(function(){
		var id = $(this).attr('data-id');
		$('#gift_modal').find('input[name=id]').val(id);
	});
});