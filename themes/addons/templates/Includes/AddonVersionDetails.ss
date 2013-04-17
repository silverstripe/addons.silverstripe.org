<pre>composer require $Name $DisplayRequireVersion</pre>

<div class="row-fluid">
	<div class="span4">
		<h5>Requires</h5>
		<% include AddonVersionDetailsLinks Items=$Requires %>
	</div>
	<div class="span4">
		<h5>Requires (Development)</h5>
		<% include AddonVersionDetailsLinks Items=$RequiresDev %>
	</div>
	<div class="span4">
		<h5>Suggests</h5>
		<% include AddonVersionDetailsLinks Items=$Suggests %>
	</div>
</div>

<div class="row-fluid">
	<div class="span4">
		<h5>Provides</h5>
		<% include AddonVersionDetailsLinks Items=$Provides %>
	</div>
	<div class="span4">
		<h5>Conflicts</h5>
		<% include AddonVersionDetailsLinks Items=$Conflicts %>
	</div>
	<div class="span4">
		<h5>Replaces</h5>
		<% include AddonVersionDetailsLinks Items=$Replaces %>
	</div>
</div>
