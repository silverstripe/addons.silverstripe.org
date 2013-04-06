<div class="page-header">
	<h1>$Title</h1>
</div>

<ul id="authors">
	<% loop $Authors %>
		<li>
			<a href="$Link">
				<img src="$GravatarUrl(32)" alt="$Name.ATT" width="32" height="32"> $Name
			</a>
		</li>
	<% end_loop %>
</ul>
