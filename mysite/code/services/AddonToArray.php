<?php

/**
 * Service for converting Addon to a nested array suitable for json-encoding in an API
 */
class AddonToArray
{

    public function convert(Addon $package)
    {
        $data = $package->toMap();
        unset($data['LastEdited']);
        unset($data['Created']);
        unset($data['ClassName']);
        unset($data['RecordClassName']);

        unset($data['VendorID']);

        // Ensure consistent typing
        $data['Rating'] = (int)$data['Rating'];

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


            unset($versionData['Name']);
            unset($versionData['Type']);
            unset($versionData['Homepage']);

            if (!empty($versionData['ExtraValue'])) {
                $versionData['ExtraValue'] = unserialize($versionData['ExtraValue']);
            }
            if (!empty($versionData['LicenseValue'])) {
                $versionData['LicenseValue'] = unserialize($versionData['LicenseValue']);
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
