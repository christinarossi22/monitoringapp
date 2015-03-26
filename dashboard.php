<?php

session_start();
if (!isset($_SESSION['user'])){
    header("location:index.php");
}

# use config.php to open a database connection ( see API.php for reference )
include_once 'config.php';

#  database connection
try {
    $DBH = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser, $dbpass);
}
catch(PDOException $e) {
    die( var_dump( $e->getMessage() ) );
}

# query the database for the ten most recent message entries, ordered by timestamp
$sql= 'SELECT * FROM LogEntry ORDER BY DateCreated DESC LIMIT 10';

$STH = $DBH->query($sql);

$recentMessages = [];
    
while( $row = $STH->fetch() ) {
    $item = [];
    $item['Message'] = $row['Message'];
    $item['Date'] = $row['DateCreated'];
    $item['Source'] = $row['Source'];
    $recentMessages[] = $item;
}    

# query the database for the top three most common message entries, ordered by number of entries

$sql = <<<QUERY
SELECT 
Message, 
COUNT(*) as 'messages_count' 
FROM LogEntry 
GROUP BY Message 
ORDER BY messages_count DESC 
LIMIT 3
QUERY;

$STH = $DBH->query( $sql );

$topMessages = [];
    
while( $row = $STH->fetch() ) {
    $item = [];
    $item['Message'] = $row['Message'];
    $item['Count'] = $row['messages_count'];
    $topMessages[] = $item;
}    

# display the results as tables on a page

?>
<a href="logout.php">Log Out</a>
<h2>Recent Messages</h2>

<table>
    <thead>
        <tr><th>Message</th><th>Source</th><th>Date</th></tr>
    </thead>
    <tbody>
<?php

foreach( $recentMessages as $message ) {
    echo '<tr><td>' . $message[ 'Message' ] . '</td><td>' . $message[ 'Source' ] . '</td><td>' . $message[ 'Date' ] . "</td></tr>\r\n";
}

?>
    </tbody>
</table>

<h2>Top Messages</h2>

<table>
    <thead>
        <tr><th>Message</th><th>Count</th></tr>
    </thead>
    <tbody>
<?php

foreach( $topMessages as $message ) {
     echo '<tr><td>' . $message[ 'Message' ] . '</td><td>' . $message[ 'Count' ] . "</td></tr>\r\n";
}

?>
    </tbody>
</table




// ajax to constantly update


