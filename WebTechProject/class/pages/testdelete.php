<?php
/**
 * A class that contains code to handle any requests for  /testdelete/
 */
     namespace Pages;

     use \Support\Context as Context;
     use \Config\Config as Config;
/**
 * Support /testdelete/
 */
    class Testdelete extends \Framework\Siteaction
    {
/**
 * Handle testdelete operations
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle(Context $context)
        {
            return '@content/testdelete.twig';
        }
    }
?>