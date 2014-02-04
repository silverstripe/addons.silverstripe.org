<div class="page-header">
	<h1>Submit your Module</h1>
</div>

<div class="content-narrow">

	<h3>Overview</h3>
	<p>
		SilverStripe modules are managed through <a href="http://getcomposer.org">Composer</a>,
		a dependency manager for PHP. It takes care of downloading modules and installing
		them into your project. 
	</p>
	<p>
		Each module needs to be registered on <a href="https://packagist.org/">packagist.org</a>,
		which is a central repository used by Composer. The addons.silverstripe.org project
		doesn't interact directly with Composer, store module metadata, or provide module downloads.
	</p>

	<p>
		If you want to learn about creating modules with SilverStripe, read our 
		<a href="http://doc.silverstripe.org/framework/en/topics/module-development">"Module Development"</a> guide.
	</p>

	<h3>Setup</h3>
	<p>
		First of all, your project needs to be in version control. Packagist supports Git, Subversion and Mercurial.
		You need to add a <code>composer.json</code> file in the root of your module. Here's an example which you can customize. 
		<pre class="pre-scrollable" style="height: 200px;">
	{
	  "name": "your-vendor-name/module-name",
	  "description": "One-liner describing your module",
	  "type": "silverstripe-module",
	  "homepage": "http://github.com/your-vendor-name/module-name",
	  "keywords": ["silverstripe", "some-tag", "some-other-tag"],
	  "license": "BSD-3-Clause",
	  "authors": [
	    {"name": "Your Name","email": "your@email.com"}
	  ],
	  "support": {
	    "issues": "http://github.com/your-vendor-name/module-name/issues"
	  },
	  "require": {
	    "silverstripe/cms": "~3.0",
	    "silverstripe/framework": "~3.0"
	  },
	  "extra": {
	    "installer-name": "elastica",
	    "screenshots": [
	      "relative/path/screenshot1.png",
	      "http://myhost.com/screenshot2.png"
	    ]
	  }
	}	
		</pre>

		<ul>
			<li>
				Adjust the "require" section to only list compatible SilverStripe versions 
				(<a href="http://getcomposer.org/doc/01-basic-usage.md#package-versions">more info</a>). 
				Common values:
				<ul>
					<li><code>3.0.*</code>: Version <code>3.0</code>, including <code>3.0.1</code>, <code>3.0.2</code> etc, excluding <code>3.1</code></li>
					<li><code>~3.0</code>: Version <code>3.0</code> or higher, including <code>3.0.1</code> and <code>3.1</code> etc, excluding <code>4.0</code></li>
					<li><code>~3.0,&lt;3.2</code>: Version <code>3.0</code> or higher, up until <code>3.2</code>, which is excluded</li>
					<li><code>~3.0,&gt;3.0.4</code>: Version <code>3.0</code> or higher, starting with <code>3.0.4</code></li>
				</ul>
			</li>
			<li>
				If you have multiple branches, add the composer.json file to each of them, 
				and adjust the "require" section accordingly (e.g. the <code>0.5</code> branch might require SilverStripe 2.4)
			</li>
			<li>Screenshots are optional, but highly encouraged.</li>
			<li>Try to reuse <a href="/tags">existing tags</a></li>
		</ul>
	</p>

	<h3>Submission</h3>
	<p>
		Now you're ready to submit the module. Please note that it takes a couple of minutes for the
		changes to be picked up by addons.silverstripe.org.
	</p>
	<p><a href="https://packagist.org/packages/submit" class="btn btn-primary">Submit on packagist.org</a></p>
	<p>
		It's helpful to set up 
		<a href="http://doc.silverstripe.org/framework/en/installation/composer">SilverStripe with Composer</a>,
		so you can test the installation.
	</p>

	$Form

</div>