/**
 * Created by 1195 on 2016-10-25.
 */


$(function(){
	// 全选checkbox
	$('.all_check').find('.iCheck-helper').on('click', function(){
		if($(this).parent('.icheckbox_square-green').hasClass('checked')){
			$('.check_item').find('.icheckbox_square-green').addClass('checked');
		}else{
			$('.check_item').find('.icheckbox_square-green').removeClass('checked');
		}
	});
	// 单个删除
	$('.delete_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#delete_hotel ').find('input[name=id]').val(id);
	});
	// 批量删除
	$('.batch_delete_btn_confirm').on('click', function(){
		var str = '';
		var i   = 0;
		$('.check_item  .icheckbox_square-green.checked').each(function(){
			var id = $(this).find('.icheck').val();
			str += id+',';
			i++;
		});
		$('#batch_delete_hotel').find('.sAmount').text(i);
		var s, newStr = "";
		s             = str.charAt(str.length-1);
		if(s == ","){
			for(var i = 0; i<str.length-1; i++){
				newStr += str[i];
			}
		}
		if(newStr != ''){
			$('#batch_delete_hotel').modal('show')
		}else{
			ManageObject.object.toast.toast('请选择！');
		}
		$('#batch_delete_hotel').find('input[name=id]').val(newStr);
	});
	// 修改
	$('.alter_btn').on('click', function(){
		var id = $(this).parent('.btn-group').attr('data-id');
		$('#alter_hotel ').find('input[name=id]').val(id);
		var hotel_name = $(this).parents('tr').find('.hotel_name').text();
		$('#alter_hotel').find('#hotel_name_a').val(hotel_name);
	});
})