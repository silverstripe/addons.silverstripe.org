<% with $Items %>
	<% if $MoreThanOnePage %>
		<div class="pagination">
			<ul>
				<% if $PrevLink %><li><a href="$PrevLink"><i class="icon-arrow-left"></i></a></li><% end_if %>

				<% loop $PaginationSummary(4) %>
					<% if $CurrentBool %>
						<li class="active"><span>$PageNum</span></li>
					<% else_if $Link %>
						<li><a href="$Link">$PageNum</a></li>
					<% else %>
						<li class="disabled"><span>&hellip;</span></li>
					<% end_if %>
				<% end_loop %>

				<% if $NextLink %><li><a href="$NextLink"><i class="icon-arrow-right"></i></a></li><% end_if %>
			</ul>
		</div>
	<% end_if %>
<% end_with %>
