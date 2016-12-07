<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * A GitHub service for communicating with the GitHub API to render Markdown. Uses Guzzle as the
 * transport method.
 *
 * @package mysite
 */
class GitHubMarkdownService extends Object
{
    /**
     * The Guzzle client
     *
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * GitHub API configuration
     * @var string
     */
    const API_BASE_URI        = 'https://api.github.com';
    const API_REQUEST_METHOD  = 'POST';
    const API_RENDER_ENDPOINT = '/markdown/raw';

    /**
     * Use GitHub's API to render markdown to HTML
     *
     * @param  string $markdown Markdown
     * @return string           HTML
     */
    public function toHtml($markdown)
    {
        try {
            /** @var Psr\Http\Message\ResponseInterface $response */
            $response = $this->getClient()
                ->request(
                    $this->getRequestMethod(),
                    $this->getEndpoint(),
                    array(
                        'headers' => $this->getHeaders(),
                        'body' => $markdown
                    )
                );
        } catch (ClientException $ex) {
            user_error($ex->getMessage());
        }

        return (string) $response->getBody();
    }

    /**
     * Get an instance of a GuzzleHttp client
     * @return GuzzleHttp\Client
     */
    public function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client(
                array(
                    'base_uri' => $this->getBaseUri()
                )
            );
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
        return self::API_RENDER_ENDPOINT;
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
}
