$(function() {

	is_submitted = false;

	if ($("img").length != 0) {
		$("img:last").bind("load", function() {
			resize_dialog();
		});
	}
	resize_dialog();
	$(".dialog").show();

	$("div.information_title").click(function(){
		$(this).nextAll(".information_contents").slideToggle(0, function(){

			parent.$.colorbox.resize({
				innerWidth: $("body").width(),
				innerHeight: $("body").height() + 10
			});
		});
	});

	$(".submit").click(function() {
		$(this).text('処理中です…');
		$(this).css("cursor", "default");

		if ($(this).attr("href") != "#") {
			$(this).closest("form").attr("action", $(this).attr("href"));
		}

		if (is_submitted === false) {
			$(this).closest("form").submit();
			is_submitted = true;
		}
		return false;
	});

	$(".submit_delete").click(function() {
		if (!window.confirm("削除してよろしいですか？")) {
			return false;
		}
		if ($(this).attr("href") != "#") {
			$(this).closest("form").attr("action", $(this).attr("href"));
		}
		$(this).closest("form").submit();
		return false;
	});

	$(".submit_bulk_delete").click(function() {
		form = $(this).closest("form");
		parent.$('input[name="delete_id[]"]:checked').map(function() {
			$("<input/>", {
				type:"hidden",
				name:"delete_id[]",
				value:$(this).val()
			}).appendTo(form);
		});
		$(this).closest("form").submit();
		return false;
	});

	$(".submit_bulk_id_mail_send").click(function() {
		form = $(this).closest("form");

		$(parent.document).find('input[name*="mail_flg_id"]:checked').map(function() {
			$("<input/>", {
				type:"hidden",
				name:"mail_flg_id[]",
				value:$(this).val()
			}).appendTo(form);
		});
		$(this).closest("form").submit();
		return false;
	});

	$(".close").click(function() {
		parent.$.colorbox.close();
	});

	$(".close_reload").click(function() {
		parent.location.href = parent.location.href;
		parent.$.colorbox.close();
	});

	$('form#login_form').keypress(function(ev) {
		if ((ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13)) {
			$(this).submit();
		} else {
			return true;
		}
	});

	// 1行おきに色を付ける（初期表示）
	$(".stripe tr:nth-child(odd)").addClass("odd");
	$(".stripe tr:nth-child(even)").addClass("even");

	$(".edit_order_add_item").click(function(){

		var $error_el = $(".new_order_add_item_error");
		$error_el.empty();

		var item_code = $("#order_add_item_code").val();
		var order_id = $("input[name=id]").val();

		if(item_code == ''){
			return false;
		}

		$.ajax({
			type : "GET",
			url : '/manage/ajax/item/info_for_edit_order.json',
			data : {
				'item_code' : item_code,
				'order_id' : order_id
			},
			success : function (data){

				if(data.error){
					$error_el.text('商品が見つかりませんでした。');
					return;
				}

				var item = data.item;
				var item_id = item.id.toString();

				if($(".digResultList").find("#item-" + item_id).length > 0){
					var message = 'すでに同じ商品が含まれています。[商品コード:' + item.code + ', 商品名:'+ item.name + ']';
					$error_el.text(message);
					return;
				}

				$(".dialog").hide();

				var html = $("<div/>").html(data.html).text(); //HTMLアンエスケープ
				$(".digResultList tbody").append(html);
				$(".digList").scrollTop($(".digList").height()+1000);

				resize_dialog();
				$(".dialog").show();

			},
			error: function(xhr, textStatus, errorThrown){
				alert('エラーが発生しました。[' + textStatus + ']');
			}
		});

		return false;
	});

});

function resize_dialog() {
	parent.$.colorbox.resize({
		innerWidth: $("body").width(),
		innerHeight: $("body").height() + 10
	});
}
