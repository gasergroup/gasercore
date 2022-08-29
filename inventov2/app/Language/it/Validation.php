<?php

/**
 * This file contains only validation errors. Each group
 * of strings is separated depending on the section they
 * belong to
 */
return [
	'brands' => [
		'name_min_length' => "Please type the brand name",
		'name_max_length' => "Brand name cannot be more than 100 characters long"
	],

	'categories' => [
		'name_min_length' => "Please type the category name",
		'name_max_length' => "Category name cannot be more than 100 characters long"
	],

	'customers' => [
		'name_min_length' => "Please type customer name",
		'name_max_length' => "Customer name cannot be more than 100 characters long",
		'internal_name_max_length' => "Internal name cannot be more than 45 characters long",
		'company_name_max_length' => "Company name cannot be more than 100 characters long",
		'tax_number_max_length' => "Tax number cannot be more than 45 characters long",
		'email_address_invalid' => "Please type a valid email address",
		'email_address_max_length' => "Email address cannot be more than 255 characters long",
		'phone_number_max_length' => "Phone number cannot be more than 20 characters long",
		'address_max_length' => "Address cannot be more than 80 characters long",
		'country_max_length' => "Country cannot be more than 30 characters long",
		'state_max_length' => "State cannot be more than 30 characters long",
		'zip_code_invalid' => "Please type a valid zip code",
		'zip_code_max_length' => "Zip code cannot be more than 12 characters long"
	],

	'suppliers' => [
		'name_min_length' => "Please type supplier name",
		'name_max_length' => "Supplier name cannot be more than 100 characters long",
		'internal_name_max_length' => "Internal name cannot be more than 45 characters long",
		'company_name_max_length' => "Company name cannot be more than 100 characters long",
		'vat_max_length' => "VAT cannot be more than 45 characters long",
		'email_address_invalid' => "Please type a valid email address",
		'email_address_max_length' => "Email address cannot be more than 255 characters long",
		'phone_number_max_length' => "Phone number cannot be more than 20 characters long",
		'address_max_length' => "Address cannot be more than 80 characters long",
		'country_max_length' => "Country cannot be more than 30 characters long",
		'state_max_length' => "State cannot be more than 30 characters long",
		'zip_code_invalid' => "Please type a valid zip code",
		'zip_code_max_length' => "Zip code cannot be more than 12 characters long"
	],

	'users' => [
		'name_min_length' => "User name must be at least 2 characters long",
		'name_max_length' => "User name cannot be more than 100 characters long",
		'username_min_length' => "Username must be at least 5 characters long",
		'username_max_length' => "Username cannot be more than 30 characters long",
		'password_min_length' => "Password must be at least 5 characters long",
		'password_max_length' => "Password cannot be more than 30 characters long",
		'password_missmatch' => "Password and password confirmation must match",
		'email_address_invalid' => "Please type a valid email address",
		'phone_number_max_length' => "Phone number cannot be more than 20 characters long",
		'role_invalid' => "Role is not valid. Please provide one of: worker, supervisor, admin",
		'frontend' => [
			'role_not_selected' => "Please select a role" // role_invalid, frontend-friendly
		]
	],

	'warehouses' => [
		'name_min_length' => "Please enter warehouse name",
		'name_max_length' => "Warehouse name cannot be more than 100 characters long",
		'address_max_length' => "Warehouse address cannot be more than 80 characters long",
		'city_max_length' => "City cannot be more than 80 characters long",
		'country_max_length' => "Country cannot be more than 30 characters long",
		'state_max_length' => "State cannot be more than 30 characters long",
		'zip_code_invalid' => "Please enter a valid zip code",
		'zip_code_max_length' => "Zip code cannot be more than 12 characters long",
		'phone_number_max_length' => "Phone number cannot be more than 20 characters long"
	],

	'items' => [
		'name_min_length' => "Please enter item name",
		'name_max_length' => "Item name cannot be more than 45 characters long",
		'code_min_length' => "Please enter item code",
		'code_type_invalid' => "Code type is not valid. Please provide one of: none, code39, ean128, ean-8, ean-13, upc-a, qr",
		'sale_price_decimal' => "Sale price has to be a valid decimal number",
		'sale_price_greater_than' => "Sale price has to be greater than 0",
		'sale_tax_decimal' => "Sale tax has to be a valid decimal number",
		'sale_tax_greater_than_equal_to' => "Sale tax cannot be less than 0",
		'weight_decimal' => "Weight has to be a valid decimal number",
		'width_decimal' => "Width has to be a valid decimal number",
		'height_decimal' => "Height has to be a valid decimal number",
		'depth_decimal' => "Depth has to be a valid decimal number",
		'min_alert_numeric' => "Minimum quantity alert has to be a valid number",
		'min_alert_greater_than_equal_to' => "Minimum quantity alert cannot be less than 0",
		'max_alert_numeric' => "Maximum quantity alert has to be a valid number",
		'max_alert_greater_than_equal_to' => "Maximum quantity alert has to be greater than 0",
		'code_invalid' => "\"{code}\" is not a valid {code_type} code",
		'code_invalid_frontend' => "Code is not valid for the selected barcode type",
		'min_alert_greater_or_equal' => "Maximum quantity alert must be greater than minimum quantity alert",
		'suppliers' => [
			'supplier_id_numeric' => "Please select a supplier",
			'part_number_max_length' => "Part number cannot be more than 45 characters long",
			'price_decimal' => "Price has to be a valid decimal number",
			'tax_decimal' => "Tax has to be a valid decimal number"
		]
	],

	'purchases' => [
		'reference_min_length' => "Please enter purchase reference",
		'reference_max_length' => "Reference cannot be more than 45 characters long",
		'supplier_id_numeric' => "Supplier ID has to be numeric",
		'warehouse_id_numeric' => "Warehouse ID has to be numeric",
		'items_required' => "Items are required",
		'shipping_cost_decimal' => "Shipping cost has to be a valid decimal number",
		'discount_decimal' => "Discount has to be a valid decimal number",
		'discount_type_invalid' => "Discount type is not valid. Please provide one of: percentage, amount",
		'tax_decimal' => "Tax has to be a valid decimal number",
		'item_tax_decimal' => "Item tax has to be a valid decimal number",
		'item_quantity_numeric' => "Item quantity has to be numeric",
		'discount_percentage_greater_than' => "Discount cannot be greater than 100%",
		'discount_amount_greater_than' => "Discount cannot exceed order's subtotal",
		'duplicate_items' => "Order cannot have duplicate items",
		'returns' => [
			'item_quantity_numeric' => "Item quantity to return must be numeric",
			'discount_amount_greater_than' => "Discount cannot exceed return's subtotal",
		],
		'frontend' => [
			'returns_missing_purchase_ref' => "Please enter purchase reference",
			'warehouse_not_selected' => "Please select a warehouse",
			'supplier_not_selected' => "Please select a supplier"
		]
	],

	'sales' => [
		'reference_min_length' => "Please enter sale reference",
		'reference_max_length' => "Reference cannot be more than 45 characters long",
		'customer_id_numeric' => "Customer ID has to be numeric",
		'warehouse_id_numeric' => "Warehouse ID has to be numeric",
		'items_required' => "Items are required",
		'shipping_cost_decimal' => "Shipping cost has to be a valid decimal number",
		'discount_decimal' => "Discount has to be a valid decimal number",
		'discount_type_invalid' => "Discount type is not valid. Please provide one of: percentage, amount",
		'tax_decimal' => "Tax has to be a valid decimal number",
		'item_tax_decimal' => "Item tax has to be a valid decimal number",
		'item_quantity_numeric' => "Item quantity has to be numeric",
		'discount_percentage_greater_than' => "Discount cannot be greater than 100%",
		'discount_amount_greater_than' => "Discount cannot exceed order's subtotal",
		'duplicate_items' => "Order cannot have duplicate items",
		'returns' => [
			'item_quantity_numeric' => "Item quantity to return must be numeric",
			'discount_amount_greater_than' => "Discount cannot exceed return's subtotal",
		],
		'frontend' => [
			'returns_missing_sale_ref' => "Please enter sale reference",
			'warehouse_not_selected' => "Please select a warehouse",
			'customer_not_selected' => "Please select a customer"
		]
	],

	'settings' => [
		'references_style_in_list' => "References style is not valid. Please provide one of: increasing, random",
		'references_increasing_length_numeric' => "References increasing length has to be numeric",
		'references_increasing_length_greater_than_equal_to' => "References increasing length has to be numeric",
		'references_random_chars_min_length' => "Please enter references random chars",
		'references_random_chars_length_numeric' => "References random chars length has to be numeric",
		'references_random_chars_length_greater_than_equal_to' => "References random chars length has to be greater than 3",
		'jwt_secret_key_min_length' => "For security reasons, JWT secret key has to be at least 20 characters long",
		'jwt_exp_numeric' => "JWT expiration has to be numeric",
		'site_title_min_length' => "Please enter site title",
		'default_locale_required' => "Please enter default locale",
		'currency_name_required' => "Please enter currency name",
		'currency_symbol_required' => "Please enter currency symbol",
		'default_locale_not_found' => "Default locale \"{default_locale}\" not found",
		'references_purchase_return_missing_append_prepend' => "Purchase return reference must be appended or prepended by at least one character",
		'references_sale_return_missing_append_prepend' => "Sale return reference must be appended or prepended by at least one character"
	],

	'adjustments' => [
		'warehouse_id_required' => "Please enter warehouse ID",
		'items_required' => "Items are required",
		'adjustment_type_invalid' => "Adjustment type is not valid. Please provide one of: subtract, add",
		'adjustment_quantity_numeric' => "Adjustment quantity must be numeric",
		'duplicate_items' => "Adjustment cannot have duplicate items",
		'frontend' => [
			'warehouse_not_selected' => "Please select a warehouse"
		]
	],

	'transfers' => [
		'transfer_quantity_numeric' => "Transfer quantity must be numeric",
		'duplicate_items' => "Transfer cannot have duplicate items",
		'frontend' => [
			'from_warehouse_not_selected' => "Please select a source warehouse",
			'to_warehouse_not_selected' => "Please select a target warehouse"
		]
	]
];