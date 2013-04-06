<% loop $Options %>
	<label class="checkbox inline">
		<input name="$Top.Name[]" type="checkbox" <% if isChecked %>checked="checked"<% end_if %> value="$Value"> $Title
	</label>
<% end_loop %>
