<?php

use Sminnee\StitchData\StitchApi;

/**
 * Sends package data to StitchData
 */
class StitchDataSender
{
    /**
     * @var string
     */
    private $accessToken = null;

    /**
     * @var string
     */
    private $clientID = null;

    /**
     * @var string
     */
    private $tableName = null;

    /**
     * @var StitchApi
     */
    private $client = null;

    public function __construct()
    {
        // Use environment defines for client ID and access token by default
        if (defined('STITCHDATA_CLIENT_ID')) {
            $this->clientID = STITCHDATA_CLIENT_ID;
        }
        if (defined('STITCHDATA_ACCESS_TOKEN')) {
            $this->accessToken = STITCHDATA_ACCESS_TOKEN;
        }
    }

    /**
     * Set the client ID of your StitchData account.
     * @return $this, for use with fluent syntax
     */
    public function setClientID($clientID)
    {
        $this->clientID = $clientID;
        return $this;
    }

    /**
     * Set the access token for the StitchData Import API
     * @return $this, for use with fluent syntax
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * Set the table name to write packages to
     * @return $this, for use with fluent syntax
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Get the client ID of your StitchData account.
     * @return string
     */
    public function getClientID()
    {
        return $this->clientID;
    }

    /**
     * Get the access token for the StitchData Import API
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get the table name to write packages to
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set the StitchApi client instance
     *
     * @param StitchApi $client
     * @return $this
     */
    public function setClient(StitchApi $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get (and/or create and set) the StitchApi client instance, allowing for lazy loading in case API arguments
     * want to be changed after construction.
     *
     * @return StitchApi
     */
    public function getClient()
    {
        // Allow lazy loading
        if (!$this->client) {
            $this->setClient(new StitchApi($this->getClientID(), $this->getAccessToken()));
        }
        return $this->client;
    }

    /**
     * Send the given package to the StitchData API
     */
    public function sendAddon(Addon $package)
    {
        // If unconfigured, silently no-op
        if (!$this->getClientID() || !$this->getAccessToken() || !$this->getTableName()) {
            return;
        }

        $this->getClient()->pushRecords(
            $this->getTableName(),
            [ 'Name' ],
            [
                $this->addonToJson($package)
            ]
        );
    }

    public function addonToJson(Addon $package)
    {
        $tz = new DateTimeZone('UTC');

        $data = $package->toMap();
        unset($data['LastEdited']);
        unset($data['Created']);
        unset($data['ClassName']);
        unset($data['RecordClassName']);

        foreach (['Released', 'LastUpdated', 'LastBuilt'] as $field) {
            if ($package->$field) {
                $datetime = is_numeric($package->$field) ? "@" . $package->$field : $package->$field;
                $data[$field] = new Datetime($datetime, $tz);
            }
        }

        if ($package->RatingDetails) {
            $data['RatingDetails'] = [];
            foreach (json_decode($package->RatingDetails, true) as $k => $v) {
                $data['RatingDetails'][] = [
                    'Name' => $k,
                    'Value' => $v,
                ];
            }
        }

        $data['Versions'] = [];
        foreach ($package->Versions() as $version) {
            $versionData = $version->toMap();
            unset($versionData['LastEdited']);
            unset($versionData['Created']);
            unset($versionData['ClassName']);
            unset($versionData['RecordClassName']);
            unset($versionData['AddonID']);

            if ($version->Released) {
                $versionData['Released'] = new Datetime($version->Released, $tz);
            }

            foreach ($version->CompatibleVersions() as $compatible) {
                $versionData['CompatibleVersions'][] = [
                    "Version" => $compatible->Name,
                    "Major" => $compatible->Major,
                    "Minor" => $compatible->Minor,
                ];
            }

            foreach ($version->Authors() as $author) {
                $authorData = $author->toMap();
                unset($authorData['ID']);
                unset($authorData['ClassName']);
                unset($authorData['RecordClassName']);
                unset($authorData['LastEdited']);
                unset($authorData['Created']);
                $versionData['Authors'][] = $authorData;
            }

            $data['Versions'][] = $versionData;
        }

        return $data;
    }
}
