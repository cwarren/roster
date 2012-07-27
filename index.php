<?php
    //  Lists all the users within a given course

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // requires/libraries

    require_once('../../config.php');
    require_once($CFG->libdir.'/tablelib.php');
    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->dirroot.'/blocks/roster/lib.php');


    ///////////////////////////////////////////////////////////////////////////////////////////////////   
    // process query params and set up relevant script environment/context

    $mode         = optional_param('mode', NULL, PARAM_INT);                  // use the MODE_ constants
    $contextid    = optional_param('contextid', 0, PARAM_INT);                // one of this or
    $courseid     = optional_param('id', 0, PARAM_INT);                       // this are required

    $blockinstanceid=optional_param('blockinstance',0,PARAM_INT);
    $blockinstance_editid=optional_param('bui_editid',0,PARAM_INT);
    if ($blockinstanceid == 0) {
        if ($blockinstance_editid == 0) {
            print_error(get_string('error_missing_block_instance_id','block_roster'));
        }
        $blockinstanceid = $blockinstance_editid;
    }
    $blockinstance = block_instance('roster', $DB->get_record('block_instances', array('id'=> $blockinstanceid)));

    $PAGE->set_url('/blocks/roster/index.php', array(
            'mode' => $mode,
            'contextid' => $contextid,
            'id' => $courseid));

    // Should use this variable so that we don't break stuff every time a variable is added or changed.
    // NOTE: this is only used in setting up a flexible_table, which we're not using since it create invalid HTML
    //    leaving htis in place in case flexible_table is fixed or replaced
//    $baseurl = new moodle_url('/blocks/roster/index.php', array(
//            'mode' => $mode,
//            'contextid' => $contextid,
//            'id' => $courseid));

    if ($contextid) {
        $context = get_context_instance_by_id($contextid, MUST_EXIST);
        if ($context->contextlevel != CONTEXT_COURSE) {
            print_error('invalidcontext');
        }
        $course = $DB->get_record('course', array('id'=>$context->instanceid), '*', MUST_EXIST);
    } else {
        $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
        $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
    }

    require_login($course);

    // prevent use of this block on the front page
    if ($course->id == SITEID) {
        error(get_string('error_courses_only','block_roster'));
    } else {
        $PAGE->set_pagelayout('incourse');
        require_capability('moodle/course:viewparticipants', $context);
    }

    if ($mode !== NULL) {
        $mode = (int)$mode;
        $SESSION->userindexmode = $mode;
    } else if (isset($SESSION->userindexmode)) {
        $mode = (int)$SESSION->userindexmode;
    } else {
        $mode = PR_MODE_NAMES;
    }

    add_to_log($course->id, 'roster', PR_mode_to_text($mode), 
               'blocks/roster/index.php?contextid='.$context->id.'&blockinstance='.$blockinstanceid.'&mode='.$mode, '');

    ///////////////////////////////////////////////////////////////////////////////////////////////////   
    // get info for users enrolled in the course
    // NOTE: this query process is based on the one in user/index.php

    // we are looking for all users assigned in this context or higher
    $contextlist = get_related_contexts_string($context);

    list($esql, $params) = get_enrolled_sql($context, NULL, 0, true);
    $joins = array("FROM {user} u");
    $wheres = array();

    $extrasql = get_extra_user_fields_sql($context, 'u', '', array(
            'id', 'username', 'firstname', 'lastname', 'email', 'city', 'country',
            'picture', 'lang', 'timezone', 'maildisplay', 'imagealt', 'lastaccess'));

    // NOTE: counts will be off if a user has multiple roles in the course!
    // TODO: figure out how to get only the role with the highest sort order
