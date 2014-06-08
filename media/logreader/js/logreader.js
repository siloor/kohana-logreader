var mobileView = false;

$("#message-tooltip").tooltip({ placement: 'bottom'});

$('#input-date-from, #input-date-to')
	.datepicker({ format: 'yyyy-mm-dd' })
	.on('changeDate', function(e) {
		$(this).datepicker('hide');
	});

var selectedMessage;

var refreshMessage = function() {
	var message = selectedMessage.data('data');
	
	var messageText = '<h4>' + message.type + '</h4>';
	messageText += '<p><span class="label label-' + message.style + '">' + message.level + '</span></p>';
	messageText += '<p><strong>Date:</strong> ' + message.date + ' ' + message.time + '</p>';
	messageText += '<p><strong>Message:</strong> ' + message.message + '</p>';
	messageText += '<p><strong>File:</strong> ' + message.file + '</p>';

	if (message.trace.length) {
		messageText += '<p><strong>Trace:</strong>';

		for (var i in message.trace) {
			messageText += '<br /><strong>' + i + ':</strong> ' + message.trace[i];
		}

		messageText += '</p>';
	}

	messageText += '<p><strong>Raw:</strong> ' + message.raw + '</p>';

	$('#message .panel-body').html(messageText);
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

$(window).trigger('resize');
