<div class="row-fluid install-box">
	<label for="install-string-{$ID}" class="install-box-label">Install</label>

	<a href="http://doc.silverstripe.org/framework/en/topics/modules#installation">
		<i class="icon-question-sign"></i>
	</a>

    <div class="install-box-field copy-field input-append">
        <input
            id="install-string-{$ID}"
            class="input-xxlarge copy-field__input"
            data-copiable-field="install-string-{$ID}"
            type="text"
            value="composer require $Name $DisplayRequireVersion"
            readonly="readonly"
        />

        <button
            class="btn copy-field__button"
            data-copies-field="install-string-{$ID}"
            title="Copy install command"
        >
            <i class="icon-paste" title="Copy install command"></i>
        </button>
        <span class="copy-field__confirmation" aria-hidden="true" aria-live="assertive"></span>
    </div>
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
