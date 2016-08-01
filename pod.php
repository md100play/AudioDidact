<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
?>
<html>
	<head>
		<title>YouTube to Podcast</title>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	</head>

	<body>
		<div class="container-fluid">
			<div class="col-sm-12">
				<h1>YouTube to Podcast</h1>
				<hr/>
				<div class="input-group" style="margin-bottom:20px; width:100%;">
					<input type="text" id="yt" class="form-control" />
				</div>
				<div class="progress">
					<div class="progress-bar progress-total" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
				<div class="progress">
					<div class="progress-bar progress-stage" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
				<h2><button onclick="ajax_stream();" class="btn-info btn">Add Video To Feed</button></h2>
				<script>
				$(function() {
					$('.input-group').each(function() {
						$(this).find('input').keypress(function(e) {
							if(e.which == 10 || e.which == 13) {
								ajax_stream();
							}
						});
					});
				});
				function ajax_stream(){
					if(!$("#yt").val()){
						return;
					}
					if (!window.XMLHttpRequest){
						alert("Your browser does not support the native XMLHttpRequest object.");
						return;
					}
					try{
						$('.progress-total').removeClass("progress-bar-success").removeClass("progress-bar-danger").addClass("progress-bar-striped").addClass("active");
						$('.progress-stage').removeClass("progress-bar-success").removeClass("progress-bar-danger").addClass("progress-bar-striped").addClass("active");
						var stages = {0:"Downloading", 1:"Converting to MP3"};
						var numberOfStages = Object.keys(stages).length;

						var xhr = new XMLHttpRequest();
						var error = null;
						xhr.previous_text = '';
						xhr.onerror = function() { alert("[XHR] Fatal Error."); };
						xhr.onreadystatechange = function() {
							try{
								if(error !== null){
									$('.progress-total').css('width', 100+'%').attr('aria-valuenow', 100).text("Error").removeClass("active").removeClass("progress-bar-striped").addClass("progress-bar-danger");
									$('.progress-stage').css('width', 100+'%').attr('aria-valuenow', 100).text("Error").removeClass("active").removeClass("progress-bar-striped").addClass("progress-bar-danger");
								}
								else if(xhr.readyState == 4){
									$('.progress-total').css('width', 100+'%').attr('aria-valuenow', 100).text("Completed").removeClass("active").removeClass("progress-bar-striped").addClass("progress-bar-success");
									$('.progress-stage').css('width', 100+'%').attr('aria-valuenow', 100).text("Completed").removeClass("active").removeClass("progress-bar-striped").addClass("progress-bar-success");
								}
								else if (xhr.readyState == 3){
									var new_response = xhr.responseText.substring(xhr.previous_text.length);
									new_response = new_response.substring(new_response.lastIndexOf('{'), new_response.lastIndexOf('}')+1);
									var result = JSON.parse(new_response);

									if(result.stage == -1){
										error = result.error;
										alert(result.error);
									}

									var totalProg = ((100/(numberOfStages))*result.stage) + (result.progress/numberOfStages);
									totalProg = Math.round(totalProg);
									result.progress = Math.round(result.progress);
									$('.progress-total').css('width', totalProg+'%').attr('aria-valuenow', totalProg).text(stages[result.stage]+" "+totalProg+"%");
									$('.progress-stage').css('width', result.progress+'%').attr('aria-valuenow', result.progress).text(stages[result.stage]+" "+result.progress+"%");

									xhr.previous_text = xhr.responseText;
								}
							}
							catch (e){
							}
						};
						xhr.open("GET", "yt.php?yt="+$("#yt").val(), true);
						xhr.send();
					}
					catch (e){
					}
				}
				</script>
			</div>
		</div>
	</body>
</html>