<div class="page-header">
	<h1>$Title</h1>
</div>

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
