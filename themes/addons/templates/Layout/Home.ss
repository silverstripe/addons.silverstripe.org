<div class="row">
	<div class="span12">
	Welcome to the <a href="http://silverstripe.org">SilverStripe</a> add-on repository, based on the <a href="http://getcomposer.org">Composer</a> packaging system for PHP.  Use this site to find modules and themes to add to your SilverStripe website.  For best results, we recommend <a href="http://docs.silverstripe.org/en/getting_started/composer">managing your project with Composer</a>.
	</div>
</div>

<form id="home-search" action="/add-ons" method="get">
	<div class="addons-search-row">
		<label for="addons-search">Search for</label>
		<input id="addons-search" type="text" name="search" class="input-block-level">

		<button type="submit" class="btn">
			<i class="icon-search"></i> Search Add-ons
		</button>
	</div>
</form>

<hr>

<div class="row">
	<div class="addons-box span6">
		<h3><a href="/add-ons?sort=downloads">Popular Add-ons</a></h3>
		<ol>
			<% loop $PopularAddons(5) %>
				<li>
					<a href="$Link">
						<span class="meta"><% include AddonDownloadStats %></span>
						<span class="name">$Name</span>
						<% include ModuleRatingVisual SmallCircle=true %>
						<span class="description">$Description</span>
					</a>
				</li>
			<% end_loop %>
		</ol>
	</div>

	<div class="addons-box span6">
		<h3>
			<a href="/add-ons?sort=newest">Newest Add-ons</a>
			<a class="pull-right" href="/add-ons/rss"><img src="themes/addons/images/feed-icon-14x14.png" alt="RSS Feed" /></a>
		</h3>
		<ol>
			<% loop $NewestAddons(5) %>
				<li>
					<a href="$Link">
						<span class="meta">$Released.Date</span>
						<span class="name">$Name</span>
						<% include ModuleRatingVisual SmallCircle=true %>
						<span class="description">$Description</span>
					</a>
				</li>
			<% end_loop %>
		</ol>
	</div>
</div>

<div class="row">
	<div class="addons-box span6">
		<h3>Newest Releases</h3>
		<ol>
			<% loop $NewestVersions(5) %>
				<li>
					<a href="$Addon.Link">
						<span class="meta">$Released.Date</span>
						<span class="name">$Name</span>
						<% include ModuleRatingVisual SmallCircle=true %>
						<span class="description">$Description</span>
					</a>
				</li>
			<% end_loop %>
		</ol>
	</div>

	<div class="addons-box span6">
		<h3>Random Add-ons</h3>
		<ul>
			<% loop $RandomAddons(5) %>
				<li>
					<a href="$Link">
						<span class="name">$Name</span>
						<% include ModuleRatingVisual SmallCircle=true %>
						<span class="description">$Description</span>
					</a>
				</li>
			<% end_loop %>
		</ul>
	</div>
</div>

<% cached List(Addon).Max(Created) %>
<div class="row">
	<div class="addons-box span12">
		<h3>Statistics</h3>
		<ul>
			<li>
				<% loop ChartData %>
					<div class="chart-data" data-x="$XValue" data-y="$YValue"></div>
				<% end_loop %>
				<div  id="chart-canvas"></div>
			</li>
		</ul>
	</div>
</div>
<% end_cached %>
