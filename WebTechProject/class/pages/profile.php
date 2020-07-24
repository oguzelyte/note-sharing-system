<?php

/**
 * A class that contains code to handle any requests for  /profile/
 * @author Olivija Guzelyte
 * @copyright 2018-2019 Newcastle University
 *
 */

namespace Pages;

use \Support\Context as Context;
use \Config\Config as Config;
use \ModelExtend\Badge as Badge;

/**
 * Support /profile/
 */
class Profile extends \Framework\Siteaction
{
    /**
     * Handle profile operations
     *
     * @param object	$context	The context object for the site
     *
     * @return string	A template name
     */
    public function handle(Context $context)
    {
        // Find current user and send it to context
        $user = \R::load('user', $context->user()->getID());
        $context->local()->addval('user', $user);

        // Find how many files user uploaded
        $uploadcount = \R::count('upload', 'user_id = ?', [$user->id]);
        $context->local()->addval('uploadcount', $uploadcount);

        $fields = ['flag', 'rating'];

        // Count how many uploads user flagged
        $flagcount = \R::count('beanlog', 'user_id = ? AND field = ?', array($user->id, $fields[0]));
        $context->local()->addval('flagcount', $flagcount);

        // Count how many uploads user rated
        $ratecount = \R::count('beanlog', 'user_id = ? AND field = ?', array($user->id, $fields[1]));
        $context->local()->addval('ratecount', $ratecount);

        // Find how many public files user uploaded
        $public = 1;
        $puploadcount = \R::count('upload', 'user_id = ? AND public = ?', [$user->id, $public]);
        $puploads = \R::findAll('upload', 'user_id = ? AND public = ?', [$user->id, $public]);
        if (!empty($puploads)) {
            $temp = 0;
            foreach ($puploads as $pupload) {
                $temp += $pupload->rating;
            }
            $avgrate = round($temp / $puploadcount, 1);
            $context->local()->addval('avgrate', $avgrate);
            $avgrate >= 4 ? $this->badgeuploadavg($user) : false;
        } else {
            $context->local()->addval('avgrate', 'No public uploads.');
        }

        // Count if uploaded 10 first notes and give badge if so
        $field = 'send';
        $badge = new Badge();
        $beanfound = $badge->getBadge(Badge::NOTEMASTER);
        \R::count('beanlog', 'user_id = ? AND field = ?', [$user->id, $field]) == 10 ? $this->storebean($beanfound->id, $user->id) : false;

        $ackbadges = $this->checkbadges($context);
        $context->local()->addval('ackbadges', $ackbadges);

        $loadbadges = \R::findAll('badge');
        $context->local()->addval('loadbadges', $loadbadges);
        $context->local()->addval('icons', Badge::ICONS);

        return '@content/profile.twig';
    }

    private function badgeuploadavg(Object $user) : void
    {
        // Find badge bean's id, and add it to the hasbadge table
        $badge = new Badge();
        $beanfound = $badge->getBadge(Badge::NOTES4AVG);
        // Check if user already has badge
        $alreadyhasbean = \R::count('hasbadge', 'user_id = ? AND badge_id = ?', [$user->id, $beanfound->id]);
        if ($alreadyhasbean == 0) {
            // Create a hasbadge bean, add user id to it
            $this->storebean($beanfound->id, $user->id);
        }
    }

    private function storebean(int $beanid, string $userid) : void
    {
        $makebadge = \R::dispense('hasbadge');
        $makebadge->user_id = $userid;
        $makebadge->badge_id = $beanid;
        \R::store($makebadge);
    }

    private function checkbadges(Context $context) : array
    {
        $loadbadges = \R::findAll('badge');
        foreach ($loadbadges as $badge)
        {
            // If user has badge
            $count = \R::count('hasbadge', 'user_id = ? AND badge_id = ?', [$context->user()->getID(), $badge->id]);
            if($count != 0)
            {
                $ackarray[$badge->id][0] = $badge->name;
                $ackarray[$badge->id][1] = $badge->description;
                $ackarray[$badge->id][2] = 'exists';
            }
            else 
            {
                $ackarray[$badge->id][0] = $badge->name;
                $ackarray[$badge->id][1] = $badge->description;
                $ackarray[$badge->id][2] = 'notexists';
            }
        }
        return $ackarray;
    }

}
