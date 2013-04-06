<div class="page-header">
	<h1>$Title</h1>
</div>

<ul class="tags">
	<% loop $Tags %>
		<li>
			<a href="$Link">
				$Name <span class="count">&times; $Count</span>
			</a>
		</li>
	<% end_loop %>
</ul>
