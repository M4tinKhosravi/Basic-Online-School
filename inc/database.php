<?php
function new_db()
{
    try
    {
            $conn = new PDO("mysql:host=". "localhost" .";dbname=". "alborz" .";charset=utf8", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
    }
    catch(PDOException $e)
    {
        die("Database Connection Failed!<br>" . $e->getMessage());
    }
}
?>