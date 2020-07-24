<?php
/**
 * A trait that allows extending the model class for the RedBean object User
 *
 * Add any new methods you want the User bean to have here.
 *
 * @author Olivija Guzelyte
 * @copyright 2018-2019 Newcastle University
 *
 */
    namespace ModelExtend;
/**
 * User table stores info about users of the system
 */
    trait User
    {
/**
 * A function to ensure that any relevant password rules are applied when
 * setting a new password. Defaults to be not-empty. Modify this method if
 * you want to implement particular password rules. (Length is really the
 * only thing you should be testing though!)
 *
 * @param string    $pw  The password
 *
 * @throws \Framework\Exception\BadValue If a bad password is detected this could be thrown
 *
 * @return bool
 */
        public static function pwValid(string $pw) : bool
        {
            return $pw !== '';
        }
/**
 * Do any extra registration stuff
 *
 * Returns an array of error messages or an empty array if OK
 *
 * @param \Support\Context $context
 *
 * @return array
 */
        public function register($context) : array
        {
            $this->bean->fullname = $context->formdata()->mustpost('fullname');
            $this->bean->setpw($context->formdata()->mustpost('password'));
            return [];
        }
/**
 * Is this user a student?
 *
 * @return bool
 */
        public function isstudent() : bool
        {
            return is_object($this->bean->hasrole('Notes', 'Student'));
        }

/**
* Is this user a staff member?
 *
 * @return bool
 */
        public function isstaff() : bool
        {
            return is_object($this->bean->hasrole('Notes', 'Staff'));
        }


/*
* Called from the "add" function when a new user is created.
 * This allows you to do any extra operations that you want to when a user is added
 *
 * @param \Support\Context $context
 *
 * @return void
 */
        public function addData(\Support\Context $context)
        {
            
        }
    }
