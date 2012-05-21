<?php 

if (! defined("_FILE_LIB_")) { define ("_FILE_LIB_", 1);

Function ContentOfUrl ($url) {
	@$file = fopen ($url, "r");
	$step = 1000;
	if ($file != null) {
		while (!feof ($file)) {
			$result .= fread ($file, $step);
		}
		fclose ($file);
	} else { 
		echo "Error while reading $filename...<BR>\n";
	}
	return $result;
}
Function ContentOfFile ($filename) {
	@$file = fopen ($filename, "r");
	if ($file != null) {
		$fsize = filesize ($filename);
		if ($fsize > 0) {
			@$result = fread ($file, $fsize);
		} else {
			$result = '';
		}
		fclose ($file);
	} else { 
		echo "Error while reading $filename...<br/>\n";
	}
	return $result;
}
Function SaveTextIntoFile ($text, $filename) {
	@$file = fopen ($filename, "w");
	if ($file != null) {
		fwrite ($file, $text);
		fclose ($file);
		@chmod ($filename, 0666);  // rw_ rw_ rw_
		return True;
	} else {
		return False;
	}
}
Function AppendTextIntoFile ($text, $filename) {
	@$file = fopen ($filename, "a+");
	if ($file != null) {
		fwrite ($file, $text);
		fclose ($file);
		@chmod ($filename, 0666);
		return True;
	} else {
		return False;
	}
}

Function VersionBackupFileIn ($file, $targetdir, $bak='v.') {
	$content = ContentOfFile ($file);
	if (!file_exists ($targetdir)) {
		mkdir ($targetdir, 0777);
	}

	$index=0;
	$backup_fn = $targetdir.$bak.$index;
	while (file_exists ($backup_fn)) {
		$backup_fn = $targetdir.$bak.$index ;
		$index = $index + 1;
	}
	SaveTextIntoFile ($content, $backup_fn);
	return $backup_fn;
}

Function BackupFile ($file, $bak='.bak') {
	$content = ContentOfFile ($file);
	$index=0;
	$backup_fn = $file.$bak.$index;
	while (file_exists ($backup_fn)) {
		$backup_fn = $file.$bak.$index ;
		$index = $index + 1;
	}
	SaveTextIntoFile ($content, $backup_fn);
	return $backup_fn;
}

Function RemoveFile ($fn) {
	unlink ($fn);
}

Function listOfNodes ($dirname) {
    $result = array ();
    if ($dir = @opendir ($dirname)) {
		while ($node = readdir ($dir)) {
			if ($node != '.' && $node != '..') {
				$fullnodename = $dirname."/".$node;
				$result [$node] = $fullnodename;
			}
		}
    }
    return $result;
}

Function listOfFiles ($dirname) {
    $result = array ();
    if ($dir = @opendir ($dirname)) {
		while ($file = readdir ($dir)) {
			if ($file != '.' && $file != '..') {
				$fullfilename = $dirname."/".$file;
				if (is_file ($fullfilename)) {
					$result [$file] = $fullfilename;
				}
			}
		}
    //} else { echo "ERROR: can not read $dirname<BR>";
	}
    return $result;
}

Function listFilesFilter ($dirname, $filter) {
    $result = array ();
    if ($dir = @opendir ($dirname)) {
		while ($file = readdir ($dir)) {
			if (eregi ($filter, $file) && $file != '.' && $file != '..') {
				$fullfilename = $dirname."/".$file;
				if (is_file ($fullfilename)) {
					$result [$file] = $fullfilename;
				}
			}
		}
    //} else { echo "ERROR: can not read $dirname<BR>";
	}
    return $result;
}

Function listOfDir ($dirname) {
    $result = array ();
    if ($dir = @opendir ($dirname)) {
		while ($file = readdir ($dir)) {
			if ($file != '.' && $file != '..') {
				$fullfilename = $dirname."/".$file;
				if (is_dir ($fullfilename)) {
					$result [$file] = $fullfilename;
				}
			}
		}
    }
    return $result;
}

Function listDirFilter ($dir, $filter) {
	#echo "listDirFilter [$dir] filter[$filter] <BR>\n";
	$dirhdl = @opendir ($dir);
	if ($dirhdl) {
		$result = array ();
		while ($file = readdir ($dirhdl)) {
			if (eregi ($filter, $file)) {
				$result[] = $dir."/".$file;
			}
		}
	}
	return $result;
}

Function listDirOf ($dirname) {
	return listOfDir($dirname);
}
Function listFileOf ($dirname) {
	return listOfFiles($dirname);
}


}
//else { echo "_FILE_LIB_ Already loaded .."; }

?>
