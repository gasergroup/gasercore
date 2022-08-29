<?php

function offer_csv_download(array $content, $filename) {
	$csv = '';
	if(count($content) > 0) {
		ob_start();
		$pointer = fopen('php://output', 'w');
		fputcsv($pointer, array_keys((array) ($content[0])));
		foreach($content as $obj)
			fputcsv($pointer, (array) $obj);
		fclose($pointer);

		$csv = ob_get_clean();
	}
	
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");

	// disposition / encoding on response body
	header("Content-Disposition: attachment;filename={$filename}.csv");
	header("Content-Transfer-Encoding: binary");

	echo $csv;
}