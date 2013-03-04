<% with $Extension %>
	<div class="page-header">
		<h1><a href="$VendorLink">$VendorName</a> / $PackageName</h1>

		<% if $Description %>
			<p class="lead">$Description</p>
		<% end_if %>
	</div>

	<div class="clearfix">
		<% if $Screenshots %>
			<ul class="screenshots inline pull-right">
				<% loop $Screenshots %>
					<li>
						<a href="$URL">
							<img src="$CroppedImage(160, 160).URL" alt="$Up.Name.ATT" class="img-polaroid">
						</a>
					</li>
				<% end_loop %>
			</ul>
		<% end_if %>

		<dl>
			<dt>Authors</dt>
			<dd><% loop $Authors %><a href="$Link">$Name</a><% if not $Last %>, <% end_if %><% end_loop %></dd>

			<dt>Packagist</dt>
			<dd><a href="$PackagistUrl">$PackagistUrl</a></dd>

			<dt>Repository</dt>
			<dd><a href="$Repository">$Repository</a></dd>

			<% with $Versions.First %>
				<% if $Homepage %>
					<dt>Homepage</dt>
					<dd><a href="$Homepage">$Homepage</a></dd>
				<% end_if %>
			<% end_with %>
		</dl>
	</div>

	<hr>

	<% with $Versions.First %>
		<h3>Version $DisplayVersion</h3>
	<% end_with %>

	<hr>

	<% if $Readme %>
		<div class="readme well collapsed">
			<div class="content">
				$Readme
			</div>
			<a href="#" class="toggle">
				<i class="icon-arrow-up"></i>
				<i class="icon-arrow-down"></i>
			</a>
		</div>
	<% end_if %>

	<% if $Versions.Count != 1 %>
		<h3>Other Versions</h3>

		<div id="versions" class="accordion">
			<% loop $Versions %>
				<% if not $First %>
					<div class="accordion-group">
						<div class="accordion-heading">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#versions" href="#version-$ID">
								$DisplayVersion
							</a>
						</div>
						<div id="version-$ID" class="accordion-body collapse">
							<div class="accordion-inner">
							</div>
						</div>
					</div>
				<% end_if %>
			<% end_loop %>
		</div>
	<% end_if %>
<% end_with %>
