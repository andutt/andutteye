<?php

require_once ("func.php");

page_header();

echo '
<body>
';

echo '
<pre>
<a href="http://www.thundera.se" onclick="return true;" class="Tips2" title="Andutteye. Andutteye is created by Andreas Utterberg (andutt) and maintained by Thundera AB. Copyright 2004-2008 All rights Reserved. Andutteye is released under GPL. Andutteye is a registered trademark of Thundera AB."><img src="themes/Phoenix/info.png" alt="" title="" /> Andutteye Controlcenter.</a>
</pre>

<div class="login">
	<center>
 		<h1>Andutt<em>eye</em> Controlcenter</h1>
		<br>
		<form method="post" action="verify.php">
 			<div>
   			<input tabindex="1" type="text" name="andutteye_username" id="andutteye_username" size="35" maxlength="40" value="Username" />
			<br />
   			<input tabindex="2" type="password" name="andutteye_password" id="andutteye_password" size="35" maxlength="40" value="Passowrd" />
  			</div>
			<br />
			<input tabindex="3" style="cursor:pointer;" class="Tips2" type="submit" alt="Click Button to Submit Form" value="Login" title="Click to access Andutteye Controlcenter" />
		</form>
	</center>
</div>
';

echo '
</body>
</html>
';

?>
