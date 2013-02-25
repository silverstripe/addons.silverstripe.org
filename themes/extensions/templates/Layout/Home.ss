<div class="page-header">
	<h1>Home</h1>
</div>

<form action="/extensions" method="get">
	<input name="search" type="text" class="input-block-level">
	<button type="submit" class="btn"><i class="icon-search"></i> Search Extensions</button>
</form>

<div class="row">
	<div class="span6">
		<h3><a href="/extensions?sort=popular">Popular Extensions</a></h3>
		<ol>
			<% loop $PopularExtensions %>
				<li>
					<a href="$Link">$Title</a> <i class="icon-download"></i> $Downloads
				</li>
			<% end_loop %>
		</ol>
	</div>

	<div class="span6">
		<h3><a href="/extensions?sort=newest">Newest Extensions</a></h3>
		<ol>
			<% loop $NewestExtensions %>
				<li>
					<a href="$Link">$Title</a> $Released.Date
				</li>
			<% end_loop %>
		</ol>
	</div>
</div>

<div class="row">
	<div class="span6">
		<h3>Random Extensions</h3>
		<ul>
			<% loop $RandomExtensions %>
				<li>
					<a href="$Link">$Title</a>
				</li>
			<% end_loop %>
		</ul>
	</div>

	<div class="span6">
		<h3>Newest Releases</h3>
		<ol>
			<% loop $NewestReleases %>
				<li>
					<a href="$Extension.Link">$Title</a> $DisplayVersion
				</li>
			<% end_loop %>
		</ol>
	</div>
</div>
