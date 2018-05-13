<!DOCTYPE html>
<html>
	<head>
		<% base_tag %>
		<title>$Title &middot; SilverStripe Add-ons</title>
		$MetaTags(false)
		<% require themedCSS("addons") %>
		<% include Favicons %>
		<link rel="stylesheet" href="$ThemeDir/css/ionicons.min.css" />
		<script type="text/javascript" src="//use.typekit.net/emt4dhq.js"></script>
		<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	</head>

	<body class="theme-theme1">
		<header class="site-header" data-0="background-position: 50% 50%;" data-544="background-position: 50% -30%;">
			<div class="global-nav header-mask">
				<div id="navWrapper">
					$GlobalNav('addons')
					<!-- to do: include SearchBox once toolbar is upgraded -->
				</div>
			</div>
		</header>
		<div id="header" class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
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

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-84547-17', 'auto', {'allowLinker': true});
			ga('require', 'linker');
			ga('linker:autoLink', [
				'www.silverstripe.com',
				'www.silverstripe.org',
				'addons.silverstripe.org',
				'api.silverstripe.org',
				'docs.silverstripe.org',
				'userhelp.silverstripe.org',
				'demo.silverstripe.org'
			]);
			ga('send', 'pageview')
		</script>
	</body>
</html>
