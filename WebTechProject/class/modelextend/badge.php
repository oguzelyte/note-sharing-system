<?php

/**
 * A class that allows convenient edits to the badges.
 * If new badges are added to the system (or any names 
 * are changed) their names need
 * to be written here.
 *
 * @author Olivija Guzelyte
 *
 */

namespace ModelExtend;

class Badge
{

    const DBPREFIX	    = '';
    const NOTEUPLOAD	= self::DBPREFIX.'Note Uploaded';
    const NOTERATE	    = self::DBPREFIX.'Note Rated';
    const NOTEFLAG	    = self::DBPREFIX.'Note Flagged';
    const NOTEDOWNLOAD	= self::DBPREFIX.'Note Downloaded';
    const NOTEEDIT	    = self::DBPREFIX.'Note Edited';
    const NOTERATED	    = self::DBPREFIX.'Your Note Rated';
    const NOTE5STAR	    = self::DBPREFIX.'5 Star Rating';
    const NOTES4AVG	    = self::DBPREFIX.'Notes Average 4';
    const NOTEDELETE	= self::DBPREFIX.'5 Notes Deleted';
    const NOTEMASTER	= self::DBPREFIX.'Note Master';
    const ICONS = ['cloud-fill','bar-chart-fill','flag-fill',
    'arrow-down','type','kanban-fill','star-fill','people-fill','x-circle-fill','unlock'
    ];

    // Get badge by name
    public function getBadge(string $name) : ?object
    {
        
        $badge = \R::findOne( 'badge', 'name = ?', [$name] );
        if (!empty($badge))
        {
            return $badge;
        }
        else
        {
            throw new \Framework\Exception\MissingBean('Cannot find badge');
        }
    }
}
?>