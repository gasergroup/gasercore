<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>

<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section variant-3">
			<div class="header">
				<div class="title">
					<?= lang('Main.transfers.new_transfer') ?>
				</div>
				<div class="desc">
					<?= lang('Main.transfers.new_transfer_description') ?>
				</div>
			</div>

			<div class="content">
				<form>
					<h6 class="h6-5 text-secondary mb-3">
						<?= lang('Main.transfers.basic_information') ?>
					</h6>

					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="from_warehouse" class="d-block"><?= lang('Main.transfers.from_warehouse') ?>*</label>
								<select name="from_warehouse" id="from_warehouse" class="custom-select">
									<option value="" disabled selected><?= lang('Main.transfers.select_from_warehouse') ?></option>
								</select>
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="to_warehouse" class="d-block"><?= lang('Main.transfers.to_warehouse') ?>*</label>
								<select name="to_warehouse" id="from_warehouse" class="custom-select">
									<option value="" disabled selected><?= lang('Main.transfers.select_to_warehouse') ?></option>
								</select>
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<div class="row mt-4">
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary"><?= lang('Main.transfers.add_items') ?></h6>
							<span class="autocomplete-desc mb-3">
								<?= lang('Main.transfers.add_items_description') ?>
							</span>

							<div class="autocomplete-container">
								<input type="text" id="item_search" name="item_search" placeholder="<?= langSlashes('Main.transfers.add_items_placeholder') ?>" autocomplete="off" class="form-control" disabled />
								<ul class="dropdown-menu" id="itemSuggestions"></ul>
							</div>
						</div>
					</div>

					<div class="row mt-4">
						<div class="col-sm text-break pl-2 pr-2">
							<div class="table-responsive">
								<table id="items" class="table table-bordered">
									<thead>
										<tr>
											<th><?= lang('Main.transfers.items.item_name') ?></th>
											<th><?= lang('Main.transfers.items.current_stock') ?></th>
											<th><?= lang('Main.transfers.items.transfer_quantity') ?></th>
											<th><?= lang('Main.transfers.items.source_warehouse_change') ?></th>
											<th><?= lang('Main.transfers.items.target_warehouse_change') ?></th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="row mt-n3">
						<div class="col-sm-12 text-break pl-2 pr-2 mt-3">
							<div class="form-group">
								<label for="notes" class="d-block"><?= lang('Main.transfers.notes') ?></label>
								<textarea name="notes" id="notes" class="form-control" rows="6"></textarea>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.transfers.create') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';

let itemsAdded = [];
let selectedFromWarehouse = 0;
let selectedToWarehouse = 0;

let warehouses = [];

let arrowRight = `<i class="fas fa-long-arrow-alt-right"></i>`;

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Populate selects
		loadWarehouses()

		// Once both warehouses are selected, enable items section
		$('select[name=from_warehouse], select[name=to_warehouse]').on('change', function(e) {
			let t = $(this)
			let from_warehouse = $('select[name=from_warehouse]').val()
			let to_warehouse = $('select[name=to_warehouse]').val()

			if(from_warehouse != '' && from_warehouse != null && to_warehouse != '' && to_warehouse != null) {
				// Same warehouse selected twice? Show error
				if(from_warehouse == to_warehouse) {
					t.prop('selectedIndex', 0)
					showError("<?= langSlashes('Errors.error') ?>", "<?= langSlashes('Errors.transfers.frontend.same_warehouse_id') ?>")
					return
				}

				selectedFromWarehouse = from_warehouse
				selectedToWarehouse = to_warehouse
				$('select[name=from_warehouse]').prop('disabled', true)
				$('select[name=to_warehouse]').prop('disabled', true)
				$('input[name=item_search]').prop('disabled', false)
			}
		})

		// When focusing on the autocomplete, show list
		$('input[name=item_search]').on('focus', e => {
			$('.autocomplete-container').addClass('open')
		})
		$('input[name=item_search]').on('blur', e => {
			// Timeout so that the item clicked listener can fire
			setTimeout(() => {
				$('.autocomplete-container').removeClass('open')
			}, 200)
		})

		// Listen for changes on the autocomplete input
		$('input[name=item_search]').on('input', e => {
			autocomplete()
		})

		// When hitting enter in the autocomplete, it's because user entered
		// an item code.. Search for it, and it if exists, load the info
		$('input[name=item_search]').on('keypress', e => {
			if(e.which == 13) {
				e.preventDefault();
				onSearchItemCode();
			}
		})

		// When selecting an item to add
		$('ul#itemSuggestions').on('click', 'li', e => {
			let id = $(e.currentTarget).data('item-id')
			addItem(id)
		})

		// To remove item
		$('table#items').on('click', 'tr td button', e => {
			let parent = $(e.currentTarget).parent().parent().parent().parent()
			let itemId = parent.data('item-id')

			parent.remove()

			let indexToRemove = -1
			itemsAdded.forEach((item, i) => {
				if(itemId == item.id)
					indexToRemove = i
			})
			itemsAdded.splice(indexToRemove, 1)
		})

		// When changing transfer quantity of an item
		$('body').on('input', 'table#items tr td input', e => {
			updateTotals()
		})

		$('form').on('submit', e => {
			e.preventDefault()
			createTransfer()
		})
	})
})(jQuery)

function onSearchItemCode() {
	let itemCode = $('input[name=item_search]').val()

	$('input[name=item_search]').blur().val('')

	axios.get(`api/items/code`, {
		params: {
			code: itemCode
		}
	}).then(response => {
		addItem(response.data.id)
	})
}

