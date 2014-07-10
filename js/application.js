
$(document).ready(function() {

	$("audio").removeAttr("controls").each(function(i, audioElement) {
		var audio = $(this);
		var trigger = $(this).next('button');
		var progressBar = $(trigger).find('.progress');
		var that = this; //closure to keep reference to current audio tag
		var src = $("source",this).attr("src");

		// Set up buttons and add pause/play logic
		$(trigger).click(function() {
			
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

			// Progress bar
			var thisAudio = $(this).parents('li').find('audio');
			var thisButton = $(this);
			var thisProgress = $(this).find('.progress');
			var progressPercentage = 0;
			$(thisAudio).on('timeupdate', function() {
			    var progressPercentage = (this.currentTime / this.duration) * 100 + '%';

			    // When done playing, remove class
			    $(thisProgress).css("width", progressPercentage);
			    if (progressPercentage == '100%') {
			    	$(thisButton).removeClass("playing");
			    }
			});
		});
	});
});