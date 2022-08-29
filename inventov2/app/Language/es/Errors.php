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
	'unexpected_error' => "Error inesperado",
	'unauthorized' => "No puede acceder a esta ruta directamente",
	'invalid_order' => "Columna o dirección de orden inválidos",
	'production_title' => "Ups!",
	'production_msg' => "Ocurrió un error, por favor intente más tarde.",

	'demo' => [
		'cannot_create_users' => "Esta es una versión de demo, la creación de nuevos usuarios está deshabilitada",
		'cannot_update_users' => "Esta es una versión de demo, la edición de usuarios está deshabilitada",
		'cannot_delete_users' => "Esta es una versión de demo, la eliminación de usuarios está deshabilitada",
		'cannot_upload_logo' => "Esta es una versión de demo, la función de subir logo está deshabilitada",
		'cannot_update_settings' => "Esta es una versión de demo, la edición de configuración está deshabilitada"
	],

	'auth' => [
		'wrong_credentials' => "Usuario o contraseña incorrectos",
		'invalid_expired_token' => "Token de autenticación inválido o expirado",
		'unauthorized' => "Usted no tiene acceso a esta ruta",
		'wrong_type' => "Tipo de login inválido. Por favor proporcione uno de: session, jwt"
	],

	'brands' => [
		'not_found' => "Marca ID {id} no encontrada",
		'already_exists' => "Marca con el nombre \"{name}\" ya existe",
	],

	'categories' => [
		'not_found' => "Categoría ID {id} no encontrada",
		'already_exists' => "Categoría con el nombre \"{name}\" ya existe",
	],

	'customers' => [
		'not_found' => "Cliente ID {id} no encontrado",
		'already_exists_name' => "Cliente con el nombre \"{name}\" ya existe",
		'already_exists_internal_name' => "Cliente con el nombre interno \"{internal_name}\" ya existe"
	],

	'suppliers' => [
		'not_found' => "Proveedor ID {id} no encontrado",
		'already_exists_name' => "Proveedor con el nombre \"{name}\" ya existe",
		'already_exists_internal_name' => "Proveedor con el nombre interno \"{internal_name}\" ya existe"
	],
	
	'users' => [
		'not_found' => "Usuario ID {id} no encontrado",
		'already_exists_username' => "Usuario con el nombre de usuario \"{username}\" ya existe",
		'already_exists_email_address' => "Usuario con el correo electrónico \"{email_address}\" ya existe",
		'not_supervisor_worker' => "Usuario no es supervisor ni trabajador",
		'not_found_warehouse' => "Warehouse ID {id} not found",
		'already_exists_warehouse_relation' => "Este usuario ya tiene acceso a esta bodega",
		'not_found_warehouse_relation' => "Este usuario no tiene acceso a esta bodega",
		'own_account' => "No puede eliminar su propia cuenta!"
	],

	'warehouses' => [
		'not_found' => "Bodega ID {id} no encontrada",
		'already_exists_name' => 'Bodega con el nombre "{name}" ya existe',
		'quantities_left' => "Hay artículos en existencia. Para poder eliminar esta bodega, ésta debe tener 0 artículos en existencia."
	],

	'items' => [
		'not_found' => "Artículo ID {id} no encontrado",
		'not_found_with_code' => "Artículo con el código \"{code}\" no encontrado",
		'not_found_with_code_warehouse' => "Artículo con el código \"{code}\" no encontrado en la bodega ID \"{warehouse}\"",
		'not_found_with_id_warehouse' => "Artículo con ID {id} no encontrado en la bodega ID {warehouse}",
		'brand_not_found' => "Marca ID {id} no existe",
		'category_not_found' => "Categoría ID {id} no existe",
		'warehouse_not_found' => "Bodega ID {id} no existe",
		'supplier_not_found' => "Proveedor ID {id} no existe",
		'item_supplier_not_found' => "Artículo ID {item_id} no tiene un proveedor con ID {supplier_id}",
		'already_exists_code' => "Código \"{code}\" ya existe",
		'quantities_left' => "Hay unidades en existencia. Para poder eliminar un artículo, éste debe tener 0 unidades en todas las bodegas.",
		'already_exists_supplier' => "Artículo ID {item_id} ya tiene un proveedor con ID {supplier_id}"
	],

	'purchases' => [
		'not_found' => "Compra ID {id} no encontrada",
		'not_found_with_reference' => "Compra con referencia \"{reference}\" no encontrada",
		'supplier_not_found' => "Proveedor ID {id} no existe",
		'warehouse_not_found' => "Bodega ID {id} no existe",
		'warehouse_unauthorized' => "No tiene acceso a esta bodega",
		'already_exists_reference' => "Esta referencia ya existe",
		'items' => [
			'malformed' => "Arreglo de artículos mal formado",
			'not_found' => "Artículo ID {id} no encontrado",
			'item_supplier_not_found' => "Artículo ID {item_id} no tiene un proveedor con ID {supplier_id}",
			'inconsistent' => "Uno o mas artículos son inconsistentes (nombre, código o precio unitario)"
		],
		'returns' => [
			'not_found' => "Devolución ID {id} no encontrada",
			'not_found_with_purchase' => "Devolución de compra ID {id} no encontrada",
			'already_exists' => "Esta compra ya tiene una devolución",
			'exceeding_qty' => "No puede devolver más artículos de los que se compraron",
			'not_enough_qty' => "No hay artículos suficientes en existencia para procesar esta devolución",
			'not_returning' => "No se encontró algún artículo para devolver. Debe devolver al menos uno.",
			'unexisting_id' => "Está intentando devolver un artículo que no existe en esta compra"
		],
		'frontend' => [
			'return_search_purchase' => "Por favor busque una referencia de compra",
			'item_already_added' => "Este artículo ya se agregó",
			'item_not_added' => "Para continuar, agregue al menos un artículo"
		]
	],

	'sales' => [
		'not_found' => "Venta ID {id} no encontrada",
		'not_found_with_reference' => "Venta con referencia \"{reference}\" no encontrada",
		'customer_not_found' => "Cliente ID {id} no existe",
		'warehouse_not_found' => "Bodega ID {id} no existe",
		'warehouse_unauthorized' => "No tiene acceso a esta bodega",
		'already_exists_reference' => "Esta referencia ya existe",
		'not_enough_qty' => "No hay artículos suficientes en existencia para procesar esta venta",
		'items' => [
			'malformed' => "Arreglo de artículos mal formado",
			'not_found' => "Artículo ID {id} no encontrado",
			'inconsistent' => "Uno o más artículos son inconsistentes (nombre, código o precio unitario)"
		],
		'returns' => [
			'not_found' => "Devolución ID {id} no encontrada",
			'not_found_with_sale' => "Devolución para venta ID {id} no encontrada",
			'already_exists' => "Esta venta ya tiene una devolución",
			'exceeding_qty' => "No puede devolver más artículos de los que se vendieron",
			'not_returning' => "No se encontró algún artículo para devolver. Debe devolver al menos uno.",
			'unexisting_id' => "Está intentando devolver un artículo que no existe en esta venta"
		],
		'frontend' => [
			'return_search_sale' => "Por favor busque una referencia de venta",
			'item_already_added' => "Este artículo ya se agregó",
			'item_not_added' => "Para continuar, agregue al menos un artículo"
		]
	],

	'stats' => [
		'wrong_timeframe' => "Periodo de tiempo incorrecto"
	],

	'settings' => [
		'logo_invalid_mime' => "Imagen debe ser un archivo PNG válido",
		'logo_invalid_dims' => "Dimensiones de la imagen deben ser 510 x 135"
	],

	'adjustments' => [
		'not_found' => "Ajuste ID {id} no encontrado",
		'warehouse_not_found' => "Bodega ID {id} no existe",
		'warehouse_unauthorized' => "No tiene acceso a esta bodega",
		'items' => [
			'malformed' => "Arreglo de artículos mal formado",
			'not_found' => "Artículo ID {id} no encontrado",
			'inconsistent' => "Uno o más artículos son inconsistentes (nombre o cantidad)"
		],
		'not_enough_qty' => "No hay artículos suficientes en existencia para procesar este ajuste de cantidad",
		'frontend' => [
			'item_already_added' => "Este artículo ya se agregó",
			'item_not_added' => "Para continuar, agregue al menos un artículos"
		]
	],

	'transfers' => [
		'not_found' => "Transferencia ID {id} no encontrada",
		'warehouse_unauthorized' => "No tiene acceso a esta bodega",
		'same_warehouse_id' => "Bodega de origen y destino deben ser distintas",
		'from_warehouse_not_found' => "Bodega de origen con ID {id} no existe",
		'to_warehouse_not_found' => "Bodega destino con ID {id} no existe",
		'items' => [
			'malformed' => "Arreglo de artículos mal formado",
			'not_found' => "Artículo ID {id} no encontrado",
			'inconsistent' => "Uno o más artículos son inconsistentes"
		],
		'not_enough_qty' => "No hay artículos suficientes en existencia para procesar esta transferencia",
		'frontend' => [
			'same_warehouse_id' => "Bodega de origen y destino deben ser distintas",
			'item_already_added' => "Este artículo ya se agregó a esta transferencia",
			'item_not_added' => "Para continuar, agregue al menos un artículos"
		]
	],

	'404' => [
		'description' => "Disculpe! La página que está buscando no existe",
		'go_back' => "Ir al inicio"
	],
	'401' => [
		'description' => "Disculpe! No tiene acceso a esta página",
		'go_back' => "Ir al inicio"
	]
];