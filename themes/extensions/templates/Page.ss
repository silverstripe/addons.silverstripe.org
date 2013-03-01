<!DOCTYPE html>
<html>
	<head>
		<% base_tag %>

		<title>$Title &middot; SilverStripe Extensions</title>
		$MetaTags(false)

		<% require css("themes/extensions/bootstrap/css/bootstrap.min.css") %>
		<% require themedCSS("main") %>

		<% require javascript("//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js") %>
		<% require javascript("themes/extensions/javascript/main.js") %>
	</head>
	<body>
		<div id="header" class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="/">
						<img src="$ThemeDir/images/header-logo.png" alt="SilverStripe Extensions">
					</a>
					<ul class="nav">
						<% loop $Menu %>
							<li class="<% if $Active %>active<% end_if %>">
								<a href="$Link">$Title</a>
							</li>
						<% end_loop %>
					</ul>
				</div>
			</div>
		</div>

		<div id="layout" class="container">
			$Layout
		</div>
	</body>
</html>
