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
 * External backpack library.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

// Adopted from https://github.com/jbkc85/openbadges-class-php.
// Author Jason Cameron <jbkc85@gmail.com>.

class OpenBadgesBackpackHandler {
    private $backpack;
    private $email;
    private $backpackuid = 0;
    private $backpackgid = 0;

    public function __construct($record) {
        $this->backpack = $record->backpackurl;
        $this->email = $record->email;
        $this->backpackuid = isset($record->backpackuid) ? $record->backpackuid : 0;
        $this->backpackgid = isset($record->backpackgid) ? $record->backpackgid : 0;
    }

    public function curl_request($action) {
        $curl = curl_init();
        switch($action) {
            case 'user':
                $url = $this->backpack."/displayer/convert/email";
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, array('email' => $this->email));
                break;
            case 'groups':
                $url = $this->backpack . '/displayer/' . $this->backpackuid . '/groups.json';
                break;
            case 'badges':
                $url = $this->backpack . '/displayer/' . $this->backpackuid . '/group/'. $this->backpackgid . '.json';
                break;
        }
        $options = array(
                CURLOPT_FRESH_CONNECT => true,
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FORBID_REUSE => true,
                CURLOPT_CONNECTTIMEOUT_MS => 500,
                );
        curl_setopt_array($curl, $options);
        $out = curl_exec($curl);
        curl_close($curl);
        return json_decode($out);
    }

    private function check_status($status) {
        switch($status) {
            case "missing":
                $response = array(
                    'status'  => $status,
                    'message' => get_string('error:nosuchuser', 'badges')
                );
                return $response;
                break;
        }
    }

    public function get_groups() {
        $json = $this->curl_request('user', $this->email);
        if ($json->status != 'okay') {
            return $this->check_status($json->status);
        }
        $this->backpackuid = $json->userId;
        return $this->curl_request('groups');
    }

    public function get_badges() {
        $json = $this->curl_request('user', $this->email);
        if ($json->status != 'okay') {
            return $this->check_status($json->status);
        }
        return $this->curl_request('badges');
    }

    public function get_url() {
        return $this->backpack;
    }
}