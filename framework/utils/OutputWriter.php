<?php
/// Copyright (c) 2004-2015, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
class Utils_OutputWriter {
    var $type = 'stdout';

    public function __construct() {
        $this->_buffer = null;
    }

    public function openStdout() {
        $this->type = 'stdout';
        ob_start();
        return true;
    }

    public function openGZipStdout() {
        if (!function_exists('ob_gzhandler')) {
            return false;
        }
        $this->type = 'gz.stdout';
        ob_start('ob_gzhandler');
        return true;
    }

    public function openFile($filename) {
        if (!$this->_writer = fopen($filename, 'wb')) {
            return false;
        }
        $this->type = 'file';
        return true;
    }

    public function openGZip($filename) {
        if (!function_exists('gzopen')) {
            return false;
        }
        if (!$this->_writer = gzopen($filename, 'wb')) {
            return false;
        }
        $this->type = 'gz.file';
        return true;
    }

    public function close() {
        switch ($this->type) {
            default:
            case 'stdout':
            case 'gz.stdout':
                ob_end_flush();
                $return = true;
                break;
            case 'file':
                $return = fclose($this->_writer);
                break;
            case 'gz.file':
                $return = gzclose($this->_writer);
                break;
        }
        unset($this->_writer);
        $this->type = 'stdout';
        return $return;
    }

    public function flush() {
        $this->_buffer = null;
        switch ($this->type) {
            default:
            case 'stdout':
            case 'gz.stdout':
                ob_flush();
                break;
            case 'file':
                fflush($this->_writer);
                break;
            case 'gz.file':
                break;
        }
    }

    public function write($data = null) {
        if ($data == null) {
            $data = $this->_buffer;
        }
        switch ($this->type) {
            default:
            case 'stdout':
            case 'gz.stdout':
                echo $data;
                if (ob_get_length() > 10240) {
                    ob_flush();
                }
                return true;
            case 'file':
                return fwrite($this->_writer, $data);
            case 'gz.file':
                return gzwrite($this->_writer, $data);
        }
    }

    public function buffer($data, $autoLineBreak = false) {
        $this->_buffer = $this->_buffer . ($autoLineBreak ? CRLF : '') . $data;
    }

//	public function start($filename = null, $mode = 'wb', $compress = null) {
//		if (!empty($filename)) {
//			if (empty($compress)) {
//				if (!$this->fp = @fopen($filename, $mode))
//					return false;
//			} else if ($compress == 'gzip') {
//				if (!$this->fp = @gzopen($filename, $mode))
//					return false;
//				$this->compress = $compress;
//			} else {
//				return false;
//			}
//			ob_start(array(&$this, "write"));
//			ob_implicit_flush();
//		} else {
//			ob_start();
//		}
//		return true;
//	}
//
//	public function end() {
//		if ($this->fp) {
//			ob_end_clean();
//			if ($this->compress)
//				@gzclose($this->fp);
//			else
//				@fclose($this->fp);
//			$this->fp = $this->compress = null;
//		} else {
//			ob_end_flush();
//		}
//	}
//
//	public function write($string) {
//		if ($this->fp) {
//			if ($this->compress)
//				gzwrite($this->fp, $string);
//			else
//				fwrite($this->fp, $string);
//			return '';
//		} else {
//			return $string;
//		}
//	}
}

?>
