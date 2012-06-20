<?php

require_once($CFG->dirroot.'/blocks/roster/lib.php');

class block_roster extends block_list {

    ///////////////////////////////////////////////////////////////////////////////
    public function init() {
        $this->title = get_string('roster', 'block_roster');
    }

    ///////////////////////////////////////////////////////////////////////////////
    public function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }

        global $CFG, $COURSE, $OUTPUT;
 
        $this->content         =  new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
 
        // get the course context, or else abort
        if (!$currentcontext = get_context_instance(CONTEXT_COURSE, $COURSE->id)) {
            $this->content = '';
            return $this->content;
        }
        
        // abort if at the site level or if viewparticipants is not allowed
        if ( ($COURSE->id == SITEID) || (!has_capability('moodle/course:viewparticipants', $currentcontext)) ) {
            $this->content = '';
            return $this->content;
        }

	// no icons - for this block they provide no useful info
        $icon_code = '&nbsp;&nbsp;';

	if (is_null($this->config) || $this->config->flaglinknames) {
          $this->content->items[] = PR_roster_link(PR_MODE_NAMES,$currentcontext->id,$this->instance);
          $this->content->icons[] = $icon_code;
        }
	if (is_null($this->config) || $this->config->flaglinkpics) {
          $this->content->items[] = PR_roster_link(PR_MODE_PICTURES,$currentcontext->id,$this->instance);
          $this->content->icons[] = $icon_code;
        }
	if (is_null($this->config) || $this->config->flaglinkfull) {
          $this->content->items[] = PR_roster_link(PR_MODE_DETAILS,$currentcontext->id,$this->instance);
          $this->content->icons[] = $icon_code;
        }
	if (is_null($this->config) || $this->config->flaglinksortfilter) {
          // sort & filter just links to the normal user/index.php page
          $this->content->items[] = '<a href="'
                                     .$CFG->wwwroot.'/user/index.php?contextid='.$currentcontext->id.'">'
                                     .get_string('linksortfilter','block_roster').'</a>';
          $this->content->icons[] = $icon_code;
        }

        return $this->content;

    } // end get_content

    ///////////////////////////////////////////////////////////////////////////////
    

}   // end class definition