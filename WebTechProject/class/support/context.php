<?php
/**
 * A wrapper so that users dont need to edit the FWContext class in order to add features.
 *
 * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
 * @copyright 2016-2018 Newcastle University
 *
 */
    namespace Support;
/**
 * A wrapper for the real Context class that allows people to extend its functionality
 * in ways that are apporpriate for their particular website.
 */
    class Context extends \Framework\Context
    {
/**
 * Any functions that you need to be available through context.
 */

        
 /**
 * Do we have a logged in student user?
 *
 * @return boolean
 */
        public function hasstudent()
        {
            /** @psalm-suppress PossiblyNullReference */
            return $this->hasuser() && $this->user()->isstudent();
        }

/**
 * Do we have a logged in student user?
 *
 * @return boolean
 */
        public function hasstaff()
        {
            /** @psalm-suppress PossiblyNullReference */
            return $this->hasuser() && $this->user()->isstaff();
        }

        /**
 * Do we have a logged in staff user?
 *
 * @return boolean
 */
        public function mustbestaff() : void
        {
            if (!$this->hasstaff())
            {
                throw new \Framework\Exception\Forbidden('Must be a staff member');
            }
        }

        
 /**
 * Check for a student and 403 if not
 *
 * @throws \Framework\Exception\Forbidden
 *
 * @return void
 */
        public function mustbestudent() : void
        {
            if (!$this->hasstudent())
            {
                throw new \Framework\Exception\Forbidden('Must be a student');
            }
        }
    }
?>
