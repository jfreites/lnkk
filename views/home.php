<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title><?= $title ?></title>
	<link rel="stylesheet"  type="text/css" href="/assets/css/styles.css">
</head>
<body>
	<div class="wrapper">
		<header>
			<p class="header__logo">
				<img src="http://placehold.it/200x90" alt="Logo">
			</p>	
		</header>

		<section>
			<div class="section__form">
				<form id="form1" action="" method="POST">
					<input id="url" class="section__form-input" type="text" placeholder="URL a reducir...">
					<br /><br />
					<button class="section__form-btn">voilà!</button>
				</form>			
			</div>

			<div class="section__shorturl">
				<div class="section__shorturl-placeholder">
					<p id="lnk_url"></p>
				</div>
			</div>
		</section>
			
	</div>

	<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type="text/javascript">
		$('#form1').submit( function(event) {
			event.preventDefault();

			var url = $('#url').val(); // capture input

            var placeholder = $('.section__shorturl-placeholder');
            placeholder.css('visibility', 'hidden');

			if (isUrlValid(url)) {

				// ajax request
				var encoding = $.post('/encode', { long_url : url });

				encoding.done(function(res) {
					//console.log(res);
                    $('#lnk_url').html('<a target="_blank" href="http://lnkk.app/' + res + '">http://lnkk.app/' + res + '</a>');
				});

				var placeholder = $('.section__shorturl-placeholder');
				placeholder.css('visibility', 'visible');

			} else {
				alert('La URL ingresada es invalida!');
			}

			$('#url').val('');		
		});

		function isUrlValid(url) {
		    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
		}
	</script>
</body>
</html>