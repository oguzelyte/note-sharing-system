<?php

/**
 * A trait that allows extending the model class for the RedBean object Upload
 *
 * Add any new methods you want the Uploadbean to have here.
 *
 * @author Olivija Guzelyte
 * @copyright 2018-2019 Newcastle University
 *
 */

namespace ModelExtend;
use \ModelExtend\Badge as Badge;

/**
 * Upload table stores info about files that have been uploaded...
 */
trait Upload
{
    /**
     * Determine if a user can access the file
     *
     * At the moment it is either the user or any admin that is allowd. Rewrite the
     * method to add more complex access control schemes.
     *
     * @param object	$user	A user object
     * @param string    $op     r for read, u for update, d for delete
     *
     * @return bool
     */
    public function canaccess($user, $op = 'r'): bool
    {
        if ($op == 'r')
        {
            return $this->bean->user->equals($user) || $user->isadmin() || $this->bean->public == 1 || $user->isstaff();
        }
        else if ($op == 'u' || $op == 'd')
        {
            return $this->bean->user->equals($user) || $user->isadmin() || $user->isstaff();
        }
    }

    /**
     * @var array   Values controlling whether or not bean operations are logged for certain beans
     */
    private static $audit = [
        'upload'
    ];

    /**
     * Hook for adding extra data to a file save.
     *
     * @param \Support\Context	$context  The context object for the site
     * @param int	                $index	  If you are reading data from an array fo files, this is the index
     *                                        in the file. You may have paralleld data arrays and need this index.
     *
     * @return void
     */
    public function addData(\Support\Context $context, int $index): void
    {

        // Access form data and current user name to populate the upload bean.
        $fdt = $context->formdata();
        $this->bean->postedby = $context->user()->fullname;
        $this->bean->public = $fdt->mustpost('publicaccess');
        if ($fdt->mustpost('publicaccess') === 'option2') {
            $this->bean->public = 1;
        } else {
            $this->bean->public = 0;
        }
        $this->bean->modulename = $fdt->mustpost('modulecode');
        $this->bean->lecturename = $fdt->mustpost('lecturename');
        $this->bean->rating = 0;
        $this->bean->ratedby = 0;
        $this->bean->ratecount = 0;
        $this->bean->flag = 0;
        // Remove space and whitespace
        $tagnospace = str_replace(' ', '', $fdt->mustpost('tags'));
        $tagnowhite = str_replace(' ', '', $tagnospace);
        $this->bean->tags = $tagnowhite;
        
        // Check if user hasn't uploaded anything, if so - give a badge.
        // It's the user's first upload.
        $fileuploads = \R::count('upload','user_id = ?', [$context->user()->getID()]);
        if ($fileuploads == 0)
        {
            // Create a hasbadge bean, add user id to it
            $makebadge = \R::dispense( 'hasbadge' );
            $makebadge->user_id = $context->user()->getID();
            // Find badge bean's id, and add it to the hasbadge table
            $badge = new Badge();
            $badgefound = $badge->getBadge(Badge::NOTEUPLOAD);
            $makebadge->badge_id = $badgefound->id;
            \R::store($makebadge);
        }

        
        $this->mklog($context, 3, $this->bean, $this->bean->id, 'send', json_encode($this->bean->export()));
    }


    /**
     * Hook for adding extra data to a file replace.
     *
     * @param \Support\Context	$context  The context object for the site
     * @param int	                $index	  If you are reading data from an array fo files, this is the index
     *                                        in the file. You may have paralleld data arrays and need this index.
     *
     * @return void
     */
    public function updateData(\Support\Context $context, array $da = null, int $index = null ): void
    {
        $fdt = $context->formdata();
        
        if ($fdt->post('publicaccess') === 'option2') {
            $this->bean->public = 1;
        } else if ($fdt->post('publicaccess') === 'option1') {
            $this->bean->public = 0;
        }

        if($fdt->post('modulecode') != 'No Change')
        {
            $this->bean->modulename = $fdt->post('modulecode');
        }

        if($fdt->post('lecturename') != 'No Change')
        {
            $this->bean->lecturename = $fdt->post('lecturename');
        }

        if($fdt->post('tags') != '')
        {
            $tagnospace = str_replace(' ', '', $fdt->post('tags'));
            $tagnowhite = str_replace(' ', '', $tagnospace);
            $this->bean->tags = $tagnowhite;
        }

        // If the user does not upload a file, just store the bean here
        // replace in Model/Upload will not be called 
        if($da != null)
        {
            $this->bean->filename = $da['name'];
        }
        else
        {
            $this->bean->added = $context->utcnow();
            \R::store($this->bean);
        }

        // Give the edit badge
        $this->editbadge($context);
        
    }

    private function mklog(\Support\Context $context, $op, string $bean, $id, string $field, $value)
    {
        $lg = \R::dispense('beanlog');
        $lg->user = $context->user();
        $lg->updated = $context->utcnow();
        $lg->op = $op;
        $lg->bean = $bean;
        $lg->bid = $id;
        $lg->field = $field;
        $lg->value = $value;
        \R::store($lg);
    }



    /**
     * Called when you try to trash to an upload. Do any cleanup in here
     *
     * @throws \Framework\Exception\Forbidden
     *
     * @return void
     */
    public function delete(): void
    {
        /**** Do not change this code *****/
        $context = \Support\Context::getinstance();

        if (!$this->bean->canaccess($context->user(), 'd')) { // not allowed
            throw new \Framework\Exception\Forbidden;
        }

        // Now delete the file
        unlink($context->local()->basedir() . $this->fname);
        //\R::trash ( $this->bean );
    }

    private function editbadge(\Support\Context $context)
    {
        // Find badge bean's id, and add it to the hasbadge table
        $badge = new Badge();
        $badgefound = $badge->getBadge(Badge::NOTEEDIT);
        // Check if user already rated anything
        $alreadydownloaded = \R::count('hasbadge', 'user_id = ? AND badge_id = ?', array($context->user()->getID(), $badgefound->id));
        if ($alreadydownloaded == 0) {
            // Create a hasbadge bean, add user id to it
            $makebadge = \R::dispense('hasbadge');
            $makebadge->user_id = $context->user()->getID();
            $makebadge->badge_id = $badgefound->id;
            \R::store($makebadge);
        }
    }
}
