<?php

/**
 * A class that contains code to access the PHP super globals
 *
 * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
 * @copyright 2019 Newcastle University
 *
 */

namespace Support;

/**
 * Utility class that returns generally useful information about parts of the site
 * The parent class (FWSiteInfo) contains a set of functions that are used by the
 * admin pages of the site.
 */
class FormData extends \Framework\FormData
{
    /**
     * Is the key in the $_FILES array?
     *
     * Note: no support for FILES in the filter_has_var function
     *
     * @param string	$name	The key
     *
     * @return bool
     */
    public function hasbutton(string $name): bool
    {
        return isset($_POST[$name]);
    }
    /**
     * Any functions that you need for accessing fields go below here.
     */
}
