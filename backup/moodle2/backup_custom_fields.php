<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines various element classes used in specific areas
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of backup_final_element that provides one interceptor for anonymization of data
 *
 * This class overwrites the standard set_value() method, in order to get (by name)
 * functions from backup_anonymizer_helper executed, producing anonymization of information
 * to happen in a clean way
 *
 * TODO: Finish phpdocs
 */
class anonymizer_final_element extends backup_final_element {

    public function set_value($value) {
        // Get parent name
        $pname = $this->get_parent()->get_name();
        // Get my name
        $myname = $this->get_name();
        // Define class and function name
        $classname = 'backup_anonymizer_helper';
        $methodname= 'process_' . $pname . '_' . $myname;
        // Invoke the interception method
        $result = call_user_func(array($classname, $methodname), $value);
        // Finally set it
        parent::set_value($result);
    }
}

/**
 * Implementation of backup_final_element that provides special handling of mnethosturl
 *
 * This class overwrites the standard set_value() method, in order to decide,
 * based on various config options, what to do with the field.
 *
 * TODO: Finish phpdocs
 */
class mnethosturl_final_element extends backup_final_element {

    public function set_value($value) {
        global $CFG;

        $localhostwwwroot = backup_plan_dbops::get_mnet_localhost_wwwroot();

        // If user wwwroot matches mnet local host one or if
        // there isn't associated wwwroot, skip sending it to file
        if ($localhostwwwroot == $value || empty($value)) {
            // Do nothing
        } else {
            parent::set_value($value);
        }
    }
}

/**
 * Implementation of {@link backup_final_element} that provides base64 encoding.
 *
 * This final element transparently encodes with base64_encode() contents that
 * normally are not safe for being stored in utf-8 xml files (binaries, serialized
 * data...).
 */
class base64_encode_final_element extends backup_final_element {

    /**
     * Set the value for the final element, encoding it as utf-8/xml safe base64.
     *
     * @param string $value Original value coming from backup step source, usually db.
     */
    public function set_value($value) {
        parent::set_value(base64_encode($value));
    }
}

/**
 * Implementation of backup_nested_element that provides special handling of files
 *
 * This class overwrites the standard fill_values() method, so it gets intercepted
 * for each file record being set to xml, in order to copy, at the same file, the
 * physical file from moodle file storage to backup file storage
 *
 * TODO: Finish phpdocs
 */
class file_nested_element extends backup_nested_element {

    protected $backupid;

    public function process($processor) {
        // Get current backupid from processor, we'll need later
        if (is_null($this->backupid)) {
            $this->backupid = $processor->get_var(backup::VAR_BACKUPID);
        }
        return parent::process($processor);
    }

    public function fill_values($values) {
        // Fill values
        parent::fill_values($values);
        // Do our own tasks (copy file from moodle to backup)
        try {
            backup_file_manager::copy_file_moodle2backup($this->backupid, $values);
        } catch (file_exception $e) {
            $this->add_result(array('missing_files_in_pool' => true));

            // Build helpful log message with all information necessary to identify
            // file location.
            $context = context::instance_by_id($values->contextid, IGNORE_MISSING);
            $contextname = '';
            if ($context) {
                $contextname = ' \'' . $context->get_context_name() . '\'';
            }
            $message = 'Missing file in pool: ' . $values->filepath  . $values->filename .
                    ' (context ' . $values->contextid . $contextname . ', component ' .
                    $values->component . ', filearea ' . $values->filearea . ', itemid ' .
                    $values->itemid . ') [' . $e->debuginfo . ']';
            $this->add_log($message, backup::LOG_WARNING);
        }
    }
}

/**
 * Implementation of backup_optigroup_element to be used by plugins stuff.
 * Split just for better separation and future specialisation
 */
class backup_plugin_element extends backup_optigroup_element { }

/**
 * Implementation of backup_optigroup_element to be used by subplugins stuff.
 * Split just for better separation and future specialisation
 */
class backup_subplugin_element extends backup_optigroup_element { }
