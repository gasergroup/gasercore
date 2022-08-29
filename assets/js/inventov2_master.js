(function($) {
	'use strict'
	$('document').ready(function() {

		// Configure axios behavior
		axios.interceptors.request.use(function(config) {
			$('.main-loader').fadeIn(100)
			return config
		}, function(error) {
			$('.main-loader').fadeOut(100)
			return Promise.reject(error)
		})

		axios.interceptors.response.use(function(response) {
			$('.main-loader').fadeOut(100)
			return response
		}, function(error) {
			$('.main-loader').fadeOut(100)
			let errorStr = (error.response
				&& error.response.data
				&& error.response.data.messages
				&& error.response.data.messages.error)
					? error.response.data.messages.error
					: _errorContent

			showError(_errorTitle, errorStr)

			return Promise.reject(error)
		})

		// Sidebar toggler on mobile devices
		$('#navbar .left a').on('click', e => {
			e.preventDefault()

			if($('#navbar').hasClass('open')) {
				$('#navbar').removeClass('open')
				$('#navbar-logo').removeClass('open')
				$('#sidebar').removeClass('open')
			}else{
				$('#navbar').addClass('open')
				$('#navbar-logo').addClass('open')
				$('#sidebar').addClass('open')
			}
		})

		// Locale switcher
		$('.dropdown.lang a').on('click', e => {
			e.preventDefault()

			let lang = $(e.currentTarget).data('locale')

			axios.post('api/locale', {
				locale: lang
			}).then(response => {
			}).then(() => location.reload())
		})

		// Sidebar dropdowns
		$('li.dropdown:not(.open) .dropdown-content').slideUp(0);
		$('li.dropdown:not(.open) a span i').removeClass('fa-caret-down').addClass('fa-caret-right')
		$('li.dropdown > a').on('click', e => {
			e.preventDefault()

			let t = $(e.currentTarget)

			if(t.children('span').children('i').hasClass('fa-caret-down')) {
				t.children('span').children('i').removeClass('fa-caret-down').addClass('fa-caret-right')
				t.next('.dropdown-content').slideUp(200)
			}else{
				t.children('span').children('i').removeClass('fa-caret-right').addClass('fa-caret-down')
				t.next('.dropdown-content').slideDown(200)
			}
		})

		// Multiple modals backdrop fix
		$(document).on('show.bs.modal', '.modal', function() {
			let zIndex = 1040 + (10 * $('.modal.show').length)
			$(this).css('z-index', zIndex)
			setTimeout(() => {
				$('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack')
			}, 0)
		})

		// Multiple modals scroll fix
		$(document).on('hidden.bs.modal', '.modal', function () {
			$('.modal.show').length && $(document.body).addClass('modal-open');
		});

		// When closing a confirmation modal, unbind buttons
		$('#confirmationModal').on('hide.bs.modal', e => {
			$('#confirmationModal button[data-confirmation=yes]').off('click')
			$('#confirmationModal button[data-confirmation=no]').off('click')
		})
	})
})(jQuery)

'use strict';

// To show an error modal
function showError(title, msg) {
	$('#errorModal .modal-header .modal-title').html(title)
	$('#errorModal .modal-body p').html(msg)
	$('#errorModal').modal('show')
}

// To show a success modal
function showSuccess(title, msg) {
	$('#successModal .modal-header .modal-title').html(title)
	$('#successModal .modal-body p').html(msg)
	$('#successModal').modal('show')
}

// To show a confirmation modal
// buttonYesCb and buttonNoCb are callbacks
function showConfirmation(title, msg, buttonYesMsg, buttonNoMsg, buttonYesCb, buttonNoCb) {
	$('#confirmationModal .modal-header .modal-title').html(title)
	$('#confirmationModal .modal-body p').html(msg)
	$('#confirmationModal button[data-confirmation=yes]').html(buttonYesMsg)
	$('#confirmationModal button[data-confirmation=no]').html(buttonNoMsg)
	$('#confirmationModal button[data-confirmation=yes]').html(buttonYesMsg)
	$('#confirmationModal button[data-confirmation=yes]').on('click', e => {
		e.preventDefault()
		if(buttonYesCb())
			$('#confirmationModal').modal('hide')
	})
	$('#confirmationModal button[data-confirmation=no]').on('click', e => {
		e.preventDefault()
		if(buttonNoCb())
			$('#confirmationModal').modal('hide')
	})
	$('#confirmationModal').modal('show')
}

class Utils {
	// If num is decimal, num will be returned as float
	// If not, 0 will be returned
	static getFloat(num) {
		if(Utils.isFloat(num) && num > 0)
			return parseFloat(num)
		return 0
	}

	// If num is integer, num will be returned as int
	// If not, 0 will be returned
	static getInt(num) {
		if(Utils.isInt(num) && num > 0)
			return parseInt(num)
		return 0
	}

	// To apply a tax (in percent) to a given amount
	static applyTax(amount, taxPercent) {
		if(taxPercent == 0) return amount
		return parseFloat(amount + (taxPercent / 100 * amount))
	}

	// To apply a discount (in percent) to a given amount
	static applyDiscount(amount, discountPercent) {
		if(discountPercent == 0) return amount
		return parseFloat(amount - (discountPercent / 100 * amount))
	}

	// To parse amount as float, and limit to 2 decimal points
	static twoDecimals(num) {
		return parseFloat(num).toFixed(2)
	}

	// To validate a float
	static isFloat(num) {
		return !isNaN(parseFloat(num)) && isFinite(num)
	}

	// To validate an integer
	static isInt(num) {
		return !isNaN(num) && parseInt(Number(num)) == num && !isNaN(parseInt(num, 10))
	}
}