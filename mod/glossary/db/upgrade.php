<?php

// This file keeps track of upgrades to
// the glossary module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_glossary_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2012022000) {

        // Define field approvaldisplayformat to be added to glossary
        $table = new xmldb_table('glossary');
        $field = new xmldb_field('approvaldisplayformat', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'default', 'defaultapproval');

        // Conditionally launch add field approvaldisplayformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // glossary savepoint reached
        upgrade_mod_savepoint(true, 2012022000, 'glossary');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015060200) {

        // Define field showtabs to be added to glossary_formats.
        $table = new xmldb_table('glossary_formats');
        $field = new xmldb_field('showtabs', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'showgroup');

        // Conditionally launch add field showtabs.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Glossary savepoint reached.
        upgrade_mod_savepoint(true, 2015060200, 'glossary');
    }

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}


