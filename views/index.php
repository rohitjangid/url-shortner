<html>
	<head>
    <title>Cognizance 2014</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
	
	<link href="css/style.css" rel="stylesheet">
	
  </head>
<body>

<div class="container">


    <div class="starter-template">
        <h1>URL Shortner</h1>
		<br>
        <div class="row">
			<div class="col-md-4 col-md-offset-4">
				<form class="form-horizontal" role="form" action="../scripts/shorturl_request.php" method="POST">
				  <div class="form-group">
					<label for="url" class="col-sm-3 control-label">URL</label>
					<div class="col-sm-9">
						<input type="url" class="form-control" id="url" name="url" placeholder="Url" required>
					</div>
				  </div>
				  <button type="submit" class="btn btn-default">Short It</button>
				</form>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<?php
					session_start();
					if(isset($_SESSION['error']))
					{
						$error=$_SESSION['error'];
						echo "<div class='alert alert-danger'>";
						echo $error;
						echo "</div>";
						unset($_SESSION['error']);
					}
					elseif(isset($_SESSION['shorturl']))
					{
						$shorturl=$_SESSION['shorturl'];
						echo "<div class='alert alert-success'>";
						echo "Short Url: localhost/manash/".$shorturl;
						echo "</div>";
						unset($_SESSION['shorturl']);
					}
				?>
			</div>
		</div>
    </div>

    
</div><!-- /.container -->	

</body>
</html>