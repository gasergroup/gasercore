'use strict'
class Validator {
	constructor() {
		// Array with objects -- { element type, element name, validation type, regex, cb, val, msg }
		this.validations = [];

		this.regexEmailAddress = /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/
	}

	// To add a validation of an input
	// elementName -- The name of the element
	// validation -- Type of validation, one of:
	//	non-empty, integer, optional-integer, decimal
	// msg -- Message to show if the input is invalid
	addInputText(elementName, validation, msg) {
		let regex = /^.*$/
		let validationType = 'regex'

		if(validation == 'non-empty')
			regex = /^.+$/
		else if(validation == 'integer')
			regex = /^\d+$/
		else if(validation == 'optional-integer')
			regex = /^\d*$/
		else if(validation == 'decimal')
			regex = /^\d+(?:\.\d+)?$/
		else if(validation == 'optional-decimal')
			regex = /^\d*(?:\.\d+)?$/
		else if(validation == 'email-address')
			regex = this.regexEmailAddress
		else if(validation == 'optional-email-address')
			validationType = 'optionalEmailAddress'

		this.validations.push({
			elementType: 'inputText',
			elementName: elementName,
			validationType: validationType,
			regex: regex,
			cb: null,
			val: null,
			msg: msg
		})
	}

	// To add a validation of an input, with a custom value
	// elementName -- The name of the element
	// validation -- Type of validation, one of:
	//	minValue, maxValue, minLength, maxLength
	// val -- Value
	// msg -- Mesage to show if the input is invalid
	addInputTextVal(elementName, validation, val, msg) {
		this.validations.push({
			elementType: 'inputText',
			elementName: elementName,
			validationType: validation,
			regex: null,
			cb: null,
			val: val,
			msg: msg
		})
	}

	// To add a validation of an input, with a custom process
	// elementName -- The name of the element
	// cb -- Function to call to execute the custom validation.
	//	Must return true/false
	// msg -- Message to show if the validation failed
	addInputTextCustom(elementName, cb, msg) {
		this.validations.push({
			elementType: 'inputText',
			elementName: elementName,
			validationType: 'custom',
			regex: null,
			cb: cb,
			val: null,
			msg: msg
		})
	}

	// To add a validation of a select
	// elementName -- The name of the select
	// validation -- Type of validation, one of:
	//	selected
	// msg -- Message to show if the validation failed
	addSelect(elementName, validation, msg) {
		this.validations.push({
			elementType: 'select',
			elementName: elementName,
			validationType: validation,
			regex: null,
			cb: null,
			val: null,
			msg: msg
		})
	}

	// To run the validation
	validate() {
		let isValid = true

		// Array of invalid inputs and selected
		let invalidInputTexts = []
		let invalidSelects = []

		this.validations.forEach(validation => {
			if(validation.elementType == 'inputText') {
				// Validate only if it's not invalid yet
				if(!invalidInputTexts.includes(validation.elementName)) {
					let isCurrentValid = true
					let element = $(`input[name='${validation.elementName}']`)
					let value = element.val()
					element.parent().children('.invalid-feedback').text(validation.msg)

					if(validation.validationType == 'regex')
						isCurrentValid = this._validateInputTextRegex(validation, element, value)
					else if(validation.validationType == 'custom')
						isCurrentValid = this._validateInputTextCustom(validation, element, value)
					else if(validation.validationType == 'minValue')
						isCurrentValid = this._validateInputMinValue(validation, element, value)
					else if(validation.validationType == 'maxValue')
						isCurrentValid = this._validateInputMaxValue(validation, element, value)
					else if(validation.validationType == 'minLength')
						isCurrentValid = this._validateInputMinLength(validation, element, value)
					else if(validation.validationType == 'maxLength')
						isCurrentValid = this._validateInputMaxLength(validation, element, value)
					else if(validation.validationType == 'optionalEmailAddress')
						isCurrentValid = this._validateOptionalEmailAddress(validation, element, value)

					isValid = isCurrentValid && isValid

					// If the input was invalid, add it to the list of invalid inputs
					if(!isCurrentValid)
						invalidInputTexts.push(validation.elementName)
				}

			}else if(validation.elementType == 'select') {
				// Validate only if it's not invalid yet
				if(!invalidSelects.includes(validation.elementName)) {
					let isCurrentValid = true
					let element = $(`select[name='${validation.elementName}']`)
					let value = element.val()
					element.parent().children('.invalid-feedback').text(validation.msg)

					if(validation.validationType == 'selected')
						isCurrentValid = this._validateSelectSelected(validation, element, value)

					isValid = isCurrentValid && isValid

					// If the select was invalid, add it to the list of invalid selects
					if(!isCurrentValid)
						invalidSelects.push(validation.elementName)
				}

			}else{
				isValid = false
			}
		})

		return isValid
	}

	_validateInputTextRegex(validation, element, value) {
		if(!validation.regex.test(value)) {
			element.addClass('is-invalid')
			return false
		}else{
			element.removeClass('is-invalid')
			return true
		}
	}

	_validateInputTextCustom(validation, element, value) {
		if(!validation.cb(value)) {
			element.addClass('is-invalid')
			return false
		}else{
			element.removeClass('is-invalid')
			return true
		}
	}

	_validateInputMinValue(validation, element, value) {
		if(value < validation.val) {
			element.addClass('is-invalid')
			return false
		}else{
			element.removeClass('is-invalid')
			return true
		}
	}
	_validateInputMaxValue(validation, element, value) {
		if(value > validation.val) {
			element.addClass('is-invalid')
			return false
		}else{
			element.removeClass('is-invalid')
			return true
		}
	}

	_validateInputMinLength(validation, element, value) {
		if(value.length < validation.val) {
			element.addClass('is-invalid')
			return false
		}else{
			element.removeClass('is-invalid')
			return true
		}
	}

	_validateInputMaxLength(validation, element, value) {
		if(value.length > validation.val) {
			element.addClass('is-invalid')
			return false
		}else{
			element.removeClass('is-invalid')
			return true
		}
	}

	_validateSelectSelected(validation, element, value) {
		if(value == null || value == '' || value == 0) {
			element.addClass('is-invalid')
			return false
		}else{
			element.removeClass('is-invalid')
			return true
		}
	}

	_validateOptionalEmailAddress(validation, element, value) {
		if(value == '' || this.regexEmailAddress.test(value)) {
			element.removeClass('is-invalid')
			return true
		}else{
			element.addClass('is-invalid')
			return false
		}
	}
}