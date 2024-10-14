import TomSelect from 'tom-select';

class FormView {
	constructor() {
		this.init();
	}

	init() {
		this.initSelects();
	}

	initSelects() {
		const selects = document.querySelectorAll(
			'.wp-block-obsidian-form-form select'
		);
		selects.forEach( ( select ) => {
			new TomSelect( select, {
				create: true,
			} );
		} );
	}
}

// Document ready.
document.addEventListener( 'DOMContentLoaded', () => {
	new FormView();
} );