function autocomplete() {
	let search = $('input[name=item_search]').val()

	axios.get(`api/items/list`, {
		params: {
			search: search
		}
	}).then(response => {
		$('ul#itemSuggestions').empty()

		response.data.forEach(item => {
			let elem = `<li data-item-id="${item.id}">`
				+ `<span class="item-name">${item.name}</span>`
				+ `<span class="item-code">${item.code}</span>`
				+ '</li>'

			$('ul#itemSuggestions').append(elem)
		})
	})
}

function loadWarehouses() {
	axios.get(`api/warehouses/list`).then(response => {
		let elems = []

		response.data.forEach(warehouse => {
			elems += `<option value="${warehouse.id}">${warehouse.name}</option>`
		})

		$('select#from_warehouse').append(elems)
		$('select#to_warehouse').append(elems)
	})
}

function addItem(itemId) {
	// Item added already? Let the user know
	if($(`table#items tr[data-item-id="${itemId}"]`).length) {
		showError("<?= langSlashes('Errors.error') ?>", "<?= langSlashes('Errors.transfers.frontend.item_already_added') ?>")
		return
	}

	axios.get(`api/items/${itemId}`).then(response => {
		let item = response.data

		// Get from and to warehouse details of this item
		let quantities = {}
		item.quantities.forEach(o => {
			if(o.warehouse.id == selectedFromWarehouse)
				quantities.from = o
			else if(o.warehouse.id == selectedToWarehouse)
				quantities.to = o
		})

		item.quantities = quantities
		item.transfer_quantity = 0

		itemsAdded.push(item)

		let td1 = '<div class="d-flex">'
			+ '<div>'
			+ '<button type="button" class="btn item-delete btn-secondary"><i class="fas fa-trash-alt"></i></button>'
			+ '</div>'
			+ '<div>'
			+ `<strong>${item.name}</strong>`
			+ '<br />'
			+ item.code
			+ '</div>'
			+ '</div>'
		let td2 = item.quantities.from.quantity
		let td3 = '<div class="input-group input-group-sm">'
				+ '<input type="text" class="form-control form-control-sm" value="0" />'
				+ '</div>'
		let td4 = `${item.quantities.from.quantity} ${arrowRight} ${item.quantities.from.quantity}`
		let td5 = `${item.quantities.to.quantity} ${arrowRight} ${item.quantities.to.quantity}`

		//table#items
		let elem = `<tr data-item-id="${itemId}">`
			+ `<td>${td1}</td>`
			+ `<td data-item-td="current_stock_from">${td2}</td>`
			+ `<td data-item-td="transfer_quantity">${td3}</td>`
			+ `<td data-item-td="source_warehouse_change">${td4}</td>`
			+ `<td data-item-td="target_warehouse_change">${td5}</td>`
			+ '</tr>'

		$('table#items').append(elem)
	})
}

function updateTotals() {
	itemsAdded.forEach((item, i) => {
		let input = $(`table#items tbody tr[data-item-id=${item.id}] input`)

		let transferQuantity = Utils.getInt(input.val())

		// If transfer quantity is greater than stock available, rewrite user input
		if(transferQuantity > item.quantities.from.quantity) {
			transferQuantity = Utils.getInt(item.quantities.from.quantity)
			input.prop('value', transferQuantity)
			input.val(transferQuantity)
		}

		// Update original array
		itemsAdded[i].transfer_quantity = transferQuantity

		let fromQuantity = Utils.getInt(item.quantities.from.quantity)
		let toQuantity = Utils.getInt(item.quantities.to.quantity)

		let currentStockFrom = Utils.getInt(item.quantities.from.quantity)
		let sourceWarehouseChange = `${fromQuantity} ${arrowRight} ${fromQuantity - transferQuantity}`
		let targetWarehouseChange = `${toQuantity} ${arrowRight} ${toQuantity + transferQuantity}`

		$(`table#items tbody tr[data-item-id=${item.id}] td[data-item-td="current_stock_from"]`).html(currentStockFrom)
		$(`table#items tbody tr[data-item-id=${item.id}] td[data-item-td="source_warehouse_change"]`).html(sourceWarehouseChange)
		$(`table#items tbody tr[data-item-id=${item.id}] td[data-item-td="target_warehouse_change"]`).html(targetWarehouseChange)
	})
}

function createTransfer() {
	// Perform initial validation
	let validator = new Validator()
	validator.addSelect('from_warehouse', 'selected', "<?= langSlashes('Validation.transfers.frontend.from_warehouse_not_selected') ?>")
	validator.addSelect('to_warehouse', 'selected', "<?= langSlashes('Validation.transfers.frontend.to_warehouse_not_selected') ?>")

	if(!validator.validate())
		return

	// Now make sure we have at least one item
	if($('table#items tbody tr').length == 0) {
		showError('<?= langSlashes('Errors.error') ?>', "<?= langSlashes('Errors.transfers.frontend.item_not_added') ?>")
		return
	}

	// Build data object!
	let data = {
		from_warehouse_id: $('select[name=from_warehouse]').val(),
		to_warehouse_id: $('select[name=to_warehouse]').val(),
		notes: $('textarea[name=notes]').val(),
		items: []
	}

	itemsAdded.forEach(item => {
		data.items.push({
			id: item.id,
			name: item.name,
			code: item.code,
			original_from_quantity: item.quantities.from.quantity,
			original_to_quantity: item.quantities.to.quantity,
			transfer_quantity: item.transfer_quantity
		})
	})

	axios.post(`api/transfers`, data).then(response => {
		if(response && response.data && response.data.id)
			location.href = `<?= base_url() ?>/transfers/${response.data.id}`
	})
}
</script>
<?= $this->endSection() ?>