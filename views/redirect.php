<!DOCTYPE html>
<html>
	<head>
		<title>Redirecting...</title>
		<meta http-equiv="refresh" content="0;url=<?php echo $this->redirect_url; ?>">
	</head>
	<body>
		<script type="text/javascript">
			window.location = "<?php echo $this->redirect_url; ?>";
		</script>

		<p><?php echo sprintf( "You are being redirected to %s", '<a href="' . $this->redirect_url . '">' . $this->redirect_url . '</a>' ); ?></p>
	</body>
</html>
