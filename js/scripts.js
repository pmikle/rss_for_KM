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
	
	$("[name=startDate]").click(function(){
		$("#date_range").show();
	});
	$("[name=endDate]").click(function(){
		$("#date_range").show();
	});	
	$("#close_date").click(function(){
		$("#date_range").hide();
	});
	$("#dater").click(function(){
		$("#date_range").hide();
		$.get(
			"/ajax.php",
			{
				val_start: $("[name=startDate]").val(),
				val_end: $("[name=endDate]").val(),
				rss_channel: $("[name=channel]").val()
			},
			onAjaxSuccess
		);

		function onAjaxSuccess(data) {
			$.when(
				$("#pole").html(data)
			).then(function(){ 
				update_obr () 
			});
		}
	});
	function update_obr () {
		
		$("[name=lp]").click(function(){
			console.log("123123123");
			nid = $(this).attr("nid");
			$.get(
				"/ajax.php",
				{
					nid: nid
				},
				onAjaxSuccess2
			);
			function onAjaxSuccess2(data) {
				
				data = jQuery.parseJSON(data);
				nid = data['nid'];
				likes = data['likes'];
				if (likes < 10) { 
					$("[name=lp][nid="+nid+"]").html(likes+'<img src=\"css/images/like.png\" class=\"likes cll\" nid=\"'+nid+'\">');
				} else {
					$.get(
						"/ajax.php",
						{
							update: true
						},
						onAjaxSuccess3
					);			
					function onAjaxSuccess3(data) {
						$.when(
							$("#dater").click()
						).then(function(){ 
							update_obr () 
						});
						
					}
				}
			}
		});
	}
	update_obr ();
});
















