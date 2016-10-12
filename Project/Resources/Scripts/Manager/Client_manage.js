/**
 * Created by qyqy on 2016-9-22.
 */
/*
 //第五种方法
 var idTmr;
 function  getExplorer() {
 var explorer = window.navigator.userAgent ;
 //ie
 if (explorer.indexOf("MSIE") >= 0) {
 return 'ie';
 }
 //firefox
 else if (explorer.indexOf("Firefox") >= 0) {
 return 'Firefox';
 }
 //Chrome
 else if(explorer.indexOf("Chrome") >= 0){
 return 'Chrome';
 }
 //Opera
 else if(explorer.indexOf("Opera") >= 0){
 return 'Opera';
 }
 //Safari
 else if(explorer.indexOf("Safari") >= 0){
 return 'Safari';
 }
 }
 function method5(tableid) {
 if(getExplorer()=='ie')
 {
 var curTbl = document.getElementById(tableid);
 var oXL = new ActiveXObject("Excel.Application");
 var oWB = oXL.Workbooks.Add();
 var xlsheet = oWB.Worksheets(1);
 var sel = document.body.createTextRange();
 sel.moveToElementText(curTbl);
 sel.select();
 sel.execCommand("Copy");
 xlsheet.Paste();
 oXL.Visible = true;

 try {
 var fname = oXL.Application.GetSaveAsFilename("Excel.xls", "Excel Spreadsheets (*.xls), *.xls");
 } catch (e) {
 print("Nested catch caught " + e);
 } finally {
 oWB.SaveAs(fname);
 oWB.Close(savechanges = false);
 oXL.Quit();
 oXL = null;
 idTmr = window.setInterval("Cleanup();", 1);
 }

 }
 else
 {
 tableToExcel(tableid)
 }
 }
 function Cleanup() {
 window.clearInterval(idTmr);
 CollectGarbage();
 }
 var tableToExcel = (function() {
 var uri = 'data:application/vnd.ms-excel;base64,',
 template = '<html><head><meta charset="UTF-8"></head><body><table>{table}</table></body></html>',
 base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) },
 format = function(s, c) {
 return s.replace(/{(\w+)}/g,
 function(m, p) { return c[p]; }) }
 return function(table, name) {
 if (!table.nodeType) table = document.getElementById(table)
 var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
 window.location.href = uri + base64(format(template, ctx))
 }
 })()*/
