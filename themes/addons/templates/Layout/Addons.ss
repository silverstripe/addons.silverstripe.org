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
	</form>
<% end_with %>
<hr>
<% if $Addons %>
    $Addons.FirstItem - $Addons.LastItem of $Addons.TotalItems
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


