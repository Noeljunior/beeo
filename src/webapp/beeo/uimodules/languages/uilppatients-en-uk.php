<?php
    /* * * * * * * * * * * * * * * * * * * * * * *
     *
     *  uipatients / en-uk
     *
     * * * * * * * * * * * * * * * * * * * * * * */

    $MASCULINO = TRUE;
    $SINGULAR = 'patient';
    $PLURAL = 'patients';




    return [
        /* * * * * * * * * * * * * * * * * * * * * GENERAL * * * * */
        'menutxt'       => ucfirst($PLURAL),
        'modulename'    => ucfirst($SINGULAR). ' management',
        'name'          => $PLURAL,
        /* * * * * * * * * * * * * * * * * * * * * PERMISSIONS * * */
        'perm1'         => 'Default view',
        'perm2'         => 'Search '.$PLURAL.' by identifier',
        'perm3'         => 'Create '.$PLURAL,
        'perm4'         => 'View a list of all '.$PLURAL,
        'perm5'         => 'View details of an existing '.$SINGULAR,
        'perm6'         => 'Edit information of an existing '.$SINGULAR,
        'perm7'         => 'Can edit the '.$SINGULAR.'s\' identifier',
        /* * * * * * * * * * * * * * * * * * * * * UP MENUS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * SEPARATORS * * * */
        'sep1'          => 'Search '.$PLURAL,
        'sep2'          => 'List of all '.$PLURAL,
        'sep3'          => ucfirst($SINGULAR).' file',
        //'sep4'          => ucfirst($SINGULAR).' information',

        'lbl1'          => ucfirst($SINGULAR).' identifier',
        'lbl2'          => 'Date the '.$SINGULAR.' was added',
        'lbl3'          => ucfirst($SINGULAR).' added by',

        'sep5'          => ucfirst($SINGULAR).'\'s history',
        'sep6'          => 'Edit '.$SINGULAR,
        'sep7'          => 'Event\'s content',

        'sepne1'        => 'Add new event',
        'lblne1'        => 'Name of the event',
        'lblne2'        => 'Date in which it happened',
        'lblne3'        => 'Hour in which it happened',
        'lblne4'        => 'Type of event',

        'sepve1'        => 'Event details',
        'lblve1'        => 'Event name',
        'lblve2'        => 'Date in which it happened',
        'lblve3'        => 'Created by',
        'lblve4'        => 'Type of event',

        'sepna1'        => 'Add new activity',
        'lblna1'        => 'Name of the activity',/*lblna1 -> lblna4*/
        'lblna2'        => 'Date in which it happened',
        'lblna3'        => 'Hour in which it happened',
        'lblna4'        => 'Type of activity',
        'sepna2'        => 'Questions',

        'sepva1'        => 'Activity details',
        'lblva1'        => 'Activity name',
        'lblva2'        => 'Date in which it happened',
        'lblva3'        => 'Created by',
        'lblva4'        => 'Type of activity',
        'sepva2'        => 'Answers given',

        /* * * * * * * * * * * * * * * * * * * * * STRINGS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * LABELS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * BUTTONS * * * * */
        'btn1'          => 'View '.$SINGULAR,
        'btn2'          => 'Submit answers',
        /* * * * * * * * * * * * * * * * * * * * * INFOS * * * * */
        'inf1'          => 'New '.$SINGULAR.' \'%1$s\' created with success.',
        //'inf2'          => ucfirst($SINGULAR).' \'%1$s\' updated with success.',
        'inf3'          => 'New event %2$s created in '.$SINGULAR.' %1$s with success.',
        'inf4'          => 'New activity %2$s created in '.$SINGULAR.' %1$s with success.',
        /* * * * * * * * * * * * * * * * * * * * * WARNINGS * * * * */
        'war1'          => 'The '.$SINGULAR.' you are trying to find does not exist. Would you like to create it?',
        //'war2'          => 'This '.$SINGULAR.' has history in the system. Editing it may affect the meaning of its history.<br>Edit with care.<br>This '.$SINGULAR.' cannot be deleted while it has history.',
        'war3'          => 'The '.$SINGULAR.' you are trying to find does not exist.',
        'war4'          => 'Please introduce the '.$SINGULAR.' identifier.',
        /* * * * * * * * * * * * * * * * * * * * * ERRORS * * * * */
        'err1'          => 'The '.$SINGULAR.' name cannot be empty.',
        'err2'          => ucfirst($SINGULAR).' name already in use.',
        /* * * * * * * * * * * * * * * * * * * * * LOGS * * * * */
        'log1'          => 'Created '.$SINGULAR.' \'%1$s\'',
        //'log2'          => 'Edited '.$SINGULAR.' \'%1$s\'',
        //'log3'          => 'Viewed informations about '.$SINGULAR.' \'%1$s\'',
        //'log4'          => 'Deleted '.$SINGULAR.' \'%1$s\'',
        'log5'          => 'Created action %2$s in '.$SINGULAR.' \'%1$s\'',
        'log6'          => 'Created event %2$s in '.$SINGULAR.' \'%1$s\'',
        /* * * * * * * * * * * * * * * * * * * * * HELP * * * * */
    
    ];
?>
