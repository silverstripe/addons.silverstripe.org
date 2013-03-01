<div class="page-header">
	<h1>$Title</h1>
</div>

<div class="input-append">
	<input type="text" placeholder="Filter vendors" data-filter="#vendors">
	<span class="add-on"><i class="icon-search"></i></span>
</div>

<ul id="vendors">
	<% loop $Vendors %>
		<li><a href="$Link">$Name <span class="label">$Count</span></a></li>
	<% end_loop %>
</ul>
