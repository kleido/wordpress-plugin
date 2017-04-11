jQuery(document).ready(function($) {
	if (typeof $('.wprengine-form').val() !== 'undefined') {
		$('input[name="same_return_parking"]').on('change', function(event) {
			event.preventDefault();
			$('.return-group').toggleClass('hidden');
		});

		coordinates = jQuery('#base_coordinates').val();
		latlng = new google.maps.LatLng(coordinates);
		radius = jQuery('#base_radius').val();
		
		var changeMode = 'none';
		var hours = jQuery('#calendar_hours').val();
		hours = hours.split(",");
		hours.forEach(function(part, index, theArray) {
		  theArray[index] = [moment().hour(theArray[index]).minute(0),moment().hour(theArray[index]).minute(59)] ;
		});

		if (jQuery('#calendar_mode').val() == 'true') {
			debug = true;
		} else {
			debug = false; 
		}
		if (jQuery('#calendar_view').val() == 'true') {
			inline = true;
		} else {
			inline = false; 
		}
		if (jQuery('#slideMode').val() == 'true') {
			slideMode = true;
		} else {
			slideMode = false; 
		}

		
		if (jQuery('#placement').val() == "default") {
			 position = "auto"; 
		} else {
			position = jQuery('#placement').val() ;
		}
		var positiong = JSON.parse('{"vertical": "'+position+'"}') ;
		
		if ($('#datetimepicker1').length > 0) {
			$('.wppickupdate').each(function(index) {
				$(this).datetimepicker({
					format: 'YYYY-MM-DD HH:mm',
					widgetPositioning: positiong,
					allowInputToggle: true,
					stepping: jQuery('#calendar_step').val(),
					sideBySide: slideMode, 
					disabledTimeIntervals: hours,
					debug: debug,
					inline: inline,
					showClose: true,
				});
			});
			$('.wpreturndate').each(function(index) {
				$(this).datetimepicker({
					format: 'YYYY-MM-DD HH:mm',
					allowInputToggle: true,
					sideBySide: slideMode,
					stepping: jQuery('#calendar_step').val(),
					disabledTimeIntervals: hours,
					widgetPositioning: positiong,
					debug: debug,
					inline: inline,
					showClose: true,
					useCurrent: false //Important! See issue #1075
				});
			});
			$("#datetimepicker1").on("dp.change", function(e) {
				$('#datetimepicker2').data("DateTimePicker").minDate(e.date);
			});
			$("#datetimepicker2").on("dp.change", function(e) {
				$('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
			});
		}
	}
	if ($('.summary').length > 0 ) {
		calculateExtrasCostToFinalCost();
	}
	if ($('.extra-service').length > 0) {
		$('.extra-service').on('change', function(event) {
			calculateExtrasCostToFinalCost();
			if ($(this).data('type') == "per day") {
				extraCost = (parseInt($(this).val()) * parseInt($(this).data('cost')))*parseInt($(this).data('days'));
				text 	  = 'for ' + parseInt($(this).data('days'))+ ' days';
			} else {
				extraCost = (parseInt($(this).val()) * parseInt($(this).data('cost')));
				text 	  = 'for all rental period';
			}
			id = $(this).data('id');
			currency = jQuery('.currency').data('currency');
			type = $(this).data('type');
			$('#ex-'+id).html('+' + currency + extraCost + ' ' + text);
		});
		
	}

	if ((typeof $('.confirm-order') !== 'undefined') && (typeof $('.tos-check') !== 'undefined')) {
		$('.confirm-order').on('submit', function(event) {
			if ($('.tos-check').is(':checked')) {
				return true;
			} else {
				alert('You must check that you agree with our Terms of Service first');
				return false;
			}
		});
	}

	// $('.confirm-order').on('submit', function(event) {
	// 	if (($('.driver-wrapper input[name="fullname"]').val() !== '') && ($('.driver-wrapper input[name="email"]').val() !== '')){
	// 		return true;
	// 	} else {
	// 		alert('You must fill name and email before you confirm your order');
	// 		return false;
	// 	}
	// });

	$('.wp-rengine-search-filter input').on('change', function(event) {
		$('.wp-rengine-search-filter').submit();
	});

	$('#change_dates').on('click', function (event) {
		event.preventDefault();
		$('.search-form').toggleClass('hidden');
	});

	//Fill parking place names for predefined mode 
	jQuery('#pickup_coordinates').each(function(event) {
		jQuery('input[name="pickup_parking_name"]').val(jQuery(this).find('option:selected').text());
	});

	jQuery('#return_coordinates').each(function(event) {
		jQuery('input[name="return_parking_name"]').val(jQuery(this).find('option:selected').text());
	});

	jQuery('#pickup_coordinates').change(function(event) {
		jQuery('input[name="pickup_parking_name"]').val(jQuery(this).find('option:selected').text());
		if (jQuery('input[name="same_return_parking"]').is(":checked")) {
			jQuery('#return_parking_name').val(jQuery(this).find('option:selected').text());
			jQuery('#return_coordinates').val(jQuery(this).val()).change();
			alert(jQuery(this).find('option:selected').text());
		}
	});

	jQuery('#return_coordinates').change(function(event) {
		jQuery('input[name="return_parking_name"]').val(jQuery(this).find('option:selected').text());
	});

	//Prediction 
	jQuery('#prediction-input').change(function(event) {
		event.preventDefault();
		changeMode = 'pickup';
		if (jQuery(this).val().length === 0) {
			jQuery('.clear_input').trigger('click');
		} else {
			jQuery('.return_parking_change').show();
		}
	});

	jQuery('#prediction-input-return').change(function(event) {
		event.preventDefault();
		changeMode = 'return';
	});

	jQuery(document).on('click', '.clear_input', function(event) {
		event.preventDefault();
		jQuery('.prediction-input').each(function(event) {
			jQuery(this).val('');
			if (jQuery(this).attr('id') === 'prediction-input') {
				jQuery(this).show();
			} else {
				jQuery(this).hide();
			}
		});
		// jQuery('#prediction-input').after(jQuery(this));
		jQuery('.icon_wrapper').attr('class', 'icon_wrapper');
		jQuery('input[name="acting_username"]').val(jQuery('input[name="logged_in_user"]').val());
		jQuery('input[name="pickup_parking"]').val('');
		jQuery('input[name="pickup_parking_name"]').val('');
		jQuery('input[name="return_parking"]').val('');
		jQuery('input[name="return_parking_name"]').val('');
		jQuery('.pickup_parking_change, .return_parking_change ').hide().removeClass('fleft');
		jQuery('.bwrapper:visible').hide();
		jQuery('#prediction_results').empty();
	});

	jQuery(document).on('click touchend', '#prediction_results li', function(event) {

		var place_id = jQuery(this).data('place_id');
		var name = jQuery(this).data('name');
		var coordinates = jQuery(this).data('coordinates');
		console.log(changeMode);
		if (!jQuery('.return-group').is(":visible")) {
			jQuery("#prediction-input").val(name);
			jQuery("#prediction-input-return").val(name);
			jQuery('input[name="pickup_coordinates"]').val(coordinates);
			jQuery('input[name="pickup_parking_name"]').val(name);
			jQuery('input[name="return_coordinates"]').val(coordinates);
			jQuery('input[name="return_parking_name"]').val(name);
		} else {
			if (changeMode == 'pickup') {
				jQuery("#prediction-input").val(name);
				jQuery('input[name="pickup_coordinates"]').val(coordinates);
				jQuery('input[name="pickup_parking_name"]').val(name);
			} else if (changeMode == 'return') {
				jQuery("#prediction-input-return").val(name);
				jQuery('input[name="return_coordinates"]').val(coordinates);
				jQuery('input[name="return_parking_name"]').val(name);
			}
		} 
		
		// else {
		// 	jQuery('#prediction-input').val(name);
		// 	jQuery('input[name="pickup_coordinates"]').val(coordinates);
		// 	jQuery('input[name="return_coordinates"]').val(coordinates);

		// 	if (jQuery(this).attr('class') === undefined) {
		// 		jQuery('.icon_wrapper').removeClass('affiliate');
		// 		jQuery('.icon_wrapper').removeClass('desk');
		// 	} else {
		// 		new_class = jQuery(this).attr('class');
		// 		jQuery('.icon_wrapper').attr('class', 'icon_wrapper ' + new_class);
		// 	}

		// }

		jQuery('#prediction_results').empty();
	});

	var timeout;

	results = document.getElementById('prediction_results');
	service = new google.maps.places.AutocompleteService();

	//PlacesService = new google.maps.places.PlacesService(map);
	jQuery('.prediction-input').each(function(index, el) {
		event.preventDefault();
		google.maps.event.addDomListener(el, 'keyup', function() {
			var query = el.value;
			
			console.log(radius);

			if (!query) {	
				results.style.display = 'none';
				return;
			}
			window.clearTimeout(timeout);
			timeout = window.setTimeout(function() {
				service.getPlacePredictions({
					input: query,
					radius: radius,
					location: latlng ,
				}, predictionsCallback);
			}, 500);
		});
	});

	jQuery(document).on('click', '#getPickupLocation', function(event) {
				event.preventDefault();

		navigator.geolocation.getCurrentPosition( success, fail );
	    function success(position)
		{
			coordinates = '('+position.coords.longitude+', '+position.coords.latitude+')';
			locationName = 'Current Location';
			jQuery('input[name="pickup_coordinates"]').val(coordinates);
			jQuery('input[name="pickup_parking_name"]').val(locationName);
			jQuery('input[name="return_coordinates"]').val(coordinates);
			jQuery('input[name="return_parking_name"]').val(locationName);
			jQuery("#prediction-input").val(name);
			jQuery('#pickup_coordinates')
				.append($("<option></option>")
	                    .attr("value",coordinates)
	                    .text(locationName)); 
			jQuery('#return_coordinates')
				.append($("<option></option>")
	                    .attr("value",coordinates)
	                    .text(locationName)); 
			jQuery('#pickup_coordinates').val(coordinates).prop('selected', true);
		}

		function fail() {};
	});

	jQuery(document).on('click', '#getReturnLocation', function(event) {
				event.preventDefault();

		navigator.geolocation.getCurrentPosition( success, fail );
	    function success(position)
		{
			coordinates = '('+position.coords.longitude+', '+position.coords.latitude+')';
			locationName = 'Current Location';
			jQuery('input[name="return_coordinates"]').val(coordinates);
			jQuery('input[name="return_parking_name"]').val(locationName);
			jQuery("#prediction-input").val(name);
			jQuery('#return_coordinates')
				.append($("<option></option>")
	                    .attr("value",coordinates)
	                    .text(locationName)); 
			jQuery('#return_coordinates').val(coordinates).prop('selected', true);
		}
		function fail() {};
	});
});

function predictionsCallback(predictions, status) {
	if (status != google.maps.places.PlacesServiceStatus.OK) {
		results.style.display = 'none';
		return;
	}
	results.innerHTML = '';
	createResults(predictions);
	results.style.display = 'block';
}

function createResults(predictions) {
	for (var i = 0, prediction; prediction = predictions[i]; i++) {
		results.appendChild(createResult(prediction));
	}
}

function createResult(prediction) {
	var text = prediction.description;
	var matches = prediction.matched_substrings;
	var result = document.createElement('li');
	var pos = 0;

	for (var i = 0, match; match = matches[i]; i++) {
		result.appendChild(createSubStrNode(text, pos, match.offset));
		pos = match.offset + match.length;
		result.appendChild(createSubStrNode(text, match.offset, pos, '#000'));
	}
	result.appendChild(createSubStrNode(text, pos, text.length));
	result.setAttribute('data-place_id', prediction.place_id);
	result.setAttribute('data-name', prediction.description);
	var obj = jQuery('<div>').appendTo('body');
	PlacesService = new google.maps.places.PlacesService(obj.get(0));
	PlacesService.getDetails(prediction, function(prediction, status) {
		if (status == google.maps.places.PlacesServiceStatus.OK) {
			result.setAttribute('data-coordinates', prediction.geometry.location.toString());
		}
	});

	return result;
}

////////////////// FUNCTION AREA /////////////////////////////////////////////

// Returns a substring text node, color is optional.
function createSubStrNode(str, first, last, color) {
	var textNode = document.createTextNode(str.substring(first, last));
	if (!color) return textNode;

	var node = document.createElement('b');
	node.appendChild(textNode);
	node.style.color = color;
	return node;
}

/**
 * Return formatted address after you insert LatLng literal
 * @param  {LatLng Literal} latlng
 * @param {anonymous function}  callback function
 * @return {string}        the formatted address
 */
function codeLatLng(latlng, callback) {
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		'location': latlng
	}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[0]) {
				callback(results[0].formatted_address);
			} else {
				callback('No address found');
			}
		} else {
			callback('Geocoder Failure');
		}
	});
}

function getUrlParams() {
	var qd = {};
	location.search.substr(1).split("&").forEach(function(item) {
		var s = item.split("="),
			k = s[0],
			v = s[1] && decodeURIComponent(s[1]);
		(qd[k] = qd[k] || []).push(v); //short-circuit
	});
	return qd;
}

function calculateExtrasCostToFinalCost() {
	var cost = 0;
	var flatPrice = parseInt(jQuery('.total-cost').data('flatcost'));
	jQuery('.extra-service').each(function(index, el) {
		if (jQuery(el).data('type') == "per day") {
			cost = (parseInt(jQuery(el).find('option:selected').val()) * parseInt(jQuery(el).data('cost'))*parseInt(jQuery(el).data('days'))) + cost;
		} else {
			cost = (parseInt(jQuery(el).find('option:selected').val()) * parseInt(jQuery(el).data('cost'))) + cost;
		}
		
	});
	currency = jQuery('.currency').data('currency');
	var finalCost = flatPrice + parseInt(cost);
	jQuery('.total-cost').html(currency + finalCost);
	jQuery('#extra-cost').text(parseInt(cost));

}