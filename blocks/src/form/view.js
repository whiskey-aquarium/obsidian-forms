import TomSelect from 'tom-select';
import validator from 'validator'; // eslint-disable-line no-unused-vars

class FormView {
	/**
	 * Constructor.
	 *
	 * @param {HTMLElement} form
	 *
	 * @return {void}
	 */
	constructor( form ) {
		this.form = form;

		this.requiredClass = 'obsidian-forms-field__required';
		this.invalidClass = 'obsidian-forms-field__error';
		this.errorMessageClass = 'obsidian-forms-field__error-message';

		this.selects = this.form.querySelectorAll( 'select' ) || [];
		this.emails = this.form.querySelectorAll( 'input[type="email"]' ) || [];
		this.phones = this.form.querySelectorAll( 'input[type="tel"]' ) || [];
		this.urls = this.form.querySelectorAll( 'input[type="url"]' ) || [];
		this.submit =
			this.form.querySelector( '#obsidian-form-submit' ) || null;
		this.requiredFields = this.form.querySelectorAll(
			`.${ this.requiredClass }`
		);
	}

	/**
	 * Initialize the form view.
	 *
	 * @return {void}
	 */
	init() {
		if ( this.selects ) {
			this.formSelectUi();
		}

		// Store original button text.
		if ( this.submit ) {
			this.submit.dataset.originalText = this.submit.textContent;
		}

		this.form.addEventListener( 'submit', this.validateForm.bind( this ) );
	}

	/**
	 * Initialize the select UI.
	 *
	 * @return {void}
	 */
	formSelectUi() {
		this.selects.forEach( ( select ) => {
			new TomSelect( select, {
				create: true,
			} );
		} );
	}

	/**
	 * Validate the form.
	 *
	 * @param {Event} event
	 *
	 * @return {boolean} True if valid, false otherwise.
	 */
	validateForm( event ) {
		let errors = 0;

		// Generic required field validation.
		if ( this.requiredFields ) {
			this.requiredFields.forEach( ( field ) => {
				const input = field.querySelector( 'input, select, textarea' );

				if ( this.validateRequiredField( field, input ) ) {
					this.setFieldValidationState( field, 'pass' );
				} else {
					errors++;
					this.setFieldValidationState(
						field,
						'fail',
						'This field is required'
					); // @todo: Use localized string or check field attributes for custom message.
				}
			} );
		}

		// Email validation.
		if ( this.emails ) {
			const emailFails = this.validateEmails();

			errors += emailFails;
		}

		// Phone validation.
		if ( this.phones ) {
			const phoneFails = this.validatePhones();

			errors += phoneFails;
		}

		// URL validation.
		if ( this.urls ) {
			const urlFails = this.validateUrls();

			errors += urlFails;
		}

		if ( errors > 0 ) {
			event.preventDefault();
			return false;
		}

		// Handle AJAX submission if enabled.
		if ( this.form.dataset.ajax === 'true' ) {
			event.preventDefault();
			this.submitViaAjax();
			return false;
		}

		return true;
	}

	/**
	 * Submit form via AJAX.
	 *
	 * @return {void}
	 */
	submitViaAjax() {
		// Get form data.
		const formData = new FormData( this.form );

		// Add action for REST endpoint.
		formData.append( 'action', 'obsidian_form_submit' );

		// Disable submit button.
		if ( this.submit ) {
			this.submit.disabled = true;
			this.submit.textContent = 'Submitting...';
		}

		// Clear previous messages.
		this.clearMessages();

		// Get REST nonce from the hidden field in the form.
		const nonceField = this.form.querySelector(
			'#obsidian_form_rest_nonce'
		);
		const nonce = nonceField ? nonceField.value : '';

		// Get REST URL.
		const restUrl = '/wp-json/obsidian-forms/v1/submit';

		// Make AJAX request.
		fetch( restUrl, {
			method: 'POST',
			body: formData,
			headers: {
				'X-WP-Nonce': nonce,
			},
		} )
			.then( ( response ) => {
				return response.json();
			} )
			.then( ( data ) => {
				if ( data.success ) {
					this.handleSuccess( data.data );
				} else {
					this.handleError( data.data );
				}
			} )
			.catch( ( error ) => {
				console.error( 'Form submission error:', error );
				this.handleError( {
					message: 'An unexpected error occurred. Please try again.',
				} );
			} )
			.finally( () => {
				// Re-enable submit button.
				if ( this.submit ) {
					this.submit.disabled = false;
					this.submit.textContent = this.submit.dataset.originalText || 'Submit';
				}
			} );
	}

