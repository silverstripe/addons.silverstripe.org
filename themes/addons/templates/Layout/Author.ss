<div class="page-header">
	<h1>$Title</h1>
</div>

<% with $Author %>
	<div id="author" class="row">
		<div class="span3">
			<img src="$GravatarUrl(200)" class="img-polaroid" alt="$Name.ATT">
		</div>
		<div class="span9">
			<dl>
				<% if $Name %>
					<dt>Name</dt>
					<dd>$Name</dd>
				<% end_if %>
				<% if $Homepage %>
					<dt>Homepage</dt>
					<dd><a href="$Homepage">$Homepage</a></dd>
				<% end_if %>
				<% if $Role %>
					<dt>Role</dt>
					<dd>$Role</dd>
				<% end_if %>
			</dl>

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
		</div>
	</div>
<% end_with %>
