<div class="page-header">
	<h1>$Title</h1>
</div>

<div class="row">
	<div class="span8">
		<h2>Extensions</h2>
		<table class="table table-striped table-hover">
			<tbody>
				<% loop $Extensions %>
					<tr>
						<td><a href="$Link">$Name</a></td>
						<td>$Description.LimitCharacters(80)</td>
					</tr>
				<% end_loop %>
			</tbody>
		</table>

		<% include Pagination Items=$Extensions %>
	</div>
	<div class="span4">
		<h2>Authors</h2>
		<ul>
			<% loop $Vendor.Authors %>
				<li><a href="$Link">$Name</a></li>
			<% end_loop %>
		</ul>
	</div>
</div>
