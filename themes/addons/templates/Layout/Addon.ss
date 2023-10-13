<% with $Addon %>
	<div class="page-header">
		<p>
			<a href="/add-ons" class="btn btn-plain"><i class="icon-arrow-left"></i> Back to add-ons</a>
		</p>

		<h1>
			<a href="$VendorLink">$VendorName</a> / $PackageName
		</h1>

		<p>$Description</p>
	</div>

	<% if not $LastBuilt %>
		<p class="alert">
			The detailed information for this add-on has not yet been built.
		</p>
	<% end_if %>

	<% if $Screenshots %>
		<ul class="screenshots inline">
			<% loop $Screenshots %>
				<li>
					<a href="$URL" target="_blank">
						<img src="$CroppedImage(160, 160).URL" alt="$Up.Name.ATT" class="img-polaroid">
					</a>
				</li>
			<% end_loop %>
		</ul>
	<% end_if %>

    <% if $Abandoned %>
        <p class="alert alert-warning">
            This package is abandoned and no longer maintained. <%-- It is suggested to use <a href="https://addons.silverstripe.org/add-ons/$Abandoned.ATT">$Abandoned.XML</a> instead.--%>
        </p>
    <% end_if %>

	<dl id="metadata">
		<% with $MasterVersion %>
			<dt>Homepage:</dt>
			<dd>
				<% if $DisplayHomepage %>
				    <a id="homepage-url" href="$DisplayHomepage" rel="nofollow">$DisplayHomepage</a>
                <% else %>N/A<% end_if %>
			</dd>
		<% end_with %>

		<dt>Packagist:</dt>
		<dd>
            <% if $Repository %>
                <a href="$PackagistUrl" rel="nofollow">$PackagistUrl</a>
            <% else %>N/A<% end_if %>
        </dd>

		<dt>Repository:</dt>
		<dd>
            <% if $Repository %>
                <a id="repository-url" href="$Repository" rel="nofollow">$Repository</a>
            <% else %>N/A<% end_if %>
        </dd>

		<% if $Authors %>
			<dt>Authors:</dt>
			<dd>
				<ul id="authors">
				<% loop $Authors %>
					<li>
						<a href="$Link">
							<img src="$GravatarUrl(32)" class="img-polaroid" alt="$Name.ATT">
							$Name
						</a>
					</li>
				<% end_loop %>
				</ul>
			</dd>
		<% end_if %>
	</dl>

	<hr>

	<div id="readme-container">
		<h3>Readme</h3>

		<div id="readme" class="collapsed">
			<div id="readme-inner">Loading...</div>
			<a href="#" id="readme-toggle">
				<i class="icon-arrow-up"></i>
				<i class="icon-arrow-down"></i>
			</a>
		</div>

		<hr>
	</div>

	<h3>Versions</h3>

	<div id="versions" class="accordion">
		<% loop $SortedVersions %>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#versions" href="#version-$ID">
						$DisplayVersion
					</a>
				</div>
				<div id="version-$ID" class="version accordion-body collapse<% if First %> in<% end_if %>">
					<div class="accordion-inner">
						<% include AddonVersionDetails %>
					</div>
				</div>
			</div>
		<% end_loop %>
	</div>

<% end_with %>
