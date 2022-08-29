<?php

/**
 * This file contains only errors. Each group of strings
 * might be separated depending on the section they
 * belong to.
 * Strings that are not in a group are used in the
 * entire system.
 */
return [
	'error' => "Error",
	'unexpected_error' => "Unexpected error occurred",
	'unauthorized' => "You can't access this endpoint directly",
	'invalid_order' => "Invalid order column or direction",
	'production_title' => "Whoops!",
	'production_msg' => "Something wrong occurred, please try again later.",

	'demo' => [
		'cannot_create_users' => "This is a demo version, the creation of new users is disabled",
		'cannot_update_users' => "This is a demo version, the editing of users is disabled",
		'cannot_delete_users' => "This is a demo version, the removal of users is disabled",
		'cannot_upload_logo' => "This is a demo version, the logo upload function is disabled",
		'cannot_update_settings' => "This is a demo version, the editing of settings is disabled"
	],

	'auth' => [
		'wrong_credentials' => "Wrong username or password",
		'invalid_expired_token' => "Invalid/expired auth token",
		'unauthorized' => "You don't have access to this endpoint",
		'wrong_type' => "Wrong login type. Please provide one of: session, jwt"
	],

	'brands' => [
		'not_found' => "Brand ID {id} not found",
		'already_exists' => "Brand name \"{name}\" already exists",
	],

	'categories' => [
		'not_found' => "Category ID {id} not found",
		'already_exists' => "Category name \"{name}\" already exists",
	],

	'customers' => [
		'not_found' => "Customer ID {id} not found",
		'already_exists_name' => "Customer with name \"{name}\" already exists",
		'already_exists_internal_name' => "Customer with internal name \"{internal_name}\" already exists"
	],

	'suppliers' => [
		'not_found' => "Supplier ID {id} not found",
		'already_exists_name' => "Supplier with name \"{name}\" already exists",
		'already_exists_internal_name' => "Supplier with internal name \"{internal_name}\" already exists"
	],
	
	'users' => [
		'not_found' => "User ID {id} not found",
		'already_exists_username' => "User with username \"{username}\" already exists",
		'already_exists_email_address' => "User with email address \"{email_address}\" already exists",
		'not_supervisor_worker' => "User is not a supervisor/worker",
		'not_found_warehouse' => "Warehouse ID {id} not found",
		'already_exists_warehouse_relation' => "This user already has access to this warehouse",
		'not_found_warehouse_relation' => "This user doesn't have access to this warehouse",
		'own_account' => "You cannot delete your own account!"
	],

	'warehouses' => [
		'not_found' => "Warehouse ID {id} not found",
		'already_exists_name' => 'Warehouse with name "{name}" already exists',
		'quantities_left' => "There are quantities left in stock. In order to delete a warehouse, your warehouse stock quantity has to be 0"
	],

	'items' => [
		'not_found' => "Item ID {id} not found",
		'not_found_with_code' => "Item with code \"{code}\" not found",
		'not_found_with_code_warehouse' => "Item with code \"{code}\" not found in warehouse ID \"{warehouse}\"",
		'not_found_with_id_warehouse' => "Item with ID {id} not found in warehouse ID {warehouse}",
		'brand_not_found' => "Brand ID {id} doesn't exist",
		'category_not_found' => "Category ID {id} doesn't exist",
		'warehouse_not_found' => "Warehouse ID {id} doesn't exist",
		'supplier_not_found' => "Supplier ID {id} doesn't exist",
		'item_supplier_not_found' => "Item ID {item_id} does not have a supplier record with supplier ID {supplier_id}",
		'already_exists_code' => "Code \"{code}\" already exists",
		'quantities_left' => "There are units left in stock. In order to delete an item, your stock in all warehouses for this item must be 0",
		'already_exists_supplier' => "Item ID {item_id} already has a supplier record with supplier ID {supplier_id}"
	],

	'purchases' => [
		'not_found' => "Purchase ID {id} not found",
		'not_found_with_reference' => "Purchase with reference \"{reference}\" not found",
		'supplier_not_found' => "Supplier ID {id} doesn't exist",
		'warehouse_not_found' => "Warehouse ID {id} doesn't exist",
		'warehouse_unauthorized' => "You don't have access to this warehouse",
		'already_exists_reference' => "This reference already exists",
		'items' => [
			'malformed' => "Items array is malformed",
			'not_found' => "Item ID {id} not found",
			'item_supplier_not_found' => "Item ID {item_id} does not have a supplier record with supplier ID {supplier_id}",
			'inconsistent' => "One or more item details are inconsistent (name, code or unit price)"
		],
		'returns' => [
			'not_found' => "Return ID {id} not found",
			'not_found_with_purchase' => "Return for purchase ID {id} not found",
			'already_exists' => "This purchase has a return already",
			'exceeding_qty' => "You can't return more items than originally purchased",
			'not_enough_qty' => "There aren't enough items in stock to process this return",
			'not_returning' => "No return quantities were found. You must to return at least a single item",
			'unexisting_id' => "You're trying to return an item ID that doesn't exist in this purchase"
		],
		'frontend' => [
			'return_search_purchase' => "Please search purchase reference",
			'item_already_added' => "This item was added already to this purchase",
			'item_not_added' => "To continue, add at least one item"
		]
	],

	'sales' => [
		'not_found' => "Sale ID {id} not found",
		'not_found_with_reference' => "Sale with reference \"{reference}\" not found",
		'customer_not_found' => "Customer ID {id} doesn't exist",
		'warehouse_not_found' => "Warehouse ID {id} doesn't exist",
		'warehouse_unauthorized' => "You don't have access to this warehouse",
		'already_exists_reference' => "This reference already exists",
		'not_enough_qty' => "There aren't enough items in stock to process this sale",
		'items' => [
			'malformed' => "Items array is malformed",
			'not_found' => "Item ID {id} not found",
			'inconsistent' => "One or more item details are inconsistent (name, code or unit price)"
		],
		'returns' => [
			'not_found' => "Return ID {id} not found",
			'not_found_with_sale' => "Return for sale ID {id} not found",
			'already_exists' => "This sale has a return already",
			'exceeding_qty' => "You can't return more items than originally sold",
			'not_returning' => "No return quantities were found. You must to return at least a single item",
			'unexisting_id' => "You're trying to return an item ID that doesn't exist in this purchase"
		],
		'frontend' => [
			'return_search_sale' => "Please search sale reference",
			'item_already_added' => "This item was added already to this sale",
			'item_not_added' => "To continue, add at least one item"
		]
	],

	'stats' => [
		'wrong_timeframe' => "Wrong timeframe"
	],

	'settings' => [
		'logo_invalid_mime' => "Image must be a valid PNG file",
		'logo_invalid_dims' => "Image dimensions must be 510 x 135"
	],

	'adjustments' => [
		'not_found' => "Adjustment ID {id} not found",
		'warehouse_not_found' => "Warehouse ID {id} doesn't exist",
		'warehouse_unauthorized' => "You don't have access to this warehouse",
		'items' => [
			'malformed' => "Items array is malformed",
			'not_found' => "Item ID {id} not found",
			'inconsistent' => "One or more item details are inconsistent (name or quantity)"
		],
		'not_enough_qty' => "There aren't enough items in stock to process this quantity adjustment",
		'frontend' => [
			'item_already_added' => "This item was added already to this adjustment",
			'item_not_added' => "To continue, add at least one item"
		]
	],

	'transfers' => [
		'not_found' => "Transfer ID {id} not found",
		'warehouse_unauthorized' => "You don't have access to this warehouse",
		'same_warehouse_id' => "Source and target warehouses must be different",
		'from_warehouse_not_found' => "Target warehouse ID {id} doesn't exist",
		'to_warehouse_not_found' => "Source warehouse ID {id} doesn't exist",
		'items' => [
			'malformed' => "Items array is malformed",
			'not_found' => "Item ID {id} not found",
			'inconsistent' => "One or more item details are inconsistent",
		],
		'not_enough_qty' => "There aren't enough items in stock in source warehouse to process this transfer",
		'frontend' => [
			'same_warehouse_id' => "Source and target warehouses must be different",
			'item_already_added' => "This item was added already to this transfer",
			'item_not_added' => "To continue, add at least one item"
		]
	],

	'404' => [
		'description' => "Sorry! Cannot seem to find the page you were looking for",
		'go_back' => "Go back to the index"
	],
	'401' => [
		'description' => "Sorry! You don't have access to this page",
		'go_back' => "Go back to the index"
	]
];