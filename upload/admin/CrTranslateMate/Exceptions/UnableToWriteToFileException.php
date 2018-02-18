<?php

namespace CrTranslateMate\Exceptions;

class UnableToWriteToFileException extends \Exception
{
    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        parent::__construct('Unable to write to the file \'' . $filePath
            . '\'. Ensure that Opencart has write permissions for this file'
            . ' and it\'s language directory (for example: try 750, 755, or 775).');
    }
}
