<?php


#configuration

include_once 'config.php';

#  database connection
try {
    $DBH = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser, $dbpass);
}
catch(PDOException $e) {
    die( var_dump( $e->getMessage() ) );
}


# creating a veriable to store the input using  a filter

$input = filter_input( INPUT_POST, 'data', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );
$json = json_decode( $input );


if ( !$json ) {
    header('HTTP/1.0 400 The request cannot be fulfilled due to bad syntax.');
    echo( $input );
    exit;
}

# Check to make sure that the message matches the format


if ( !$json->message || !$json->type ) {
    header('HTTP/1.0 400 The request cannot be fulfilled due to bad syntax.');
    echo( $input );
    exit;
}

# set  variable for the sql statement 
#insert into the database

$sql = 'insert into LogEntry (Message, Source) value (:message, :source)';

$data = [
    'message' => $json->message,
    'source' => $json->type,
];

try {
    $stmt = $DBH->prepare($sql);
    $stmt->execute($data);
} catch( PDOException $e ) {
    header('HTTP/1.0 500 “The server encountered an unexpected condition which prevented it from fulfilling the request.');
    exit;
}

# run the SQL statement and if it through all the code
header('HTTP/1.0 200 The request was successful.');
echo 'success';