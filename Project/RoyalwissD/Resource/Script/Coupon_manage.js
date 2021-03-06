/**
 * Created by qyqy on 2016-10-8.
 */
var couponManage = {
	liTemp:'<li class="coupon_number_item"><span>$number</span></li>',
	num   :0
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
			couponManage.num = 0;
		}
		if(index == 1){
			$('#create_coupon').find('input[name=requestType]').val('batch_create');
			couponManage.num = 1;
		}
		console.log(couponManage.num);
	});
	$('.coupon_area').on('change', function(){
		var id = $(this).val();
		$('#create_coupon').find('input[name=coupon_area]').val(id);
	});
	// 修改代金券
	$('.modify_btn').on('click', function(){
		var id         = $(this).parent('.btn-group').attr('data-id');
		var name       = $(this).parents('tr').find('.name').text();
		var type       = $(this).parents('tr').find('.type').text();
		var price      = $(this).parents('tr').find('.price').text();
		var start_time = $(this).parents('tr').find('.start_time').text();
		var end_time   = $(this).parents('tr').find('.end_time').text();
		var comment    = $(this).parents('tr').find('.comment').text();
		$('#modify_coupon').find('input[name=id]').val(id);
		$('#modify_coupon').find('input[name=name]').val(name);
		$('#modify_coupon').find('input[name=price]').val(price);
		$('#modify_coupon').find('#coupon_type_a').find('option').each(function(){
			console.log($(this).text()+''+type);
			if(Common.Trim($(this).text()) == Common.Trim(type)){
				$(this).prop('selected', true);
			}
		});
		$('#modify_coupon').find('input[name=start_time]').val(start_time);
		$('#modify_coupon').find('input[name=end_time]').val(end_time);
		$('#modify_coupon').find('textarea[name=comment]').val(comment);
	});
	// 自动批量获取卡号
	$('.auto_get_number').on('click', function(){
		try{
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
			$('#create_coupon').find('input[name=coupon_area]').val(newStr);
		}catch(error){
			console.log(error);
			ManageObject.object.toast.toast('批量创建代金券参数错误，请重新填写！');
		}
	});
	// 创建代金券
});
//  num传入的数字，n需要的字符长度
function PrefixInteger(num, n){
	return (Array(n).join(0)+num).slice(-n);
}
// 新增代金券限制
function checkCreate(){
	var $coupon_name   = $('#coupon_name');
	var $number        = $('#number');
	var $price         = $('#price');
	var $start_time    = $('#start_time');
	var $end_time      = $('#end_time');
	var $coupon_number = $('input[name=coupon_area]');
	if($coupon_name.val() == ''){
		ManageObject.object.toast.toast("项目名称不能为空");
		$coupon_name.focus();
		return false;
	}
	if($price.val() == ''){
		ManageObject.object.toast.toast("价格不能为空");
		$price.focus();
		return false;
	}
	return true;
}
// 修改代金券限制
function checkAlter(){
	var $selected_meeting = $('#selected_meeting_a');
	var $coupon_name      = $('#coupon_name_a');
	var $price            = $('#price_a');
	var $start_time       = $('#start_time_a');
	var $end_time         = $('#end_time_a');
	if($coupon_name.val() == ''){
		ManageObject.object.toast.toast("项目名称不能为空");
		$coupon_name.focus();
		return false;
	}
	if($price.val() == ''){
		ManageObject.object.toast.toast("价格不能为空");
		$price.focus();
		return false;
	}
	return true;
}