	/**
	 * Handle successful form submission.
	 *
	 * @param {Object} data Response data.
	 *
	 * @return {void}
	 */
	handleSuccess( data ) {
		const messageContainer = this.form.querySelector(
			'.obsidian-form-message-container'
		);

		if ( messageContainer ) {
			messageContainer.innerHTML = `<div class="obsidian-form-message obsidian-form-message--success" role="alert"><p>${
				data.message || 'Thank you! Your form has been submitted successfully.'
			}</p></div>`;
		}

		// Reset form.
		this.form.reset();

		// Scroll to message.
		messageContainer?.scrollIntoView( {
			behavior: 'smooth',
			block: 'nearest',
		} );

		// Check for redirect.
		if ( data.redirect_url ) {
			setTimeout( () => {
				window.location.href = data.redirect_url;
			}, 1500 );
		}
	}

	/**
	 * Handle form submission error.
	 *
	 * @param {Object} data Error data.
	 *
	 * @return {void}
	 */
	handleError( data ) {
		const messageContainer = this.form.querySelector(
			'.obsidian-form-message-container'
		);

		if ( messageContainer ) {
			const errorMessage =
				data.message ||
				'There was an error submitting your form. Please try again.';
			messageContainer.innerHTML = `<div class="obsidian-form-message obsidian-form-message--error" role="alert"><p>${ errorMessage }</p></div>`;
		}

		// Display field-specific errors.
		if ( data.errors ) {
			Object.keys( data.errors ).forEach( ( fieldName ) => {
				const field = this.form.querySelector(
					`[name="${ fieldName }"]`
				);

				if ( field ) {
					this.setFieldValidationState(
						field,
						'fail',
						data.errors[ fieldName ]
					);
				}
			} );
		}

		// Scroll to message.
		messageContainer?.scrollIntoView( {
			behavior: 'smooth',
			block: 'nearest',
		} );
	}

	/**
	 * Clear all messages.
	 *
	 * @return {void}
	 */
	clearMessages() {
		const messageContainer = this.form.querySelector(
			'.obsidian-form-message-container'
		);

		if ( messageContainer ) {
			messageContainer.innerHTML = '';
		}

		// Clear field errors.
		this.form.querySelectorAll( '.' + this.invalidClass ).forEach( ( field ) => {
			this.setFieldValidationState( field, 'pass' );
		} );
	}

	/**
	 * Validate email fields.
	 *
	 * @return {number} The number of errors.
	 */
	validateEmails() {
		let errors = 0;

		this.emails.forEach( ( email ) => {
			// Skip validation if the field is required and empty, this will be caught by the required field validation.
			if ( this.isFieldRequired( email ) && ! email.value ) {
				return;
			}

			if ( validator.isEmail( email.value ) || ! email.value ) {
				this.setFieldValidationState( email, 'pass' );
			} else {
				errors++;
				this.setFieldValidationState(
					email,
					'fail',
					'Enter a valid email address'
				); // @todo: Use localized string or check field attributes for custom message.
			}
		} );

		return errors;
	}

