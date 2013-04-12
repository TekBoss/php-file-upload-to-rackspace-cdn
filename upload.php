<!DOCTYPE html>
<html>
<head>
<title>Upload File(s) to Rackspace Cloud</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<style>
form {font-size:12px; color:navy; font-family:verdana; }
</style>
</head>
<body>

<br>

<div align="center">
<form action="uploader.php" enctype="multipart/form-data" method="POST">
   	<!--
    Username: <input name="username" type="text" value="" /><br />
    Key: <input name="key" type="password" value="" /><br />
    //-->
    Container Name: <input name="container_name" type="text" value="" /><br />
    <br>
    File: <input name="upload" type="file" /><br />
    <br>
    <input name="submit" type="submit" value="Upload To Rackspace!" />
</form>
</div>

<br>

</body>
</html>