<?php
/* Project Babel
 *
 * Author: Xin, Liu (Livid)
 * File: /htdocs/core/AirmailCore.php
 * Usage: Airmail Class
 * Format: 1 tab indent(4 spaces), LF, UTF-8, no-BOM
 *
 * Subversion Keywords:
 *
 * $Id: AirmailCore.php 60 2007-02-05 08:00:48Z livid $
 * $LastChangedDate: 2007-02-05 16:00:48 +0800 (Mon, 05 Feb 2007) $
 * $LastChangedRevision: 60 $
 * $LastChangedBy: livid $
 * $URL: http://svn.cn.v2ex.com/svn/babel/trunk/htdocs/core/AirmailCore.php $
 *
 * Copyright (C) 2006 Livid Liu <v2ex.livid@mac.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

if (@V2EX_BABEL != 1) {
	die('<strong>Project Babel</strong><br /><br />Made by <a href="http://www.v2ex.com/">V2EX</a> | software for internet');
}

/* S Airmail class */

/* type:

 0 => unknown
 1 => sign up
 2 => notify new reply for own topic
 
*/

class Airmail {
	public $body;

	public $headers;
	public $params;

	private $_mbox_unicode;

	public function __construct($receiver, $subject, $body, $db, $type = 0) {
		$this->headers = array();
		require_once(BABEL_PREFIX . '/res/mbox_unicode.php');
		$this->_mbox_unicode = $_mbox_unicode;

		foreach ($this->_mbox_unicode as $mbox) {
			if (preg_match(mbox_to_pattern($mbox), $receiver)) {
				$_flag_unicode = true;
			}
		}

		if (!$_flag_unicode) {
			$this->headers['content-type'] = 'text/plain; charset=gb2312';
			$this->headers['subject'] = '=?GB2312?B?' . base64_encode(mb_convert_encoding($subject, 'GB2312', 'UTF-8')) . '?=';
			$this->body = mb_convert_encoding($body, 'GBK', 'UTF-8');
		} else {
			$this->headers['content-type'] = 'text/plain; charset=utf-8';
			$this->headers['subject'] = '=?UTF-8?B?' . base64_encode($subject) . '?=';
			$this->body = $body;
		}

		$this->headers['from'] = BABEL_AM_FROM;
		$this->headers['to'] = $receiver;
		
		$this->params = array();
		$this->params["sendmail_path"] = '/usr/sbin/sendmail';
	}
	
	public function __destruct() {
	}
	
	public function vxSend() {
		$m =& Mail::factory('sendmail', $this->params);
		$m->send($this->headers['to'], $this->headers, $this->body);
	}
}

/* E Airmail class */
?>
