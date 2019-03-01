<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injectable;

/**
 * A GitHub service for communicating with the GitHub API to render Markdown. Uses Guzzle as the
 * transport method.
 *
 * @package mysite
 */
class GitHubMarkdownService
{

    use Injectable;


    /**
     * The Guzzle client
     *
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * The GitHub repository context
     *
     * @var string
     */
    protected $context;

    /**
     * GitHub API configuration
     * @var string
     */
    const API_BASE_URI        = 'https://api.github.com';
    const API_REQUEST_METHOD  = 'POST';
    const API_RENDER_ENDPOINT = '/markdown';

    /**
     * Use GitHub's API to render markdown to HTML
     *
     * @param  string $markdown Markdown
     * @return string           HTML
     */
    public function toHtml($markdown)
    {
        $body = '';
        try {
            /** @var Psr\Http\Message\RequestInterface $request */
            $request = $this->getClient()
                ->request(
                    $this->getRequestMethod(),
                    $this->getEndpoint(),
                    [
                        'headers' => $this->getHeaders(),
                        'body' => $this->getPayload($markdown)
                    ]
                );
            $body = (string) $request->getBody();
        } catch (RequestException $ex) {
            user_error($ex->getMessage());
            return '';
        }

        return $body;
    }

    /**
     * Get the GitHub repository context
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set the GitHub repository context
     * @param  string $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Get an instance of a GuzzleHttp client
     * @return GuzzleHttp\Client
     */
    public function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client(['base_uri' => $this->getBaseUri()]);
        }

        return $this->client;
    }

    /**
     * Get the GitHub base URI
     * @return string
     */
    public function getBaseUri()
    {
        return self::API_BASE_URI;
    }

    /**
     * Get the HTTP request method to use for the request
     * @return string
     */
    public function getRequestMethod()
    {
        return self::API_REQUEST_METHOD;
    }

    /**
     * Get the markdown parse endpoint for GitHub's API
     * @return string
     */
    public function getEndpoint()
    {
        $endpoint = self::API_RENDER_ENDPOINT;
        if (!$this->getContext()) {
            $endpoint .= '/raw';
        }
        if (defined('SS_GITHUB_CLIENT_ID') && defined('SS_GITHUB_CLIENT_SECRET')) {
            $endpoint .= sprintf('?client_id=%s&client_secret=%s', SS_GITHUB_CLIENT_ID, SS_GITHUB_CLIENT_SECRET);
        }
        return $endpoint;
    }

    /**
     * Get any custom headers to use for the request
     * @return array
     */
    public function getHeaders()
    {
        return array(
            'User-Agent'   => 'silverstripe/addons-site',
            'Content-Type' => 'text/x-markdown'
        );
    }

    /**
     * Get the payload for for the GitHub Markdown API endpoint, either in "GFM" mode if there is a repository
     * context, or in raw mode if not.
     *
     * @see https://developer.github.com/v3/markdown/
     * @param  string $markdown
     * @return string           JSON or markdown
     */
    public function getPayload($markdown)
    {
        if ($this->getContext()) {
            return Convert::raw2json(
                array(
                    'text'    => $markdown,
                    'mode'    => 'gfm',
                    'context' => $this->getContext()
                )
            );
        }

        return $markdown;
    }
}
