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

# License

This block is released under the Williams College Software License (http://oit.williams.edu/itech/learning-objects/software-licensce/) as of June 2012. Here is a copy of the license text:

The Williams College Software License (WCSL)
Version 1, February 2007

Copyright (C) 2007 Williams College Office for Information Technology
22 Lab Campus Drive, Williamstown, MA 01267 USA

Everyone is permitted to copy and distribute verbatim copies of this license document, but changing it is not allowed.

The really short summary:

This work is free to use and share, but if you want to make money off it you need to talk to the copyright holder. There’s no warranty nor support.

The short summary:

You can use this work for free so long as credit is given. You copy and/or distribute it however you want as long as you’re not making money from it (if you want to make money from it you need to contact the copyright holders and negotiate a separate license, or wait). You can not make changes to it (to prevent circumvention of the previous restriction). After 4 years the latter two restrictions go away – this license eventually reverts to the GNU General Public License version 3 or later.

It’s provided completely ‘as is’. The author(s) and anyone who distributes it provide no warranty, guarantee, or support.

The legal version:

Preamble

This license is intended to allow a works to be freely used, shared, and distributed while simultaneously allowing the copyright holders the opportunity to receive compensation if any individual or organization derives revenue from the work. This opportunity takes the form of a period of time from the initial copyright claim during which the license restricts derivation, modification, and commercial distribution and after which this license reverts to the GNU General Public License (GPL), version 3 or later (http://www.gnu.org/licenses/gpl.html). Any individual or organization which wishes to generate revenue from the work must acquire a separate license or must wait until the license reverts to the GPL.

This license disallows a Distributor of the work from receiving compensation from the recipient of the work. This license prohibits modification of and derivation from the work to prevent circumvention of the distribution restriction. If you wish to incorporate parts of the Program into other programs or to use the Program in some form of compensated distribution then write to the copyright holder to negotiate a separate license.

For example, under this license you would be free to make copies work and mail them to a few hundred collegues, post the work for free download on a website or filesharing system, or hand out copies on CDs at a presentation at a conference. However, a conference organization (as opposed to an attendee) that wanted to distribute the software at a conference for which there was a fee, or a publisher that wanted to package the software with a textbook for which there was a fee would not be able to do so within 4 years of the initial copyright claim. In the latter cases those organization would need to negotiate a new license. A blogger would be free to distribute the work via their blog as long as their blog was freely readable and available (even if the blog contains advertising), but if there were a subscription fee for the blog then the blogger would require a separate license.

TERMS AND CONDITIONS

0. Definitions:

“This License” refers to version 1 or later of the Williams College Software License (WCSL).

“The Program” refers to any copyrightable work licensed under this License. Each licensee is addressed as “you”. “Licensees” and “recipients” may be individuals or organizations.

To “use the Program” means to run the program and interact with it.

The “Source Code” for a work means the preferred form of the work for making modifications to it. For an executable work, complete source code means all the source code for all modules it contains, plus any associated interface definition files, plus the scripts used to control compilation and installation of the executable. However, as a special exception, the source code distributed need not include anything that is normally distributed (in either source or binary form) with the major components (compiler, kernel, and so on) of the operating system on which the executable runs, unless that component itself accompanies the executable.

The “Object Code” or “Executeable Form” of a work means the preferred form for using the work for its intended function.

To “modify” a work means to copy from or adapt all or part of the work in a fashion requiring copyright permission, other than the making of an exact copy. The resulting work is called a “modified version” of the earlier work or a work “based on” the earlier work.

To “propagate” a work means to do anything with it that, without permission, would make you directly or secondarily liable for infringement under applicable copyright law, except executing it on a computer or modifying a private copy. Propagation includes copying, distribution (with or without modification), making available to the public, and in some countries other activities as well.

To “convey” a work means any kind of propagation that enables other parties to make or receive copies. Mere interaction with a user through a computer network, with no transfer of a copy, is not conveying.

A “Distributor” is any individual or organization that propogates or conveys the work.

“Protection Period” is the time during which this license applies, and after which this license reverts to the GNU General Public License, version 3 or later. The Protection Period starts on the first day of the most specied date period in the most recent copyright claim and ends 1461 days (i.e. 4 years) after that date.

1. You may copy, and distribute verbatim copies of the Program’s Source Code as you receive it, in any medium, provided you abide by all these:

a) that you conspicuously and appropriately publish on each copy an appropriate copyright notice and disclaimer of warranty;
b) keep intact all the notices that refer to this License and to the absence of any warranty;
c) give any other recipients of the Program a copy of this License along with the Program;
d) you receive no compensation from the recipient, for either the Program alone or any package of which the program is a part.

2. You may copy, and distribute the Program in Executable Form under the terms of Sections 1 above provided that you also accompany it with the complete corresponding machine-readable Source Code, which also must be distributed under the terms of Section 1 above on a medium customarily used for software interchange.

If distribution of the Executeable Form is made by offering access to copy from a designated place, then offering equivalent access to copy the Source Code from the same place counts as distribution of the source code.

3. You may use the program. If work resulting from the use of this Program is published or otherwise distributed it must indicate that this program was used for that work.

4. You may not modify the Program; you may not make derivative works from it; you may not combine it with other works.

5. You are not required to accept this License. However, using, copying, or distributing the Program is prohibited by law if you do not accept this License. By using, copying, or distributing this Program you indicate your acceptance of this License to do so, and all its terms and conditions.

Modifying the Program is prohibited by this license (section 4) if you accept it, and by law if you do not.

6. Any recipient of the Program in any form automatically receives this License for that Program from the original licensor. You may not impose any further restrictions on the recipients’ exercise of the rights granted herein. You are not responsible for enforcing compliance by third parties to this License.

7. If conditions are imposed on you (whether by court order, agreement or otherwise) that contradict the conditions of this License, they do not excuse you from the conditions of this License. If you cannot satisfy simultaneously your obligations under this License and any other pertinent obligations, then as a consequence you may not use, copy, or distribute the Program.

8. If the distribution and/or use of the Program is restricted in some areas either by patents or by copyrighted interfaces, the original copyright holder who places the Program under this License may add an explicit limitations excluding those areas (geo-political or otherwise), so that use and distribution is permitted only where not restricted. In such case, this License incorporates the limitation as if written in the body of this License.

9. Williams College may publish revised and/or new versions of the Williams College Software License from time to time. Such new versions will be similar in spirit to the present version, but may differ in detail to address new problems or concerns.

Each version is given a distinguishing, incremental version number. If the Program specifies a version number of this License which applies to it and “any later version”, you have the option of following the terms and conditions either of that version or of any later version published by the Williams College. If the Program does not specify a version number of this License, you may choose any version ever published by the Williams College.

10. At the expiration of the Protection Period this license is replaced in whole by the GNU General Public License version 3 or later. The copyright holder remains the same, and all terms and conditions of this License are removed and replaced with the terms and conditions of the GNU General Public License version 3 or later (http://www.gnu.org/licenses/gpl.html).

11. THIS PROGRAM IS PROVIDED WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION.

12. IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY DISTRIBUTE THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF DATA, DATA BEING RENDERED INACCURATE, LOSSES SUSTAINED BY YOU OR THIRD PARTIES, OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.

13. If any portion of this License is held invalid or unenforceable under any particular circumstance, the balance of this License is intended to apply and the License as a whole is intended to apply otherwise.

END OF TERMS AND CONDITIONS

