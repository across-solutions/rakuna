$(function() {
	canTouch = 'ontouchstart' in window;

	is_order_submitted = false;

	//mouseイベント判定
	if(window.onmousedown === null){
		canTouch = false;
	}

	$(".lineupimage:last").bind("load", function() {
		$(".lineup").lineUp();
	}).each(function() {
		if (this.complete) {
			$(this).load();
		}
	});

	$(".trigger").on("click",function(){
		$(this).next().slideToggle();
	});

	$("#order_submit").click(function() {
		$(this).text('発注中です…');
		$(this).css("cursor","default");

		if ( is_order_submitted === false ) {
			$(this).closest('form').submit();
			is_order_submitted = true;
		}

		return false;
	});

	$(".submit").click(function() {
		$(this).closest("form").submit();
		return false;
	});

	$('form#login_form').keypress(function(ev) {
		if ((ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13)) {
			$(this).submit();
		} else {
			return true;
		}
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
			async: false,
			success : function(data) {
				if (!check_error(data)) {
					return;
				}
				data.exist ? $("div.cash").css("visibility","visible") : $("div.cash").css("visibility","hidden");
				$("#count_item").html(number_format(data.count_item));
				$("#payment_tax").html(number_format(data.payment_tax));
				$("#payment").html(number_format(data.payment));
				$("#tax").html(number_format(data.tax));
				$("#total_amount_case").html(number_format(data.total_amount_case));
				$("#total_amount").html(number_format(data.total_amount));
			},
			error : function(data) {
				$(location).attr("href", "/order/login/logout");
			}
		});
	});

	tmp_sort_num = 0;
	$(".sortNum").on("focus", function() {
		tmp_sort_num = $(this).val();
	});
	$(".sortNum").on("focusout", function() {
		newval = $(this).val();

		if (newval == "") {
			return true;
		}

		if (!$.isNumeric(newval)) {
			$(this).val(tmp_sort_num);
			$(this).attr("placeholder", "");
			alert("数値を入力してください");
			return false;
		}

		if (newval < 1) {
			$(this).val(tmp_sort_num);
			$(this).attr("placeholder", "");
			alert("1以上の数値を入力してください");
			return false;
		}

		if (newval > 99999) {
			$(this).val(tmp_sort_num);
			$(this).attr("placeholder", "");
			alert("99,999以下の数値を入力してください");
			return false;
		}

		if (!newval.match(/^[0-9]+$/)) {
			$(this).val(tmp_sort_num);
			$(this).attr("placeholder", "");
			alert("整数を入力してください");
			return false;
		}
		return true;
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
				data.exist ? $("div.cash").css("visibility","visible") : $("div.cash").css("visibility","hidden");
				$(this).closest("ul").find("input.amount").val(data.amount);
				$("#count_item").html(number_format(data.count_item));
				$("#payment_tax").html(number_format(data.payment_tax));
				$("#payment").html(number_format(data.payment));
				$("#tax").html(number_format(data.tax));
				$("#total_amount_case").html(number_format(data.total_amount_case));
				$("#total_amount").html(number_format(data.total_amount));
			},
			error : function(data) {
				$(this).removeClass("waved");
				$(location).attr("href", "/order/login/logout");
			}
		});
		return false;
	});

	$(".item_favorite").on(canTouch ? "touchend" : "click", function() {
		if (canTouch && !modifyFlg) {
			return false;
		}

		$.ajax({
			type : "GET",
			url : $(this).attr("href"),
			cache : false,
			context : this,
			success : function(data) {
				if (!check_error(data)) {
					return;
				}
				if (data.is_favorite) {
					$(this).parent().children("p.label_fav").text("星ボタンをタップでお気に入りを解除できます。");
					$(this).children("span").removeClass("icon-star-empty").addClass("icon-star");
				} else {
					$(this).parent().children("p.label_fav").text("星ボタンをタップでお気に入りに登録できます。");
					$(this).children("span").removeClass("icon-star").addClass("icon-star-empty");
				}
			},
			error : function(data) {
				$(location).attr("href", "/order/login/logout");
			}
		});
		return false;
	});

	$("#freewordSubmit").click(function() {
		location.href = create_search_url("freeword", $("#freeword").val());
		return false;
	});

	$("select#select_category").change(function() {
		location.href = create_search_url("category", $(this).val());
		return false;
	});

	$("select#select_recommended_group").change(function() {
		location.href = create_recommended_group_url(location.pathname, $(this).val());
		return false;
	});

	$(".dialog").click(function() {
		$.colorbox({
			iframe : true,
			href : $(this).attr("href"),
			closeButton : false,
			speed : 0,
			fadeOut: 0,
			opacity: 0.7,
			title : false,
			fastIframe: false,
			width:"90%",
			height:"90%",
			maxHeight:"550px",
			maxWidth:"768px"
		});
		return false;
	});

	$(".dialog_inline").colorbox({
		transition:"elastic",
		speed:0,
		fadeOut:0,
		title:false,
		opacity:0.7,
		closeButton:false,
		width:"90%",
		height:"90%",
		maxHeight:"350px",
		maxWidth:"768px",
		inline:true
	});

	$(".close").click(function() {
		$.colorbox.close();
		return false;
	});

	$(".link_close").click(function() {
		$.colorbox.close();
	});

	$("#delivery_date_unspecified").click(function() {
		$("#delivery_date_select").attr("disabled", "disabled");
		$("#delivery_date_select option:selected").attr("selected", false);
	});

	$("#delivery_date_specified").click(function() {
		$("#delivery_date_select").removeAttr("disabled");
	});

	if (location.hash != "") {
		if ($(".histNav").find("li#" + location.hash.substr(1)).size() > 0) {
			$(".histNav").find("li#" + location.hash.substr(1) + " ul").show();
		}
	}

	$(".submit_history_into_cart").click(function() {
		if (confirm("履歴と同じ商品にカートが上書きされます。よろしいですか？")) {
			$(location).attr("href", $(this).attr("href") );
		}
		return false;
	});

	$(".list-accordion").on(canTouch ? "touchend" : "click", function() {
		if (canTouch && !modifyFlg) {
			return false;
		}

		$.cookie("accordionToggle", $.cookie("accordionToggle") == "0" ? "1" : "0", {expires:365, path:"/"});
		setAccordion();
		return false;
	});

	$("select#delivery_select").on("change",function() {
		clear_delivery();
		//clear_delivery_date();

		if ($(this).val() == "") {
			return false;
		}
		$.ajax({
			type : "GET",
			cache : false,
			url : "/order/ajax/delivery/data/" + $(this).val() + ".json",
			success : function(data) {
				if (!check_error(data)) {
					return;
				}
				$("#delivery_name").val(data.name);
				$("#delivery_receiver_name1").val(data.receiver_name1);
				$("#delivery_receiver_name2").val(data.receiver_name2);
				$("#delivery_zip").val(data.zip);
				$("#delivery_address1").val(data.address1);
				$("#delivery_address2").val(data.address2);
				$("#delivery_address3").val(data.address3);
				$("#delivery_tel").val(data.tel);
				$("#delivery_fax").val(data.fax);
/*
				$("#delivery_date_select").children("option").remove();
				$.each(data.dates, function(k, val) {
					$.map(val, function(name, date) {
						$("#delivery_date_select").append($("<option>").val(date).text(name));
					});
				});
*/
			},
			error : function(data) {
				$(location).attr("href", "/order/login/logout");
			}
		});
	});

	$(".scChks").on("change",function(){
		$(".scItemDesc").removeClass("disp");
		$(this).parent().next().addClass("disp");
		$("html, body").animate({scrollTop: $(this).parent().offset().top}, 500, "swing");
		return false;
	});

	$("input[name='delivery_kind']:radio").on("change",function() {
		//clear_delivery_date();

		var val = $(this).val();
		if (val == 1) {
			clear_delivery();
			$("select#delivery_select option").attr("selected", false)

			$.ajax({
				type : "GET",
				url : "/order/ajax/member/data.json",
				cache : false,
				context : this,
				async: false,
				success : function(data) {
					if (!check_error(data)) {
						return;
					}

					$("#delivery_date_select").children("option").remove();
					$.each(data.dates, function(k, val) {
						$.map(val, function(name, date) {
							$("#delivery_date_select").append($("<option>").val(date).text(name));
						});
					});

				},
				error : function(data) {
					$(location).attr("href", "/order/login/logout");
				}
			});
		}
	});

	$("select#order_type_select").on("change",function() {
		if ($(this).val() == "") {
			$("#shipping_div_select").val("");
			$("#warehouse_div_select").val("");
			return false;
		}

		$.ajax({
			type : "GET",
			cache : false,
			url : "/order/ajax/type/data/" + $(this).val() + ".json",
			success : function(data) {
				if (!check_error(data)) {
					return;
				}
				$("#shipping_div_select").val(data.code);
				$("#warehouse_div_select").val(data.warehouse_code);
			},
			error : function(data) {
				$(location).attr("href", "/order/login/logout");
			}
		});
	});

	setAccordion();

});

