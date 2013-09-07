<div class="row-fluid install-box">
	<strong class="install-box-label">Install</strong>
	<a href="http://doc.silverstripe.org/framework/en/topics/modules#installation">
		<i class="icon-question-sign"></i>
	</a>
	<pre>composer require $Name $DisplayRequireVersion</pre>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
		width="110"
		height="14"
		class="clippy">
	<param name="movie" value="$ThemeDir/thirdparty/clippy/clippy.swf"/>
	<param name="allowScriptAccess" value="always" />
	<param name="quality" value="high" />
	<param name="scale" value="noscale" />
	<param NAME="FlashVars" value="text=composer require $Name $DisplayRequireVersion">
	<param name="bgcolor" value="#fff">
	<embed src="$ThemeDir/thirdparty/clippy/clippy.swf"
		width="110"
		height="14"
		name="clippy"
		quality="high"
		allowScriptAccess="always"
		type="application/x-shockwave-flash"
		pluginspage="http://www.macromedia.com/go/getflashplayer"
		FlashVars="text=composer require $Name $DisplayRequireVersion"
		bgcolor="#fff"
	/>
	</object>
</div>

<div class="row-fluid">
	<div class="span4">
		<h5>Requires</h5>
		<% include AddonVersionDetailsLinks Items=$Requires %>
	</div>
	<% if RequiresDev %>
	<div class="span4">
		<h5>Requires (Development)</h5>
		<% include AddonVersionDetailsLinks Items=$RequiresDev %>
	</div>
	<% end_if %>
	<% if Suggests %>
	<div class="span4">
		<h5>Suggests</h5>
		<% include AddonVersionDetailsLinks Items=$Suggests %>
	</div>
	<% end_if %>
</div>

<% if Provides || Conflicts || Replaces %>
	<div class="row-fluid">
		<% if Provides %>
		<div class="span4">
			<h5>Provides</h5>
			<% include AddonVersionDetailsLinks Items=$Provides %>
		</div>
		<% end_if %>
		<% if Conflicts %>
		<div class="span4">
			<h5>Conflicts</h5>
			<% include AddonVersionDetailsLinks Items=$Conflicts %>
		</div>
		<% end_if %>
		<% if Replaces %>
		<div class="span4">
			<h5>Replaces</h5>
			<% include AddonVersionDetailsLinks Items=$Replaces %>
		</div>
		<% end_if %>
	</div>
<% end_if %>
