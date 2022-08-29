$(document).ready(function() {
	/**** Header waypoints ****/
	$('#main-top').waypoint(function(direction) {
		if(direction == 'down')
			$('#header').addClass('shrink');
		else
			$('#header').removeClass('shrink');
	}, {offset:'0px'});


	/**** Sidebar ****/
	$('li.closed').children('ul').slideUp(1);

	$('li.closed > a, li.opened > a').click(function(evt) {
		evt.preventDefault();

		var t = this;

		if($(this).children('i').hasClass('fa-caret-left'))
			$(t).children('i').removeClass('fa-caret-left').addClass('fa-caret-down');
		else if($(this).children('i').hasClass('fa-caret-down'))
			$(t).children('i').removeClass('fa-caret-down').addClass('fa-caret-left');

		$(this).parent().children('ul').slideToggle(200);
	});

	$('#main-container nav#sidebar li a').click(function(evt) {
		var elem = $(this).attr('data-scrollto')
		if(elem !== undefined) {
			evt.preventDefault();

			$('html, body').animate({
				scrollTop: $('section#section-' + elem).offset().top - 60
			}, 500);
		}
	});

	// $('button#top').fadeOut(0);
	$('#main-top').waypoint(function(direction) {
		if(direction == 'down')
			fadeInCss('button#top', 1000);
		else
			fadeOutCss('button#top', 1000);
	}, { offset: '-100px' });

	$('button#top').click(function(evt) {
		evt.preventDefault();

		$('html, body').animate({
			scrollTop: 0
		}, 500);
	})

	function fadeOutCss(elem) {
		$(elem).css('visibility', 'hidden');
		$(elem).removeClass('shown');
	}

	function fadeInCss(elem) {
		$(elem).css('visibility', 'visible');
		$(elem).addClass('shown');
	}


	/**** Functions highlighter ****/
	var codeElements = $('code.docHighlighter');

	var varReg = /(\$.*?)(,|\s)/g;
	var characterReg = /(,|\(|\))/g;
	var booleanReg = /(false|true)/g;
	var stringReg = /(("|').*?("|'))/g;

	codeElements.each(function() {
		var string = this.innerHTML;
		parsed = string.replace(stringReg, '<span class="code-string">$1</span>');
		parsed = parsed.replace(varReg, '<span class="code-var">$1</span>$2');
		parsed = parsed.replace(characterReg, '<span class="code-characters">$1</span>');
		parsed = parsed.replace(booleanReg, '<span class="code-boolean">$1</span>');

		this.innerHTML = parsed;
	});
});