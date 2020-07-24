<?php
/**
 * A class that contains code to handle any requests for  /studenthome/
 * @author Olivija Guzelyte
 * @copyright 2018-2019 Newcastle University
 *
 */
     namespace Pages;

     use \Support\Context as Context;
     use \Config\Config as Config;
/**
 * Support /studenthome/
 */
    class Studenthome extends \Framework\Siteaction
    {
/**
 * Handle studenthome operations
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle(Context $context)
        {
            return '@content/studenthome.twig';
        }
    }
?>