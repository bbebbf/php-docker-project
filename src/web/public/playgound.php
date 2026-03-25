<html>
  <head>
    <title>Hallo von PHP Spielwiese</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
  </head>
<body>
<h1>Spielwiese</h1>
<pre>

<?php
   require_once(__DIR__ . '/inc/SelectPages.php');
   require_once(__DIR__ . '/inc/SelectPagesSqlProvider.php');
   require_once(__DIR__ . '/inc/SelectPagesSqlProviderMsSqlServer.php');


   $dbh = new PDO("sqlsrv:Server=".$_ENV["DB_HOST"].";Database=".$_ENV["DB_NAME"],
        $_ENV["DB_USER"], $_ENV["DB_PASSWORD"],
        array());

    $sqlServerSelectPagesSqlProvider = new SelectPages\SelectPagesSqlProviderMsSqlServer();

    $selectPages = new SelectPages\SelectPages(
        $dbh,
        $sqlServerSelectPagesSqlProvider,
        'SELECT * FROM dbo.WP_Nachricht',
        'Nachrichten_ID',
        5
    );
    $result = $selectPages->fetch(1);
    print_r($result);
?>

</pre>
</body>
</html>