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
 * Baking badges library.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

class PNG_MetaDataHandler
{
    private $_contents;
    private $_size;
    private $_chunks;

    public function __construct($contents) {
        $this->_contents = $contents;
        $png_signature = pack("C8", 137, 80, 78, 71, 13, 10, 26, 10);

        // Read 8 bytes of PNG header and verify.
        $header = substr($this->_contents, 0, 8);

        if ($header != $png_signature) {
            debugging('This is not a valid PNG image');
        }

        $this->_size = strlen($this->_contents);

        $this->_chunks = array();

        // Skip 8 bytes of header.
        $position = 8;
        do {
            $chunk = @unpack('Nsize/a4type', substr($this->_contents, $position, 8));
            $this->_chunks[$chunk['type']][] = substr($this->_contents, $position + 8, $chunk['size']);

            // Skip 12 bytes chunk overhead.
            $position += $chunk['size'] + 12;
        } while ($position < $this->_size);
    }

    // Checks if key already exists in the chunk of said type.
    public function check_chunks($type, $check) {
        if (array_key_exists($type, $this->_chunks)) {
            foreach (array_keys($this->_chunks[$type]) as $typekey) {
                list($key, $data) = explode("\0", $this->_chunks[$type][$typekey]);

                if (strcmp($key, $check) == 0) {
                    debugging('Key "' . $check . '" already exists in "' . $type . '" chunk.');
                    return false;
                }
            }
        }
        return true;
    }

    // Adds a chunk to contents.
    public function add_chunks($type, $key, $value) {
        if (strlen($key) > 79) {
            debugging('Key is too big');
        }

        // Baking iTXt is not working at the moment. Need to have a look at this when Mozilla spec is out of beta.
        if ($type == 'iTXt') {
            $data = $key . "\0" . 0 . 0 . "json" . "\0" . "''" . "\0" . '{"method": "hosted", "assertionUrl": "' . $value . '"}';
        } else {
            $data = $key . "\0" . $value;
        }
        $crc = pack("N", crc32($type . $data));
        $len = pack("N", strlen($data));

        // Chunk format: length + type + data + CRC.
        // CRC is a CRC-32 computed over the chunk type and chunk data.
        $newchunk = $len .  $type  . $data . $crc;

        $result = substr($this->_contents, 0, $this->_size - 12)
                . $newchunk
                . substr($this->_contents, $this->_size - 12, 12);

        return $result;
    }
}