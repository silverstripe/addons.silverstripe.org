<% if $Items.count %>
	<ul>
		<% loop $Items %>
			<li>
				<% if $Link %>
					<a href="$Link">$Name</a>: <% if $Description %>$Description<% else %>$Constraint<% end_if %>
				<% else %>
					$Name: <% if $Description %>$Description<% else %>$Constraint<% end_if %>
				<% end_if %>
			</li>
		<% end_loop %>
	</ul>
<% else %>
	<p class="muted">None</p>
<% end_if %>
