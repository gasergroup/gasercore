<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex">

	<title><?= lang('Errors.production_title') ?></title>

	<style type="text/css">
		<?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
	</style>
</head>
<body>

	<div class="container text-center">

		<h1 class="headline"><?= lang('Errors.production_title') ?></h1>

		<p class="lead"><?= lang('Errors.production_msg') ?></p>

	</div>

</body>

</html>
