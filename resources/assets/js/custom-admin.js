jQuery(document).ready(function($) {
	$(document).on('click', '.parking-row-add', function(event) {
		event.preventDefault();
		var parkingGroup = $(this).parents('.parking-group');
		var parkingGroupClone = parkingGroup.clone();
		parkingGroupClone.find('input').each(function(index, el) {
			var inputName = $(this).attr('name');
			if (inputName.search('name') > -1) {
				$(this).siblings('label').attr('for', 'parking_name[' + ($('.parking-group').length + 1) + ']');
				$(this).attr('name', 'parking_name[' + ($('.parking-group').length + 1) + ']');
			}
			if (inputName.search('coordinates') > -1) {
				$(this).siblings('label').attr('for', 'parking_coordinates[' + ($('.parking-group').length + 1) + ']');
				$(this).attr('name', 'parking_coordinates[' + ($('.parking-group').length + 1) + ']');
			}
		});
		parkingGroupClone.insertAfter(parkingGroup);
	});

	$(document).on('click', '.parking-row-del', function(event) {
		event.preventDefault();
		$(this).parents('.parking-group').remove();
	});

	$(document).on('click', '#paypal', function(event) {
		$('.paypal-settings').toggle();
		$('.stripe-settings').hide();
		$('.payzen-settings').hide();
		$('.viva-settings').hide();
		$('.alpha-settings').hide();
		var checked = $("#alpha").is(":checked");
		if (checked) { 
			$("#alpha").attr("checked",false);
		}
		var checked = $("#stripe").is(":checked");
		if (checked) { 
			$("#stripe").attr("checked",false);
		}
		var checked2 = $("#payzen").is(":checked");
		if (checked2) { 
			$("#payzen").attr("checked",false);
		}
		var checked3 = $("#viva").is(":checked");
		if (checked3) { 
			$("#viva").attr("checked",false);
		}
		if ($(this).is(":checked")) {
			$('#wpRengineGateway').val('paypal');
		} else {
			$('#wpRengineGateway').val('false');
		}
		
	});

	$(document).on('click', '#alpha', function(event) {
		$('.paypal-settings').hide();
		$('.alpha-settings').toggle();
		$('.stripe-settings').hide();
		$('.payzen-settings').hide();
		$('.viva-settings').hide();
		var checked = $("#stripe").is(":checked");
		if (checked) { 
			$("#stripe").attr("checked",false);
		}
		var checked = $("#paypal").is(":checked");
		if (checked) { 
			$("#paypal").attr("checked",false);
		}
		var checked2 = $("#payzen").is(":checked");
		if (checked2) { 
			$("#payzen").attr("checked",false);
		}
		var checked3 = $("#viva").is(":checked");
		if (checked3) { 
			$("#viva").attr("checked",false);
		}
		if ($(this).is(":checked")) {
			$('#wpRengineGateway').val('alpha');
		} else {
			$('#wpRengineGateway').val('false');
		}
		
	});

	$(document).on('click', '#stripe', function(event) {
		$('.stripe-settings').toggle();
		$('.paypal-settings').hide();
		$('.payzen-settings').hide();
		$('.viva-settings').hide();
		$('.alpha-settings').hide();
		var checked = $("#alpha").is(":checked");
		if (checked) { 
			$("#alpha").attr("checked",false);
		}
		var checked = $("#paypal").is(":checked");
		if (checked) { 
			$("#paypal").attr("checked",false);
		}
		var checked2 = $("#payzen").is(":checked");
		if (checked2) { 
			$("#payzen").attr("checked",false);
		}
		var checked3 = $("#viva").is(":checked");
		if (checked3) { 
			$("#viva").attr("checked",false);
		}
		if ($(this).is(":checked")) {
			$('#wpRengineGateway').val('stripe');
		} else {
			$('#wpRengineGateway').val('false');
		}

	});

	$(document).on('click', '#payzen', function(event) {
		$('.payzen-settings').toggle();
		$('.paypal-settings').hide();
		$('.stripe-settings').hide();
		$('.viva-settings').hide();
		$('.alpha-settings').hide();
		var checked = $("#alpha").is(":checked");
		if (checked) { 
			$("#alpha").attr("checked",false);
		}
		var checked = $("#stripe").is(":checked");
		if (checked) { 
			$("#stripe").attr("checked",false);
		}
		var checked2 = $("#paypal").is(":checked");
		if (checked2) { 
			$("#paypal").attr("checked",false);
		}
		var checked3 = $("#viva").is(":checked");
		if (checked3) { 
			$("#viva").attr("checked",false);
		}
		if ($(this).is(":checked")) {
			$('#wpRengineGateway').val('payzen');
		}  else {
			$('#wpRengineGateway').val('false');
		}

	});

	$(document).on('click', '#viva', function(event) {
		$('.viva-settings').toggle();
		$('.paypal-settings').hide();
		$('.stripe-settings').hide();
		$('.payzen-settings').hide();
		$('.alpha-settings').hide();
		var checked = $("#alpha").is(":checked");
		if (checked) { 
			$("#alpha").attr("checked",false);
		}
		var checked = $("#stripe").is(":checked");
		if (checked) { 
			$("#stripe").attr("checked",false);
		}
		var checked2 = $("#paypal").is(":checked");
		if (checked2) { 
			$("#paypal").attr("checked",false);
		}
		var checked3 = $("#payzen").is(":checked");
		if (checked3) { 
			$("#payzen").attr("checked",false);
		}
		if ($(this).is(":checked")) {
			$('#wpRengineGateway').val('viva');
		} else {
			$('#wpRengineGateway').val('false');
		}

	});

		$(document).on('click', '#cash', function(event) {
		$('.cash-settings').toggle();
	
		if ($(this).is(":checked")) {
			$('#wpRengineCash').val('on');
		} else {
			$('#wpRengineCash').val('off');
		}

	});


});