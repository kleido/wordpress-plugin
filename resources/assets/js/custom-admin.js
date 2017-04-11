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
});