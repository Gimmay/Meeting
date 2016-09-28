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
}

$(function(){
	$('#excel_file').on('change',function(){
		$('#import_sub').trigger('click');
		var set = setInterval(function(){
			$('#Excal_hide_btn').trigger('click')
			getIframeData(set);
		},2000)
	});

	$('.receivables_btn').on('click',function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('input[name=id]').val(id);
	});
	$('.review_btn').on('click', function(){
		var id  = $(this).parent('.btn-group').attr('data-id');
		var mid = $('body').attr('data-meeting-id');
		Common.ajax({
			data    :{requestType:'review', id:id, mid:mid},
			callback:function(r){
				if(r.status){
					ManageObject.object.toast.onQuasarHidden(function(){
						var url       = location.href;
						location.href = url;
					});
					ManageObject.object.toast.toast('审核成功');
				}
			}
		});
	});
});

function getIframeData(set){
	var data = document.getElementById('fileUpload_iframe').contentWindow.document
					   .getElementsByTagName('body')[0].innerHTML;
	if(data){
		console.log(data);
		var data = $.parseJSON(data);
		console.log(data);
		var str  = '';
		var str2 = '';
		$.each(data.data.dbHead, function(index, value){
			console.log(value.desc);
			str2 += clientManage.optTemp.replace('$name', value.desc).replace('$numOpt', index);
		});
		console.log(str2);
		$.each(data.data.head, function(index, value){
			var num = index+1;
			console.log(str2);
			//$('#ExcelHeadTable').append("<tr>\n\t<td class=\"check_item\"><input type=\"checkbox\" class=\"icheck_excel\"></td>\n\t<td>"+num+"</td><td>"+value+"</td>\n\t<td>\n\t\t<select name=\"client_info\" id=\"\" class=\"form-control\">\n\t\t\t<option value=\"\">姓名</option>\n\t\t\t<option value=\"\">性别</option>\n\t\t\t<option value=\"\">出生日期</option>\n\t\t\t<option value=\"\">地址</option>\n\t\t\t<option value=\"\">身份证号</option>\n\t\t\t<option value=\"\">职称</option>\n\t\t\t<option value=\"\">职务</option>\n\t\t\t<option value=\"\">手机号</option>\n\t\t\t<option value=\"\">电话号码</option>\n\t\t\t<option value=\"\">电子邮箱</option>\n\t\t\t<option value=\"\">状态</option>\n\t\t\t<option value=\"\">会所名称</option>\n\t\t\t<option value=\"\">开拓顾问</option>\n\t\t\t<option value=\"\">服务顾问</option>\n\t\t\t<option value=\"\">陪同人姓名</option>\n\t\t\t<option value=\"\">登记人ID</option>\n\t\t\t<option value=\"\">创建日期</option>\n\t\t\t<option value=\"\">备注</option>\n\t\t\t<option value=\"\">保留字段1</option>\n\t\t\t<option value=\"\">保留字段2</option>\n\t\t\t<option value=\"\">保留字段3</option>\n\t\t\t<option value=\"\">保留字段4</option>\n\t\t\t<option value=\"\">保留字段5</option>\n\t\t\t<option value=\"\">保留字段6</option>\n\t\t\t<option value=\"\">保留字段7</option>\n\t\t\t<option value=\"\">保留字段8</option>\n\t\t</select>\n\t</td>\n</tr>");
			str += clientManage.tableTemp.replace('$num', num).replace('$value', value).replace('$opt', str2)
							   .replace('$id', index);
		});
		$('#ExcelHeadTable').html(str);
		$('.icheck_excel').iCheck({
			checkboxClass:'icheckbox_square-green',
			radioClass   :'iradio_square-green'
		})
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
			var s, s2, newStr="", newStr2 = "";
			s                          = str.charAt(str.length-1);
			if(s == ","){
				for(var i = 0; i<str.length-1; i++){
					newStr += str[i];
				}
				console.log(newStr);
			}
			var newStr_arr = newStr.split(',')
			console.log(newStr_arr);
			s2 = str2.charAt(str2.length-1);
			if(s2 == ","){
				for(var i = 0; i<str2.length-1; i++){
					newStr2 += str2[i];
				}
				console.log(newStr2);
			}
			var newStr_arr2 = newStr2.split(',')
			Common.ajax({
			 data:{requestType:'save_excel_data', excel:newStr,table:newStr2}, async:false, callback:function(data){
				 /*	ManageObject.object.loading.complete();*/
				 console.log(data);
			 	}
			 });
		});
	}
};
