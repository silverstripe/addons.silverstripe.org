<div class="page-header">
	<h1>$Title</h1>
</div>

<% with $ExtensionsSearchForm %>
	<form $FormAttributes>
		<fieldset>
			<% with $FieldMap %>
				<% with $search %>
					<label for="$ID">$Title</label> $Field
				<% end_with %>

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

			<button type="submit" class="btn btn-info">
				<i class="icon-search icon-white"></i> Search
			</button>
		</fieldset>
	</form>
<% end_with %>

<% if $Extensions %>
	<table id="extensions" class="table table-striped">
		<tbody>
			<% loop $Extensions %>
				<tr>
					<td><i class="$TypeIcon"></i></td>
					<td><a href="$Link">$Name</a></td>
					<td>$Description.LimitCharacters(60)</td>
					<td><i class="icon-download"></i> $Downloads</td>
				</tr>
			<% end_loop %>
		</tbody>
	</table>

	<% include Pagination Items=$Extensions %>
<% else %>
	<p>There are no extensions to display.</p>
<% end_if %>
