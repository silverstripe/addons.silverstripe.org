<div class="page-header">
	<h1>$Title</h1>
</div>

<% with $Author %>
	<div class="row">
		<div class="span2">
			<img src="$GravatarUrl(200)" class="img-polaroid" alt="$Name.ATT">
		</div>
		<div class="span10">
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

			<h2>Extensions</h2>
			<ul>
				<% loop $Extensions %>
					<li><a href="$Link">$Name</a><br><small>$Description</small></li>
				<% end_loop %>
			</ul>
		</div>
	</div>
<% end_with %>
