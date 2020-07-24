<?php
/**
 * A class that contains code to handle any requests for  /staffhome/
 * @author Olivija Guzelyte
 * @copyright 2018-2019 Newcastle University
 *
 */
     namespace Pages;

     use \Support\Context as Context;
     use \Config\Config as Config;
/**
 * Support /staffhome/
 */
    class Staffhome extends \Framework\Siteaction
    {
/**
 * Handle staffhome operations
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle(Context $context)
        {
            return '@content/staffhome.twig';
        }
    }
?>