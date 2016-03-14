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
 * @package block_glossary_random
 * @subpackage backup-moodle2
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Specialised restore task for the glossary_random block
 * (using execute_after_tasks for recoding of glossaryid)
 *
 * TODO: Finish phpdocs
 */
class restore_glossary_random_block_task extends restore_block_task {

    protected function define_my_settings() {
    }

    protected function define_my_steps() {
    }

    public function get_fileareas() {
        return array(); // No associated fileareas
    }

    public function get_configdata_encoded_attributes() {
        return array(); // No special handling of configdata
    }

    /**
     * This function, executed after all the tasks in the plan
     * have been executed, will perform the recode of the
     * target glossary for the block. This must be done here
     * and not in normal execution steps because the glossary
     * may be restored after the block.
     */
    public function after_restore() {
        global $DB;

        // Get the blockid
        $blockid = $this->get_blockid();

        // Extract block configdata and update it to point to the new glossary
        if ($configdata = $DB->get_field('block_instances', 'configdata', array('id' => $blockid))) {
            $config = unserialize(base64_decode($configdata));
            if (!empty($config->glossary)) {
                // Get glossary mapping and replace it in config
                if ($glossarymap = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'glossary', $config->glossary)) {
                    $mappedglossary = $DB->get_record('glossary', array('id' => $glossarymap->newitemid),
                        'id,course,globalglossary', MUST_EXIST);
                    $config->glossary = $mappedglossary->id;
                    $config->courseid = $mappedglossary->course;
                    $config->globalglossary = $mappedglossary->globalglossary;
                    $configdata = base64_encode(serialize($config));
                    $DB->set_field('block_instances', 'configdata', $configdata, array('id' => $blockid));
                } else {
                    // The block refers to a glossary not present in the backup file.
                    $DB->set_field('block_instances', 'configdata', '', array('id' => $blockid));
                }
            }
        }
    }

    static public function define_decode_contents() {
        return array();
    }

    static public function define_decode_rules() {
        return array();
    }
}
