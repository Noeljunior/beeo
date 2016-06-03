<?php
    /* * * * * * * * * * * * * * * * * * * * * * *
     *
     *  uisysadmin / en-uk
     *
     * * * * * * * * * * * * * * * * * * * * * * */

    return [
        /* * * * * * * * * * * * * * * * * * * * * GENERAL * * * * */
        'menutxt'       => 'Sysadmin',
        'modulename'    => 'System administration',
        /* * * * * * * * * * * * * * * * * * * * * PERMISSIONS * * * * */
        'perm1'         => 'Default view',
        /* * * * * * * * * * * * * * * * * * * * * UP MENUS * * * * */
        'menu1'         => 'Profiles',
        'menu2'         => 'Permissions',
        /* * * * * * * * * * * * * * * * * * * * * SEPARATORS * * * * */
        'sep1'          => 'System profiles',
        'sep2'          => 'Profile details',
        'sep3'          => 'Create new profile',
        'sep4'          => 'Edit profile',
        'sep5'          => 'Delete profile',
        'sep6'          => 'System permissions',
        /* * * * * * * * * * * * * * * * * * * * * STRINGS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * LABELS * * * * */
        'lbl1'          => 'Name',
        'lbl2'          => 'Id',
        'lbl3'          => 'Description',
        /* * * * * * * * * * * * * * * * * * * * * BUTTONS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * INFOS * * * * */
        'inf1'          => 'New profile %1$s created with success.',
        'inf2'          => 'Profile %1$s edited with success.',
        'inf3'          => 'Profile %1$s deleted with success.',
        /* * * * * * * * * * * * * * * * * * * * * WARNINGS * * * * */
        'war1'          => 'There are users associated with this profile. Editing it may affect their use of the platform.<br>Edit with care.',
        'war2'          => 'There are users associated with this profile. Deleting it may affect their use of the platform.<br>Continue with care.',
        /* * * * * * * * * * * * * * * * * * * * * ERRORS * * * * */
        'err1'          => 'The profile name cannot be empty.',
        'err2'          => 'The id cannot be empty.',
        'err3'          => 'Profile name already in use.',
        'err4'          => 'Id already in use.',
        /* * * * * * * * * * * * * * * * * * * * * LOGS * * * * */
        'log1'          => 'Created profile \'%1$s\'',
        'log2'          => 'Edited the profile \'%1$s\'',
        'log3'          => 'Deleted the profile \'%1$s\'',
        'log4'          => 'Deleted the system permissions.',
        'log5'          => 'Added new system permissions.',
        /* * * * * * * * * * * * * * * * * * * * * HELP * * * * */
        'help1'         => 'In this page are listed the system profiles for acessing the platform. To each of these are associated different system permissions, editable on the next page. Each user can be granted these profiles.<br>To be noted that only users with sysadmin profile can visualize this menu.',
        'help2'         => 'Here you can watch details on the profile \'%1$s\'.<br>To edit it, click on the button.',
        'help3'         => 'In this page are listed the system permissions. Here you can give or remove each permission to each different profile.<br>Please notice that permissions have precedence rules based on common sense:<br>default view has precedence over all the others (if a profile does not have permission or default view, then will not be able to do anything else, even if has permission for such;<br>with no access to a users information, cannot edit;<br>not being able to edit, will not be able to delete).',
    
    ];
?>
