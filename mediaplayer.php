<?php
	if(isset($_POST['mediaUpload'])) {
		if(empty($_FILES['media']['name'])) { 
			echo "<div class='errorMessage fadeIn'>Please select file to upload!</div>";
		}
		else {
			$filename = htmlentities(stripslashes($_FILES['media']['name']));
			$filename = preg_replace('/(#_-)/', '', $filename);
			// $filename = str_replace('#', '', $filename);
			$file_info = explode('.', $filename);
			$file_ext = strtolower(end($file_info));
			switch($file_ext) {
				case 'mp3':
					//Upload file path
					$upload_filepath = "audiotracks/".$filename;
					if(is_uploaded_file($_FILES['media']['tmp_name'])) {
						echo "<div class='errorMessage success fadeIn'>Track uploaded successfully...!</div>";
					}
					#process the file upload here...
					if(!move_uploaded_file($_FILES['media']['tmp_name'], $upload_filepath)) {
						echo "<div class='errorMessage fadeIn'>Sorry could not upload file to the specified directory!</div>";
					}
				break;
				default:
					echo "<div class='errorMessage fadeIn'>Invalid file type, only (.mp3) file format accepted!</div>";
					unlink($_FILES['media']['tmp_name']); //then remove temp file if failed to upload file
			}
		}
	}

	if(isset($_REQUEST['action']) && $_REQUEST['action'] == "remove") {
		$item = htmlentities($_REQUEST['item']);
		$path = "audiotracks/";
		$files = scandir($path);
		foreach($files as $object) {
			if($object==$item) {
				unlink($path.$item);
			}
		}
		header("Location: ".$_SERVER['PHP_SELF']);
	}

	function scanTracks() {
		$path = "audiotracks/";
		$files = scandir($path);
		$mediadata = array();
		for($i = 0; $i < count($files); $i++) {
			$file_info = explode('.', $files[$i]);
			$ext = end($file_info);
			$ext = strtolower($ext);
			if($ext=="mp3") {
			  $mediadata[] = $files[$i];
			}
		}
		// header('Content-Type: application/json');
		return json_encode($mediadata);
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Media Player</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="boostrap3/font-awesome/css/font-awesome.min.css">
	<style type="text/css">
		body {
			font-family: 'helvetica', arial;
			overflow: hidden;
		}

		.errorMessage {
			position: absolute;
			width: 400px;
			max-width: 100%;
			background-color: tomato;
			border-radius: 5px;
			color: #fff;
			padding: 15px 10px;
			text-align: center;
			z-index: 2;
			font-weight: bold;
		}
		.errorMessage.success {
			background-color: #00BD9B;
		}
		.container {
			position: absolute;
			width: 600px;
			max-width: 100%;
			margin: 20px auto 0 450px;
			background-color: #555555;
			padding: 0 5px;
			border-radius: 5px;
			box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.5);
			z-index: 1;
		}
		.header {
			/*background-color: #666;*/
			color: tomato;
			text-align: center;
			border-bottom: thin solid #999;
			font-size: 1.8em;
			margin-bottom: 10px;
			padding: 5px;
		}
		/* media player track seek/duration */
		.seeker {
			width: 100%;
			-webkit-appearance: none;
			outline: 0 none;
		}
		.seeker::-webkit-slider-runnable-track {
			height: 6px;
			background: skyblue;
			border: 0px solid #000101;
			/*border-radius: 5px;*/
		}
		.seeker::-webkit-slider-thumb {
			width: 15px;
			height: 15px;
			background: tomato;
			-webkit-appearance: none;
			border-radius: 50%;
			cursor: pointer;
			margin-top: -3.6px;
			filter: drop-shadow(1px 1px 5px skyblue);
		}
		.seeker:hover::-webkit-slider-thumb {
			background: #fff;
			filter: drop-shadow(1px 1px 5px #fff);
		}
		.seeker:active::-webkit-slider-thumb {
			background: #00BD9B;
		}
		.table-seek {
			width: 100%;
			max-width: 100%;
		}
		.table-seek td {
			vertical-align: bottom;
			color: tomato;
			text-align: center;
		}

		.track-name {
			color: skyblue;
		}
		/*media controls*/
		.player-controls {
			/*background-color: skyblue;*/
			padding: 10px;
			border-radius: 5px;
			text-align: center;
		}
		.controls {
			/*background-color: tomato;*/
			color: tomato;
			border: 2px solid tomato;
			border-radius: 50%;
			padding: 8px 10px;
			display: inline-block;
			list-style-type: none;
			font-size: 1.5em;
			margin-right: 15px;
			vertical-align: middle;
			text-align: center;
			/*filter: drop-shadow(1px 1px 2px tomato);*/
			box-shadow: inset 0 1px 4px rgba(0, 0, 0, .075), 0 0 6px tomato;/*#ce8483;*/
		}
		.controls:nth-child(3) {
			padding: 15px 20px;
		}
		.controls:hover {
			cursor: pointer;
			color: #fff;
			border-color: #fff;
			/*filter: drop-shadow(1px 1px 4px #fff);*/
		}
		.controls:active {
			color: skyblue;
			border-color: skyblue;
			box-shadow: inset 0 1px 4px rgba(0, 0, 0, .075), 0 0 6px skyblue;
		}
		/*Volume controls*/
		.volume-controls {
			background-color: #777;
			padding: 8px;
			border-radius: 0 0 5px 5px;
			margin: 0 -5px 0 -5px;
		}
		.volume-icon {
			color: tomato;
			font-size: 1.8em;
			cursor: pointer;
		}
		.volume-icon:hover {
			color: #fff;
			filter: drop-shadow(1px 1px 5px skyblue);
		}
		.volume-toggle {
			position: relative;
			top: -3px;
			width: 100%;
			-webkit-appearance: none;
			outline: 0 none;
		}
		.volume-toggle::-webkit-slider-runnable-track {
			height: 3px;
			background: skyblue;
			border: 0px solid #000101;
			border-radius: 5px;
		}
		.volume-toggle::-webkit-slider-thumb {
			width: 12px;
			height: 12px;
			background: tomato;
			-webkit-appearance: none;
			border-radius: 50%;
			cursor: pointer;
			margin-top: -3.6px;
			filter: drop-shadow(1px 1px 5px skyblue);
		}
		.volume-toggle:hover::-webkit-slider-thumb {
			background: #fff;
			filter: drop-shadow(1px 1px 5px #fff);
		}
		.volume-toggle:active::-webkit-slider-thumb {
			background: #00BD9B;
		}
		/* Menu track list style*/
		.list-container {
			position: absolute;
			top: 10px;
			width: 350px;
			max-height: 400px;
			overflow-y: auto;
			background-color: #555;
			color: #fff;
			max-width: 100%;
			margin: 0 auto;
			padding: 2px 0;
			border-radius: 2px;
			box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.5);
		}
		.list-container::-webkit-scrollbar {
	      width: 8px;
	    }
	    .list-container::-webkit-scrollbar-track {
	      box-shadow: inset;
	      background-color: #555;
	    }
	    .list-container::-webkit-scrollbar-thumb {
	      background: -webkit-linear-gradient(360deg, tomato 30%, brown); /*#5bc0de rgba(0, 0, 0, 0.8);*/ /*darkgrey;*/
	      outline: 1px solid slategrey;
	      border-radius: 10px;
	    }
		.list-item {
			background-color: #777;
			margin-bottom: 3px;
			padding: 15px 5px;
			list-style-type: none;
		}
		.list-item:hover {
			background-color: #00BD9B;
			color: #fff;
			cursor: pointer;
		}
		.active {
			background-color: tomato !important;
		}

		/* pload form adding new track to playlist*/
		#addNew {
			float: right;
		}
		#addNew:hover {
			color: #00BD9B;
			cursor: pointer;
			filter: drop-shadow(1px 1px 5px #00BD9B);
		}
		#addNew:active {
			color: #fff;
		}
		.upload-form {
			position: absolute;
			width: 400px;
			max-width: 100%;
			margin: 0 auto;
			background: -webkit-linear-gradient(#777, #555 80%);
			border: thin solid tomato;
			border-radius: 5px;
			padding: 5px;
			box-shadow: 1px 1px 4px skyblue; /*rgba(0, 0, 0, 0.5);*/
			z-index: 2;
			transform: translate(550px , 20px);
			visibility: hidden;
		}
		#close {
			float: right;
			color: tomato;
			cursor: pointer;
		}
		#close:hover {
			color: #fff;
		}
		#close:active {
			color: #dd0000;
		}
		.form-header {
			font-size: 1.3em;
			font-weight: bold;
			color: tomato;
			padding: 5px 0;
			text-align: center;
			margin-bottom: 15px;
			border-bottom: thin solid #999;
		}
		.form-control {
			width: 96%;
			padding: 5px;
			border: thin solid tomato;
			border-radius: 5px;
			color: #fff;
			margin-bottom: 15px;
		}
		.btn-upload {
			width: 100%;
			padding: 10px 5px;
			border-radius: 5px;
			border: thin solid tomato;
			background-color: tomato;
			color: #fff;
			font-weight: bold;
			margin-bottom: 15px;
		}
		.btn-upload:hover {
			background-color: transparent;
			color: tomato;
			cursor: pointer; 
		}
		.btn-upload:focus,active {
			background-color: #00BD9B;
			color: #fff;
			border-color: #00BD9B;
			outline: 0 none;
		}
		.fadeIn { /*add class to animate fadeIn of an object*/
			animation: fadeIn .30s ease-in;
		}
		@keyframes fadeIn { /*controls animation of fading object*/
			0%{ opacity: 0; }
			100%{ opacity: 1; }
		}
		.bubbles {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: 0;
		}
		.bubble div {
			position: absolute;
			/*list-style-type: none;*/
			/*width: 20px;*/
			/*height: 20px;*/
			/*background: rgb(0, 0, 0, 0.5);*/
			color: tomato;
			/*animation: animate 25s linear infinite;*/
			top: -160px;
			filter: drop-shadow(1px 1px 5px rgba(0, 0, 0, 0.5));
		}
		.bubble div:nth-child(1) {
			left: 25%;
			font-size: 80px;
			color: skyblue;
			width: 80px;
			/*height: 80px;*/
			animation-delay: 0;
		}
		.bubble div:nth-child(2) {
			left: 10%;
			font-size: 20px;
			color: tomato;
			width: 20px;
			/*height: 20px;*/
			animation-delay: 2s;
			animation-duration: 12s;
		}
		.bubble div:nth-child(3) {
			left: 70%;
			font-size: 20px;
			color: #00BD9B;
			width: 20px;
			/*height: 20px;*/
			animation-delay: 4s;
		}
		.bubble div:nth-child(4) {
			left: 40%;
			font-size: 60px;
			color: tomato;
			width: 60px;
			/*height: 60px;*/
			animation-delay: 0s;
			animation-duration: 18s;
		}
		.bubble div:nth-child(5) {
			left: 65%;
			font-size: 60px;
			color: skyblue;
			width: 60px;
			/*height: 60px;*/
			animation-delay: 0s;
		}
		.bubble div:nth-child(6) {
			left: 75%;
			font-size: 110px;
			color: tomato;
			width: 110px;
			/*height: 110px;*/
			animation-delay: 3s;
		}
		.bubble div:nth-child(7) {
			left: 35%;
			font-size: 150px;
			color: #00BD9B;
			width: 150px;
			/*height: 150px;*/
			animation-delay: 7s;
		}
		.bubble div:nth-child(8) {
			left: 50%;
			font-size: 25px;
			color: #00BD9B;
			width: 25px;
			/*height: 25px;*/
			animation-delay: 15s;
			animation-duration: 45s;
		}
		.bubble div:nth-child(9) {
			left: 20%;
			font-size: 15px;
			color: tomato;
			width: 15px;
			/*height: 15px;*/
			animation-delay: 2s;
			animation-duration: 35s;
		}
		.bubble div:nth-child(10) {
			left: 85%;
			font-size: 150px;
			color: tomato;
			width: 150px;
			/*height: 150px;*/
			animation-delay: 2s;
			animation-duration: 11s;
		}
		@keyframes animate {
			0% {
				transform: translateY(0) rotate(0deg) scale(0.50);
				opacity: 1;
				/*border-radius: 0;*/
			}
			100% {
				transform: translateY(1000px) rotate(720deg) scale(1);
				opacity: 0;
				/*border-radius: 50%;*/
			}
		}
		.remove {
			font-size: 1.2em;
			color: skyblue;
			margin-right: 5px;
			z-index: 3;
		}
		.remove:hover {
			color: #dd0000;
		}
		.remove:active {
			color: skyblue;
		}
	</style>
