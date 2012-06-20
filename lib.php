<?php

define('PR_DEFAULT_PAGE_SIZE', 5000);
define('PR_SHOW_ALL_PAGE_SIZE', 5000);
define('PR_MODE_NAMES', 0);
define('PR_MODE_PICTURES', 1);
define('PR_MODE_DETAILS', 2);

//////////////////////////////////////////////////////////////////////////////////////////
function PR_roster_link($linkmode=PR_MODE_NAMES,$contextid,$block_instance) 
{
    if (! $contextid) {
        return '';
    }
    if (! $block_instance) {
        return '';
    }

    global $CFG;
    $href  = $CFG->wwwroot.'/blocks/roster/?contextid='.$contextid.'&mode='.$linkmode.'&blockinstance='.$block_instance->id;

    $linktitle = PR_mode_to_text($linkmode);

    return '<a href="'.$href.'">'.$linktitle."</a>";
}

//////////////////////////////////////////////////////////////////////////////////////////
function PR_mode_to_text($m)
{
    if ($m == PR_MODE_NAMES) {
        return get_string('linknames', 'block_roster');
    }
    if ($m == PR_MODE_PICTURES) {
        return get_string('linkpics', 'block_roster');
    }
    if ($m == PR_MODE_DETAILS) {
        return get_string('linkfull', 'block_roster');
    }
}
