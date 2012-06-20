moodle_block_roster
===================

A moodle 2.3 block plugin that provides simple course rosters. It supports three display options:
    1. text-based table/list
        - role
        - name (first, last)
        - username
        - email
        - optionally, location (configurable)
        - last access
        * jQuery plugin tablesorter allows in-place sorting of the user list
    2. pictures
        - picture
        - name
        - role
        * jQuery code allows a learning mode which hides the name and role until the mouse hovers over the image
    3. details
        - picture
        - name
        - role
        - username
        - email
        - optionally, location (configurable)
        - last access

This block aims for a streamlined, simple interface, and so does not support pagination, filtering, searching, etc. However, it does (optionally) provide a link to the core user listing page (user/index.php), which does have all those capabilities.


organization
-------------------
