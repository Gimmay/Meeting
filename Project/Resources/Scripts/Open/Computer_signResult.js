/**
 * Created by qyqy on 2016-12-12.
 */
$(function(){
	var $success_message_obj = $('#mt_success');
	var $client_name_obj     = $success_message_obj.find('.client_name');
	var $unit_obj            = $success_message_obj.find('.unit');
	var $order_obj           = $success_message_obj.find('.order');
	var inverval_flag        = setInterval(function(){
		Common.ajax({
			url     :'',
			data    :{requestType:'signResult:get_sign_info'},
			type    :'post',
			callback:function(r){
				if(r['found']){
					$client_name_obj.text(r.clientName);
					$unit_obj.text(r.unit);
					$order_obj.text(r.order);
					$success_message_obj.slideDown();
				}else $success_message_obj.slideUp();
			}
		})
	}, ThisObject.data.refreshTime*1000);
});