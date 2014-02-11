<?php
/**
 * Mageplace Flash Magazine
 *
 * @category	Mageplace
 * @package		Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license	 	http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Model_File_Uploader extends Varien_File_Uploader
{
	protected $_uploadFunction = 'copy';

	public function setFilesUploadMode($upload_function = 'copy')
	{
		$this->_uploadFunction = $upload_function;
	}

	/**
	 * Used to save uploaded file into destination folder with
	 * original or new file name (if specified)
	 *
	 * @param string $destinationFolder
	 * @param string $newFileName
	 * @access public
	 * @return void|bool
	 */
	public function save($destinationFolder, $newFileName=null)
	{
		$this->_validateFile();

		if( $this->_allowCreateFolders ) {
			$this->_createDestinationFolder($destinationFolder);
		}

		if( !is_writable($destinationFolder) ) {
			throw new Exception('Destination folder is not writable or does not exists.');
		}

		$result = false;

		$destFile = $destinationFolder;
		$fileName = ( isset($newFileName) ) ? $newFileName : self::getCorrectFileName($this->_file['name']);
		if( $this->_enableFilesDispersion ) {
			$fileName = $this->correctFileNameCase($fileName);
			$this->setAllowCreateFolders(true);
			$this->_dispretionPath = self::getDispretionPath($fileName);
			$destFile.= $this->_dispretionPath;
			$this->_createDestinationFolder($destFile);
		}

		if( $this->_allowRenameFiles ) {
			$fileName = self::getNewFileName(self::_addDirSeparator($destFile).$fileName);
		}

		$destFile = self::_addDirSeparator($destFile) . $fileName;

		$upload_function = $this->_uploadFunction;
		$result = $upload_function($this->_file['tmp_name'], $destFile);

		if( $result ) {
			@chmod($destFile, 0777);
			if ( $this->_enableFilesDispersion ) {
				$fileName = str_replace(DIRECTORY_SEPARATOR, '/', self::_addDirSeparator($this->_dispretionPath)) . $fileName;
			}
			$this->_uploadedFileName = $fileName;
			$this->_uploadedFileDir = $destinationFolder;
			$result = $this->_file;
			$result['path'] = $destinationFolder;
			$result['file'] = $fileName;
			return $result;
		} else {
			return $result;
		}
	}

	private function _createDestinationFolder($destinationFolder)
	{
		if( !$destinationFolder ) {
			return $this;
		}

		if (substr($destinationFolder, -1) == DIRECTORY_SEPARATOR) {
			$destinationFolder = substr($destinationFolder, 0, -1);
		}

		if (!(@is_dir($destinationFolder) || @mkdir($destinationFolder, 0777, true))) {
			throw new Exception("Unable to create directory '{$destinationFolder}'.");
		}
		return $this;

		$destinationFolder = str_replace('/', DIRECTORY_SEPARATOR, $destinationFolder);
		$path = explode(DIRECTORY_SEPARATOR, $destinationFolder);
		$newPath = null;
		$oldPath = null;
		foreach( $path as $key => $directory ) {
			if (trim($directory)=='') {
				continue;
			}
			if (strlen($directory)===2 && $directory{1}===':') {
				$newPath = $directory;
				continue;
			}
			$newPath.= ( $newPath != DIRECTORY_SEPARATOR ) ? DIRECTORY_SEPARATOR . $directory : $directory;
			if( is_dir($newPath) ) {
				$oldPath = $newPath;
				continue;
			} else {
				if( is_writable($oldPath) ) {
					mkdir($newPath, 0777);
				} else {
					throw new Exception("Unable to create directory '{$newPath}'. Access forbidden.");
				}
			}
			$oldPath = $newPath;
		}
		return $this;
	}

	private function _getMimeType()
	{
		return $this->_file['type'];
	}

	private function _setUploadFileId($fileId)
	{
		if (empty($_FILES)) {
			throw new Exception('$_FILES array is empty');
		}

		if (is_array($fileId)) {
			$this->_uploadType = self::MULTIPLE_STYLE;
			$this->_file = $fileId;
		} else {
			preg_match("/^(.*?)\[(.*?)\]$/", $fileId, $file);

			if( count($file) > 0 && (count($file[0]) > 0) && (count($file[1]) > 0) ) {
				array_shift($file);
				$this->_uploadType = self::MULTIPLE_STYLE;

				$fileAttributes = $_FILES[$file[0]];
				$tmp_var = array();

				foreach( $fileAttributes as $attributeName => $attributeValue ) {
					$tmp_var[$attributeName] = $attributeValue[$file[1]];
				}

				$fileAttributes = $tmp_var;
				$this->_file = $fileAttributes;
			} elseif( count($fileId) > 0 && isset($_FILES[$fileId])) {
				$this->_uploadType = self::SINGLE_STYLE;
				$this->_file = $_FILES[$fileId];
			} elseif( $fileId == '' ) {
				throw new Exception('Invalid parameter given. A valid $_FILES[] identifier is expected.');
			}
		}
	}
}
