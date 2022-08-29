<?php

/**
 * This file contains only validation errors. Each group
 * of strings is separated depending on the section they
 * belong to
 */
return [
	'brands' => [
		'name_min_length' => "Por favor ingrese el nombre de la marca",
		'name_max_length' => "Nombre no puede tener más de 100 caracteres"
	],

	'categories' => [
		'name_min_length' => "Por favor ingrese el nombre de la categoría",
		'name_max_length' => "Nombre no puede tener más de 100 caracteres"
	],

	'customers' => [
		'name_min_length' => "Por favor ingrese el nombre del cliente",
		'name_max_length' => "Nombre del cliente no puede tener más de 100 caracteres",
		'internal_name_max_length' => "Nombre interno no puede tener más de 45 caracteres",
		'company_name_max_length' => "Nombre de compañía no puede tener más de 100 caracteres",
		'tax_number_max_length' => "RFC no puede tener más de 45 caracteres",
		'email_address_invalid' => "Por favor ingrese un correo electrónico válido",
		'email_address_max_length' => "Correo electrónico no puede tener más de 255 caracteres",
		'phone_number_max_length' => "Número de teléfono no puede tener más de 20 caracteres",
		'address_max_length' => "Dirección no puede tener más de 80 caracteres",
		'country_max_length' => "País no puede tener más de 30 caracteres",
		'state_max_length' => "Estado no puede tener más de 30 caracteres",
		'zip_code_invalid' => "Por favor ingrese un código postal válido",
		'zip_code_max_length' => "Código postal no puede tener más de 12 caracteres"
	],

	'suppliers' => [
		'name_min_length' => "Por favor ingrese el nombre del proveedor",
		'name_max_length' => "Nombre del proveedor no puede tener más de 100 caracteres",
		'internal_name_max_length' => "Nombre interno no puede tener más de 45 caracteres",
		'company_name_max_length' => "Nombre de compañía no puede tener más de 100 caracteres",
		'vat_max_length' => "RFC no puede tener más de 45 caracteres",
		'email_address_invalid' => "Por favor ingrese un correo electrónico válido",
		'email_address_max_length' => "Correo electrónico no puede tener más de 255 caracteres",
		'phone_number_max_length' => "Número de teléfono no puede tener más de 20 caracteres",
		'address_max_length' => "Dirección no puede tener más de 80 caracteres",
		'country_max_length' => "País no puede tener más de 30 caracteres",
		'state_max_length' => "Estado no puede tener más de 30 caracteres",
		'zip_code_invalid' => "Por favor ingrese un código postal válido",
		'zip_code_max_length' => "Código postal no puede tener más de 12 caracteres"
	],

	'users' => [
		'name_min_length' => "Nombre debe contener al menos 2 caracteres",
		'name_max_length' => "Nombre no puede tener más de 100 caracteres",
		'username_min_length' => "Nombre de usuario debe contener al menos 5 caracteres",
		'username_max_length' => "Nombre de usuario no puede tener más de 30 caracteres",
		'password_min_length' => "Contraseña debe contener al menos 5 caracteres",
		'password_max_length' => "Contraseña no puede tener más de 30 caracteres",
		'password_missmatch' => "Contraseña y su confirmación deben ser iguales",
		'email_address_invalid' => "Por favor ingrese un correo electrónico válido",
		'phone_number_max_length' => "Número de teléfono no puede tener más de 20 caracteres",
		'role_invalid' => "Rol inválido. Proporcione uno de: worker, supervisor, admin",
		'frontend' => [
			'role_not_selected' => "Por favor seleccione un rol" // role_invalid, frontend-friendly
		]
	],

	'warehouses' => [
		'name_min_length' => "Por favor ingrese el nombre de la bodega",
		'name_max_length' => "Nombre de la bodega no puede tener más de 100 caracteres",
		'address_max_length' => "Dirección no puede tener más de 80 caracteres",
		'city_max_length' => "Ciudad no puede tener más de 80 caracteres",
		'country_max_length' => "País no puede tener más de 30 caracteres",
		'state_max_length' => "Estado no puede tener más de 30 caracteres",
		'zip_code_invalid' => "Por favor ingrese un código postal válido",
		'zip_code_max_length' => "Código postal no puede tener más de 12 caracteres",
		'phone_number_max_length' => "Número de teléfono no puede tener más de 20 caracteres"
	],

	'items' => [
		'name_min_length' => "Por favor ingrese el nombre del artículo",
		'name_max_length' => "Nombre del artículo no puede tener más de 45 caracteres",
		'code_min_length' => "Por favor ingrese código",
		'code_type_invalid' => "Tipo de código no es válido. Proporcione uno de: none, code39, ean128, ean-8, ean-13, upc-a, qr",
		'sale_price_decimal' => "Precio de venta debe ser un número decimal válido",
		'sale_price_greater_than' => "Precio de venta debe ser mayor que 0",
		'sale_tax_decimal' => "Impuesto de venta debe ser un número decimal válido",
		'sale_tax_greater_than_equal_to' => "Impuesto de venta no puede ser menor que 0",
		'weight_decimal' => "Peso debe ser un número decimal válido",
		'width_decimal' => "Ancho debe ser un número decimal válido",
		'height_decimal' => "Alto debe ser un número decimal válido",
		'depth_decimal' => "Profundidad debe ser un número decimal válido",
		'min_alert_numeric' => "Alerta de cantidad mínima debe ser un número válido",
		'min_alert_greater_than_equal_to' => "Alerta de cantidad mínima no puede ser menor que 0",
		'max_alert_numeric' => "Alerta de cantidad máxima debe ser un número válido",
		'max_alert_greater_than_equal_to' => "Alerta de cantidad máxima debe ser mayor que 0",
		'code_invalid' => "\"{code}\" no es un código válido de tipo {code_type}",
		'code_invalid_frontend' => "Código no es válido para el tipo de código seleccionado",
		'min_alert_greater_or_equal' => "Alerta de cantidad máxima debe ser mayor que la alerta de cantidad mínima",
		'suppliers' => [
			'supplier_id_numeric' => "Por favor seleccione un proveedor",
			'part_number_max_length' => "Número de parte no puede tener más de 45 caracteres",
			'price_decimal' => "Precio debe ser un número decimal válido",
			'tax_decimal' => "Impuesto debe ser un número decimal válido"
		]
	],

	'purchases' => [
		'reference_min_length' => "Por favor ingrese referencia de compra",
		'reference_max_length' => "Referencia no puede tener más de 45 caracteres",
		'supplier_id_numeric' => "ID de proveedor debe ser numérico",
		'warehouse_id_numeric' => "ID de bodega debe ser numérico",
		'items_required' => "Artículos son requeridos",
		'shipping_cost_decimal' => "Costo de envío debe ser un número decimal válido",
		'discount_decimal' => "Descuento debe ser un número decimal válido",
		'discount_type_invalid' => "Tipo de descuento inválido. Proporcione uno de: percentage, amount",
		'tax_decimal' => "Impuesto debe ser un número decimal válido",
		'item_tax_decimal' => "Impuesto de artículo debe ser un número decimal válido",
		'item_quantity_numeric' => "Cantidad de artículos debe ser numérica",
		'discount_percentage_greater_than' => "Descuento no puede exceder 100%",
		'discount_amount_greater_than' => "Descuento no puede exceder el subtotal de la orden",
		'duplicate_items' => "La orden no puede tener artículos repetidos",
		'returns' => [
			'item_quantity_numeric' => "Cantidad de artículos a devolver debe ser numérica",
			'discount_amount_greater_than' => "Descuento no puede exceder el subtotal de la devolución",
		],
		'frontend' => [
			'returns_missing_purchase_ref' => "Por favor ingrese la referencia de compra",
			'warehouse_not_selected' => "Por favor seleccione una bodega",
			'supplier_not_selected' => "Por favor seleccione un proveedor"
		]
	],

	'sales' => [
		'reference_min_length' => "Por favor ingrese referencia de venta",
		'reference_max_length' => "Referencia no puede tener más de 45 caracteres",
		'customer_id_numeric' => "ID de cliente debe ser numérico",
		'warehouse_id_numeric' => "ID de bodega debe ser numérico",
		'items_required' => "Artículos son requeridos",
		'shipping_cost_decimal' => "Costo de envío debe ser un número decimal válido",
		'discount_decimal' => "Descuento debe ser un número decimal válido",
		'discount_type_invalid' => "Tipo de descuento inválido. Proporcione uno de: percentage, amount",
		'tax_decimal' => "Impuesto debe ser un número decimal válido",
		'item_tax_decimal' => "Impuesto de artículo debe ser un número decimal válido",
		'item_quantity_numeric' => "Cantidad de artículos debe ser numérica",
		'discount_percentage_greater_than' => "Descuento no puede exceder 100%",
		'discount_amount_greater_than' => "Descuento no puede exceder el subtotal de la orden",
		'duplicate_items' => "La orden no puede tener artículos repetidos",
		'returns' => [
			'item_quantity_numeric' => "Cantidad de artículos a devolver debe ser numérica",
			'discount_amount_greater_than' => "Descuento no puede exceder el subtotal de la devolución",
		],
		'frontend' => [
			'returns_missing_sale_ref' => "Por favor ingrese la referencia de venta",
			'warehouse_not_selected' => "Por favor seleccione una bodega",
			'customer_not_selected' => "Por favor seleccione un cliente"
		]
	],

	'settings' => [
		'references_style_in_list' => "Estilo inválido. Proporcione uno de: increasing, random",
		'references_increasing_length_numeric' => "Longitud de referencias incrementables debe ser numérica",
		'references_increasing_length_greater_than_equal_to' => "Longitud de referencias debe ser numérica",
		'references_random_chars_min_length' => "Por favor ingrese caracteres permitidos para generar referencias aleatorias",
		'references_random_chars_length_numeric' => "Longitud para referencias aleatorias debe ser numérica",
		'references_random_chars_length_greater_than_equal_to' => "Longitud para referencias aleatorias debe ser mayor que 3",
		'jwt_secret_key_min_length' => "Por razones de seguridad, la llave secreta de JWTs debe contener al menos 20 caracteres",
		'jwt_exp_numeric' => "Expiración de JWTs debe ser numérica",
		'site_title_min_length' => "Por favor ingrese título",
		'default_locale_required' => "Por favor ingrese localización por defecto",
		'currency_name_required' => "Por favor ingrese nombre de moneda",
		'currency_symbol_required' => "Por favor ingrese símbolo de moneda",
		'default_locale_not_found' => "Localización \"{default_locale}\" no encontrada",
		'references_purchase_return_missing_append_prepend' => "Referencias de devolución de compras deben contener al menos un caracter antes o después.",
		'references_sale_return_missing_append_prepend' => "Referencias de devolución de ventas deben contener al menos un caracter antes o después."
	],

	'adjustments' => [
		'warehouse_id_required' => "Por favor ingrese ID de bodega",
		'items_required' => "Artículos son requeridos",
		'adjustment_type_invalid' => "Tipo de ajuste inválido. Proporcione uno de: subtract, add",
		'adjustment_quantity_numeric' => "Cantidad de ajuste debe ser numérica",
		'duplicate_items' => "Ajuste no puede tener artículos repetidos",
		'frontend' => [
			'warehouse_not_selected' => "Por favor seleccione una bodega"
		]
	],

	'transfers' => [
		'transfer_quantity_numeric' => "Cantidad de transferencia debe ser numérica",
		'duplicate_items' => "Transferencia no puede tener artículos repetidos",
		'frontend' => [
			'from_warehouse_not_selected' => "Por favor seleccione una bodega de origen",
			'to_warehouse_not_selected' => "Por favor seleccione una bodega destino"
		]
	]
];