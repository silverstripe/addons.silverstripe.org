<% with $Addon %>
	<div class="page-header">
		<p>
			<a href="/add-ons" class="btn btn-plain"><i class="icon-arrow-left"></i> Back to add-ons</a>
		</p>

		<h1>
			<a href="$VendorLink">$VendorName</a> / $PackageName
		</h1>

		<p>$Description</p>
	</div>

	<% if not $LastBuilt %>
		<p class="alert">
			The detailed information for this add-on has not yet been built.
		</p>
	<% end_if %>

	<% if $Screenshots %>
		<ul class="screenshots inline">
			<% loop $Screenshots %>
				<li>
					<a href="$URL" target="_blank">
						<img src="$CroppedImage(160, 160).URL" alt="$Up.Name.ATT" class="img-polaroid">
					</a>
				</li>
			<% end_loop %>
		</ul>
	<% end_if %>

	<dl id="metadata">
		<% with $Versions.First %>
			<% if $Homepage %>
				<dt>Homepage:</dt>
				<dd><a href="$Homepage">$Homepage</a></dd>
			<% end_if %>
		<% end_with %>

		<% if $Authors %>
			<dt>Authors:</dt>
			<dd>
				<% loop $Authors %>
					<a href="$Link">$Name</a><% if not $Last %>, <% end_if %>
				<% end_loop %>
			</dd>
		<% end_if %>

		<dt>Packagist:</dt>
		<dd><a href="$PackagistUrl">$PackagistUrl</a></dd>

		<dt>Repository:</dt>
		<dd>
			<a href="$Repository">$Repository</a>
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
				width="110"
				height="14"
				class="clippy">
			<param name="movie" value="$ThemeDir/thirdparty/clippy/clippy.swf"/>
			<param name="allowScriptAccess" value="always" />
			<param name="quality" value="high" />
			<param name="scale" value="noscale" />
			<param NAME="FlashVars" value="text=$Repository">
			<param name="bgcolor" value="#fff">
			<embed src="$ThemeDir/thirdparty/clippy/clippy.swf"
				width="110"
				height="14"
				name="clippy"
				quality="high"
				allowScriptAccess="always"
				type="application/x-shockwave-flash"
				pluginspage="http://www.macromedia.com/go/getflashplayer"
				FlashVars="text=$Repository"
				bgcolor="#fff"
			/>
			</object>
		</dd>
	</dl>

	<hr>

	<% if $Readme %>
		<h3>Readme</h3>

		<div id="readme" class="collapsed">
			<div id="readme-inner">
				$Readme
			</div>
			<a href="#" id="readme-toggle">
				<i class="icon-arrow-up"></i>
				<i class="icon-arrow-down"></i>
			</a>
		</div>

		<hr>
	<% end_if %>

	<h3>Versions</h3>

	<div id="versions" class="accordion">
		<% with $SortedVersions.First %>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#versions" href="#version-$ID">
						$DisplayVersion
					</a>
				</div>
				<div id="version-$ID" class="version accordion-body collapse in">
					<div class="accordion-inner">
						<% include AddonVersionDetails %>
					</div>
				</div>
			</div>
		<% end_with %>

		<% if $SortedVersions.Count != 1 %>
			<div class="accordion-group accordion-separator">
				<div class="accordion-heading">
					Other Versions
				</div>
			</div>

			<% loop $Versions %>
				<% if not $First %>
					<div class="accordion-group">
						<div class="accordion-heading">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#versions" href="#version-$ID">
								$DisplayVersion
							</a>
						</div>
						<div id="version-$ID" class="version accordion-body collapse">
							<div class="accordion-inner">
								<% include AddonVersionDetails %>
							</div>
						</div>
					</div>
				<% end_if %>
			<% end_loop %>
		<% end_if %>
	</div>

	<div id="disqus_thread"></div>
	 <script type="text/javascript">
		var disqus_shortname = 'silverstripe-addons'; 
		(function() {
				var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
				dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
				(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	</script>
	<noscript>
		Please enable JavaScript to view the 
		<a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a>
	</noscript>
	<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
		
<% end_with %>
