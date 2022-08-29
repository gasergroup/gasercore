<?php namespace App\Controllers\Backend;

use NumberFormatter;

class Stats extends BaseController {
	/**
	 * To get initial stats for supervisors and admins
	 * 
	 * Supervisors will only get stats related to the warehouses
	 * they have access to
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function index($timeframe) {
		$today = new \DateTimeImmutable('now');

		// Configure timeframe
		if($timeframe == 'today') {
			$from = $today;
			$to = $today;
		}else if($timeframe == '7-days') {
			$from = $today->modify('7 days ago');
			$to = $today;
		}else if($timeframe == 'this-month') {
			$from = $today->modify('first day of this month');
			$to = $today->modify('last day of this month');
		}else if($timeframe == 'this-year') {
			$from = $today->modify('first day of january');
			$to = $today->modify('last day of december');
		}else if($timeframe == 'last-year') {
			$from = $today->modify('first day of january last year');
			$to = $today->modify('last day of december last year');
		}else{
			return $this->fail(lang('Errors.stats.wrong_timeframe'));
		}

		// Convert timeframe to an actual date
		$from = $from->format('Y-m-d');
		$to = $to->format('Y-m-d');

		if($this->logged_user->role == 'admin') {
			// If user is admin, we'll get all of the information...

			$sales = $this->sales->statSales($from, $to);
			$sales_returns = $this->sales_returns->statSalesReturns($from, $to);
			$purchases = $this->purchases->statPurchases($from, $to);
			$purchases_returns = $this->purchases_returns->statPurchasesReturns($from, $to);
			$value_in_stock = $this->items->statValueInStock();

		}else{
			// If user is supervisor, we'll restrict records by the warehouses
			// he has access to
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);

			$sales = $this->sales->statSales($from, $to, true, $warehouseIds);
			$sales_returns = $this->sales_returns->statSalesReturns($from, $to, true, $warehouseIds);
			$purchases = $this->purchases->statPurchases($from, $to, true, $warehouseIds);
			$purchases_returns = $this->purchases_returns->statPurchasesReturns($from, $to, true, $warehouseIds);
			$value_in_stock = $this->items->statValueInStock(true, $warehouseIds);
		}

		// Calculate stats
		$revenue = $sales - $sales_returns;
		$profits = $revenue - ($purchases - $purchases_returns);

		// Fix floating-point issues
		$revenue = number_format($revenue, 2);
		$profits = number_format($profits, 2);
		$purchases = number_format($purchases, 2);
		$value_in_stock = number_format($value_in_stock, 2);

		return $this->respond([
			'revenue' => $revenue,
			'profits' => $profits,
			'purchases' => $purchases,
			'value_in_stock' => $value_in_stock
		]);
	}

	/**
	 * To get cash flow information ready to be graphed
	 * 
	 * Supervisors will only get stats related to the warehouses
	 * they have access to
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function cash_flow($timeframe) {
		// Configure timeframe
		if($timeframe == '7-days')
			$days = $this->createFormattedPeriod('6 days ago', 'today', 'P1D', 'Y-m-d');
		else if($timeframe == 'this-month')
			$days = $this->createFormattedPeriod('first day of this month', 'last day of this month', 'P1D', 'Y-m-d');
		else if($timeframe == 'last-month')
			$days = $this->createFormattedPeriod('first day of previous month', 'last day of previous month', 'P1D', 'Y-m-d');
		else if($timeframe == 'this-year')
			$days = $this->createFormattedPeriod('first day of january this year', 'last day of december this year', 'P1M', 'Y-m');
		else if($timeframe == 'last-year')
			$days = $this->createFormattedPeriod('first day of january previous year', 'last day of december previous year', 'P1M', 'Y-m');
		else
			return $this->fail(lang('Errors.stats.wrong_timeframe'));

		// If user is admin, we'll get all of the information
		// If user is supervisor, we'll restrict records by the warehouses
		// he has access to
		if($this->logged_user->role == 'admin') {
			$limitByWarehouses = false;
			$warehouseIds = [];
		}else{
			$limitByWarehouses = true;
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
		}

		// Get data, depending on the timeframe
		if($timeframe == '7-days' || $timeframe == 'this-month' || $timeframe == 'last-month') {
			$sales = $this->sales->statSalesForGraphWithRange($days[0], end($days), $limitByWarehouses, $warehouseIds);
			$sales_returns = $this->sales_returns->statSalesReturnsForGraphWithRange($days[0], end($days), $limitByWarehouses, $warehouseIds);
			$purchases = $this->purchases->statPurchasesForGraphWithRange($days[0], end($days), $limitByWarehouses, $warehouseIds);
			$purchases_returns = $this->purchases_returns->statPurchasesReturnsForGraphWithRange($days[0], end($days), $limitByWarehouses, $warehouseIds);
		}else{
			$year = explode('-', $days[0])[0];
			$sales = $this->sales->statSalesForGraphWithYear($year, $limitByWarehouses, $warehouseIds);
			$sales_returns = $this->sales_returns->statSalesReturnsForGraphWithYear($year, $limitByWarehouses, $warehouseIds);
			$purchases = $this->purchases->statPurchasesForGraphWithYear($year, $limitByWarehouses, $warehouseIds);
			$purchases_returns = $this->purchases_returns->statPurchasesReturnsForGraphWithYear($year, $limitByWarehouses, $warehouseIds);
		}

		// Now transform results, to index being the date:
		// [ [date1 => value1], [date2 => value2]...]
		$sales = $this->toAssociativeDateArray($sales, 'grand_total');
		$sales_returns = $this->toAssociativeDateArray($sales_returns, 'grand_total');
		$purchases = $this->toAssociativeDateArray($purchases, 'grand_total');
		$purchases_returns = $this->toAssociativeDateArray($purchases_returns, 'grand_total');

		// Fill missing dates, and also build incomes/expenses/profits data
		$finalSales = [];
		$finalSalesReturns = [];
		$finalPurchases = [];
		$finalPurchasesReturns = [];
		$incomes = [];
		$expenses = [];
		$profits = [];
		foreach($days as $day) {
			$finalSales[$day] = $sales[$day] ?? 0;
			$finalSalesReturns[$day] = $salesReturns[$day] ?? 0;
			$finalPurchases[$day] = $purchases[$day] ?? 0;
			$finalPurchasesReturns[$day] = $purchasesReturns[$day] ?? 0;

			$incomes[$day] = $finalSales[$day] - $finalSalesReturns[$day];
			$expenses[$day] = $finalPurchases[$day] - $finalPurchasesReturns[$day];
			$profits[$day] = $incomes[$day] - $expenses[$day];
		}

		// Done!
		return $this->respond([
			'labels' => $days,
			'incomes' => array_values($incomes),
			'expenses' => array_values($expenses),
			'profits' => array_values($profits)
		]);
	}

	// Creates a period between two dates
	private function createPeriod($timeBegin, $timeEnd, $interval, $includeEnd = true) {
		$begin = date_create_immutable($timeBegin);
		$end = date_create_immutable($timeEnd);

		if($includeEnd)
			$end = $end->modify('+1 day');

		$i = new \DateInterval($interval);
		$period = new \DatePeriod($begin, $i, $end);

		return $period;
	}

	// Creates a period array
	private function createFormattedPeriod($timeBegin, $timeEnd, $interval, $format, $includeEnd = true) {
		$period = $this->createPeriod($timeBegin, $timeEnd, $interval, $includeEnd);
		$a = [];

		foreach($period as $p)
			$a[] = $p->format($format);
		
		return $a;
	}

	// Converts an array of results, to an array where the index of each
	// element is the date:
	// [ [date1 => value1], [date2 => value2]...]
	private function toAssociativeDateArray($arr, $valueName) {
		$res = [];
		foreach($arr as $item)
			$res[$item->created_at] = $item->{$valueName};
			
		return $res;
	}
}