<?php
 
class block_roster_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
 
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
 
        // whether to display various roster views
        $mform->addElement('advcheckbox', 'config_flaglinknames', get_string('config_label_flaglinknames', 'block_roster'));
        $mform->setDefault('config_flaglinknames', true);
        $mform->setType('config_flaglinknames', PARAM_BOOL);        

        $mform->addElement('advcheckbox', 'config_flaglinkpics', get_string('config_label_flaglinkpics', 'block_roster'));
        $mform->setDefault('config_flaglinkpics', true);
        $mform->setType('config_flaglinkpics', PARAM_BOOL);        

        $mform->addElement('advcheckbox', 'config_flaglinkfull', get_string('config_label_flaglinkfull', 'block_roster'));
        $mform->setDefault('config_flaglinkfull', true);
        $mform->setType('config_flaglinkfull', PARAM_BOOL);        

        $mform->addElement('advcheckbox', 'config_flaglinksortfilter', get_string('config_label_flaglinksortfilter', 'block_roster'));
        $mform->setDefault('config_flaglinksortfilter', true);
        $mform->setType('config_flaglinksortfilter', PARAM_BOOL);        

        // whether to show location info inthe listing views (city and country)
        $mform->addElement('advcheckbox', 'config_show_location_info', get_string('config_label_location', 'block_roster'));
        $mform->setDefault('config_show_location_info', false);
        $mform->setType('config_show_location_info', PARAM_BOOL);        

	
    }
}