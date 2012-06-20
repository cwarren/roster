# Moodle Block: Roster

A moodle 2.3 block plugin that provides simple course rosters. It supports three display options:

1. text-based table/list

    - role
    - name (first, last)
    - username
    - email
    - optionally, location (configurable)
    - last access
    - **jQuery plugin tablesorter allows in-place sorting of the user list**

1. pictures

    - picture
    - name
    - role
    - **jQuery code allows a learning mode which hides the name and role until the mouse hovers over the image**

1. details

    - picture
    - name
    - role
    - username
    - email
    - optionally, location (configurable)
    - last access

This block aims for a streamlined, simple interface, and so does not support pagination, filtering, searching, etc. However, it (optionally) provides a link to the core user listing page (user/index.php), which does have all those capabilities.

# Installation

Installation for this block follows the normal method for installing blocks / plugins for moodle:

1. download or clone this repository
1. put it in your *moodle_home*/blocks/ folder
1. log in to your moodle site as an admin and go to Site Administration : Notifications

# Code Organization

- *block_roster.php (standard block file): displays the block (which just has a list of links to the different roster options); defines the block class*
- *edit_form.php (standard block file) : display the configuration page for the block*
- *version.php (standard block file) : version info for the block*
- */lang/en/block_roster.php (standard block file): language strings specific to this block*
- **index.php** : deals with display of the roster; linked to from the block
- **lib.php** : constants and a couple of functions common to both the class definition and the roster display
- **styles.css** : style info for the rosters (used in the roster display, not the block display)
- **js/Mottie-tablesorter-0c2c9a7** : a jQuery plugin for in-place table sorting; used in the list display of the roster

