<div class="page-header">
	<h1>Tags</h1>
</div>

<div class="input-append">
	<input type="text" placeholder="Filter tags" data-filter="#tags">
	<span class="add-on"><i class="icon-search"></i></span>
</div>

<ol id="tags">
	<% loop $Tags %>
		<li><a href="$Link">$Name <span class="label">$Count</span></a></li>
	<% end_loop %>
</ol>
