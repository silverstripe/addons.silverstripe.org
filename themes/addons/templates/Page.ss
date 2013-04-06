<!DOCTYPE html>
<html>
	<head>
		<% base_tag %>

		<title>$Title &middot; SilverStripe Add-ons</title>
		$MetaTags(false)

		<% require themedCSS("addons") %>
		<% require javascript("//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js") %>
		<% require javascript("themes/addons/bootstrap/js/bootstrap.min.js") %>
		<% require javascript("themes/addons/javascript/addons.js") %>
	</head>
	<body>
		<div id="header" class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="/">
						<img src="$ThemeDir/images/logo.png" alt="SilverStripe Add-ons">
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
