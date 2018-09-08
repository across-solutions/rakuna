$(function() {
	canTouch = 'ontouchstart' in window;

	$(".submit").click(function() {
		form = $(this);
		if (form.hasClass("overlap")) {
			return false;
		}
		form.closest("form").submit();
		form.addClass("overlap");
		setTimeout(function() {
			if (form.hasClass("overlap")) {
				form.removeClass("overlap");
			}
		}, 5000);
		return false;
	});

	$('form#login_form').keypress(function(ev) {
		if ((ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13)) {
			$(this).submit();
		} else {
			return true;
		}
	});
});