<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>

<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section variant-3">
			<div class="header">
				<div class="title">
					<?= lang('Main.adjustments.new_adjustment') ?>
				</div>
				<div class="desc">
					<?= lang('Main.adjustments.new_adjustment_description') ?>
				</div>
			</div>

			<div class="content">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.adjustments.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="warehouse" class="d-block"><?= lang('Main.adjustments.warehouse') ?>*</label>
								<select name="warehouse" id="warehouse" class="custom-select">
									<option value="" disabled selected><?= lang('Main.adjustments.select_warehouse') ?></option>
								</select>
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">

						</div>
					</div>

					<div class="row mt-4">
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary"><?= lang('Main.adjustments.add_items') ?></h6>
							<span class="autocomplete-desc mb-3">
								<?= lang('Main.adjustments.add_items_description') ?>
							</span>

							<div class="autocomplete-container">
								<input type="text" id="item_search" name="item_search" placeholder="<?= langSlashes('Main.adjustments.add_items_placeholder') ?>" autocomplete="off" class="form-control" disabled />
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
											<th><?= lang('Main.adjustments.items.item_name') ?></th>
											<th><?= lang('Main.adjustments.items.current_stock') ?></th>
											<th><?= lang('Main.adjustments.items.adjustment_type') ?></th>
											<th><?= lang('Main.adjustments.items.adjustment_quantity') ?></th>
											<th><?= lang('Main.adjustments.items.stock_after_adjustment') ?></th>
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
								<label for="notes" class="d-block"><?= lang('Main.adjustments.notes') ?></label>
								<textarea name="notes" id="notes" class="form-control" rows="6"></textarea>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.adjustments.create') ?>
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
let selectedWarehouse = 0;

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Populate selects
		loadWarehouses()

		// Once a warehouse is selected, enable items section
		$('select[name=warehouse]').on('change', e => {
			let warehouse = $('select[name=warehouse]').val()

			if(warehouse != '' && warehouse != null) {
				selectedWarehouse = warehouse
				$('select[name=warehouse]').prop('disabled', true)
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

		// When changing adjustment type of an item
		$('table#items').on('change', 'tr td select', e => {
			console.log('select changed');
			updateTotals()
		})

		// When changing adjustment quantity of an item
		$('table#items').on('tr td input', 'input', e => {
			updateTotals()
		})

		$('form').on('submit', e => {
			e.preventDefault()
			createAdjustment()
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

		$('select#warehouse').append(elems)
	})
}

function addItem(itemId) {
	// Item added already? Let the user know
	if($(`table#items tr[data-item-id="${itemId}"]`).length) {
		showError("<?= langSlashes('Errors.error') ?>", "<?= langSlashes('Errors.adjustments.frontend.item_already_added') ?>")
		return
	}

	axios.get(`api/items/${itemId}/warehouse/${selectedWarehouse}`).then(response => {
		let item = response.data

		item.adjustment_type = 'add'
		item.adjustment_quantity = 0

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
		let td2 = item.quantity
		let td3 = '<div class="input-group input-group-sm">'
				+ '<select class="form-control form-control-sm">'
				+ '<option value="add"><?= langSlashes('Main.adjustments.add') ?></option>'
				+ '<option value="subtract"><?= langSlashes('Main.adjustments.subtract') ?></option>'
				+ '</select>'
				+ '</div>'
		let td4 = '<div class="input-group input-group-sm">'
				+ '<input type="text" class="form-control form-control-sm" value="0" />'
				+ '</div>'
		let td5 = 0

		//table#items
		let elem = `<tr data-item-id="${itemId}">`
			+ `<td>${td1}</td>`
			+ `<td data-item-td="current_stock">${td2}</td>`
			+ `<td data-item-td="adjustment_type">${td3}</td>`
			+ `<td data-item-td="adjustment_quantity">${td4}</td>`
			+ `<td data-item-td="stock_after_adjustment">${td5}</td>`
			+ '</tr>'

		$('table#items').append(elem)
	})
}

function updateTotals() {
	itemsAdded.forEach((item, i) => {
		let select = $(`table#items tbody tr[data-item-id=${item.id}] select`)
		let input = $(`table#items tbody tr[data-item-id=${item.id}] input`)

		let adjustmentType = select.val()
		let adjustmentQuantity = Utils.getInt(input.val())

		// If adjustment type is subtract, and adjustment quantity is grater than
		// stock, rewrite user input
		if(adjustmentType == 'subtract' && adjustmentQuantity > item.quantity) {
			adjustmentQuantity = item.quantity
			$(`table#items tbody tr [data-item-id=${item.id}] input`).val(adjustmentQuantity)
		}

		// Update original array
		itemsAdded[i].adjustment_type = adjustmentType
		itemsAdded[i].adjustment_quantity = adjustmentQuantity

		let stockAfterAdjustment = Utils.getInt(item.quantity)
		if(adjustmentType == 'add')
			stockAfterAdjustment += adjustmentQuantity
		else
			stockAfterAdjustment -= adjustmentQuantity

		$(`table#items tbody tr[data-item-id=${item.id}] td[data-item-td="stock_after_adjustment"]`).html(stockAfterAdjustment)
	})
}

function createAdjustment() {
	// Perform initial validation
	let validator = new Validator()
	validator.addSelect('warehouse', 'selected', "<?= langSlashes('Validation.adjustments.frontend.warehouse_not_selected') ?>")

	if(!validator.validate())
		return

	// Now make sure we have at least one item
	if($('table#items tbody tr').length == 0) {
		showError('<?= langSlashes('Errors.error') ?>', "<?= langSlashes('Errors.adjustments.frontend.item_not_added') ?>")
		return
	}

	// Build data object!
	let data = {
		warehouse_id: $('select[name=warehouse]').val(),
		notes: $('textarea[name=notes]').val(),
		items: []
	}

	itemsAdded.forEach(item => {
		data.items.push({
			id: item.id,
			name: item.name,
			code: item.code,
			quantity: item.quantity,
			adjustment_type: item.adjustment_type,
			adjustment_quantity: item.adjustment_quantity
		})
	})

	axios.post(`api/adjustments`, data).then(response => {
		if(response && response.data && response.data.id)
			location.href = `<?= base_url() ?>/adjustments/${response.data.id}`
	})
}
</script>
<?= $this->endSection() ?>