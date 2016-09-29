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
	tableTemp:'<tr>\n\t<td class="check_item_excel"><input type="checkbox" class="icheck_excel" value="$id"></td>\n\t<td>$num</td><td data-id="$id">$value</td>\n\t<td>\n\t\t<select name="client_info" id="" class="form-control">\n\t\t\t$opt\n\t\t</select>\n\t</td>\n</tr>'
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
	$('.check_sign').find('.iCheck-helper').on('click', function(){
			var mvc    = $('#Quasar').attr('data-mvc-name');
			var suffix = $('#Quasar').attr('data-page-suffix');
			var link   = new Quasar.UrlClass(1, mvc, suffix);
			var signed = link.getUrlParam('signed');
			if(signed == 1){
				var new_url = link.delUrlParam('signed');
				location.replace(new_url);
			}else{
				var signed_url = link.setUrlParam('signed', 1, location.href, {
					except:mvc,
					suffix:suffix
				});
				location.href  = signed_url;
			}
	});
	// 已审核客户列表
	$('.check_review').find('.iCheck-helper').on('click', function(){
			var mvc          = $('#Quasar').attr('data-mvc-name');
			var suffix       = $('#Quasar').attr('data-page-suffix');
			var link         = new Quasar.UrlClass(1, mvc, suffix);
			var reviewed = link.getUrlParam('reviewed');
			if(reviewed == 1){
				var new_url = link.delUrlParam('reviewed');
				location.replace(new_url);
			}else{
				var reviewed_url = link.setUrlParam('reviewed', 1, location.href, {
					except:mvc,
					suffix:suffix
				});
				location.href    = reviewed_url;
			}
	});
	// 已收款客户列表
	$('.check_receivables').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			var mvc               = $('#Quasar').attr('data-mvc-name');
			var suffix            = $('#Quasar').attr('data-page-suffix');
			var link              = new Quasar.UrlClass(1, mvc, suffix);
			var receivablesed_url = link.setUrlParam('reviewed', 1, location.href, {
				except:mvc,
				suffix:suffix
			});
			location.href         = receivablesed_url;
		}else{
			var mvc       = $('#Quasar').attr('data-mvc-name');
			var signedUrl = link.setUrlParam('receivablesed', 1, location.href, {
				except:mvc,
				suffix:".aspx"
			});
			location.href = signedUrl;
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
		var id = $(this).parent('.btn-group').attr('data-id');
		$('input[name=id]').val(id);
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
			console.log(newStr);
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
					ManageObject.object.toast.toast(r.message, 1);
				}
			}
		});
	});
	var mvc    = $('#Quasar').attr('data-mvc-name');
	var suffix = $('#Quasar').attr('data-page-suffix');
	var link   = new Quasar.UrlClass(1, mvc, suffix);
	var signed = link.getUrlParam('signed');
	console.log(signed);
	if(signed == 1){
		$('.check_sign').find('.icheckbox_square-green').addClass('checked');
	}else{
		$('.check_sign').find('.icheckbox_square-green').removeClass('checked');
	}
	var reviewed = link.getUrlParam('reviewed');
	console.log(signed);
	if(reviewed == 1){
		$('.check_review').find('.icheckbox_square-green').addClass('checked');
	}else{
		$('.check_review').find('.icheckbox_square-green').removeClass('checked');
	}
});
function getIframeData(set){
	var data = document.getElementById('fileUpload_iframe').contentWindow.document
					   .getElementsByTagName('body')[0].innerHTML;
	if(data){
		data     = $.parseJSON(data);
		var str  = '';
		var str2 = '';
		$.each(data.data.dbHead, function(index, value){
			console.log(value.desc);
			str2 += clientManage.optTemp.replace('$name', value.desc).replace('$numOpt', index);
		});
		$.each(data.data.head, function(index, value){
			var num = index+1;
			str += clientManage.tableTemp.replace('$num', num).replace('$value', value).replace('$opt', str2)
							   .replace('$id', index);
		});
		$('#ExcelHeadTable').html(str);
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
				console.log(newStr);
			}
			var newStr_arr = newStr.split(',');
			console.log(newStr_arr);
			s2 = str2.charAt(str2.length-1);
			if(s2 == ","){
				for(var i = 0; i<str2.length-1; i++){
					newStr2 += str2[i];
				}
				console.log(newStr2);
			}
			var newStr_arr2 = newStr2.split(',');
			Common.ajax({
				data:{requestType:'save_excel_data', excel:newStr, table:newStr2}, async:false, callback:function(data){
					/*	ManageObject.object.loading.complete();*/
				}
			});
		});
	}
}