//    $select = "SELECT roster_roleinfo.name AS rolename,
    // NOTE: the first field here, junkunique, is needed to make get_records_sql work (bleah!); if using recordsets switch to above line instead
    $select = "SELECT CONCAT(u.id, roster_roleinfo.name) AS junkunique,
                      roster_roleinfo.name AS rolename,
                      u.id, u.username, u.firstname, u.lastname,
                      u.email, u.city, u.country, u.picture,
                      u.lang, u.timezone, u.maildisplay, u.imagealt,
                      COALESCE(ul.timeaccess, 0) AS lastaccess$extrasql";
    $joins[] = "JOIN ($esql) e ON e.id = u.id"; // course enrolled users only
    $joins[] = "LEFT JOIN {user_lastaccess} ul ON (ul.userid = u.id AND ul.courseid = :courseid)"; // not everybody accessed course yet
    // NOTE: this additional join is a big difference from the query in user/index - this join gets the role for each user
    // ALSO NOTE: this causes users with multiple roles to appear
    // twice in the result set - the output process deals with that
    // through a uniquifying hash, but the count could be off
    $joins[] = "LEFT JOIN (SELECT DISTINCT ra.userid, r.id, r.name, r.shortname, r.sortorder
                           FROM {role_assignments} ra, {role} r
                           WHERE r.id = ra.roleid
                             AND ra.contextid $contextlist
                          ) roster_roleinfo ON roster_roleinfo.userid=u.id";

    $params['courseid'] = $course->id;

    // performance hacks - we preload user contexts together with accounts
    list($ccselect, $ccjoin) = context_instance_preload_sql('u.id', CONTEXT_USER, 'ctx');
    $select .= $ccselect;
    $joins[] = $ccjoin;

    $from = implode("\n", $joins);
    if ($wheres) {
        $where = "WHERE " . implode(" AND ", $wheres);
    } else {
        $where = "";
    }

    $from = implode("\n", $joins);
    if ($wheres) {
        $where = "WHERE " . implode(" AND ", $wheres);
    } else {
        $where = "";
    }

    $sort = ' ORDER BY roster_roleinfo.sortorder, u.lastname, u.firstname';

    // NOTE: if you're worried about especially large class lists use recordsets instead of records (takes less memory, but runs a bit slower)...
//     $num_users_in_list = $DB->count_records_sql("SELECT COUNT(u.id) $from $where", $params);
//     $userlist = $DB->get_recordset_sql("$select $from $where $sort", $params); // can't grab the count from the recordset - it's in the data structure, but protected

    //...but I'm using get_records_sql because not that concerned about memory use in this case and it removes a DB call
    $userlist = $DB->get_records_sql("$select $from $where $sort", $params);
    $num_users_in_list = count($userlist);


