var escapeHtml = function(unsafe) {
	return unsafe
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#039;");
};

function API() {};

API.call = function(method, service, data, callback) {
	API.callUrl(method, apiUrl + service, data, callback);
};

API.callUrl = function(method, url, data, callback) {
	$.ajax({
		type: method,
		url: url,
		data: data,
		dataType: 'json',
		success: function(data) { callback.call(this, data); },
		error: function(data, error) { callback.call(this, { result: false, data: { errors: [{ code: 600, text: error }] } } ); }
	});
};

var mobileView = false;

$("#message-tooltip").tooltip({ placement: 'bottom'});

$('#input-date-from, #input-date-to').parent()
	.datetimepicker({
		format: 'YYYY-MM-DD HH:mm:ss'
	});

var selectedMessage;

var showResultAlert = function(type, title, message) {
	$('#result-alert-container').html(_.template($('#result-alert-template').html(), { type: type, title: title, message: message }));
};

var refreshMessage = function() {
	$('#message .panel-body').html(_.template($('#message-template').html(), {
		message: selectedMessage.data('data'),
		link: selectedMessage.find('a').attr('href')
	}));
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

$('#logs tbody').on('click', 'tr.message a', function(e) {
	e.preventDefault();

	if (selectedMessage) {
		selectedMessage.removeClass('active');
	}

	selectedMessage = $(this).closest('tr.message');

	selectedMessage.addClass('active');

	refreshMessage();
	
	if (mobileView) {
		$('html, body').animate({
			scrollTop: $("#message").offset().top
		}, 800);
	}
});

// Auto Refresh
var autoRefresh = false
	autoRefreshInterval = 0,
	autoRefreshTimeout = 0;

var setAutoRefresh = function(value) {
	autoRefresh = value;
	
	if (autoRefresh) {
		$('.next-refresh').show();

		refreshMessages();
	}
	else {
		$('.next-refresh').hide();

		clearInterval(autoRefreshInterval);
	}
};

var refreshMessages = function() {
	var loadingTime = 0;
	
	$('.next-refresh-counter').html(autoRefreshTime - loadingTime);

	clearInterval(autoRefreshInterval);
	
	autoRefreshInterval = setInterval(function() {
		loadingTime++;

		$('.next-refresh-counter').html(autoRefreshTime - loadingTime);
		
		if (loadingTime >= autoRefreshTime) {
			clearInterval(autoRefreshInterval);
			
			var inputParams = {
				all_matches_before_id: allMatchesBeforeId
			};
			
			var lastMessage = $('#content #logs tr.message:first').data('data');
			
			if (lastMessage) {
				inputParams.last_message_id = lastMessage.id;
			}

			API.callUrl('GET', autoRefreshUrl, inputParams, function(data) {
				if (data.result) {
					var newMessages = $(data.data.html);
					allMatchesBeforeId += data.data.all_matches;
					
					var newRows = newMessages.find('#logs tr.message');
					
					if (newRows.length && $('#logs tr .no-message').length) {
						$('#logs tr .no-message').closest('tr').remove();
					}

					$('#content #logs tbody').prepend(newRows);
					
					var numRows = $('#logs tr.message').length;

					if (numRows > filters.limit) {
						$('#logs tr.message').slice(-1 * (numRows - filters.limit)).remove();
					}
					
					$('#content #messages-pagination').html(newMessages.find('#messages-pagination'));
					
					if (autoRefresh) {
						refreshMessages();
					}
				}
				else {
					showResultAlert('danger', 'Warning!', data.data.errors[0].text);
				}
			});
		}
	}, 1000);
};

$('#auto-refresh').on('change', function(e) {
	setAutoRefresh($('#auto-refresh').is(':checked'));
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
