<?php

// URL to which we should send query.
//$target_url = 'http://46.4.36.142:8080/predator/Micropogonias%20undulatus/listPrey';
$target_url = 'http://local.gomexsi.tamucc.edu/gomexsi/query-test-return.php';



?><!DOCTYPE html>
<html>
	<head>
		<title>Query Test</title>
		
		<link rel="stylesheet" href="/wp-content/themes/rhm-framework/style.css" />
		<style>#content{width: 100%; box-sizing: border-box;}</style>
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="/wp-content/themes/rhm-framework/js/jquery-1.9.1.min.js"><\/script>')</script>
		<script>
			// Wait for the DOM to be ready.
			$(document).ready(function(){
			
				// Test form submit action.
				$('form#test-form').submit(function(e){
				
					// Prevent actual form submission.  We'll do this with AJAX.
					e.preventDefault();
					
					// AJAX settings.
					var settings = {
						url		:	'/gomexsi/query-proxy.php',
						type	:	'POST',
						data	:	{
										species : encodeURIComponent($('input#species').val()),
										queryUrl : encodeURIComponent($(this).attr('action'))
									},
						success	:	function(data, status, jqXHR){
										$('#status').html(satus);
										$('#results').html(data);
									},
						error	:	function(jqXHR, errorType){
										$('#status').html(errorType);
										$('#results').html('');
									}
					}
					
					// Make the request.
					$.ajax(settings);
				});
				
				// Clear results.
				$('a#clear').click(function(e){
					e.preventDefault();
					$('#results').html('');
				});
			});
		</script>
	</head>
	<body>
		<section id="content-wrapper">
			<div class="container">
				<div id="content">
					<div class="container">
						<h1>GoMexSI Query Test Form</h1>
						<form action="<?php echo $target_url; ?>" id="test-form">
							<label>Species Name:
								<input type="text" id="species" />
							</label>
							<input type="submit" value="Query" /> <span id="satus"></span>
						</form>
						<hr style="margin: 2em 0;" />
						<div style="float: right;"><a href="#" id="clear">Clear Results</a></div>
						<h2>Results:</h2>
						<pre id="results" style="min-height: 100px;"></pre>
					</div>
				</div>
			</div>
		</section>
	</body>
</html>