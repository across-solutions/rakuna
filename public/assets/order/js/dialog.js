$(function() {
	canTouch = 'ontouchstart' in window;

	is_order_submitted = false;

	//mouseイベント判定
	if(window.onmousedown === null){
		canTouch = false;
	}

	if ($("img").length != 0) {
		$("img:last").bind("load", function() {
			resize_dialog();
		});
	}
	resize_dialog();

	$(".close").click(function() {
		parent.$.colorbox.close();
	});

	$(".close_reload").click(function() {
		parent.location.href = parent.location.href;
		parent.$.colorbox.close();
	});

	$(".amount").on("focus", function() {
		$(this).attr("placeholder", $(this).val());
		$(this).val("");
	});
	$(".amount").on("focusout", function() {
		newval = $(this).val();
		oldval = $(this).attr("placeholder");

		if (newval == "" || newval == oldval) {
			$(this).val(oldval);
			$(this).attr("placeholder", "");
			return false;
		}

		if (!$.isNumeric(newval)) {
			$(this).val(oldval);
			$(this).attr("placeholder", "");
			alert("数値を入力してください");
			return false;
		}

		if (newval < 0) {
			$(this).val(oldval);
			$(this).attr("placeholder", "");
			alert("0以上の数値を入力してください");
			return false;
		}

		if (newval > 99) {
			$(this).val(oldval);
			$(this).attr("placeholder", "");
			alert("99以下の数値を入力してください");
			return false;
		}

		$.ajax({
			type : "GET",
			url : $(this).attr("href") + "?amount=" + $(this).val(),
			cache : false,
			context : this,
			success : function(data) {
				if (!check_error(data)) {
					return;
				}
				data.exist ? parent.$("div.cash").css("visibility","visible") : parent.$("div.cash").css("visibility","hidden");
				parent.$("#count_item").html(number_format(data.count_item));
				parent.$("#payment_tax").html(number_format(data.payment_tax));
				parent.$("#payment").html(number_format(data.payment));
				parent.$("#tax").html(number_format(data.tax));
				parent.$("#total_amount_case").html(number_format(data.total_amount_case));
				parent.$("#total_amount").html(number_format(data.total_amount));

				parent.$("#" + $(this).closest("div").find("input.main_set").val()).val(data.amount);
			},
			error : function(data) {
				parent.location.assign("/order/login/logout")
				parent.$.colorbox.close();
			}
		});
	});

	modifyFlg = false;
	if (canTouch) {
		$(".item_modify, .item_favorite, .list-accordion").on("touchstart", function() {
			modifyFlg = true;
		});
		$(".item_modify, .item_favorite, .list-accordion").on("touchmove", function() {
			modifyFlg = false;
		});
		$(".item_modify, .item_favorite, .list-accordion").on("click", function() {
			return false;
		});
	}

	$(".item_modify").on(canTouch ? "touchend" : "click", function() {
		if (canTouch && !modifyFlg) {
			return false;
		}

		if( $(this).hasClass("plus") && $(this).closest("ul").find("input.amount").val() >= 99 ) {
			alert("数値は99以下までしか入力できません");
			return false;
		}

		$(this).addClass("waved");
		$.ajax({
			type : "GET",
			url : $(this).attr("href"),
			cache : false,
			context : this,
			success : function(data) {
				$(this).removeClass("waved");
				if (!check_error(data)) {
					return;
				}
				data.exist ? parent.$("div.cash").css("visibility","visible") : parent.$("div.cash").css("visibility","hidden");
				$(this).closest("ul").find("input.amount").val(data.amount);
				parent.$("#count_item").html(number_format(data.count_item));
				parent.$("#payment_tax").html(number_format(data.payment_tax));
				parent.$("#payment").html(number_format(data.payment));
				parent.$("#tax").html(number_format(data.tax));
				parent.$("#total_amount_case").html(number_format(data.total_amount_case));
				parent.$("#total_amount").html(number_format(data.total_amount));

				parent.$("#" + $(this).closest("div").find("input").val()).val(data.amount);
			},
			error : function(data) {
				parent.location.assign("/order/login/logout")
				parent.$.colorbox.close();
			}
		});
		return false;
	});
});

function resize_dialog() {
	parent.$.colorbox.resize({
		innerWidth: $("body").width(),
		innerHeight: $("body").height() + 40
	});
}

function number_format(num) {
	return num.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
}

/**
 * 非同期処理エラーチェック
 */
function check_error(data) {
	if (!data.error) {
		return true;
	}
	switch(data.error) {
		case "alert":
			alert(data.message);
			break;
		case "item_renewal":
			parent.location.assign("/order/information/item_renewal")
			parent.$.colorbox.close();
			break;
		case  "auth":
			alert("タイムアウトしました。再度ログインして下さい");
			parent.location.assign("/order/login/logout")
			parent.$.colorbox.close();
			break;
		case "not_found":
			parent.location.assign("/order/error/404")
			parent.$.colorbox.close();
			break;
		case "fatal":
			parent.location.assign("/order/login/logout")
			parent.$.colorbox.close();
			break;
	}

	return false;
}