<html>
<title>DB-Info von Docker</title>
<body>
<div>
    <pre>
    <?php print("DB_HOST = ".$_ENV["DB_HOST"]."\n"); ?>
    <?php print("DB_USER = ".$_ENV["DB_USER"]."\n"); ?>
    <?php print("DB_PASSWORD = ".$_ENV["DB_PASSWORD"]."\n"); ?>
    <?php print("DB_NAME = ".$_ENV["DB_NAME"]."\n"); ?>
    </pre>
</div>
<?php
    try {
        $dbh = new PDO("sqlsrv:Server=".$_ENV["DB_HOST"].";Database=".$_ENV["DB_NAME"],
        $_ENV["DB_USER"], $_ENV["DB_PASSWORD"],
        array());
        print("Successfully connected to database '".$_ENV["DB_NAME"]."'.");
    }
    catch (PDOException $e) {
        print("Error occurred while connecting to database '".$_ENV["DB_NAME"]."'.");
        print_r($e);
    }
    
?>
</body>
</html>