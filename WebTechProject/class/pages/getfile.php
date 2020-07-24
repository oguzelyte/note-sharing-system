<?php

/**
 * A class that contains code to handle any requests for  /getfile/
 */

namespace Pages;

use \Support\Context as Context;
use \Config\Config as Config;

/**
 * Support /getfile/
 */
class Getfile extends \Framework\Siteaction
{
    /**
     * Handle getfile operations
     *
     * @param object	$context	The context object for the site
     *
     * @return string	A template name
     */
    public function handle(Context $context)
    {
        return '@content/getfile.twig';
    }
}
