jQuery(document).ready(function($) {

  $(".pickup_range").each(function(el) {
  	$(this).flatpickr({
	    altInput: true,
	    enableTime: true,
	    mode: "range",
	    allowInput:true,
	    minDate:"today",
	  });
  });

    $(".pickup_date_range").each(function(el) {
  	$(this).flatpickr({
	    altInput: true,
	    // enableTime: true,
	    mode: "range",
	    allowInput:true,
	    minDate:"today",
	  });
  });

$(".pickup_date_time").flatpickr({
    altInput: true,
    enableTime: true,
  });

$(".pickup_date").flatpickr({
    allowInput:true,
    enableTime: false,
  });

 flatpickr('#pickup_time', {
    enableTime: true,
    noCalendar:true,
    dateFormat:"H:i",
    time_24hr: true,
     allowInput:true,
    onChange: function (selectedDates) {
          jQuery("#return_time").val(jQuery('#pickup_time').val());
      }
    // allowInput:true
});

flatpickr('#return_time', {
    enableTime: true,
    noCalendar:true,
    dateFormat:"H:i",
    time_24hr: true,
    allowInput:true
});

	//Prediction 
jQuery('#pickup_date').change(function(event) {
	jQuery('#return_date').val(jQuery('#pickup_date').val());
	jQuery('#return_date').next('input').val(jQuery('#pickup_date').val());
});

	$('.day-cell a').on('touchend click', function(event) {
			event.preventDefault();
			// Change the start date -------------
			if ($('input[name="pickup_datetime"]').length > 0 ){
				var old_pickup_time =  $('input[name="pickup_datetime"]').val();
				old_return_time = $('input[name="return_datetime"]').val();
			}
			if ($('.pickup_range').length > 0 ){
				var date_time =  $('.pickup_range').val();
				var date_time_arr = date_time.split(" to ");
				old_pickup_time = date_time_arr[0];
				old_return_time = date_time_arr[1];

			}
			var date = new Date(old_pickup_time);
			var hours = date.getHours();
			var minutes = date.getMinutes();
			if (hours<10) { hours = '0'+hours; }
			if (minutes<10) { minutes = '0'+minutes; }
			var numberOfDaysToAdd =  $(this).data("days");
			var new_date_time = $(this).data("date")+ ' ' + hours + ':'+ minutes;
			$('input[name="pickup_datetime"]').val(new_date_time);
			if ($('.pickup_range').length > 0 ){
				$('.pickup_range').val(new_date_time);
			}
			// Change the end date -------------
			var date = new Date(new_date_time);
			var new_end_date = date.setDate(date.getDate() + numberOfDaysToAdd); 
			var date = new Date(old_return_time);
			var hours = date.getHours();
			var minutes = date.getMinutes();
			if (hours<10) { hours = '0'+hours; }
			if (minutes<10) { minutes = '0'+minutes; }
			console.log(old_pickup_time);
			var newDate = new Date(new_end_date);
			var new_end_str = newDate.toISOString().split('T')[0];
			var new_date_time = new_end_str + ' ' + hours + ':'+ minutes;
			$('input[name="return_datetime"]').val(new_date_time);
			if ($('.pickup_range').length > 0 ){
				$('.pickup_range').val($('.pickup_range').val() + ' to ' + new_date_time);
			}
			$('.wprengine-form').submit();
		});

	$('.timeCell').on('touchend click', function (event) {
		event.preventDefault();
		service = $(this).data('service-id');
		
		if ($(this).hasClass('ui-selectee')) {
            $(this).addClass("ui-selected");
            $('.service-'+service+" .timeCell").siblings().removeClass("ui-selectee");
            $(this).next('.timeCell').addClass("ui-selectee");
            total = total + $(this).data('value');
            end = $(this).data('selectedendtime');
          } else {
          	start = $(this).data('selectedtime');
            $('.service-'+service+" .timeCell").siblings().removeClass("ui-selected");
            $(this).addClass("ui-selected");
            $(this).next('.timeCell').addClass("ui-selectee");
            $('.total-price-modal ').text(''); 
            total =  $(this).data('value');
            end = $(this).data('selectedendtime');

          }

          var slotsArray = [];
          var slotsNo = 0; 
          $('.service-'+service+' .ui-selected').each(function(e) {
            slotsNo = slotsNo + 1;
            slotsArray.push($(this).data('selectedtime'))
          });

          var self2 = $('.service-'+service+' .book-button') ;
          if ($('.service-'+service+' .service-total').val()) {
            newTotal = $('.service-'+service+' .service-total').val() ;
          } else {
            newTotal = total;
          }
          console.log(self2);
          $('.service-'+service+' .price-interval-block').removeClass('hidden');
          $('.service-'+service+' .service-unit').text($(this).data('unit'));
          $('.service-'+service+' .service-total').text('');
		  $('.service-'+service+' .book-button').attr('data-service-id', service); 
		  $('.service-'+service+' .book-button').attr('data-selectedrategroup', $(this).data('selectedrategroup')); 
		  $('.service-'+service+' .book-button').attr('data-end', end);
		  $('.service-'+service+' .book-button').attr('data-start', start);
		  $('.service-'+service+' .book-button').attr('data-date', $(this).data('selecteddate'));
		  $('.service-'+service+' .book-button').attr('data-selected-slots',JSON.stringify(slotsArray));
          var data2 = {
                "id"            : service ,
                "start"         : self2.data('date'),
                "end"           : self2.data('date'),
                "spots"         : self2.data('spots'),
                'filters'       : self2.data('filters'),
                'slotsNo'       : slotsNo,
                'rategroup'     : self2.data('selectedrategroup'),
                'slotsArray'    : slotsArray, 
                'total'         : newTotal 
            };

           $.when(ajax2()).done(function(a1){});
           function ajax2() {
              return $.ajax({
                  url: 'https://app.rese.io/api/offers' ,
                  data: {myData:JSON.stringify(data2)},
                  dataType:"json",
                  cache: false,
                  type: "POST",
                   success: function(json) { 

                    $('#slots-'+self2.data("date")).val(JSON.stringify(slotsArray));
                    $('#rategroup-id-'+self2.data("date")).val(json.rategroup);
                    $('#slotValue-'+self2.data("date")).val(self2.data('encrypt-value'));

                    if (json.appliedOffers) {
                      total = json.appliedOffers.new_value;
                      // $('#total-'+self.data("date")).val($appliedOffers );
                    } 
                    $('.service-'+service+' .book-button').attr('data-total', total);
                    $('.service-'+service+' .service-total').text(total);
                }
              });
            }
	});
	$('.price-interval-block .book-button').on('touchend click', function (event) {
		event.preventDefault();
		href = $(this).attr('href');
		href = href+'&'+'service-id='+$(this).data('service_id')+'&'+'selectedRategroup='+$(this).data('selectedrategroup')+'&'+'selectedTime='+$(this).data('start')+'&'+'selectedEndTime='+$(this).data('end')+'&'+'selectedDate='+$(this).data('date')+'&'+'totalPrice='+$(this).data('total')+'&'+'selectedSlots='+$(this).data('selected-slots');
		$(this).attr('href', href);
		$(location).attr('href', href);
	});
	var changeMode = 'none';

	if (typeof $('.wprengine-form').val() !== 'undefined') {
		$('.return-group').each(function(){
			if ($(this).data('type') != 'transfers') {
				$(this).addClass('hidden');
			} else {
				changeMode = 'pickup';
			}
			
		});
		$('.wprengine-results-form').hide();
		$('input[name="same_return_parking"]').on('change', function(event) {
			event.preventDefault();
			form = $(this).closest('form');
			// $('.return-group').toggleClass('invisible');
			form.find('.return-group').toggleClass('hidden');
		});

		if (typeof google === 'object' && typeof google.maps === 'object') {
			coordinates = jQuery('#base_coordinates').val();
			latlng = new google.maps.LatLng(coordinates);
			radius = jQuery('#base_radius').val();
		}
		
		if ( jQuery('#calendar_hours').lenght > 0) {
			var hours = jQuery('#calendar_hours').val();
			hours = hours.split(",");
			hours.forEach(function(part, index, theArray) {
			  theArray[index] = [moment().hour(theArray[index]).minute(0),moment().hour(theArray[index]).minute(59)] ;
			});
		}

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
				if ($(this).data('slotsno') > 1 ) {
					extraCost = (parseFloat($(this).val()) * parseFloat($(this).data('cost')))*parseInt($(this).data('slotsno'));
				} else {
					extraCost = (parseFloat($(this).val()) * parseFloat($(this).data('cost')))*parseInt($(this).data('days'));
				}
				text 	  = '';
			} else {
				extraCost = (parseFloat($(this).val()) * parseFloat($(this).data('cost')));
				text 	  = '';
			}
			id = $(this).data('id');
			currency = jQuery('.currency').text();
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
		$('.wprengine-results-form').toggle();
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
		console.log(jQuery(this).find('option:selected').text());
		if (jQuery('input[name="same_return_parking"]').is(":checked")) {
			jQuery('#return_parking_name').val(jQuery(this).find('option:selected').text());
			jQuery('#return_coordinates').val(jQuery(this).val()).change();
			// alert(jQuery(this).find('option:selected').text());
		}
	});

	jQuery('#return_coordinates').change(function(event) {
		jQuery('input[name="return_parking_name"]').val(jQuery(this).find('option:selected').text());
	});

	//Prediction 
	jQuery('#prediction-input').change(function(event) {
		changeMode = 'pickup';
		if (jQuery(this).val().length === 0) {
			jQuery('.clear_input').trigger('click');
		} else {
			jQuery('.return_parking_change').show();
		}
	});
	$('form').each(function(el){
			$(this).find('#prediction-input-return').change(function(event) {
			event.preventDefault();
			changeMode = 'return';
		});
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
		form = $(this).closest('form');

		if (!form.find('.return-group').is(":visible")) {
			form.find("#prediction-input").val(name);
			form.find("#prediction-input-return").val(name);
			form.find('input[name="pickup_coordinates"]').val(coordinates);
			form.find('input[name="pickup_parking_name"]').val(name);
			form.find('input[name="return_coordinates"]').val(coordinates);
			form.find('input[name="return_parking_name"]').val(name);
		} else {
			if (changeMode == 'pickup') {
				form.find("#prediction-input").val(name);
				form.find('input[name="pickup_coordinates"]').val(coordinates);
				form.find('input[name="pickup_parking_name"]').val(name);
			} else if (changeMode == 'return') {
				form.find("#prediction-input-return").val(name);
				form.find('input[name="return_coordinates"]').val(coordinates);
				form.find('input[name="return_parking_name"]').val(name);
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

		form.find('#prediction_results').empty();
	});

	var timeout;
	var isMapApiLoaded = false;

	window.mapsCallback = function() {
		console.log('Google callback for maps has started');
		isMapApiLoaded = true;
		if ($('#prediction_results').length > 0 ) {
			results = document.getElementById('prediction_results');
			if (results != null) {
				service = new google.maps.places.AutocompleteService();
			}
			coordinates = jQuery('#base_coordinates').val();
			latlng = new google.maps.LatLng(coordinates);
			if (jQuery('#base_radius').lenght > 0){
				radius = jQuery('#base_radius').val();
			} else {
				radius = 10000;
			}
			
			jQuery(document).ready(function($) {
					$('.prediction-input').each(function(index, el) {
					// el.preventDefault();
					$("#prediction_results").css("border", "1px solid #eee");
					google.maps.event.addDomListener(el, 'keyup', function() {
						var query = el.value;
						// console.log(radius);
						var form = $(this).closest('form');
						element = $(this).attr('id');
						if (element == 'prediction-input-return') {
							changeMode = 'return';
						}
						results = form.find('#prediction_results')[0];
						pos = $(el).offset().top;
						form.find('#prediction_results').offset({top: pos+50});
						posleft = $(el).offset().left;
						form.find('#prediction_results').offset({left: posleft});
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
			});
		}
	};

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

	$('.book-button').on('click', function() {
	    var $this = $(this);
	    var initialText = $this.html();
	    jQuery('input[name="cashProcess"]').val(0);
     	$('.confirm-order').submit();
    	$this.attr('disabled', 'disabled').html($this.data('loading-text'));
     	setTimeout(function () {
            $this.removeAttr('disabled').html(initialText);
        }, 8000);
	});

	$('.search-button1').on('click', function() {
	    var $this = $(this);
	    var initialText = $this.html();
     	$this.attr('disabled', 'disabled').html($this.data('loading-text'));
     	$(this).closest("form").submit();
     	$('#wp-rengine-loader').removeClass('hidden');
     	// setTimeout(function () {
      //       $this.removeAttr('disabled').html(initialText);
      //   }, 8000);
	});

	$('.button-cash').on('click', function(event) { 
		event.preventDefault();
		jQuery('input[name="cashProcess"]').val(1);
		var $this = $(this);
     	$this.attr('disabled', 'disabled').html($this.data('loading-text'));
     	$('.confirm-order').submit();
     	setTimeout(function () {
            $this.removeAttr('disabled').html($this.data('loading-text'));
        }, 8000);
		
	});

	$('.more-hours').on('touchend click', function(event) {
		var target = $(this).data('target');
		target = target.replace("#collapsehrs-","");
		$('#'+target).addClass('hidden');
	});
});

jQuery(document).on('click', '.control', function(event) {
    event.preventDefault();
    var amountControl = jQuery(this).siblings('.amount-control');
    var calcPriceAmount = amountControl.data('calc');
    var calcPrice = jQuery(this).siblings('.extras-price').find('span');
    var currentVal = parseInt(amountControl.val(), 10);
    var extra = amountControl.data('reflect');
    var description = jQuery("#row-"+extra).find('.extra-name').text();
    if (jQuery(this).hasClass('minus-control') && (currentVal > 0)) {
      amountControl.val(currentVal - 1);
      amountControl.trigger('change');
      extraTotal = (amountControl.val() * calcPriceAmount);
      if (extraTotal == 0 ){
        jQuery('#'+extra).remove();
      } 
     
    } else {
      jQuery('#'+extra).remove();
    }
    if (jQuery(this).hasClass('plus-control')) {
      amountControl.val(currentVal + 1);
      amountControl.trigger('change');

      extraTotal = (amountControl.val() * calcPriceAmount);
      jQuery('#extras-section').append('<div class="extra-item" id="'+extra+'""></div>');
     
    }
   
  });

jQuery(document).on('click touchend', '#masterPass', function(event) {
	jQuery('#digest').val(jQuery('#digest2').val());
});

jQuery(document).on('click touchend', '#creditCard', function(event) {
	jQuery('#digest').val(jQuery('#digest1').val());
});

jQuery(document).on('click touchend', '#payment_cash', function(event) {
	jQuery('#cash-button').removeClass('hidden');
	jQuery('#gateway-button').addClass('hidden');
});

jQuery(document).on('click touchend', '.pay-gateway', function(event) {
	jQuery('#cash-button').addClass('hidden');
	jQuery('#gateway-button').removeClass('hidden');

});

jQuery(document).on('click touchend', '.pay-gateway', function(event) {
	jQuery('#cash-button').addClass('hidden');
	jQuery('#gateway-button').removeClass('hidden');
	
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
	var flatPrice = parseFloat(jQuery('.total-cost').data('flatcost'));
	jQuery('.extra-service').each(function(index, el) {
		if (jQuery(el).data('type') == "per day") {
			if (jQuery(el).data('slotsno') > 1 ) {
				cost = (parseFloat(jQuery(el).find('option:selected').val()) * parseFloat(jQuery(el).data('cost'))*parseInt(jQuery(el).data('slotsno'))) + cost;
			} else {
				cost = (parseFloat(jQuery(el).find('option:selected').val()) * parseFloat(jQuery(el).data('cost'))*parseInt(jQuery(el).data('days'))) + cost;
			}
		} else {
			cost = (parseFloat(jQuery(el).find('option:selected').val()) * parseFloat(jQuery(el).data('cost'))) + cost;
		}
		
	});
	var currency = jQuery('#totalCostId').data('currency');
	var percentage = jQuery('#percentage').text();
	var finalCost = flatPrice + parseFloat(cost);
	console.log(cost);
	jQuery('.total-cost').html(currency + finalCost);
	jQuery('#extra-cost').text(parseFloat(cost));
	
	if (percentage > 0 ) {
		jQuery('.pay-now').html(currency + parseFloat(finalCost*percentage/100).toFixed(2));
	}


}