var clientManage = {
	optTemp  :'<option value="$numOpt">$name</option>',
	tableTemp:'<tr>\n\t<td class="check_item_excel"><input type="checkbox" class="icheck_excel" value="$id"></td>\n\t<td>$num</td><td class="excel_name" data-id="$id">$value</td>\n\t<td>\n\t\t<select name="client_info" id="" class="form-control select_h">\n\t\t\t$opt\n\t\t</select>\n\t</td>\n</tr>'
};
$(function(){
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
		var $quasar = $('#Quasar');
		var mvc     = $quasar.attr('data-mvc-name');
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
		var $quasar = $('#Quasar');
		var mvc     = $quasar.attr('data-mvc-name');
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
		var $quasar = $('#Quasar');
		var mvc     = $quasar.attr('data-mvc-name');
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
		var $quasar = $('#Quasar');
		var mvc     = $quasar.attr('data-mvc-name');
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
		var $quasar = $('#Quasar');
		var mvc     = $quasar.attr('data-mvc-name');
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
		var $quasar = $('#Quasar');
		var mvc     = $quasar.attr('data-mvc-name');
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
		$('#import_sub').trigger('click');
		var set = setInterval(function(){
			$('#Excal_hide_btn').trigger('click');
			getIframeData(set);
		}, 2000)
	});
	// 收款按钮
	$('.receivables_btn').on('click', function(){
		var name = $(this).parents('tr').find('.name').text();
		var id   = $(this).parent('.btn-group').attr('data-id');
		$('#receivables_modal').find('input[name=id]').val(id);
		$('#receivables_modal').find('input[name=name]').val(name);
	});
	//审核按钮
	$('.review_btn').on('click', function(){
		var id  = $(this).parent('.btn-group').attr('data-id');
		var mid = $('body').attr('data-meeting-id');
		Common.ajax({
			data    :{requestType:'review', id:id, mid:mid},
			callback:function(r){
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
		var id  = $(this).parent('.btn-group').attr('data-id');
		var mid = $('body').attr('data-meeting-id');
		Common.ajax({
			data    :{requestType:'anti_review', id:id, mid:mid},
			callback:function(r){
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
		$('#batch_review_client').find('input[name=id]').val(newStr);
	});
	// 批量取消审核客户
	$('.batch_anti_review_btn_confirm').on('click', function(){
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
		$('#batch_anti_review_client').find('input[name=id]').val(newStr);
	});
	// 签到按钮
	$('.sign_btn').on('click', function(){
		var $body = $('body');
		var id    = $(this).parent('.btn-group').attr('data-id');
		var mid   = $body.attr('data-meeting-id');
		var sid   = $body.attr('data-place-id');
		Common.ajax({
			data    :{requestType:'sign', id:id, mid:mid, sid:sid},
			callback:function(r){
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
		var id  = $(this).parent('.btn-group').attr('data-id');
		var mid = $('body').attr('data-meeting-id');
		Common.ajax({
			data    :{requestType:'anti_sign', id:id, mid:mid},
			callback:function(r){
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
		$('#batch_sign_point').find('input[name=id]').val(newStr);
	});
	// 批量取消签到
	$('.batch_anti_sign_point ').on('click', function(){
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
		$('#batch_anti_sign_point').find('input[name=id]').val(newStr);
	});
	// 删除客户
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_client').find('input[name=id]').val(id);
	});
	// 批量删除客户
	$('.batch_delete_btn_confirm').on('click', function(){
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
		$('#batch_delete_client').find('input[name=id]').val(newStr);
	});
	// 发送消息
	$('.send_message_btn').on('click', function(){
		var id  = $(this).parent('.btn-group').attr('data-id');
		var mid = $('body').attr('data-meeting-id');
		Common.ajax({
			data    :{requestType:'send_message', id:id, mid:mid},
			callback:function(r){
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						location.reload(true);
					});
					ManageObject.object.toast.toast('发送消息成功', 1);
				}else{
					ManageObject.object.toast.toast('发送消息失败', 1);
				}
			}
		});
	});
	// 批量发送消息
	$('.batch_send_message_btn_confirm').on('click', function(){
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
		$('#batch_send_message').find('input[name=id]').val(newStr);
	});
	// 修改签到点 (single)
	$('.alter_sign_point_btn').on('click', function(){
		var cid = $(this).parent().attr('data-id');
		$('#alter_sign_place_cid').val(cid).attr('value', cid);
	});
	// 修改签到点 (multi)
	$('.assign_sign_place').on('click', function(){
		var str = '';
		$('.check_item .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+','
		});
		str = str.substr(0, str.length-1);
		$('#alter_multi_sign_place_cid').val(str).attr('value', str);
	});
	// 人员状态列表（签到\审核\收款）
	var mvc         = $('#Quasar').attr('data-mvc-name');
	var suffix      = $('#Quasar').attr('data-page-suffix');
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
});
function getIframeData(set){
	var data = document.getElementById('fileUpload_iframe').contentWindow.document
					   .getElementsByTagName('body')[0].innerHTML;
	if(data){
		data        = $.parseJSON(data);
		var str     = '';
		var str2    = '';
		var dbIndex = data.data.dbIndex;
		$.each(data.data.dbHead, function(index, value){
			str2 += clientManage.optTemp.replace('$name', value.desc).replace('$numOpt', index);
		});
		$.each(data.data.head, function(index, value){
			var num = index+1;
			str += clientManage.tableTemp.replace('$num', num).replace('$value', value).replace('$opt', str2)
							   .replace('$id', index);
		});
		$('#ExcelHeadTable').html(str);
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
				$(this).find('option').eq('15').attr('selected', true);
			}
		});
		$('.icheck_excel').iCheck({
			checkboxClass:'icheckbox_square-green',
			radioClass   :'iradio_square-green'
		});
		clearInterval(set);
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
			Common.ajax({
				data    :{requestType:'save_excel_data', excel:newStr, table:newStr2, dbIndex:dbIndex},
				async   :false,
				callback:function(data){
					ManageObject.object.loading.complete();
					console.log(data);
				}
			});
		});
	}
}



