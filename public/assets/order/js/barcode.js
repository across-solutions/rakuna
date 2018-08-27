$(function() {
	$("#readStart").click(function() {
		$(this).hide();
		$("header").hide();
		$(".boxPadding").hide();
		$("footer").hide();
		$(".codeMsg").show();
		$("#barcodeList").show();
		$("#barcode").focus();
	});

	$("#barcodeList").find("a").click(function() {
		$("#barcode").focus();
	});

	barcode_urlbase = "/order/ajax/barcode/item/";
	$("#count_type").click(function() {
		if ($(this).text() == "バラ") {
			$(this).text("ケース");
			barcode_urlbase = "/order/ajax/barcode/item_case/";
		} else {
			$(this).text("バラ");
			barcode_urlbase = "/order/ajax/barcode/item/";
		}
		return false;
	});

	$("#barcode").keypress(function(e) {
		if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
			if ($(this).val() == "") {
				return false;
			}
			$.ajax({
				type : "GET",
				url : barcode_urlbase + $(this).val() + ".json",
				cache : false,
				context : this,
				success : function(data) {
					if (!check_error(data)) {
						return;
					}
					if (data.id == "not_found") {
						$(".codeButton").hide();
						$(".itemName").hide();
						alert("商品が登録されていません");
						$(this).val("");
						return;
					}
					data.exist ? $("div.cash").css("visibility","visible") : $("div.cash").css("visibility","hidden");

					$(".codeButton").show();
					$(".itemName").show();
					$("#case_count").find(".plus").attr("href", "/order/ajax/cart/plus_case/" + data.id + ".json");
					$("#case_count").find(".minus").attr("href", "/order/ajax/cart/minus_case/" + data.id + ".json");
					$("#case_count").find(".del").attr("href", "/order/ajax/cart/del_case/" + data.id + ".json");
					$("#case_count").find(".amount").attr("href", "/order/ajax/cart/update_case/" + data.id + ".json");
					$("#case_count").find(".amount").val(data.amount_case);
					$("#bara_count").find(".plus").attr("href", "/order/ajax/cart/plus/" + data.id + ".json");
					$("#bara_count").find(".minus").attr("href", "/order/ajax/cart/minus/" + data.id + ".json");
					$("#bara_count").find(".del").attr("href", "/order/ajax/cart/del/" + data.id + ".json");
					$("#bara_count").find(".amount").attr("href", "/order/ajax/cart/update/" + data.id + ".json");
					$("#bara_count").find(".amount").val(data.amount);
					$("#item_name").text(data.name);
					$("#item_image").attr("src", data.img);

					$(".codeMsg").hide();

					$(this).val("");
				},
				error : function(data) {
					$(location).attr("href", "/order/login/logout");
				}
			});
			return false;
		}
	});
});

$(document).on("cbox_closed", function() {
	if ($("#barcode").size() > 0) {
		$("#readStart").show();
		$("#barcodeList").hide();
	}
});