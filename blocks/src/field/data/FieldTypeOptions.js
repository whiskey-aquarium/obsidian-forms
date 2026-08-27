import {
	link,
	envelope,
	calendar,
	overlayText,
	chevronDown,
	check,
} from '@wordpress/icons';

export const fieldTypeOptions = [
	{
		label: 'Text',
		value: 'text',
		component: 'input',
	},
	{
		label: 'URL',
		value: 'url',
		component: 'input',
		icon: link,
	},
	{
		label: 'Email',
		value: 'email',
		component: 'input',
		icon: envelope,
	},
	{
		label: 'Number',
		value: 'number',
		component: 'input',
	},
	{
		label: 'Date',
		value: 'date',
		component: 'input',
		icon: calendar,
	},
	{
		label: 'Time',
		value: 'time',
		component: 'input',
	},
	{
		label: 'Textarea',
		value: 'textarea',
		component: 'textarea',
		icon: overlayText,
	},
	{
		label: 'Select',
		value: 'select',
		component: 'select',
		icon: chevronDown,
	},
	{
		label: 'Checkbox',
		value: 'checkbox',
		component: 'checkbox',
		icon: check,
	},
	{
		label: 'Radio',
		value: 'radio',
		component: 'radio',
	},
	{
		label: 'Phone',
		value: 'tel',
		component: 'input',
	},
	{
		label: 'Range',
		value: 'range',
		component: 'input',
	},
];
