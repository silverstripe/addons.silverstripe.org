<form id="home-extensions-search" action="/extensions" method="get">
	<input name="search" type="text" class="input-block-level">
	<button type="submit" class="btn"><i class="icon-search"></i> Search Extensions</button>
</form>

<div class="row">
	<div class="extensions-box span6">
		<h3><a href="/extensions?sort=downloads">Popular Extensions</a></h3>
		<ol class="extensions">
			<% loop $PopularExtensions(6) %>
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

	<div class="extensions-box span6">
		<h3><a href="/extensions?sort=newest">Newest Extensions</a></h3>
		<ol class="extensions">
			<% loop $NewestExtensions(6) %>
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
	<div class="extensions-box span6">
		<h3>Random Extensions</h3>
		<ul class="extensions">
			<% loop $RandomExtensions(6) %>
				<li>
					<a href="$Link">
						<span class="name">$Name</span>
						<span class="description">$Description</span>
					</a>
				</li>
			<% end_loop %>
		</ul>
	</div>

	<div class="extensions-box span6">
		<h3>Newest Releases</h3>
		<ol class="extensions">
			<% loop $NewestReleases(6) %>
				<li>
					<a href="$Extension.Link">
						<span class="meta">$DisplayVersion</span>
						<span class="name">$Name</span>
						<span class="description">$Description</span>
					</a>
				</li>
			<% end_loop %>
		</ol>
	</div>
</div>
