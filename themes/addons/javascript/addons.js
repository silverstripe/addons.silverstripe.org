async function getDefaultBranch(repo) {
  try {
    const response = await fetch(`https://api.github.com/repos/${repo}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/vnd.github+json',
        'X-GitHub-Api-Version': '2022-11-28',
      }
    });
    const json = await response.json();
    return json.default_branch;
  } catch {
    return null;
  }
}

async function getReadme(repo, defaultBranch) {
  try {
    const readmeResponse = await fetch(`https://raw.githubusercontent.com/${repo}/${defaultBranch}/README.md`);
    const rawReadme = await readmeResponse.text();

    const renderedResponse = await fetch(`https://api.github.com/markdown`, {
      method: 'POST',
      headers: {
        "content-type": "text/x-markdown",
        'Accept': 'application/vnd.github+json',
        'X-GitHub-Api-Version': '2022-11-28',
      },
      body: JSON.stringify({
        text: rawReadme,
        context: repo,
      })
    });
    const rendered = await renderedResponse.text();

    return rendered;
  } catch {
    return null;
  }
}

async function setReadme(repo) {
  const defaultBranch = await getDefaultBranch(repo);
  if (!defaultBranch) {
    return;
  }

  const readme = await getReadme(repo, defaultBranch);
  if (!readme) {
    jQuery('#readme-container').hide();
    return;
  }

  jQuery('#readme-inner').html(readme);
}

jQuery(function($) {
  // fetch readme
  let repoURL = $('#repository-url').attr('href');
  if (!repoURL) {
    repoURL = $('#homepage-url').attr('href') ?? '';
  }
  if (repoURL && repoURL.startsWith('https://github.com/')) {
    if (repoURL.endsWith('.git')) {
      repoURL = repoURL.replace('.git', '');
    }
    const repo = repoURL.replace('https://github.com/', '');
    setReadme(repo);
  } else {
    jQuery('#readme-container').hide();
  }

  // toggle readme collapsed
	$('#readme-toggle').on('click', function() {
		$(this).parents('#readme').toggleClass('collapsed');
		return false;
	});

	// Bootstrap tooltips
	$('[data-toggle=tooltip]').tooltip();

	$('#popularsort').on('change', function() {
		var selection = $(this).val();
		$.post('/addonSort', {'type': selection}, function(result) {
			if(result.success) {
				$('#popularAddons').html(result.body);
				// We need to re-bind the tooltips
                $('[data-toggle=tooltip]').tooltip();
            }
		}, 'JSON')
	});

	$('[data-copies-field]').on('click', function(e) {
	    var button = $(e.target);
	    var message = button.siblings('.copy-field__confirmation');
	    var fields = $('[data-copiable-field=' + button.data('copies-field') + ']');

	    if (fields.length === 1) {
            fields[0].select();
            document.execCommand('copy');

            // Notify the user visually / audibly that the copy succeeded
            button.addClass('copy-field__button--triggered');
            message.attr('aria-hidden', 'false');
            message.text('Copied!');
            setTimeout(function() {
                this.removeClass('copy-field__button--triggered');
                message.attr('aria-hidden', 'true');
            }.bind(button), 1000);
        }
    });
});
