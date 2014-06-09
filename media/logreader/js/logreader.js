function API() {};

API.call = function(method, service, data, callback) {
	$.ajax({
		type: method,
		url: apiUrl + service,
		data: data,
		dataType: 'json',
		success: function(data) { callback.call(this, data); },
		error: function(data, error) { callback.call(this, { result: false, data: { errors: [{ code: 600, text: error }] } } ); }
	});
};

var mobileView = false;

$("#message-tooltip").tooltip({ placement: 'bottom'});

$('#input-date-from, #input-date-to')
	.datepicker({ format: 'yyyy-mm-dd' })
	.on('changeDate', function(e) {
		$(this).datepicker('hide');
	});

var selectedMessage;

var showResultAlert = function(type, title, message) {
	$('#result-alert-container').html(_.template($('#result-alert-template').html(), { type: type, title: title, message: message }));
};

var refreshMessage = function() {
	$('#message .panel-body').html(_.template($('#message-template').html(), { message: selectedMessage.data('data') }));
};

$(window).on('scroll', function(e) {
	if ($(this).scrollTop() > 50) {
		$('#message-container').addClass('fixed');
	}
	else {
		$('#message-container').removeClass('fixed');
	}
});

$(window).on('resize', function(e) {
	mobileView = $(window).width() < 750;
	
	var panelMaxHeight = mobileView ? 'none' : ($(window).height() - 130) + 'px';
	
	$('#message .panel-body').css('max-height', panelMaxHeight);
});

$('#logs tbody').on('click', 'tr.message', function(e) {
	if (selectedMessage) {
		selectedMessage.removeClass('active');
	}

	selectedMessage = $(this);

	selectedMessage.addClass('active');

	refreshMessage();
	
	if (mobileView) {
		$('html, body').animate({
			scrollTop: $("#message").offset().top
		}, 800);
	}
});

$('#create-test-message-btn').on('click', function(e) {
	API.call('POST', 'create_test_message', {}, function(data) {
		if (data.result) {
			showResultAlert('success', 'Success', 'Test message created. <a href="' + baseUrl + '" class="alert-link">Click here to get today\'s messages!</a>');
		}
		else {
			showResultAlert('danger', 'Warning!', data.data.errors[0].text);
		}
	});
});

$(window).trigger('resize');
