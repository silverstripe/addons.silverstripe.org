<div class="page-header">
	<p>
		<a href="/add-ons" class="btn btn-plain"><i class="icon-arrow-left"></i> Back to add-ons</a>
	</p>

	<h1>$Title</h1>
</div>

<div class="row">
	<div class="span8">
		<h2>Add-ons</h2>

		<table class="table table-hover table-striped table-boxed">
			<tbody>
				<% loop $Addons %>
					<tr>
						<td><a href="$Link">$Name</a></td>
						<td>$Description.LimitCharacters(50)</td>
					</tr>
				<% end_loop %>
			</tbody>
		</table>

		<% include Pagination Items=$Addons %>
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
