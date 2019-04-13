<?php

namespace App\Helper;


class FileUtility {
	/**
	 * This functions check recursive permissions and recursive existence parent folders,
	 * before creating a folder. To avoid the generation of errors/warnings.
	 *
	 * @return bool
	 *     true folder has been created or exist and writable.
	 *     False folder not exist and cannot be created.
	 */
	public static function createWritableFolder($folder) {
		if (file_exists($folder)) {
			// Folder exist.
			return is_writable($folder);
		}
		// Folder not exit, check parent folder.
		$folderParent = dirname($folder);
		if ($folderParent != '.' && $folderParent != '/') {
			if (!static::createWritableFolder(dirname($folder))) {
				// Failed to create folder parent.
				return false;
			}
			// Folder parent created.
		}

		if (is_writable($folderParent)) {
			// Folder parent is writable.
			if (mkdir($folder, 0777, true)) {
				// Folder created.
				return true;
			}
			// Failed to create folder.
		}
		// Folder parent is not writable.
		return false;
	}

	/**
	 * This functions check recursive permissions and recursive existence parent folders,
	 * before creating a file/folder. To avoid the generation of errors/warnings.
	 *
	 * @return bool
	 *     true has been created or file exist and writable.
	 *     False file not exist and cannot be created.
	 */
	public static function createWritableFile($file) {
		// Check if conf file exist.
		if (file_exists($file)) {
			// check if conf file is writable.
			return is_writable($file);
		}

		// Check if conf folder exist and try to create conf file.
		if (static::createWritableFolder(dirname($file)) && ($handle = fopen($file, 'a'))) {
			fclose($handle);
			return true; // File conf created.
		}
		// Inaccessible conf file.
		return false;
	}
}