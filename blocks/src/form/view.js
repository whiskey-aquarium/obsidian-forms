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
		this.selects = this.form.querySelectorAll( 'select' ) || [];
		this.emails = this.form.querySelectorAll( 'input[type="email"]' ) || [];
		this.phones = this.form.querySelectorAll( 'input[type="tel"]' ) || [];
		this.submit =
			this.form.querySelector( '#obsidian-form-submit' ) || null;
		this.invalidClass = 'obsidian-forms-field__error';
		this.requiredFields = this.form.querySelectorAll(
			'.obsidian-forms-field__required'
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
	 * @return {void}
	 */
	validateForm( event ) {
		event.preventDefault();

		let errors = 0;

		if ( this.requiredFields ) {
			this.requiredFields.forEach( ( field ) => {
				const input = field.querySelector( 'input, select, textarea' );

				if ( ! this.validateRequiredField( field, input ) ) {
					errors++;
					this.toggleFieldValidationState( field, true );
				} else {
					this.toggleFieldValidationState( field, false );
				}
			} );
		}

		if ( errors ) {
			alert( 'Please check your email and phone fields.' ); // eslint-disable-line no-alert, no-undef
		} else {
			this.form.submit();
		}
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
	 * Toggle the validation state of a field.
	 *
	 * @param {HTMLElement} element
	 * @param {boolean}     state
	 *
	 * @return {void}
	 */
	toggleFieldValidationState( element, state ) {
		const parent = this.getParentFieldContainer( element );

		if ( state ) {
			parent.classList.add( this.invalidClass );
		} else {
			parent.classList.remove( this.invalidClass );
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
}

// Document ready.
document.addEventListener( 'DOMContentLoaded', () => {
	const forms = document.querySelectorAll( '.wp-block-obsidian-form-form' );

	forms.forEach( ( form ) => {
		const formScripts = new FormView( form );

		formScripts.init();
	} );
} );
