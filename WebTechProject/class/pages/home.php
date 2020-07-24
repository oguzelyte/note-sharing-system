<?php
 /**
  * Class for handling home pages
  *
  * @author Olivija Guzelyte
  * @copyright 2012-2019 Newcastle University
  */
    namespace Pages;
    
    use \Support\Context as Context;
/**
 * A class that contains code to implement a home page
 */
    class Home extends \Framework\SiteAction
    {
/**
 * Handle various contact operations /
 *
 * @param \Support\Context	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle(Context $context)
        {
            if($context->hasstudent())
            {
                return '@content/studenthome.twig';
            }
            else if($context->hasstaff())
            {
                return '@content/staffhome.twig';
            }
            else
            {
                return '@content/index.twig'; 
            }
        }
    }
?>
