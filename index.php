<? 
	$json_file = file_Get_contents("json/audio-files.json"); 
	$json = json_decode($json_file);

	function array_sort($array, $on, $order=SORT_ASC)
	{

	    $new_array = array();
	    $sortable_array = array();

	    if (count($array) > 0) {
	        foreach ($array as $k => $v) {
	            if (is_array($v)) {
	                foreach ($v as $k2 => $v2) {
	                    if ($k2 == $on) {
	                        $sortable_array[$k] = $v2;
	                    }
	                }
	            } else {
	                $sortable_array[$k] = $v;
	            }
	        }

	        switch ($order) {
	            case SORT_ASC:
	                asort($sortable_array);
	            break;
	            case SORT_DESC:
	                arsort($sortable_array);
	            break;
	        }

	        foreach ($sortable_array as $k => $v) {
	            $new_array[$k] = $array[$k];
	        }
	    }

	    return $new_array;
	}

	$sorted_entries = array_sort($json->entries, 'album', SORT_ASC);
?>




<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>	<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>	<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>Michael Bicycle</title>
		<meta name="description" content="">
		<meta name="author" content="">

		<meta name="viewport" content="width=device-width,initial-scale=1">

		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/smart-grid.min.css">
		<? /*
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/smart-grid-doc.css">
		<link rel="stylesheet" href="css/smart-grid-demo.css">
		*/ ?>
		<link rel="stylesheet/less" type="text/css" href="css/styles.less" />
		<link href='http://fonts.googleapis.com/css?family=Chau+Philomene+One' rel='stylesheet' type='text/css'>


		<script src="js/modernizr.js"></script>
		<script src="js/less-1.3.3.min.js" type="text/javascript"></script>
	</head>

	<body>
		<header role="banner" class="row">
			<div class="container hd">
				<h1>Michael Bicycle</h1>
			</div><!-- end .container -->
		</header><!-- end #hd -->

		<div class="container hd" id="main">
			<div class="row">
				<div class="columns twelve">
<? 
	$not_first_album = true;
	$last_album = '';
	$this_album = '';
	foreach($sorted_entries as $record) {  
		$this_album = $record->album;
		if ($this_album != $last_album) {
			if ($not_first_album) {
				print '</ul>';
			}
			echo '<h2>Album: “'.$this_album.'”</h2>';
			if ($not_first_album) {
				print '<ul class="audio-container">';
			}
		}
		$last_album = $this_album;
		$not_first_album = true;
?>
						<li>
							<audio  preload="none" title="<?=$record->title?> <span><?=$record->duration;?></span>">
								<source  src="audio/mp3/<?=$record->filename?>.mp3" type="audio/mpeg">
								<source src="audio/ogg/<?=$record->filename?>.ogg" type="audio/ogg">
							</audio>
						</li>
<? } ?> 
					</ul>
				</div>
			</div>
			<div class="columns twelve">
				<div id="disqus_thread"></div>
				<script type="text/javascript">
					/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
					var disqus_shortname = 'michaelbicycle'; // required: replace example with your forum shortname

					/* * * DON'T EDIT BELOW THIS LINE * * */
					(function() {
						var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
						dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
						(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
					})();
				</script>
				<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
				<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
    			</div>
		</div><!-- end #main -->

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
		<script type="text/javascript" charset="utf-8">

			$(document).ready(function() {

				$("audio").removeAttr("controls").each(function(i, audioElement) {
					var audio = $(this);
					var that = this; //closure to keep reference to current audio tag
					var src = $("source",this).attr("src");

					// When done playing, remove class
					$(this).bind('ended', function(){
						$(this).next().removeClass("playing");
					}, false);

					// Set up buttons and add pause/play logic
					$(audio).parent().append($('<button>'+audio.attr("title")+'</button>').click(function() {

						var trigger = $(that).next("button");
						$(audio).addClass('clicked');
						$(trigger).addClass('played');

						// Check all the audio files to see if they are currently playing
						$('.audio-container audio').each(function() {

							$(this).next().removeClass("playing");

							// If this is playing...
							if(!this.paused){

								// Pause this playing
								this.pause();

								// This playing one is NOT the one we just clicked so start it over
								if ($(this).hasClass("clicked")) {} else {
									this.currentTime = 0;
								}
							// If this is paused...
							} else {
								// This paused one IS the one we just clicked
								if ($(this).hasClass("clicked")) {
									this.play();
									$(this).next().addClass("playing");
								}
							}
						});
						$(audio).removeClass('clicked');
					}));
				});
			});
		</script>

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-41780314-1', 'cookieword.com');
			ga('send', 'pageview');
		</script>
	</body>
</html>
