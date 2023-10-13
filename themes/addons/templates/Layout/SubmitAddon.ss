<div class="page-header">
	<h1>Submit your Module</h1>
</div>

<div class="content-narrow">

	<h3>Overview</h3>
	<p>
		Silverstripe CMS modules are managed through <a href="http://getcomposer.org">Composer</a>,
		a dependency manager for PHP. It takes care of downloading modules and installing
		them into your project.
	</p>
	<p>
		Each module needs to be registered on <a href="https://packagist.org/">packagist.org</a>,
		which is a central repository used by Composer. The addons.silverstripe.org project
		doesn't interact directly with Composer, store module metadata, or provide module downloads.
	</p>

	<p>
		If you want to learn about creating modules with Silverstripe CMS, read our
		<a href="https://docs.silverstripe.org/en/developer_guides/extending/modules/#create">"Module Development"</a> guide.
	</p>

	<h3>Setup</h3>
	<p>
		First of all, your project needs to be in version control. Packagist supports Git, Subversion and Mercurial.
		You need to add a <code>composer.json</code> file in the root of your module. Here's an example which you can customize.
		<pre class="pre-scrollable" style="height: 200px;">
	{
	  "name": "your-vendor-name/module-name",
	  "description": "One-liner describing your module",
	  "type": "silverstripe-vendormodule",
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
	    "silverstripe/cms": "^5.0",
	    "silverstripe/framework": "^5.0"
	  },
	}
		</pre>

		<ul>
			<li>Try to reuse <a href="/tags">existing tags</a></li>
		</ul>
	</p>

	<h3>Submission</h3>
	<p>
		Now you're ready to submit the module. Please note that it takes a while for the
		changes to be picked up by addons.silverstripe.org.
	</p>
	<p><a href="https://packagist.org/packages/submit" class="btn btn-primary">Submit on packagist.org</a></p>
	<p>
		It's helpful to set up
		<a href="http://docs.silverstripe.org/en/getting_started/composer/">Silverstripe CMS with Composer</a>,
		so you can test the installation.
	</p>

	$Form

</div>
