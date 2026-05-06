<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>login check</title>
</head>
<body>
<script>
	(function() {
		var data = "{json_encode($data)}"

		if(window.parent) {
			window.parent.postMessage(data, 'https://{$recipienthost}');
		}
	})();
</script>
</body>
</html>