<?php
/**
 * A class that contains code to handle any requests for  /test/
 */
     namespace Pages;

     use \Support\Context as Context;
     use \Config\Config as Config;
/**
 * Support /test/
 */
    class Test extends \Framework\Siteaction
    {
/**
 * Handle test operations
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle(Context $context)
        {
            return '@content/test.twig';
        }
    }
?>