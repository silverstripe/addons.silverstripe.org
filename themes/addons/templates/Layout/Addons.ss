<div class="page-header">
	<h1>Modules and Themes</h1>
</div>

<div class="add-ons">
<% with $AddonsSearchForm %>
	<form $FormAttributes>
		<% with $FieldMap %>
			<div class="addons-search-row">
				<% with $search %>
					<label for="$ID">$Title</label> $Field
				<% end_with %>

				<button type="submit" class="btn">
					<i class="icon-search"></i> Search Add-ons
				</button>
			</div>

			<div class="row">
				<div class="span4">
					<% with $type %>
						<label for="$ID">$Title</label> $Field
					<% end_with %>
				</div>
				<div class="span4">
					<% with $compatibility %>
						<label for="$ID">$Title</label> $Field
					<% end_with %>
				</div>
				<div class="span4">
					<% with $sort %>
						<label for="$ID">$Title</label> $Field
					<% end_with %>
				</div>
			</div>
		<% end_with %>
		<div id="legacy-search-notice" class="alert" style="display: none;">
			Some 2.4 compatible modules aren't listed here,
			you might have better luck <br>searching through our
			legacy database at <a href="http://www.silverstripe.org/modules">silverstripe.org/modules</a>
		</div>
	</form>
<% end_with %>
<hr>
<div class="addons-list-type">
	<a href="$LinkWithSearch('view=expanded')"<% if ListView = 'expanded' %> class="current"<% end_if %>><i class="icon-th-large"></i></a>
	<a href="$LinkWithSearch('view=list')"<% if ListView = 'list' %> class="current"<% end_if %>><i class="icon-th-list"></i></a>
</div>
<% if $Addons %>
	<% if ListView = 'expanded' %>
	<div class="row">
		<% loop $Addons %>
			<div class="addons-box span6">
				<h3>
					<% if $Type == "module" %>
						<i class="icon-th-large"></i>
					<% else_if $Type == "theme" %>
						<i class="icon-picture"></i>
					<% end_if %>
					<a href="$Link">$Name</a>
				</h3>
				<div class="addons-box-holder">
					<% if Screenshots %>
					<div class="placeholder img">
						<% loop Screenshots %>
						<% if First %>
						<img src="$SetRatioSize(150,150).Link" />
						<% end_if %>
						<% end_loop %>
					</div>
					<% else %>
					<div class="placeholder"><!-- --></div>
					<% end_if %>
					<% if MasterVersion %>
					<% loop MasterVersion %>
					<span class="version">Version $DisplayVersion</span>
					<% end_loop %>
					<% end_if %>
					<span class="meta"><% include AddonDownloadStats %></span>
					<span class="description">$Description.LimitCharacters(100)</span>
				</div>
			</div>
			<% if $MultipleOf(2) %></div><div class="row"><% end_if %>
		<% end_loop %>
	</div>
	<% else %>
	<table class="table table-hover table-striped table-boxed table-addons">
		<tbody>
			<% loop $Addons %>
			<tr>
				<td>
					<% if $Type == "module" %>
						<i class="icon-th-large"></i>
					<% else_if $Type == "theme" %>
						<i class="icon-picture"></i>
					<% end_if %>
					<a href="$Link">$Name</a>
				</td>
				<td>$Description.LimitCharacters(60)</td>
				<td>
					<% include AddonDownloadStats %>
				</td>
			</tr>
			<% end_loop %>
		</tbody>
	</table>
	<% end_if %>
	<% include Pagination Items=$Addons %>
<% else %>
	<p>There are no add-ons to display.</p>
<% end_if %>
</div>

	

