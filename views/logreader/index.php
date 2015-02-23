<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="" />
		<meta name="author" content="" />
		<link rel="shortcut icon" href="<?php print LogReader_URL::static_base(); ?>ico/favicon.ico" />
		<title><?php print ucfirst($content->name); ?> - Kohana LogReader</title>
		<link href="<?php print LogReader_URL::static_base(); ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" />
		<link href="<?php print LogReader_URL::static_base(); ?>bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" />
		<link href="<?php print LogReader_URL::static_base(); ?>bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
		<!--[if lt IE 9]>
		<script src="<?php print LogReader_URL::static_base(); ?>bootstrap/js/html5shiv.js"></script>
		<script src="<?php print LogReader_URL::static_base(); ?>bootstrap/js/respond.min.js"></script>
		<![endif]-->
		<link href="<?php print LogReader_URL::static_base(); ?>css/logreader.css" rel="stylesheet" />
		<?php if (isset($stylesheets) && $stylesheets): foreach ($stylesheets as $stylesheet): ?>
		<link href="<?php print $stylesheet; ?>" rel="stylesheet" />
		<?php endforeach; endif; ?>
		<script>
		var baseUrl = '<?php print LogReader_URL::base(); ?>';
		var apiUrl = '<?php print LogReader_URL::api_base(); ?>';
		</script>
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-static-top" role="navigation">
			<div class="container- fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?php print LogReader_URL::base(); ?>" title="Kohana LogReader">Kohana LogReader</a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav navbar-right">
						<?php if ($user !== NULL): ?>
						<li><a href="javascript:void(0);">Signed in as <?php print $user['username']; ?></a></li>
						<li><a href="<?php print LogReader_URL::logout_url(); ?>" title="Logout">Logout</a></li>
						<?php endif; ?>
					</ul>
					<ul class="nav navbar-nav">
						<li <?php if ($content->name === 'messages') print 'class="active"'; ?>><a href="<?php print LogReader_URL::base(); ?>" title="Messages">Messages</a></li>
						<li <?php if ($content->name === 'about') print 'class="active"'; ?>><a href="<?php print LogReader_URL::base(); ?>about" title="About">About</a></li>
					</ul>
					<?php if ($content->name === 'messages' && $is_tester_available): ?>
					<button type="button" class="btn btn-default navbar-btn" id="create-test-message-btn">Create Test Message</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div id="content">
			<?php print $content; ?>
		</div>
		<script src="<?php print LogReader_URL::static_base(); ?>js/underscore-min.js"></script>
		<script src="<?php print LogReader_URL::static_base(); ?>js/jquery-1.11.1.min.js"></script>
		<script src="<?php print LogReader_URL::static_base(); ?>js/moment.min.js"></script>
		<script src="<?php print LogReader_URL::static_base(); ?>bootstrap/js/bootstrap.min.js"></script>
		<script src="<?php print LogReader_URL::static_base(); ?>bootstrap/js/bootstrap-datetimepicker.min.js"></script>
		<?php if (isset($javascripts) && $javascripts): foreach ($javascripts as $javascript): ?>
		<script src="<?php print $javascript; ?>"></script>
		<?php endforeach; endif; ?>
	</body>
</html>