	/**
	 * Validate phone fields.
	 *
	 * @return {number} The number of errors.
	 */
	validatePhones() {
		let errors = 0;

		this.phones.forEach( ( phone ) => {
			// Skip validation if the field is required and empty, this will be caught by the required field validation.
			if ( this.isFieldRequired( phone ) && ! phone.value ) {
				return;
			}

			if (
				validator.isMobilePhone( phone.value, 'en-US' ) ||
				! phone.value
			) {
				this.setFieldValidationState( phone, 'pass' );
			} else {
				errors++;
				this.setFieldValidationState(
					phone,
					'fail',
					'Enter a valid phone number'
				); // @todo: Use localized string or check field attributes for custom message.
			}
		} );

		return errors;
	}

	/**
	 * Validate URL fields.
	 *
	 * @return {number} The number of errors.
	 */
	validateUrls() {
		let errors = 0;

		this.urls.forEach( ( url ) => {
			// Skip validation if the field is required and empty, this will be caught by the required field validation.
			if ( this.isFieldRequired( url ) && ! url.value ) {
				return;
			}

			if ( validator.isURL( url.value ) || ! url.value ) {
				this.setFieldValidationState( url, 'pass' );
			} else {
				errors++;
				this.setFieldValidationState(
					url,
					'fail',
					'Enter a valid URL'
				); // @todo: Use localized string or check field attributes for custom message.
			}
		} );

		return errors;
	}

	/**
	 * Get the parent field container.
	 *
	 * @param {HTMLElement} element
	 *
	 * @return {HTMLElement} The parent field container.
	 */
	getParentFieldContainer( element ) {
		return element.closest( '.wp-block-obsidian-form-field' );
	}

	/**
	 * Check if a field is required.
	 *
	 * @param {HTMLElement} element
	 *
	 * @return {boolean} Whether the field is required.
	 */
	isFieldRequired( element ) {
		if ( element.classList.contains( 'wp-block-obsidian-form-field' ) ) {
			return element.classList.contains(
				'obsidian-forms-field__required'
			);
		}

		return this.getParentFieldContainer( element ).classList.contains(
			'obsidian-forms-field__required'
		);
	}

	/**
	 * Set the validation state of a field.
	 *
	 * @param {HTMLElement} element
	 * @param {string}      state
	 * @param {string}      message
	 *
	 * @return {void}
	 */
	setFieldValidationState(
		element,
		state = 'pass',
		message = 'This field has an error'
	) {
		const parent = this.getParentFieldContainer( element );
		const errorMessage = parent.querySelector(
			`.${ this.errorMessageClass }`
		);

		if ( state === 'fail' ) {
			parent.classList.add( this.invalidClass );

			if ( ! errorMessage ) {
				parent.appendChild( this.generateErrorMessage( message ) );
			} else {
				errorMessage.textContent = message;
			}
		} else {
			parent.classList.remove( this.invalidClass );

			if ( errorMessage ) {
				errorMessage.remove();
			}
		}
	}

	/**
	 * Validate a required field.
	 *
	 * @param {HTMLElement} field
	 * @param {HTMLElement} input
	 *
	 * @return {boolean} Whether the field is valid.
	 *
	 * @todo Add validation feedback for "This field is required."
	 */
	validateRequiredField( field, input ) {
		// Catch checkboxes and radios first.
		if (
			field.classList.contains( 'obsidian-forms-field__checkbox' ) ||
			field.classList.contains( 'obsidian-forms-field__radio' )
		) {
			return field.querySelectorAll( 'input:checked' ).length > 0;
		}

		return input.value.length > 0;
	}

	/**
	 * Generate an error message.
	 *
	 * @param {string} message
	 *
	 * @return {string} The error message.
	 */
	generateErrorMessage( message ) {
		const messageContainer = document.createElement( 'div' );

		messageContainer.classList.add( this.errorMessageClass );
		messageContainer.textContent = message;

		return messageContainer;
	}
}

// Document ready.
document.addEventListener( 'DOMContentLoaded', () => {
	const forms = document.querySelectorAll( '.wp-block-obsidian-form-form' );

	forms.forEach( ( form ) => {
		const formScripts = new FormView( form );

		formScripts.init();
	} );
} );
