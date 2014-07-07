<?
require_once('../getid3/getid3/getid3.php');

function tagReader($file){
    $id3v23 = array("TIT2","TALB","TPE1","TRCK","TDRC","TLEN","USLT");
    $id3v22 = array("TT2","TAL","TP1","TRK","TYE","TLE","ULT");
    $fsize = filesize($file);
    $fd = fopen($file,"r");
    $tag = fread($fd,$fsize);
    $tmp = "";
    $replace_find = array("&amp;");
	$replace_replace = array("&");
    fclose($fd);
    if (substr($tag,0,3) == "ID3") {
        $result['FileName'] = $file;
        $result['TAG'] = substr($tag,0,3);
        $result['Version'] = hexdec(bin2hex(substr($tag,3,1))).".".hexdec(bin2hex(substr($tag,4,1)));
    }
    if($result['Version'] == "4.0" || $result['Version'] == "3.0"){
        for ($i=0;$i<count($id3v23);$i++){
            if (strpos($tag,$id3v23[$i].chr(0))!= FALSE){
                $pos = strpos($tag, $id3v23[$i].chr(0));
                $len = hexdec(bin2hex(substr($tag,($pos+5),3)));
                // $data = substr($tag, $pos, 9+$len);
                $data = substr($tag, $pos, 10+$len);
                for ($a=0;$a<strlen($data);$a++){
                    $char = substr($data,$a,1);
                    if($char >= " " && $char <= "~") $tmp.=$char;
                }
                if(substr($tmp,0,4) == "TIT2") $result['Title'] = substr($tmp,4);
                if(substr($tmp,0,4) == "TALB") $result['Album'] = substr($tmp,4);
                if(substr($tmp,0,4) == "TPE1") $result['Author'] = substr($tmp,4);
                if(substr($tmp,0,4) == "TRCK") $result['Track'] = substr($tmp,4);
                if(substr($tmp,0,4) == "TDRC") $result['Year'] = substr($tmp,4);
                if(substr($tmp,0,4) == "TLEN") $result['Lenght'] = substr($tmp,4);
                if(substr($tmp,0,4) == "USLT") $result['Lyric'] = str_replace($replace_find,$replace_replace,htmlspecialchars(substr($tmp,7)));
                $tmp = "";
            }
        }
    }
    if($result['Version'] == "2.0"){
        for ($i=0;$i<count($id3v22);$i++){
            if (strpos($tag,$id3v22[$i].chr(0))!= FALSE){
                $pos = strpos($tag, $id3v22[$i].chr(0));
                $len = hexdec(bin2hex(substr($tag,($pos+3),3)));
                $data = substr($tag, $pos, 6+$len);
                for ($a=0;$a<strlen($data);$a++){
                    $char = substr($data,$a,1);
                    if($char >= " " && $char <= "~") $tmp.=$char;
                }
                if(substr($tmp,0,3) == "TT2") $result['Title'] = substr($tmp,3);
                if(substr($tmp,0,3) == "TAL") $result['Album'] = substr($tmp,3);
                if(substr($tmp,0,3) == "TP1") $result['Author'] = substr($tmp,3);
                if(substr($tmp,0,3) == "TRK") $result['Track'] = substr($tmp,3);
                if(substr($tmp,0,3) == "TYE") $result['Year'] = substr($tmp,3);
                if(substr($tmp,0,3) == "TLE") $result['Lenght'] = substr($tmp,3);
                if(substr($tmp,0,3) == "ULT") $result['Lyric'] = str_replace($replace_find,$replace_replace,htmlspecialchars(substr($tmp,6)));
                $tmp = "";
            }
        }
    }
    return $result;
}
?>




<?php
	// Variable
	$file_path = "../audio/mp3";
	$new_file_content = "";

	// Open the object
	$new_file_content =  "{\r\n\t\t";
	$new_file_content .=  '"entries":[';

	// Go through all files and build list
	if ($dir = @opendir($file_path)) {
		while (($file = readdir($dir)) !== false) {
			switch($file) {
				case ".DS_Store";
				break;
				case ".":
				break;
				case "..":
				break;
				default: 

				// variables
		 		$file_name = preg_replace("/\\.[^.\\s]{3,4}$/", "", $file);
		 		$file_path_name_extension = $file_path."/".$file;
		 		$audio_id3_tags = tagReader($file_path_name_extension);
		 		$getID3 = new getID3;
				$audio_id3_tags2 = $getID3->analyze($file_path_name_extension);
				getid3_lib::CopyTagsToComments($audio_id3_tags2);
				
				// Build list
				
				// Unless it's the first one, end the previous row (so we can have a comma in all but the last one)
				if ($cur) {
					$new_file_content .=  '"},';
				} 

				// Build rest of the list
				$new_file_content .=  "\r\n\t\t";

				$new_file_content .=  '{"filename": "';
		 		$new_file_content .=  $file_name;
		 		$new_file_content .=  '",';

		 		$new_file_content .=  '"title":"';
				$new_file_content .=  $audio_id3_tags[Title];
				$new_file_content .=  '",';

				$new_file_content .=  '"duration":"';
				$new_file_content .=  @$audio_id3_tags2['playtime_string'];	

				$cur++;
				break;
			}
	  	}  
		closedir($dir);
	}

	// Close the last row
	$new_file_content .=  '"}';

	// Close the object
	$new_file_content .=  "	\r\n\t ] \r\n }";

	// Print out object
	echo $new_file_content;

?>






					 
<?php

	// PRINT OUT ON PAGE
	/*

	// Variable
	$file_path = "../audio/mp3";

	// Open the object
	print "{\r\n\t";
	print '"entries":[';

	// Count how many entries there are
	$count_files = "1";
	if ($dir = @opendir($file_path)) {
		while (($file = readdir($dir)) !== false) {
			switch($file) {	
				case ".":
				break;
				case "..":
				break;
				default: 
				$count_files++;
				break;
			}
	  	}  
		closedir($dir);
	}

	// Go through all files and build list
	if ($dir = @opendir($file_path)) {
		while (($file = readdir($dir)) !== false) {
			switch($file) {	
				case ".":
				break;
				case "..":
				break;
				default: 

				// variables
		 		$file_name = preg_replace("/\\.[^.\\s]{3,4}$/", "", $file);
		 		$file_path_name_extension = $file_path."/".$file;
		 		$audio_id3_tags = tagReader($file_path_name_extension);
		 		$getID3 = new getID3;
				$audio_id3_tags2 = $getID3->analyze($file_path_name_extension);
				getid3_lib::CopyTagsToComments($audio_id3_tags2);
				
				// Build list
				
				// Unless it's the first one, end the previous row (so we can have a comma in all but the last one)
				if ($cur) {
					print '"},';
				} 
				print "\r\n\t\t";

				echo '{"filename": "';
		 		echo $file_name;
		 		print '",';

		 		print '"title":"';
				echo $audio_id3_tags[Title];
				print '",';

				print '"duration":"';
				echo @$audio_id3_tags2['playtime_string'];	

				$cur++;
				break;
			}
	  	}  
		closedir($dir);
	}

	// Close the last row
	print '"}';

	// Close the object
	print "	\r\n\t ] \r\n }";

	*/

?>


<?php
	// Write json object to file
	$file = '../json/audio-files.json';

	// Open the file to get existing content
	// $current = file_get_contents($file);
	// Append a new person to the file
	// $new_file_content = "Testy";
	// Write the contents back to the file
	file_put_contents($file, $new_file_content);
?>


