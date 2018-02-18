<?php

namespace CrTranslateMate;

/**
 * Class Constants
 * Mainly used to decouple classes from constants and make for easier unit testing
 *
 * @package CrTranslateMate
 */
class Constants
{
    public function get($constant)
    {
        return defined($constant) ? constant($constant) : '';
    }
}