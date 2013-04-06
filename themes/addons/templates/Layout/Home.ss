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
						<span class="meta"><i class="icon-download"></i> $Downloads</span>
						<span class="name">$Name</span>
						<span class="description">$Description</span>
					</a>
				</li>
			<% end_loop %>
		</ol>
	</div>

	<div class="addons-box span6">
		<h3><a href="/add-ons?sort=newest">Newest Add-ons</a></h3>
		<ol>
			<% loop $NewestAddons(5) %>
				<li>
					<a href="$Link">
						<span class="meta">$Released.Date</span>
						<span class="name">$Name</span>
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
						<span class="description">$Description</span>
					</a>
				</li>
			<% end_loop %>
		</ul>
	</div>
</div>
