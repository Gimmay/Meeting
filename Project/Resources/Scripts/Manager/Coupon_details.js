/**
 * Created by 1195 on 2016-10-8.
 */
/**
 * Created by qyqy on 2016-10-8.
 */


var couponManage = {
	liTemp:'<li class="coupon_number_item"><span>$number</span></li>',
};
$(function(){

	//单个新增和批量新增
	$('#create_way li').on('click', function(){
		var index = $(this).index();
		$('#create_way li').removeClass('active');
		$(this).addClass('active');
		$('.c_way').addClass('hide');
		$('.c_way').eq(index).removeClass('hide');
		if(index == 0){
			$('#create_coupon').find('input[name=requestType]').val('create');
		}
		if(index == 1){
			$('#create_coupon').find('input[name=requestType]').val('batch_create');
		}
	});
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 单个创建券
	$('.coupon_area').on('change', function(){
		var id = $(this).val();
		$('#create_coupon').find('input[name=hide_coupon_area]').val(id);
	});
	// 修改券
	$('.modify_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		var coupon_name = $(this).parents('tr').find('td.name').text();
		var comment = $(this).parents('tr').find('td.comment').text();
		var coupon_code = $(this).parents('tr').find('td.coupon_code').text();
		$('#modify_coupon').find('input[name=id]').val(id);
		$('#modify_coupon').find('#coupon_name_a').val(coupon_name);
		$('#modify_coupon').find('input[name=comment]').val(comment);
		$('#modify_coupon').find('input[name=coupon_code]').val(coupon_code);
		ManageObject.object.loading.loading();
		Common.ajax({
			data:{requestType:'alter_coupon', id:id}, async:false, callback:function(data){
				ManageObject.object.loading.complete();
				console.log(data);
				if(data){
					ManageObject.object.meetingSelect.setHtml(data.name);
				}
			}
		});
	});
	// 删除券
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_coupon').find('input[name=id]').val(id);
	});
	// 批量删除券
	$('.batch_delete_btn_confirm ').on('click', function(){
		var str = '';
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+','
		});
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		$('#batch_delete_coupon').find('input[name=id]').val(newStr);
	});
	// 自动获取券号
	$('.auto_get_number').on('click', function(){
		var $prefix       = $('#prefix');
		var $start_number = $('#start_number');
		var $number       = $('#number');
		var $lenght       = $('#length');
		var str           = '';
		var str2          = '';
		for(var i = 0; i<$number.val(); i++){
			var len       = $lenght.val();
			var s_number  = $start_number.val();
			var n         = Number(s_number)+Number(i);
			var realLen   = len-$prefix.val().length;
			var aa        = PrefixInteger(n, realLen);
			str += ($prefix.val()+''+aa)+',';
			var li_number = $prefix.val()+''+aa;
			str2 += couponManage.liTemp.replace('$number', li_number);
		}
		$('.list_coupon_number').html(str2);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		console.log(newStr);
		$('#create_coupon').find('input[name=hide_coupon_area]').val(newStr);
	});
	/////// 券的筛选 ////////
	// 未使用
	$('.unused_filter').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#Quasar');
		var mvc     = $quasar.attr('data-mvc-name');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('status');
		if(param == 0){
			var new_url = link.delUrlParam('status');
			location.replace(new_url);
		}else{
			var reviewed_url = link.setUrlParam('status', 0);
			location.replace(reviewed_url);
		}
	});
	// 已使用
	$('.used_filter').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#Quasar');
		var mvc     = $quasar.attr('data-mvc-name');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('status');
		if(param == 1){
			var new_url = link.delUrlParam('status');
			location.replace(new_url);
		}else{
			var reviewed_url = link.setUrlParam('status', 1);
			location.replace(reviewed_url);
		}
	});
	// 退费
	$('.refund_filter').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#Quasar');
		var mvc     = $quasar.attr('data-mvc-name');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('status');
		if(param == 2){
			var new_url = link.delUrlParam('status');
			location.replace(new_url);
		}else{
			var reviewed_url = link.setUrlParam('status', 2);
			location.replace(reviewed_url);
		}
	});
	// 券状态列表（未使用\已使用\退费）
	var mvc    = $('#Quasar').attr('data-mvc-name');
	var suffix = $('#Quasar').attr('data-page-suffix');
	var link   = new Quasar.UrlClass(1, mvc, suffix);
	var status   = link.getUrlParam('status');
	if(status == 1) $('.used_filter').find('.iradio_square-green').addClass('checked');
	if(status == 0) $('.unused_filter').find('.iradio_square-green').addClass('checked');
	if(status == 2) $('.refund_filter').find('.iradio_square-green').addClass('checked');
});
//  num传入的数字，n需要的字符长度
function PrefixInteger(num, n){
	return (Array(n).join(0)+num).slice(-n);
}
// 新增券限制
function checkCreate(){
	var $selected_meeting = $('#selected_meeting');
	var $coupon_name      = $('#coupon_name');
	var $number           = $('#number');
	var $price            = $('#price');
	var $start_time       = $('#start_time');
	var $end_time         = $('#end_time');
	if($selected_meeting.text() == ''){
		ManageObject.object.toast.toast("会议不能为空");
		$selected_meeting.focus();
		return false;
	}
	if($coupon_name.val() == ''){
		ManageObject.object.toast.toast("券名不能为空");
		$coupon_name.focus();
		return false;
	}
	if($price.val() == ''){
		ManageObject.object.toast.toast("价格不能为空");
		$price.focus();
		return false;
	}
	/*if($number.val() == ''){
	 ManageObject.object.toast.toast("数量不能为空");
	 $number.focus();
	 return false;
	 }*/
	if($start_time.val() == ''){
		ManageObject.object.toast.toast("开始时间不能为空");
		$start_time.focus();
		return false;
	}
	if($end_time.val() == ''){
		ManageObject.object.toast.toast("结束时间不能为空");
		$end_time.focus();
		return false;
	}
}
// 修改券限制
function checkAlter(){
	var $selected_meeting = $('#selected_meeting_a');
	var $coupon_name      = $('#coupon_name_a');
	var $price            = $('#price_a');
	var $start_time       = $('#start_time_a');
	var $end_time         = $('#end_time_a');
	if($selected_meeting.text() == ''){
		ManageObject.object.toast.toast("会议不能为空");
		$selected_meeting.focus();
		return false;
	}
	if($coupon_name.val() == ''){
		ManageObject.object.toast.toast("券名不能为空");
		$coupon_name.focus();
		return false;
	}
	if($price.val() == ''){
		ManageObject.object.toast.toast("价格不能为空");
		$price.focus();
		return false;
	}
	if($start_time.val() == ''){
		ManageObject.object.toast.toast("开始时间不能为空");
		$start_time.focus();
		return false;
	}
	if($end_time.val() == ''){
		ManageObject.object.toast.toast("结束时间不能为空");
		$end_time.focus();
		return false;
	}
}
