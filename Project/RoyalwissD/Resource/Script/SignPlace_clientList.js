/**
 * Created by qyqy on 2016-9-22.
 */
var ThisObject = {
	optTemp          :'<option value="$numOpt">$name</option>',
	tableTemp        :'<tr>\n\t<td class="check_item_excel"><input type="checkbox" class="icheck_excel" value="$id"></td>\n\t<td>$num</td><td class="excel_name" data-id="$id">$value</td>\n\t<td>\n\t\t<select name="client_info" id="" class="form-control select_h">\n\t\t\t$opt\n\t\t</select>\n\t</td>\n</tr>',
	uploadInterval   :null,
	signBntTemp      :'<a class="btn btn-default btn-sm" href="javascript:void(0)" role="button" data-id="$id">$signName</a>',
	signActiveBntTemp:'<a class="btn btn-default btn-sm active" href="javascript:void(0)" role="button" data-id="$id">$signName</a>'
};
$(function(){
	var quasar_script = document.getElementById('quasar_script');
	// 实例化Url类
	var url_object    = new Quasar.UrlClass(1, quasar_script.getAttribute('data-url-sys-param'), quasar_script.getAttribute('data-page-suffix'));
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
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 已签到客户列表
	$('.check_signed').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('signed');
		if(param == 1){
			var new_url = link.delUrlParam('signed');
			location.replace(new_url);
		}else{
			var signed_url = link.setUrlParam('signed', 1);
			location.replace(signed_url);
		}
	});
	// 未签到客户列表
	$('.check_not_signed').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('signed');
		if(param == 0){
			var new_url = link.delUrlParam('signed');
			location.replace(new_url);
		}else{
			var signed_url = link.setUrlParam('signed', 0);
			location.replace(signed_url);
		}
	});
	// 已审核客户列表
	$('.check_reviewed').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('reviewed');
		if(param == 1){
			var new_url = link.delUrlParam('reviewed');
			location.replace(new_url);
		}else{
			var reviewed_url = link.setUrlParam('reviewed', 1);
			location.replace(reviewed_url);
		}
	});
	// 未审核客户列表
	$('.check_not_reviewed').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('reviewed');
		if(param == 0){
			var new_url = link.delUrlParam('reviewed');
			location.replace(new_url);
		}else{
			var reviewed_url = link.setUrlParam('reviewed', 0);
			location.replace(reviewed_url);
		}
	});
	// 已收款客户列表
	$('.check_receivables').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('receivables');
		if(param == 1){
			var new_url = link.delUrlParam('receivables');
			location.replace(new_url);
		}else{
			var reviewed_url = link.setUrlParam('receivables', 1);
			location.replace(reviewed_url);
		}
	});
	// 未收款客户列表
	$('.check_not_receivables').find('.iCheck-helper').on('click', function(){
		var $quasar = $('#quasar_script');
		var mvc     = $quasar.attr('data-url-sys-param');
		var suffix  = $quasar.attr('data-page-suffix');
		var link    = new Quasar.UrlClass(1, mvc, suffix);
		var param   = link.getUrlParam('receivables');
		if(param == 0){
			var new_url = link.delUrlParam('receivables');
			location.replace(new_url);
		}else{
			var reviewed_url = link.setUrlParam('receivables', 0);
			location.replace(reviewed_url);
		}
	});
	//导入excel
	$('#excel_file').on('change', function(){
		ManageObject.object.loading.loading();
		$('#import_sub').trigger('click');
		ThisObject.uploadInterval = setInterval(function(){
			$('#ExcelHead').modal('show');
			getIframeData();
		}, 2000);
	});
	//审核按钮
	$('.review_btn').on('click', function(){
		var cid = $(this).parent('.btn-group').attr('data-id');
		var mid = $('body').attr('data-meeting-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'review', cid:cid, mid:mid},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
					ManageObject.object.toast.toast('审核成功', 1);
				}
			}
		});
	});
	// 取消审核按钮
	$('.anti_review_btn').on('click', function(){
		var cid = $(this).parent('.btn-group').attr('data-id');
		var mid = $('body').attr('data-meeting-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'anti_review', cid:cid, mid:mid},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
					ManageObject.object.toast.toast('取消审核成功', 1);
				}
			}
		});
	});
	// 批量审核客户
	$('.batch_review_btn_confirm').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_review_client').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_review_client').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择参会人员！');
		}
		$('#batch_review_client').find('input[name=cid]').val(newStr);
	});
	// 批量取消审核客户
	$('.batch_anti_review_btn_confirm').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_anti_review_client').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_anti_review_client').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择参会人员！');
		}
		$('#batch_anti_review_client').find('input[name=cid]').val(newStr);
	});
	// 签到按钮
	$('.sign_btn').on('click', function(){
		var $body = $('body');
		var cid   = $(this).parent('.btn-group').attr('data-id');
		var mid   = $body.attr('data-meeting-id');
		var sid   = $body.attr('data-place-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'sign', cid:cid, mid:mid, sid:sid},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
					ManageObject.object.toast.toast('签到成功', 1);
				}else{
					ManageObject.object.toast.toast(r.message, 1);
				}
			}
		});
	});
	// 取消签到按钮
	$('.anti_sign_btn').on('click', function(){
		var cid = $(this).parent('.btn-group').attr('data-id');
		var mid = $('body').attr('data-meeting-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'anti_sign', cid:cid, mid:mid},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
					ManageObject.object.toast.toast('取消签到成功', 1);
				}else{
					ManageObject.object.toast.toast(r.message, 1);
				}
			}
		});
	});
	// 批量签到
	$('.batch_sign_point').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_sign_point').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_sign_point').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择参会人员！');
		}
		$('#batch_sign_point').find('input[name=cid]').val(newStr);
	});
	// 批量取消签到
	$('.batch_anti_sign_point ').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_anti_sign_point').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_anti_sign_point').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择参会人员！');
		}
		$('#batch_anti_sign_point').find('input[name=cid]').val(newStr);
	});
	// 删除客户
	$('.delete_btn').on('click', function(){
		var id      = $(this).parent('.btn-group').attr('data-id');
		var join_id = $(this).parent('.btn-group').attr('data-join-id');
		var $object = $('#delete_client');
		$object.find('input[name=cid]').val(id);
		$object.find('input[name=join_id]').val(join_id);
	});
	// 批量删除客户
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '', str_join = '', i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id      = $(this).find('.icheck').val();
			var join_id = $(this).find('.icheck').attr('data-join-value');
			str += id+',';
			str_join += join_id+',';
			i++;
		});
		$('#batch_delete_client').find('.sAmount').text(i);
		str = str.substr(0, str.length-1);
		if(str != ''){
			$('#batch_delete_client').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择参会人员！');
		}
		str_join    = str_join.substr(0, str_join.length-1);
		var $object = $('#batch_delete_client');
		$object.find('input[name=cid]').val(str);
		$object.find('input[name=join_id]').val(str_join);
	});
	// 发送消息
	$('.send_message_btn').on('click', function(){
		var cid = $(this).parent('.btn-group').attr('data-id');
		var mid = $('body').attr('data-meeting-id');
		ManageObject.object.loading.loading();
		Common.ajax({
			data    :{requestType:'send_message', cid:cid, mid:mid},
			callback:function(r){
				ManageObject.object.loading.complete();
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
				}
				ManageObject.object.toast.toast(r.message, 1);
			}
		});
	});
	// 批量发送消息
	$('.batch_send_message_btn_confirm').on('click', function(){
		var str = '', i = 0;
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_send_message').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_send_message').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择参会人员！');
		}
		$('#batch_send_message').find('input[name=cid]').val(newStr);
	});
	// 分配签到点 (single)
	$('.alter_sign_point_btn').on('click', function(){
		var cid = $(this).parent().attr('data-id');
		var str = '';
		$('#alter_sign_place_cid').val(cid);
		Common.ajax({
			data    :{requestType:'get_assign_sign_place', cid:cid},
			callback:function(r){
				$.each(r.data, function(index, value){
					if(value.assign_status == 1){
						str += ThisObject.signActiveBntTemp.replace('$id', value.id).replace('$signName', value.name);
					}else if(value.assign_status == 0){
						str += ThisObject.signBntTemp.replace('$id', value.id).replace('$signName', value.name);
					}
				});
				$('#alter_sign_point').find('.coupon_list').html(str);
				$('#alter_sign_point .coupon_list a').on('click', function(){
					if($(this).hasClass('active')){
						$(this).removeClass('active');
					}else{
						$(this).addClass('active');
					}
					var arr = [];
					$('#alter_sign_point .coupon_list a.active').each(function(){
						var id = $(this).attr('data-id');
						arr.push(id);
					});
					$('#alter_sign_point').find('input[name=sign_place]').val(arr);
				});
			}
		});
	});
	// 分配签到点 (multi)
	$('.assign_sign_place').on('click', function(){
		var str = '';
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+','
		});
		str = str.substr(0, str.length-1);
		if(str != ''){
			$('#batch_alter_sign_point').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择参会人员！');
		}
		$('#alter_multi_sign_place_cid').val(str).attr('value', str);
	});
	$('#batch_alter_sign_point .coupon_list a').on('click', function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
		var arr = [];
		$('#batch_alter_sign_point .coupon_list a.active').each(function(){
			var id = $(this).attr('data-id');
			arr.push(id);
		});
		$('#batch_alter_sign_point').find('input[name=sign_place]').val(arr);
	});
	(function(){
		// 人员状态列表（签到\审核\收款）
		var mvc         = $('#quasar_script').attr('data-url-sys-param');
		var suffix      = $('#quasar_script').attr('data-page-suffix');
		var link        = new Quasar.UrlClass(1, mvc, suffix);
		var signed      = link.getUrlParam('signed');
		var reviewed    = link.getUrlParam('reviewed');
		var receivables = link.getUrlParam('receivables');
		if(signed == 1) $('.check_signed').find('.iradio_square-green').addClass('checked');
		if(signed == 0) $('.check_not_signed').find('.iradio_square-green').addClass('checked');
		if(reviewed == 1) $('.check_reviewed').find('.iradio_square-blue').addClass('checked');
		if(reviewed == 0) $('.check_not_reviewed').find('.iradio_square-blue').addClass('checked');
		if(receivables == 1) $('.check_receivables').find('.iradio_square-red').addClass('checked');
		if(receivables == 0) $('.check_not_receivables').find('.iradio_square-red').addClass('checked');
	})();
	// 收款
	$('.btn_receivables').on('click', function(){
		var id   = $(this).parent('.btn-group').attr('data-id');
		var name = $(this).parents('tr').find('.name').text();
		$('#add_receivables').find('#client_name').val(name);
		$('#add_receivables').find('input[name=cid]').val(id);
	});
	// 选择代金券
	$('#add_receivables .coupon_list a').on('click', function(){
		var id = $(this).attr('data-id');
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
		eachAddReceivables();
	});
});
function eachAddReceivables(){
	var arr = [];
	$('#add_receivables .coupon_list a.active').each(function(){
		var id = $(this).attr('data-id');
		arr.push(id)
	});
	$('#add_receivables').find('input[name=coupon_code]').val(arr);
}
function getIframeData(){
	var data = document.getElementById('fileUpload_iframe').contentWindow.document
		.getElementsByTagName('body')[0].innerHTML;
	if(data){
		data        = $.parseJSON(data);
		var str     = '';
		var str2    = '';
		var dbIndex = data.data.dbIndex;
		$.each(data.data.dbHead, function(index, value){
			str2 += ThisObject.optTemp.replace('$name', value.desc).replace('$numOpt', index);
		});
		$.each(data.data.head, function(index, value){
			var num = index+1;
			str += ThisObject.tableTemp.replace('$num', num).replace('$value', value).replace('$opt', str2)
				.replace('$id', index);
		});
		$('#ExcelHeadTable').html(str);
		ManageObject.object.loading.complete();
		// 遍历 Excel表头字段 和系统的对应的字段（映射）
		$('.select_h').each(function(){
			var name = $(this).parents('tr').find('.excel_name').text();
			name     = name.replace(/^\s+|\s+$/g, "");
			$(this).find('option').each(function(){
				var opt_name = $(this).text();
				opt_name     = opt_name.replace(/^\s+|\s+$/g, "");
				if(name == opt_name){
					$(this).attr("selected", true);
				}
			});
			// 将option 值 转换为数组
			var str = [];
			for(var i = 0; i<$(this).find('option').length; i++){
				str[i] = $(this).find('option').eq(i).text();
			}
			// 如果数组里面 与 所定义值 存在 --返回 0 ，若不存在 返回 -1。
			if(str.toString().indexOf(name)> -1){
			}else{
				// 若未找到则全部存到保留字段1
				$(this).find('option').eq('17').attr('selected', true);
			}
		});
		$('.icheck_excel').iCheck({
			checkboxClass:'icheckbox_square-green',
			radioClass   :'iradio_square-green'
		});
		//noinspection JSCheckFunctionSignatures
		clearInterval(ThisObject.uploadInterval);
		// 全选checkbox
		$('.all_check_excal').find('.iCheck-helper').on('click', function(){
			if($(this).parent('.icheckbox_square-green').hasClass('checked')){
				$('.check_item_excel').find('.icheckbox_square-green').addClass('checked');
			}else{
				$('.check_item_excel').find('.icheckbox_square-green').removeClass('checked');
			}
		});
		/*$('select[name=client_info]').on('change',function(){
		 $(this).find("option:selected").val();
		 });*/
		// Excel表头提交
		$('.btn_save_excel').on('click', function(){
			var str  = '';
			var str2 = '';
			$('.check_item_excel .icheckbox_square-green.checked').each(function(){
				var id  = $(this).find('.icheck_excel').val();
				str += id+',';
				var id2 = $(this).parents('tr').find("option:selected").val();
				str2 += id2+',';
			});
			var s, s2, newStr = "", newStr2 = "";
			s                 = str.charAt(str.length-1);
			if(s == ","){
				for(var i = 0; i<str.length-1; i++){
					newStr += str[i];
				}
			}
			var newStr_arr = newStr.split(',');
			s2             = str2.charAt(str2.length-1);
			if(s2 == ","){
				for(var i = 0; i<str2.length-1; i++){
					newStr2 += str2[i];
				}
			}
			var newStr_arr2 = newStr2.split(',');
			ManageObject.object.loading.loading();
			Common.ajax({
				data    :{requestType:'save_excel_data', excel:newStr, table:newStr2, dbIndex:dbIndex},
				async   :false,
				callback:function(data){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
					ManageObject.object.loading.complete();
					ManageObject.object.toast.toast(data.message);
				}
			});
		});
	}
}
// 保存选择的代金券Value
function keepCode(){
	var str = '';
	$('.number_list_box.selected .number_list a').each(function(){
		var id = $(this).attr('data-id');
		str += id+','
	});
	var s, newStr = "";
	s             = str.charAt(str.length-1);
	if(s == ","){
		for(var i = 0; i<str.length-1; i++){
			newStr += str[i];
		}
	}
	$('#receivables_modal').find('input[name=code]').val(newStr);
}
