<!DOCTYPE html>
<html>
	<head>
		<% base_tag %>

		<title>$Title &middot; SilverStripe Add-ons</title>
		$MetaTags(false)
		
		<% require themedCSS("addons") %>
		<% require css("//silverstripe.org/toolbar/css/toolbar.css") %>
		<% require javascript("//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js") %>
		<% require javascript("themes/addons/bootstrap/js/bootstrap.min.js") %>
		<% require javascript("themes/addons/javascript/addons.js") %>
		<% require javascript("//silverstripe.org/toolbar/javascript/toolbar.js?site=addons&amp;searchShow=true") %>
		<% require javascript("//www.google.com/jsapi") %>
		<% require javascript("themes/addons/javascript/chart.js") %>
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
							<li class="<% if $Active %>active<% end_if %> $MenuItemType">
								<a href="$Link"<% if MenuItemType == 'button' %> class="btn"<% end_if %>>$Title</a>
							</li>
						<% end_loop %>
					</ul>
				</div>
			</div>
		</div>

		<div id="layout" class="container">
			$Layout
		</div>

		<% include Footer %>

		<% if GATrackingCode %>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', '$GATrackingCode', 'silverstripe.org');
		  ga('send', 'pageview');
		</script>
		<% end_if %>
	</body>
</html>
