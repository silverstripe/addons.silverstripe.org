<div class="row-fluid">
	<div class="span4">
		<h4>Requires</h4>
		<% include ExtensionLinksList Items=$Requires %>
	</div>
	<div class="span4">
		<h4>Requires (Development)</h4>
		<% include ExtensionLinksList Items=$RequiresDev %>
	</div>
	<div class="span4">
		<h4>Suggests</h4>
		<% include ExtensionLinksList Items=$Suggests %>
	</div>
</div>

<div class="row-fluid">
	<div class="span4">
		<h4>Provides</h4>
		<% include ExtensionLinksList Items=$Provides %>
	</div>
	<div class="span4">
		<h4>Conflicts</h4>
		<% include ExtensionLinksList Items=$Conflicts %>
	</div>
	<div class="span4">
		<h4>Replaces</h4>
		<% include ExtensionLinksList Items=$Replaces %>
	</div>
</div>
