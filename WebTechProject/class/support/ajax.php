<?php

/**
 * A class that handles Ajax calls
 *
 * @author Olivija Guzelyte
 * @copyright 2017-2019 Newcastle University
 *
 */

namespace Support;

use Support\Context as Context;
use ModelExtend\Badge as Badge;

/**
 * Handles Ajax Calls.
 */
class Ajax extends \Framework\Ajax
{
    /**
     * Add functions that implement your AJAX operations here and register them
     * in the handle method below.
     */

    public function uploaddelete(Context $context)
    {

        $beans = $this->findRow($context, self::$allowBean);
        $rest = $context->rest();
        $bean = strtolower($rest[1]);
        $log = in_array($bean, self::$audit);
        $method = $context->web()->method();

        if (count($rest) < 2) {
            throw new \Framework\Exception\BadValue('No table name');
            /* NOT REACHED */
        }
        switch ($method) {
            case 'DELETE': // /ajax/bean/KIND/ID/
                $id = $rest[2] ?? 0; // get the id from the URL
                if ($id <= 0) {
                    throw new \Framework\Exception\BadValue('Missing value');
                }
                $bn = $context->load($bean, $id);
                if ($log) {
                    $this->mklog($context, 2, $bean, $id, '*', json_encode($bn->export()));
                }
                // Give the 5 delete badge (if requirements met)
                $this->rateflag5delbadge($context, '*', Badge::NOTEDELETE);
                \R::trash($bn);
                break;
            case 'PUT': // update a field   /ajax/bean/KIND/ID/FIELD/[FN]
                list($bean, $id, $field, $more) = $context->restcheck(3);
                $this->beanCheck($beans, $bean, $field);
                $bn = $context->load($bean, $id, TRUE);
                $alreadyflagged = \R::findOne('beanlog', 'bid = ? AND user_id = ? AND field = ?', array($id, $context->user()->getID(), $field));
                $alreadyrated = \R::findOne('beanlog', 'bid = ? AND user_id = ? AND field = ?', array($id, $context->user()->getID(), $field));
                $old = $bn->$field;
                if (!empty($alreadyflagged)) {
                    throw new \Framework\Exception\Forbidden('Permission Denied');
                } else if ($field == "flag") {
                    $this->rateflag5delbadge($context, $field, Badge::NOTEFLAG);
                    $bn->$field = $old + 1;
                }

                if (!empty($alreadyrated)) {
                    throw new \Framework\Exception\Forbidden('Permission Denied');
                } else if ($field == "rating") {
                    $more[0] == 5 ? $this->badgetoaction($context, $bn, Badge::NOTE5STAR) : false;
                    $this->rateflag5delbadge($context, $field, Badge::NOTERATE);
                    $oldratedby = $bn->ratedby;
                    $oldratedby == 0 ? $this->badgetoaction($context, $bn, Badge::NOTERATED) : false;

                    $oldratecount = $bn->ratecount;

                    $bn->ratedby = $oldratedby + 1;
                    $bn->ratecount = $oldratecount + $more[0];

                    $bn->$field = round(($bn->ratecount / $bn->ratedby), 1);
                }

                \R::store($bn);
                if ($log) {
                    $this->mklog($context, 1, $bean, $bn->getID(), $field, $old);
                }
                break;
            case 'GET':
            default:
                throw new \Framework\Exception\BadOperation($method . ' not supported');
        }
    }

    private function badgetoaction(Context $context, Object $upload, string $badgetype)
    {
        // Find badge bean's id, and add it to the hasbadge table
        $badge = new Badge();
        $badgefound = $badge->getBadge($badgetype);
        // Check if user already rated anything
        $ratedid = \R::findOne('user', 'id = ?', [$upload->user_id]);
        $alreadybeenrated = \R::count('hasbadge', 'user_id = ? AND badge_id = ?', array($ratedid->id, $badgefound->id));
        if ($alreadybeenrated == 0) {
            // Create a hasbadge bean, add user id to it
            $makebadge = \R::dispense('hasbadge');
            $makebadge->user_id = $ratedid->id;
            $makebadge->badge_id = $badgefound->id;
            \R::store($makebadge);
        }
    }

    public function download(Context $context)
    {
        // Find badge bean's id, and add it to the hasbadge table
        $badge = new Badge();
        $badgefound = $badge->getBadge(Badge::NOTEDOWNLOAD);
        // Check if user already downloaded anything
        $alreadydownloaded = \R::count('hasbadge', 'user_id = ? AND badge_id = ?', array($context->user()->getID(), $badgefound->id));
        if ($alreadydownloaded == 0) {
            // Create a hasbadge bean, add user id to it
            $makebadge = \R::dispense('hasbadge');
            $makebadge->user_id = $context->user()->getID();
            $makebadge->badge_id = $badgefound->id;
            \R::store($makebadge);
        }
    }