</head>
<body>
	<div class="bubble">
		<div class="fa fa-snowflake-o"></div>
		<div class="fa fa-snowflake-o"></div>
		<div class="fa fa-snowflake-o"></div>
		<div class="fa fa-snowflake-o"></div>
		<div class="fa fa-snowflake-o"></div>
		<div class="fa fa-snowflake-o"></div>
		<div class="fa fa-snowflake-o"></div>
		<div class="fa fa-snowflake-o"></div>
		<div class="fa fa-snowflake-o"></div>
		<div class="fa fa-snowflake-o"></div>
	</div>

	<div class="upload-form">
		<i class="fa fa-close" id="close"></i>
		<div class="form-header">Upload New <i class="fa fa-music"></i></div>
		<form action="" method="POST" enctype="multipart/form-data">
			<div><input type="file" name="media" class="form-control"></div>
			<div><button type="submit" name="mediaUpload" class="btn-upload" onclick="return confirmAction();"><i class="fa fa-plus"></i> Add Tack</button></div>
		</form>
	</div>
	<!-- Playlist -->
	<div class="list-container">
		<div style="text-align: center;font-weight: bold; font-size:1.5em; padding: 10px;color: tomato;">
			<div style="float: left;color: tomato;cursor: pointer;" class="fa fa-refresh" onclick="pageRefresh();" title="Reload Page"></div>
			<span class="fa fa-list-alt"></span>
			Play List
			<i class="fa fa-plus" id="addNew" title="Add new track to playlist"></i>
		</div>
		<div id="track-list">No Tracks Available</div>
	</div>
	<!-- Music player -->
	<div class="container">
		<div class="header"> <span class="head-text">Start Playing</span></div>
		<div class="player-seek">
			<table border="0" class="table-seek">
				<tr>
					<td colspan="3">
						<div class="track-name">- - - - -</div>
					</td>
				</tr>
				<tr>
					<td width="80px"><span id="timer">--:--</span></td>
					<td>
						<span><input type="range" class="seeker" min="0" value="0" max="1" title="Seek"></span>
						<span id="player"></span>
					</td>
					<td width="80px"><span id="duration">00:00</span></td>
				</tr>
			</table>
		</div>
		
		<div class="player-controls">
			<div>
				<li class="controls" title="Shuffle tracks" id="shuffle"><i class="fa fa-random"></i></li>
				<li class="controls" title="Previous track" id="previous"><i class="fa fa-fast-backward"></i></li>
				<li class="controls" title="Play/Pause track" id="play"><i class="fa fa-play" id="playstatus"></i></li>
				<li class="controls" title="Next track" id="next"><i class="fa fa-fast-forward"></i></li>
				<li class="controls" title="Reload track" id="reload"><i class="fa fa-rotate-left"></i></li>
			</div>
		</div>
		<div class="volume-controls">
			<table border="0">
				<tr>
					<td width="30px"><i class="fa fa-volume-down volume-icon" title="mute volume"></i></td>
					<td><input type="range" class="volume-toggle" min="0" value="0.1" step="0.1" max="1.0" title="Volume Control"></td>
				</tr>
			</table>
			
		</div>
	</div>
	<script type="text/javascript">
		let tracks = <?php echo scanTracks() ?>; //get arry list of tracks
		// console.log(tracks[3]);
		let trackItem = $('#track-list');
		let items = "";

		// var shuffle =  Math.ceil(Math.random() * tracks.length-1);

		for(var i=1; i < tracks.length; i++) {
			// console.log(tracks[i]);
			items += "<li class='list-item' onclick='toggleActive(this,"+i+");'><a href='javascript:void(0)' onclick='removeItem(\""+tracks[i]+"\")'><i class='fa fa-times remove' title='Delete track'></i></a>"+tracks[i].slice(0, -4)+"</li>";
		}
		trackItem.innerHTML = items;

	/*----------------------VolumeControl-------------------------*/
		let mute = $('.volume-icon');
		mute.addEventListener('click', function() {
			var song = document.getElementsByTagName('audio')[0];
			if(hasClass(mute, 'fa-volume-down') || hasClass(mute, 'fa-volume-up')) { //mute.classList.contains('fa-volume-down')
				mute.classList.add('fa-volume-off');
				mute.classList.remove('fa-volume-down');
				mute.classList.remove('fa-volume-up');
				song.volume = 0;
				$('.volume-toggle').value = 0;
				attr(mute,'title', 'muted');
				mute.style.color = '#fff';
			}
			else {
				song.volume = 0.2;
				$('.volume-toggle').value = 0.2;
				mute.classList.add('fa-volume-down');
				mute.classList.remove('fa-volume-off');
				mute.classList.remove('fa-volume-up');
				attr(mute,'title', 'mute volume');
				mute.removeAttribute('style');
			}
		});
		function pageRefresh() {
			location.reload(true);
		}
		//remove item from track list
		function removeItem(obj) {
			var action = confirm('Remove this item?');
			if(action==false) {
				return false;
			} else {
				//reload window passing parameter values to delete item
				window.open('mediaplayer.php?action=remove&item='+obj,'_self');
			}
		}

		//returns query selector object
		function $(id) {
			return document.querySelector(id);
		}
		//check if element has specified class name
		function hasClass(selectorObj, classname) {
			return selectorObj.classList.contains(classname);
		}
		//function to set attributes, accept Object, AttrProperty and AttrValue
		function attr(self, attrProp='', attrValue='') {
			return self.setAttribute(attrProp, attrValue);
		}
		//function toggles active on selection of track on Track List Menu
		function toggleActive(obj, n) {
			var action = confirm('Play this track?');
			if(action == false) {
				return false;
			} else {
				loadTrack(curTrack = n);
				//get all list items
				items = document.getElementsByClassName('list-item');
				// then remove active class if any was set
				for(i in items) {
					//check if has specified class
					if(hasClass(items[i],'active')) {
						items[i].classList.remove('active');
					} else {
						//then set new class to the selected item
						obj.classList.add('active');
					}
				}
			}
		}
		
		//on click play button music controls events
		$('#play').addEventListener('click', function(e) {
			var mediaObj = document.getElementsByTagName('audio')[0];
			PlayPause(mediaObj);
			mediaControls(e);
		});
		//controls the play button state
		function mediaControls(thisEvent) {
			if(hasClass($('#playstatus'), 'fa-play')) {
				$('#playstatus').classList.remove('fa-play');
				$('#playstatus').classList.add('fa-pause');
				setPlayState(thisEvent);
			} else {
				$('#playstatus').classList.add('fa-play');
				$('#playstatus').classList.remove('fa-pause');
				setPlayState(thisEvent);
			}
		}
		//toggle control the play state color
		function setPlayState(obj) {
			if(obj.hasAttribute('style')) {
				obj.removeAttribute('style');
			} else {
				obj.style.color = "skyblue";
				obj.style.borderColor = "skyblue";
			}
		}
		//function to display title with volume percentage
		function displayVolumePercent(event, level) {
			switch(level) {
				case '0':
					attr(event, 'title', 'Volume 0%');
				break;
				case '0.1':
					attr(event, 'title', 'Volume 10%');
				break;
				case '0.2':
					attr(event, 'title', 'Volume 20%');
				break;
				case '0.3':
					attr(event, 'title', 'Volume 30%');
				break;
				case '0.4':
					attr(event, 'title', 'Volume 40%');
				break;
				case '0.5':
					attr(event, 'title', 'Volume 50%');
				break;
				case '0.6':
					attr(event, 'title', 'Volume 60%');
				break;
				case '0.7':
					attr(event, 'title', 'Volume 70%');
				break;
				case '0.8':
					attr(event, 'title', 'Volume 80%');
				break;
				case '0.9':
					attr(event, 'title', 'Volume 90%');
				break;
				case '1':
					attr(event, 'title', 'Volume 100%');
				break;
				default:
					console.log('Invalid value for volume level given!');
				break;
			}

		}
		//initialize variable with first track
		var curTrack = 1;

		function loadTrack(n) {
			
			if(n > tracks.length-1) { //number greater than total tracks, then load first track
				curTrack = 1;
			}
			else if(n < 1) { //else if number less than minimum track value (i.e 0 or -1, -0), then load number of total tracks
				curTrack = tracks.length-1;
			}
			else {
				curTrack = n;
			}
			
			let path = 'audiotracks/';
			$('#player').innerHTML = ""; //clear player, prepare for new audio element
			let song = "";//document.getElementsByTagName('audio')[0];
			song = new Audio(path+tracks[curTrack]); //load track
			song.src = path+tracks[curTrack];
			song.load(); //loads a new song to player
			$('#player').append(song);
			PlayPause(song); //call function to play loaded track
			mediaControls($('#play')); //call function to handle play button state
			$('.head-text').innerHTML = "Now Playing... <i class='fa fa-music'></i>";
			$('.track-name').innerHTML = 'Track '+curTrack+' : '+tracks[curTrack].slice(0, -4);
		}
		

		//function to load Prevous/Next track
		function prevNextTrack(n) {
			loadTrack(curTrack += n); //pass index value of track
	    }
	    function PlayPause(obj='') { //toggle audio play/pause state
			if(obj.paused) {
				obj.play(); //play audio
			} else {
				obj.pause(); //pause audio
			}
			//control volume range slider
	    	$('.volume-toggle').value = obj.volume = 0.1;
	    	mute.classList.add('fa-volume-down');
			mute.classList.remove('fa-volume-off');
			mute.removeAttribute('style');
	    	//Event automatically updates on track play
			obj.addEventListener('timeupdate', function() {
				//calculate total duration of loaded track
	    		var durMin = parseInt((obj.duration / 60) % 60); //all of track Minutes
	    		var durSec = parseInt(obj.duration % 60); //all of track Seconds
				$('#duration').innerHTML = durMin+ ":" +durSec;
				//calculate track current play time
				var s = parseInt(obj.currentTime % 60); //current track time in Seconds
				var m = parseInt((obj.currentTime / 60) % 60); //current track time in Minutes
				$('#timer').innerHTML = m+":"+s; //finally display play time
				//assign attrubute max value of slider with track duration
		    	attr($('.seeker'), 'max', parseInt(obj.duration,10) );
		    	//as track plays update the slider, with current track time
		    	$('.seeker').value = parseInt(obj.currentTime, 10);
			}, false);
		}
	    //event gets previous track
	    $('#previous').addEventListener('click', function() {
	    	prevNextTrack(-1);
	    });
	    //event gets next track
	    $('#next').addEventListener('click', function() {
	    	prevNextTrack(+1);
	    });
	    //event reload current track start from beginning
		$('#reload').addEventListener('click', function() {
			var song = document.getElementsByTagName('audio')[0];
			song.currentTime = 0;
		});

		//on event Volume control slider
		$('.volume-toggle').addEventListener('change', function() {
			var song = document.getElementsByTagName('audio')[0];
			var volSliderValue = $('.volume-toggle').value;
			song.volume = volSliderValue; //assing to volume controller
			if(volSliderValue == 0) { //if volume down set mute icon
				mute.classList.add('fa-volume-off');
				mute.classList.remove('fa-volume-down');
				mute.classList.remove('fa-volume-up');
				attr(mute,'title', 'muted');
				mute.style.color = '#fff';
			}
			else if(volSliderValue == 1) { //maximum volume value
				mute.classList.add('fa-volume-up');
				mute.classList.remove('fa-volume-off');
				mute.classList.remove('fa-volume-down');
				attr(mute,'title', 'mute volume');
				mute.removeAttribute('style');
			}
			else { //medium volume value
				mute.classList.add('fa-volume-down');
				mute.classList.remove('fa-volume-up');
				mute.classList.remove('fa-volume-off');
				attr(mute,'title', 'mute volume');
				mute.removeAttribute('style');
			}

			//call function to display title with volume percentage
			displayVolumePercent(this, volSliderValue);
		});
		//confirm user action on submit upload form
		function confirmAction() {
			let action = confirm('Continue add new track?');
			if(action == false) {
				return false;
			}
		}
		/*$('.btn-upload').addEventListener('click', function() {
			return confirmAction();
		});*/
		//event handler controls closing of upload form
		$('#close').addEventListener('click', function() {
			$('.upload-form').style.visibility = "hidden";
			$('.upload-form').classList.remove('fadeIn');
		});
		//event handler controls opening of upload form
		$('#addNew').addEventListener('click', function() {
			$('.upload-form').style.visibility = "visible";
			$('.upload-form').classList.add('fadeIn');
		});
		//delay time with 10,000 miliseconds
		setTimeout(function() {
			$('.errorMessage').style.visibility = "hidden";
			// location.reload(true);
			window.open('mediaplayer.php','_self');
		}, 10000);

	</script>
</body>
</html>