function clear_delivery() {
	$("#delivery_name").val("");
	$("#delivery_receiver_name1").val("");
	$("#delivery_receiver_name2").val("");
	$("#delivery_zip").val("");
	$("#delivery_address1").val("");
	$("#delivery_address2").val("");
	$("#delivery_address3").val("");
	$("#delivery_tel").val("");
	$("#delivery_fax").val("");
}

function clear_delivery_date() {
	$("#delivery_date_select").children("option").not(":first-child").remove();
}

function setAccordion() {

	var status = $.cookie("accordionToggle");
	if (status == "0") {
		//アコーディオンを閉じる
		$(".list-accordion").parent().closest("div").removeClass("disp");
		$(".list-accordion").find("span.icon-chevron-left").removeClass("icon-chevron-left").addClass("icon-chevron-right");
	} else {
		//アコーディオンを開く
		$(".list-accordion").parent().closest("div").addClass("disp");
		$(".list-accordion").find("span.icon-chevron-right").removeClass("icon-chevron-right").addClass("icon-chevron-left");
	}
}

function create_search_url(key, value) {
	queries = [];
	replaced = false;
	$.each(location.search.substring(1).split("&"), function() {
		tmp = this.split("=");
		if (tmp.length != 2) {
			queries.push(this);
			return;
		}
		tmpkey = tmp[0];
		if (tmpkey == "page") {
			return;
		}
		if (tmpkey == key) {
			queries.push(key + "=" + encodeURIComponent(value));
			replaced = true;
		} else {
			queries.push(tmpkey + "=" + tmp[1]);
		}
	});
	if (!replaced) {
		queries.push(key + "=" + encodeURIComponent(value));
	}
	return location.pathname + "?" + queries.join("&");
}

function number_format(num) {
	return num.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
}

function create_recommended_group_url(path, val){
	var seperator = "/";
	var lastindex = path.lastIndexOf(seperator);
	if ( lastindex == '-1' ) {
	    return false;
	}
	var subpath = path.substr(0,lastindex);
	if ( subpath.length < 1 ) {
	    return false
	}
	return subpath + seperator + val;
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
			$(location).attr("href", "/order/information/item_renewal");
			break;
		case  "auth":
			alert("タイムアウトしました。再度ログインして下さい");
			$(location).attr("href", "/order/login/logout");
			break;
		case "not_found":
			$(location).attr("href", "/order/error/404");
			break;
		case "fatal":
			$(location).attr("href", "/order/login/logout");
			break;
	}

	return false;
}
