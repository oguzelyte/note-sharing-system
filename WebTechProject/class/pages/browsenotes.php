<?php

/**
 * A class that contains code to handle any requests for  /browsenotes/
 *  @author Olivija Guzelyte
 *  @copyright 2018-2019 Newcastle University
 *
 */

namespace Pages;

use \Support\Context as Context;
use \Config\Config as Config;

/**
 * Support /browsenotes/
 */
class Browsenotes extends \Framework\Siteaction
{
    /**
     * Handle browsenotes operations
     *
     * @param object	$context	The context object for the site
     *
     * @return string	A template name
     */
    public function handle(Context $context)
    {

        // Fetching current user's ID
        $user = $context->user();
        $context->local()->addval('currentuser', $user);

        // Getting all notes in the system
        $public = 1;
        $uploads = \R::findAll('upload', 'public = ?', [$public]);
        $context->local()->addval('uploads', $uploads);

        // Check if user wants to filter by tags
        if(!empty($uploads) && isset($_POST['tagclicked']))
        {
            // Find entered value
            $name= $_POST['nameentered'];
            $namenospace = str_replace(' ', '', $name);
            $namenowhite = str_replace(' ', '', $namenospace);

            // Find if tag exists within upload
            foreach($uploads as $key=>$upload)
            {   
                $tags = explode(",", $upload->tags);
                $tagsarr[$key] = $tags;
                $found = in_array($namenowhite, $tagsarr[$key]);
                if($found)
                {
                    $founduploads[$key] = $upload;
                }
            }

            // Return uploads taged by said tag
            if(isset($founduploads))
            {
                $context->local()->addval('founduploads', $founduploads);
            }
            else
            {
                $noresults = 'No results found, fetching files.';
                $context->local()->addval('noresults', $noresults);
                $context->local()->addval('founduploads', null);
            }
        }

        // Getting modal's formdata to edit files.
        $fd = $context->formdata();
        
        // If files is set
        if ($fd->hasfile('uploads'))
        {
            $nofile = 0;
            $oldupload = \R::load('upload', $fd->post('oldupload'));
            if (Config::UPUBLIC && Config::UPRIVATE)
            { # need to check the flag could be either private or public
                foreach ($fd->posta('public') as $ix => $public) {
                    $da = $fd->filedata('uploads', $ix);
                    // Check if a file was uploaded, if not - break
                    if($da['size'] == 0)
                    {
                        $nofile += 1;
                        break;
                    }
                    // Issue a replace file if new upload found
                    $oldupload->replace($context, $da, $public, $context->user(), $ix);
                    header("Refresh:0");
                }
            } 
            else 
            {
                foreach (new \Framework\FAIterator('uploads') as $ix => $fa) { # we only support private or public in this case so there is no flag
                    // Check if a file was uploaded, if not - break
                    if($fa['size'] == 0)
                    {
                        $nofile += 1;
                        break;
                    }
                    // Issue a replace file if new upload found
                    $oldupload->replace($context, $fa, Config::UPUBLIC, $context->user(), $ix);
                    header("Refresh:0");
                }
            }
            // If no file upload found, edit the bean itself without changing the file
            if ($nofile == 1)
            {
                $oldupload = \R::load('upload', $fd->post('oldupload'));
                $oldupload->updateData($context);
                header("Refresh:0");
            }
        }        
        
        return '@content/browsenotes.twig';
    }
}