// echo "<pre>"; print_r($userlist); echo '</pre>';
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////   
    // display results

    $PAGE->set_title("$course->shortname: ".get_string('roster','block_roster'));
    $PAGE->set_heading($course->fullname);
    $PAGE->set_pagetype('course-view-' . $course->format);
    $PAGE->add_body_class('path-user');                     // So we can style it independently
    $PAGE->set_other_editing_capability('moodle/course:manageactivities');
    
    // CSW
    $PAGE->navbar->add(get_string('roster','block_roster'));
    $PAGE->navbar->add(PR_mode_to_text($mode).' ('.$num_users_in_list.')');

    echo $OUTPUT->header();
    echo '<link rel="stylesheet" type="text/css" href="styles.css"/>';

    echo '<div class="roster_content">'; // putting everything in an enclosing div creates a local namespace that can be used in the stylesheet

    // Get the hidden field list
    if (has_capability('moodle/course:viewhiddenuserfields', $context)) {
        $hiddenfields = array();  // teachers and admins are allowed to see everything
    } else {
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    }

    if ($num_users_in_list < 1) {
        echo $OUTPUT->heading(get_string('nothingtodisplay'));

    } else // ($num_users_in_list > 0) 
    {
        $datestring = new stdClass();
        $datestring->year  = get_string('year');
        $datestring->years = get_string('years');
        $datestring->day   = get_string('day');
        $datestring->days  = get_string('days');
        $datestring->hour  = get_string('hour');
        $datestring->hours = get_string('hours');
        $datestring->min   = get_string('min');
        $datestring->mins  = get_string('mins');
        $datestring->sec   = get_string('sec');
        $datestring->secs  = get_string('secs');

        $countries = get_string_manager()->get_list_of_countries();
        $strnever = get_string('never');

        //////////////////////////////////////////////////////////////////////////////
        // Different modes are largely separate presentations of the same core data (fetched above)

        //////////////////////////////////////////////////////////////////////////////
        if ($mode === PR_MODE_NAMES) { // a table-based display of the course participants

            ///////// determine what data will be displayed
            $userfields = array(
                array('header' => get_string('role')                       ,'fieldname' => 'rolename'),
                array('header' => get_string('firstname')                  ,'fieldname' => 'firstname'),
                array('header' => get_string('lastname','block_roster')    ,'fieldname' => 'lastname'),
                array('header' => get_string('username')                   ,'fieldname' => 'username'),
                array('header' => get_string('email')                      ,'fieldname' => 'email')
            );
            if ($blockinstance->config->show_location_info) {
                $userfields[] = array('header' => get_string('city')       ,'fieldname' => 'city');
                $userfields[] = array('header' => get_string('country')    ,'fieldname' => 'country');
            }
            if (!isset($hiddenfields['lastaccess'])) {
                $userfields[] = array('header' => get_string('lastaccess') ,'fieldname' => 'lastaccess');
            }

            $displaytablecolumns = array();
            $displaytableheaders = array();
            foreach ($userfields as $userfield) {
                $tablecolumns[] = $userfield['fieldname'];
                $tableheaders[] = $userfield['header'];
            }

// NOTE: not using the built in table renderer because it creates bad table HTML (no thead section)
// leaving this code in place in case a later moodle iteration fixes that renderer
/*
            ////////// set up the table to do the display
            $displaytable = new flexible_table('roster-index-listing-'.$course->id);
            $displaytable->define_baseurl($baseurl->out());

            $displaytable->define_columns($tablecolumns);
            $displaytable->define_headers($tableheaders);
            $displaytable->attributes['class'] = 'courseroster_listing tablesorter';
            $displaytable->set_attribute('cellspacing', '0');
            $displaytable->set_attribute('id', 'courseroster_listing');

	    $displaytable->setup();
*/
            
            echo '<table class="roster_list_names tablesorter" id="roster_list_names">';
            echo '<thead><tr><th>'.implode('</th><th>',$tableheaders).'</th></tr></thead>';
            echo '<tbody>';

            ////////// populate the table with data
            $usersprinted = array(); // this hash is used to prevent duplicate display of users
            foreach ($userlist as $user) {

                if (in_array($user->id, $usersprinted)) { /// Prevent duplicates by r.hidden - MDL-13935
                    continue;
                }
                $usersprinted[] = $user->id;

               // make sure there's a usable value for lastaccess
               if ($user->lastaccess) {
                    $user->lastaccess = format_time(time() - $user->lastaccess, $datestring);
                } else {
                    $user->lastaccess = $strnever;
                }

               // make sure there's a usable value for country
                if (empty($user->country)) {
                    $user->country = '';
                } else {
                    $user->country = $countries[$user->country];
                }

                // populate the data array
		$data = array();
                foreach ($userfields as $userfield) {
                    $data[] = $user->{$userfield['fieldname']};
                }
                echo '<tr><td>'.implode('</td><td>',$data).'</td></tr>';
//                $displaytable->add_data($data);
            }

//	    echo '<pre>'; print_r($displaytable); echo '</pre>';
//	    $displaytable->print_html();
            echo '</tbody>';
            echo "</table>";
        } // end ($mode === PR_MODE_NAMES)
        //////////////////////////////////////////////////////////////////////////////
        elseif ($mode === PR_MODE_PICTURES) { // a grid of larger pictures of course participants

            echo '<div id="self_quiz_button_block"><input type="button" id="self_quiz_button" '.
                         'value="'.get_string('learningmode_turn_on','block_roster').'" '.
                         'title="'.get_string('learningmode_turn_on_tip','block_roster').'" '.
                         'onvalue="'.get_string('learningmode_turn_on','block_roster').'" '.
                         'ontitle="'.get_string('learningmode_turn_on_tip','block_roster').'" '.
                         'offvalue="'.get_string('learningmode_turn_off','block_roster').'" '.
                         'offtitle="'.get_string('learningmode_turn_off_tip','block_roster').'" '.
                         'curstate="off" '.
                         '/></div>';

            echo '<ul id="roster_list_pictures">';
            $usersprinted = array();
            foreach ($userlist as $user) {
                if (in_array($user->id, $usersprinted)) { /// Prevent duplicates by r.hidden - MDL-13935
                    continue;
                }
                $usersprinted[] = $user->id;

                echo '<li class="userinfo_section">';
                echo '<div class="infoblock_pic">';

                echo '<div class="userinfo_pic">';
                echo $OUTPUT->user_picture($user, array('size' => 100, 'courseid'=>$course->id));
                echo '</div>';

                echo '<div class="userinfo_fullname">';
                echo $user->{'firstname'}.' '.$user->{'lastname'};
                echo '</div>';

                echo '<div class="userinfo_courserole">';
                echo $user->{'rolename'};
                echo '</div>';

                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';

        } // end ($mode === PR_MODE_PICTURES)
        //////////////////////////////////////////////////////////////////////////////
        elseif ($mode === PR_MODE_DETAILS) { // a list of larger pictures of course participants with additional information about each

            echo '<ul id="roster_list_details">';
            $usersprinted = array();
            foreach ($userlist as $user) {
                if (in_array($user->id, $usersprinted)) { /// Prevent duplicates by r.hidden - MDL-13935
                    continue;
                }
                $usersprinted[] = $user->id;

                echo '<li>';
                echo '<div class="userinfo_section">';

                echo ' <div class="infoblock_pic">';

                echo '  <div class="userinfo_pic">';
                echo $OUTPUT->user_picture($user, array('size' => 100, 'courseid'=>$course->id));
                echo '  </div>';

                echo '  <div class="userinfo_fullname">';
                echo $user->{'firstname'}.' '.$user->{'lastname'};
                echo '  </div>';

                echo '  <div class="userinfo_courserole">';
                echo $user->{'rolename'};
                echo '  </div>';

                echo ' </div>';

                echo ' <div class="infoblock_nonpic">';
                echo '  <div class="userinfo_username">'.$user->{'username'}.'</div>';
                echo '  <div class="userinfo_email">'.$user->{'email'}.'</div>';
                if ($blockinstance->config->show_location_info) {
                    echo '  <div class="userinfo_city">'.$user->{'city'}.'</div>';
                    echo '  <div class="userinfo_country">'.$user->{'country'}.'</div>';
                }
                if (!isset($hiddenfields['lastaccess'])) {
                    if ($user->lastaccess) {
                        $user->lastaccess = format_time(time() - $user->lastaccess, $datestring);
                    } else {
                        $user->lastaccess = $strnever;
                    }
                    echo '  <div class="userinfo_lastaccess">Last access: '.$user->{'lastaccess'}.'</div>';
                }
                echo ' </div>';

                echo '</div>';
                echo '</li>';
            } // end foreach
            echo '</ul>';

        } // end ($mode === PR_MODE_DETAILS)

    } // end ($num_users_in_list > 0) (i.e. there are users to show)

    echo '</div>';  // class="roster_content"

////////////////////////////////////////////////////////////////////////////////////////////////////   
// javascript enhancements

//echo "<pre>"; print_r($PAGE->theme); echo "<pre>";

// NOTE: tried various approaches to checking whether jQuery has already been loaded, but nothing worked
//    - dynamic loading in JS leads to error 'tablesorter not defined', which I expect has to do with load order and timing issues
//    - checking $PAGE->theme->javascripts_footer fails - that reports 'jquery-1.7.2.min' even when that lib isn't loaded (at least, the load call doesn't appear in the page source, and the subsequent jquery code doesn't run unless I explicitly load the jquery library)

    echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="js/Mottie-tablesorter-0c2c9a7/css/blue/style.css" />';
    echo '<script type="text/javascript" src="js/Mottie-tablesorter-0c2c9a7/js/jquery.tablesorter.min.js"></script>';

//$cswtmp = print_r($PAGE,true);

    echo $OUTPUT->footer();

    echo '
<script type="text/javascript">
//<![CDATA[
jQuery.noConflict();
jQuery(document).ready(function(){

    jQuery("#roster_list_names").tablesorter({ sortList: [[0,1],[2,0]] });

    jQuery("#self_quiz_button_block").css("display","block");

    jQuery("#self_quiz_button").click(function(evt){
        if (jQuery(this).attr("curstate") == "off") {
            jQuery(this).attr("curstate","on");
            jQuery(this).attr("value",jQuery(this).attr("offvalue"));
            jQuery(this).attr("title",jQuery(this).attr("offtitle"));
            jQuery(this).css("font-weight","bold");
            jQuery(".userinfo_fullname").css("display","none");
            jQuery(".userinfo_courserole").css("display","none");
        } else {
            jQuery(this).attr("curstate","off");
            jQuery(this).attr("value",jQuery(this).attr("onvalue"));
            jQuery(this).attr("title",jQuery(this).attr("ontitle"));
            jQuery(this).css("font-weight","inherit");
            jQuery(".userinfo_fullname").css("display","block");
            jQuery(".userinfo_courserole").css("display","block");
        }
    });
});
//]]>
</script>
';

    // bit of safety to close userlist if it was created as a recordset
    if (($userlist) && ( gettype($userlist) == 'object')) {
        $userlist->close();
    }

    exit;
