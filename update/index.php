<?
$project_name = "inspirational-streetwise-magazine-vendor";

// Get ID 3 plugin does it's magic
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
	// BUILD A JSON OBJECT FROM THE GET ID3 ARRAY

	// Variable
	$file_path = "../audio/mp3";
	$json_timestamp_current = date('Y-m-d_G-i-s');
	$json_timestamp_current_display = date('M j, Y G:i:s');

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
					$new_file_content .=  '"';
					$new_file_content .=  "\r\n\t\t";
					$new_file_content .=  '},';
				}

				// Build rest of the list
				$new_file_content .=  "\r\n\t\t";
				$new_file_content .=  '{';

				$new_file_content .=  "\r\n\t\t\t\t";
				$new_file_content .=  '"album":"';
				$new_file_content .=  @$audio_id3_tags2['comments']['album'][0];
				$new_file_content .=  '",';

				$new_file_content .=  "\r\n\t\t\t\t";
				$new_file_content .=  '"filename":"';
		 		$new_file_content .=  $file_name;
		 		$new_file_content .=  '",';

		 		$new_file_content .=  "\r\n\t\t\t\t";
		 		$new_file_content .=  '"title":"';
				//$new_file_content .=  $audio_id3_tags[Title];
				$new_file_content .=  @$audio_id3_tags2['comments']['title'][0];
				$new_file_content .=  '",';

				$new_file_content .=  "\r\n\t\t\t\t";
				$new_file_content .=  '"date":"';
				$new_file_content .=  @$audio_id3_tags2['comments']['comment'][0];
				$new_file_content .=  '",';

				

				$replace_find = array("&amp;","&#13;","&#10;");
				$replace_replace = array("&","<br>","<br>");

				$new_file_content .=  "\r\n\t\t\t\t";
				$new_file_content .=  '"lyric":"';
				//$new_file_content .=  @$audio_id3_tags[Lyric];
				$new_file_content .= str_replace($replace_find,$replace_replace,htmlentities(@$audio_id3_tags2['comments_html']['unsynchronised_lyric'][0]));
				$new_file_content .=  '",';

				$new_file_content .=  "\r\n\t\t\t\t";
				$new_file_content .=  '"duration":"';
				$new_file_content .=  @$audio_id3_tags2['playtime_string'];

				$cur++;
				break;
			}
	  	}
		closedir($dir);
	}

	// Close the last AUDIO row
	$new_file_content .=  '"';
	$new_file_content .=  "\r\n\t\t";
	$new_file_content .=  '}';





	// Close the AUDIO ENTRIES row
	$new_file_content .=  "	\r\n\t ], \r\n\t";


	// Add a timestamp row
	$new_file_content .=  '"timestamp":"';
	$new_file_content .=  $json_timestamp_current;
	$new_file_content .=  '"';
	$new_file_content .=  "	\r\n\t ";

	// Close the object
	$new_file_content .=  "\r\n }";

	$replace_find2 =    array("<br>"       ,'"','	</b>"'  ,'"<b>:</b>"','</b>"<b>','	"<b>');
	$replace_replace2 = array("<br>\t\t\t\t ",'</b>"<b>','	"' ,'":"'      ,'</b>"','"<b class="term">');

?>

<?php
	// Write json object to file



	$file = '../json/audio-files.json';

	// Get timestamp from last JSON version to make a unique file name backup json file
	$json_file = file_Get_contents("../json/audio-files.json");
	$json = json_decode($json_file);
	$json_timestamp_last = $json->{'timestamp'};

	// First make a backup of the current json file
	$newfile = '../json/backups/audio-files-'.$json_timestamp_last.'.json';
	if (!copy($file, $newfile)) {
	    echo "failed to copy $file...\n";
	}

	// Open the file to get existing content
	// $current = file_get_contents($file);
	// Append a new person to the file
	// $new_file_content = "Testy";
	// Write the contents back to the file
	file_put_contents($file, $new_file_content);
?>

<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>	<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>	<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>JSON</title>
	<meta name="viewport" content="width=device-width,initial-scale=1">
</head>



<body class="p-<?=$page_name?>">
	<div class="row">
		<header role="banner">
			<div class="container hd clearfix">
				<h1>Build JSON Object for Michael Bicycle</h1>
			</div><!-- end .container -->

		</header><!-- end #hd -->
	</div>
	<div class="container hd" class="main">
		<section>
			<div class="row">
				<div class="columns twelve">
					<h2>JSON Object</h2>
					<p>Latest build: <em><?=$json_timestamp_current_display?></em>. <button class="update-json-cta">Update</button></p>
					<div class="code-output">
						<pre>
<? echo str_replace($replace_find2,$replace_replace2,$new_file_content); ?>
						</pre>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="columns twelve">
					<h2>ID3 Tag Array</h2>
					<div class="code-output">
						<pre>
<? print_r(@$audio_id3_tags2); ?>
						</pre>
					</div>
				</div>
			</div>
		</section>
	</div><!-- /. main -->
	<script>
	$(".update-json-cta").click(function(){
		location.reload();
	});

	</script>
</body>
</html>

