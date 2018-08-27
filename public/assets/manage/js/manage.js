$(function() {
	$(".submit").click(function() {
		$(this).closest("form").submit();
		return false;
	});

	$("form#login_form").keypress(function(ev) {
		if ((ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13)) {
			$(this).submit();
		} else {
			return true;
		}
	});

	$(".bulk_delete").click(function(event) {
		if ($("input[name='delete_id[]']:checked").length == 0) {
			alert("削除したいデータをチェックしてください");
			event.stopImmediatePropagation();
			return false;
		}
	});

	$(".under_construction").click(function() {
		alert("工事中");
		return false;
	});

	$(".submit_csv_confirm").click(function() {
		if (confirm("MOS標準フォーマットに戻しますがよろしいですか？")) {
			if ($(this).attr("href") != "#") {
				$(this).closest("form").attr("action", $(this).attr("href"));
			}
			$(this).closest("form").submit();
		}
		return false;
	});

	$(".submit_img_confirm").click(function() {
		if (confirm("デフォルト画像に戻しますがよろしいですか？")) {
			if ($(this).attr("href") != "#") {
				$(this).closest("form").attr("action", $(this).attr("href"));
			}
			$(this).closest("form").submit();
		}
		return false;
	});
//	$(".submit_button_with_confirm").click(function() {
//		if (confirm($(this).attr("confirm_message"))) {
//			if ($(this).attr("href") != "#") {
//				$(this).closest("form").attr("action", $(this).attr("href"));
//			}
//			$(this).closest("form").submit();
//		}
//		return false;
//	});


	$(".bulk_id_mail").click(function(event) {
		if ($("input[id='mail_flg_id']:checked").length == 0) {
			alert("メールを送信したい発注者をチェックしてください");
			event.stopImmediatePropagation();
			return false;
		}
	});

	$('th input:checkbox').change(function(){
		if ($(this).is(':checked')) {
			$(this).parents('table').find("input:checkbox").prop('checked', true);
		}
		else {
			$(this).parents('table').find("input:checkbox").prop('checked', false);
		}
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
			width : "100px",
			height : "100px"
		});
		return false;
	});

	$(".openDatePicker").click(function() {
		$(this).parent().children(".datepicker").datepicker("show");
	});

	$.datepicker.setDefaults({
		dateFormat:"yy-mm-dd",
		yearSuffix:"年",
		showMonthAfterYear:true,
		monthNames:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"],
		dayNames:["日","月","火","水","木","金","土"],
		dayNamesMin:["日","月","火","水","木","金","土"],
		minDate:"2015-01-01",
		hideIfNoPrevNext:true,
	});

	$(".date_range_picker").each(function(index, wrap_el){

		var $range_wrap_el = $(wrap_el);

		$range_wrap_el.find('.datepicker').datepicker({

			beforeShow:function(input) {
				var year = $(this).parent().children(".year").val();
				var month = $(this).parent().children(".month").val();
				var day = $(this).parent().children(".day").val();
				var date = new Date(year, month-1, day);
				if (date.getFullYear() == year && date.getMonth() == month-1 && date.getDate() == day) {
					$(this).datepicker('setDate', date);
				} else {
					$(this).datepicker('setDate', new Date());
				}
			},
			onSelect:function(date) {
				var vals = date.split("-");
				if (vals.length == 3) {
					$(this).parent().children(".year").val(vals[0]);
					$(this).parent().children(".month").val(vals[1]);
					$(this).parent().children(".day").val(vals[2]);
				}

				var set_vals_if_empty = function (el, vals){
					var is_empty = el.find(".year").val() == '' &&
						el.find(".month").val() == '' &&
						el.find(".day").val() == '';

					if(is_empty){
						el.find(".year").val(vals[0]);
						el.find(".month").val(vals[1]);
						el.find(".day").val(vals[2]);
					}
				};

				if($(this).parent().hasClass('range_start_date')){
					var $end_el = $range_wrap_el.find('.range_end_date');
					set_vals_if_empty($end_el, vals);
				} else {
					var $start_el = $range_wrap_el.find('.range_start_date');
					set_vals_if_empty($start_el, vals);
				}
			}
		});

		$range_wrap_el.find('.range_start_date .clear_button').click(function() {
			$el = $range_wrap_el.find('.range_start_date');
			$el.find(".year").val('----');
			$el.find(".month").val('--');
			$el.find(".day").val('--');
			return false;
		});

		$range_wrap_el.find('.range_end_date .clear_button').click(function() {
			$el = $range_wrap_el.find('.range_end_date');
			$el.find(".year").val('----');
			$el.find(".month").val('--');
			$el.find(".day").val('--');
			return false;
		});
	});

	// CSV設定
	$("div.sortWrap .empty li").draggable({
		connectToSortable: ".to",
		helper:"clone"
	});
	$("div.sortWrap .from").sortable({
		connectWith: ".to",
		placeholder: "placeholder"
	});
	$("div.sortWrap .to").sortable({
		connectWith: ".sortItem",
		placeholder: "placeholder"
	});
	$(".sortItem").sortable({
		cancel:"*",
		placeholder: "placeholder2",
		update: function(event, ui) {
			if (ui.item.hasClass("no")) {
				$("div.sortWrap .to").sortable("cancel");
				alert("必須項目は外せません");
				return;
			}
			if (ui.item.hasClass("empty_field")) {
				ui.item.remove();
				return;
			}
			ui.item.appendTo($("div.sortWrap .from"));
		}
	});

	// 1行おきに色を付ける（初期表示）
	$(".stripe tr:nth-child(odd)").addClass("odd");
	$(".stripe tr:nth-child(even)").addClass("even");

	// マウスが乗った行に色を付ける
	$(".stripe tr").mouseover(function() {
			$(this).addClass("over");
		}).mouseout(function() {
			$(this).removeClass("over");
  	});


	// 選択された行に色を付け､再度選択されたら解除
	$(".stripe tr").click(function() {

    	if ($(this).hasClass("selected")) {
      		$(this).removeClass("selected");
    	} else {
			//ほかの行は選択解除
			$("tr.selected").removeClass("selected");

			$(this).addClass("selected");
		}
	});

	$(".select_calendar_all_day").on('change', function () {
		var select_day_el = $(this);
		var checked = select_day_el.prop('checked');

		$('input[name="calendar[]"]').each(function(){
			var date = new Date($(this).val());
			if(date.getDay() == select_day_el.val()){
				$(this).prop('checked', checked);
			}
		});
	});

	$(document).on('click', ".new_order_delete_row", function(e){
		e.preventDefault();

		var ret = confirm( "本当に削除しますか？" );
		if(ret){
			$(this).closest('tr').remove();
		}
		return false;
	});
});

function number_format(num) {
	return num.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
}
