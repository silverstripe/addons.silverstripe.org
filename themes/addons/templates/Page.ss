<!DOCTYPE html>
<html>
	<head>
		<% base_tag %>

		<title>$Title &middot; SilverStripe Add-ons</title>
		$MetaTags(false)

		<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
		<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/favicon-194x194.png" sizes="194x194">
		<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
		<link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
		<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/manifest.json"> <%-- Controls which icon to use for Android Chrome --%>
		<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="msapplication-TileColor" content="#005b94">
		<meta name="msapplication-TileImage" content="/mstile-144x144.png">
		<meta name="theme-color" content="#1b354c">
		
		<% require themedCSS("addons") %>
		<% require javascript("//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js") %>
		<% require javascript("themes/addons/bootstrap/js/bootstrap.min.js") %>
		<% require javascript("themes/addons/javascript/addons.js") %>
		<% require javascript("//www.google.com/jsapi") %>
		<% require javascript("themes/addons/javascript/chart.js") %>
		<link rel="stylesheet" href="themes/addons/css/ionicons.min.css" />
		<script type="text/javascript" src="//use.typekit.net/emt4dhq.js"></script>
		<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
		<script>window.GLOBAL_NAV_SECONDARY_ID = 16;</script>
	</head>
	<body class="theme-theme1">
		<header class="site-header" data-0="background-position: 50% 50%;" data-544="background-position: 50% -30%;">
			<div class="global-nav header-mask">
				<div id="navWrapper">
					$GlobalNav('addons')
					<% include SearchBox %>
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
				'silverstripe.com',
				'silverstripe.org',
				'addons.silverstripe.org',
				'api.silverstripe.org',
				'doc.silverstripe.org',
				'userhelp.silverstripe.org',
				'demo.silverstripe.org'
			]);
			ga('send', 'pageview')
		</script>
	</body>
</html>
