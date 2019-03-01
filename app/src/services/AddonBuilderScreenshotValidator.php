<?php

use SilverStripe\Assets\Upload_Validator;

/**
 * Validates that an upload is a valid add-on screenshot.
 */
class AddonBuilderScreenshotValidator extends Upload_Validator
{

    public function __construct()
    {
        $this->setAllowedExtensions(array('jpg', 'jpeg', 'png'));
        $this->setAllowedMaxFileSize(250000);
    }

    public function validate()
    {
        if (!$this->isValidSize()) {
            $this->errors[] = 'The file is too large';
            return false;
        }

        if (!$this->isValidExtension()) {
            $this->errors[] = 'The file does not have a valid extension';
            return false;
        }

        if (!$this->isValidImage()) {
            $this->errors[] = 'The file does not appear to be an image';
            return false;
        }

        return true;
    }

    public function isValidImage()
    {
        $size = getimagesize($this->tmpFile['tmp_name']);

        if (!is_array($size)) {
            return false;
        }

        return $size[2] == IMAGETYPE_JPEG || $size[2] == IMAGETYPE_PNG;
    }
}
