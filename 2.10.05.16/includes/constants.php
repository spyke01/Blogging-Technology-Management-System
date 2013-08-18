<?php 
/***************************************************************************
 *                               constants.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***************************************************************************/

//=====================================================
// Application
//=====================================================
define('A_NAME', 'fts_bts');
define('A_VERSION', '2.10.05.16');

//=====================================================
// Debug Level
//=====================================================
//define('DEBUG', 1); // Debugging on
define('DEBUG', 0); // Debugging off

//=====================================================
// Global state
//=====================================================
define('ACTIVE', 1);
define('INACTIVE', 0);

//=====================================================
// Allow Comments
//=====================================================
define('ALLOW_COMMENTS', 1);
define('DO_NOT_ALLOW_COMMENTS', 0);

//=====================================================
// User Levels <- Do not change these values!!
//=====================================================
define('USER', 0);
define('ADMIN', 1);
define('MOD', 2);
define('BANNED', 3);

//=====================================================
// Menus
//=====================================================
define('TOPMENU', 1);

//=====================================================
// Menu types
//=====================================================
define('MENU_INTERNAL', 1);
define('MENU_EXTERNAL', 2);

//=====================================================
// Entry types
//=====================================================
define('PAGE_BBCODE', 1);
define('PAGE_WYSIWYG', 2);

//=====================================================
// Entry visibility
//=====================================================
define('VISIBLE', 1);
define('HIDDEN', 0);

//=====================================================
// System Settings
//=====================================================
$FTS_TIMEZONES = array(
	"-12" => "[UTC - 12] Baker Island Time",
	"-11" => "[UTC - 11] Niue Time, Samoa Standard Time",
	"-10" => "[UTC - 10] Hawaii-Aleutian Standard Time, Cook Island Time",
	"-9.5" => "[UTC - 9:30] Marquesas Islands Time",
	"-9" => "[UTC - 9] Alaska Standard Time, Gambier Island Time",
	"-8" => "[UTC - 8] Pacific Standard Time",
	"-7" => "[UTC - 7] Mountain Standard Time",
	"-6" => "[UTC - 6] Central Standard Time",
	"-5" => "[UTC - 5] Eastern Standard Time",
	"-4.5" => "[UTC - 4:30] Venezuelan Standard Time",
	"-4" => "[UTC - 4] Atlantic Standard Time",
	"-3.5" => "[UTC - 3:30] Newfoundland Standard Time",
	"-3" => "[UTC - 3] Amazon Standard Time, Central Greenland Time",
	"-2" => "[UTC - 2] Fernando de Noronha Time, South Georgia &amp; the South Sandwich Islands Time",
	"-1" => "[UTC - 1] Azores Standard Time, Cape Verde Time, Eastern Greenland Time",
	"0" => "[UTC] Western European Time, Greenwich Mean Time",
	"1" => "[UTC + 1] Central European Time, West African Time",
	"2" => "[UTC + 2] Eastern European Time, Central African Time",
	"3" => "[UTC + 3] Moscow Standard Time, Eastern African Time",
	"3.5" => "[UTC + 3:30] Iran Standard Time",
	"4" => "[UTC + 4] Gulf Standard Time, Samara Standard Time",
	"4.5" => "[UTC + 4:30] Afghanistan Time",
	"5" => "[UTC + 5] Pakistan Standard Time, Yekaterinburg Standard Time",
	"5.5" => "[UTC + 5:30] Indian Standard Time, Sri Lanka Time",
	"5.75" => "[UTC + 5:45] Nepal Time",
	"6" => "[UTC + 6] Bangladesh Time, Bhutan Time, Novosibirsk Standard Time",
	"6.5" => "[UTC + 6:30] Cocos Islands Time, Myanmar Time",
	"7" => "[UTC + 7] Indochina Time, Krasnoyarsk Standard Time",
	"8" => "[UTC + 8] Chinese Standard Time, Australian Western Standard Time, Irkutsk Standard Time",
	"8.75" => "[UTC + 8:45] Southeastern Western Australia Standard Time",
	"9" => "[UTC + 9] Japan Standard Time, Korea Standard Time, Chita Standard Time",
	"9.5" => "[UTC + 9:30] Australian Central Standard Time",
	"10" => "[UTC + 10] Australian Eastern Standard Time, Vladivostok Standard Time",
	"10.5" => "[UTC + 10:30] Lord Howe Standard Time",
	"11" => "[UTC + 11] Solomon Island Time, Magadan Standard Time",
	"11.5" => "[UTC + 11:30] Norfolk Island Time",
	"12" => "[UTC + 12] New Zealand Time, Fiji Time, Kamchatka Standard Time",
	"12.75" => "[UTC + 12:45] Chatham Islands Time",
	"13" => "[UTC + 13] Tonga Time, Phoenix Islands Time",
	"14" => "[UTC + 14] Line Island Time"
);
?>