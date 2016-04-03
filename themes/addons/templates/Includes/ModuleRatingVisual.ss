<% if $AdjustedHelpfulRobotScore >= 70 %>
<div class="circle<% if $SmallCircle %> circle-sml<% end_if %> green"></div>
<% else %>
	<% if $AdjustedHelpfulRobotScore >= 40 %>
	<div class="circle<% if $SmallCircle %> circle-sml<% end_if %> green">
		<div class="circle-half"></div>
	</div>
	<% else %>
		<div class="circle<% if $SmallCircle %> circle-sml<% end_if %> grey">
			<div class="circle-half"></div>
		</div>
	<% end_if %>
<% end_if %>


						
