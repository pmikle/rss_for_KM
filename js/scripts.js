$(function() {
	$('#date_range').datepicker({
		range: 'period', // режим - выбор периода
		numberOfMonths: 2,
		onSelect: function(dateText, inst, extensionRange) {
			// extensionRange - объект расширения
			$('[name=startDate]').val(extensionRange.startDateText);
			$('[name=endDate]').val(extensionRange.endDateText);
		}
	});

	$('#date_range').datepicker('setDate', ['+4d', '+8d']);

	// объект расширения (хранит состояние календаря)
	var extensionRange = $('#date_range').datepicker('widget').data('datepickerExtensionRange');
	if(extensionRange.startDateText) $('[name=startDate]').val(extensionRange.startDateText);
	if(extensionRange.endDateText) $('[name=endDate]').val(extensionRange.endDateText);
 	
	$("[name=channel]").bind('change', function(){
		$.get(
			"/ajax.php",
			{
				name: $("[name=channel]").attr('name'),
				val: $("[name=channel]").val()
			},
			onAjaxSuccess
		);

		function onAjaxSuccess(data) {
			$("#pole").html(data);
		}
	});
	
	$("#dater").click(function(){

		$.get(
			"/ajax.php",
			{
				name: 'date',
				val_start: $("[name=startDate]").val(),
				val_end: $("[name=endDate]").val(),
			},
			onAjaxSuccess
		);

		function onAjaxSuccess(data) {
			$("#pole").html(data);
		}
	});

});