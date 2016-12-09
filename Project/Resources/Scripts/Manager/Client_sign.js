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
	$('form#form').on('submit', function(){
		var keyword = $search_input.val();
		var new_url = url_object.setUrlParam('keyword', keyword);
		location.replace(new_url);
		return false;
	});
});