    private function mklog(Context $context, $op, string $bean, $id, string $field, $value)
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

    // Function to give the first time rate or flag badge
    private function rateflag5delbadge(Context $context, string $field, string $badgename)
    {

        // Check if user has already done anything
        $beancount = \R::count('beanlog', 'user_id = ? AND field = ?', array($context->user()->getID(), $field));
        if (($beancount == 0 && $field != '*') || ($beancount == 5 && $field == '*')) {
            // Create a hasbadge bean, add user id to it
            $makebadge = \R::dispense('hasbadge');
            $makebadge->user_id = $context->user()->getID();
            // Find badge bean's id, and add it to the hasbadge table
            $badge = new Badge();
            $badgefound = $badge->getBadge($badgename);
            $makebadge->badge_id = $badgefound->id;
            \R::store($makebadge);
        }
    }

    /**
     * Check if a bean/field combination is allowed and the field exists and is not id
     *
     * @internal
     * @param array   $beans
     * @param string  $bean
     * @param string  $field
     *
     * @throws Framework\Exception\Forbidden
     *
     * @return bool
     */
    protected function beanCheck(array $beans, string $bean, string $field): bool
    {
        $this->fieldExists($bean, $field);
        if (!isset($beans[$bean]) || (!empty($beans[$bean]) && !in_array($field, $beans[$bean]))) { // no permission to update this field
            throw new \Framework\Exception\Forbidden('Permission denied');
        }
        return TRUE;
    }


    /**
     * Check that a bean has a field. Do not allow id field to be manipulated.
     *
     * @param string    $type    The type of bean
     * @param string    $field   The field name
     *
     * @throws \Framework\Exception\BadValue
     * @return void
     */
    private function fieldExists(string $type, string $field): void
    {
        if (!\Support\Siteinfo::hasField($type, $field) || $field == 'id') {
            throw new \Framework\Exception\BadValue('Bad field: ' . $field);
            /* NOT REACHED */
        }
    }
    /**
     * If you are using the pagination or search hinting features of the framework then you need to
     * add some appropriate vaues into these arrays.
     *
     * The key to both the array fields is the name of the bean type you are working with.
     */
    /**
     * @var array   Values controlling whether or not pagination calls are allowed
     */
    private static $allowPaging = [
        // 'bean' => [TRUE, [['ContextName', 'RoleName']]] // TRUE if login needed, then an array of roles required in form [['context name', 'role name']...] (can be empty)
    ];
    /**
     * @var array   Values controlling whether or not search hint calls are allowed
     */
    private static $allowHints = [
        // 'bean' => ['field', TRUE, [['ContextName', 'RoleName']]] // TRUE if login needed, then an array of roles required in form [['context name', 'role name']...] (can be empty)
    ];
    /**
     * @var array   Values controlling whether or not calls on the bean operation are allowed
     */
    private static $allowBean = [
        [[['Notes', 'Student']], ['upload' => []]] // an array of roles required in form [['context name', 'role name']...] (can be empty)
    ];
    /**
     * @var array   Values controlling whether or not calls on the toggle operation are allowed
     */
    private static $allowToggle = [
        // [[['ContextName', 'RoleName']], [ 'bean' => [...fields...], ...]] // an array of roles required in form [['context name', 'role name']...] (can be empty)
    ];
    /**
     * @var array   Values controlling whether or not calls on the table operation are allowed
     */
    private static $allowTable = [
        // [[['ContextName', 'RoleName']], [ 'bean', ....] // an array of roles required in form [['context name', 'role name']...] (can be empty)
    ];
    /**
     * @var array   Values controlling whether or not bean operations are logged for certain beans
     */
    private static $audit = [
        'upload'
    ];
    /**
     * Handle AJAX operations
     *
     * @param \Support\Context	$context	The context object for the site
     *
     * @return void
     */
    public function handle(Context $context): void
    {

        $this->operation(['uploaddelete'], [TRUE, ['Notes', 'Student']]);
        $this->operation(['downloadbadge'], [TRUE, ['Notes', 'Student']]);
        // TRUE if login needed, then an array of roles required in form [['context name', 'role name']...] (can be empty)
        $this->pageOrHint(self::$allowPaging, self::$allowHints);
        $this->beanAccess(self::$allowBean, self::$allowToggle, self::$allowTable, self::$audit);
        parent::handle($context);
    }
}
