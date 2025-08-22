<?php
// Database helper functions for both MySQL and PostgreSQL

// Check if connection is PDO (PostgreSQL) or mysqli (MySQL)
function isPostgreSQL($conn) {
    return $conn instanceof PDO;
}

// Universal query function
function db_query($conn, $sql) {
    if (isPostgreSQL($conn)) {
        // PostgreSQL
        return $conn->query($sql);
    } else {
        // MySQL
        return mysqli_query($conn, $sql);
    }
}

// Universal fetch function
function db_fetch_assoc($result) {
    if ($result instanceof PDOStatement) {
        // PostgreSQL
        return $result->fetch(PDO::FETCH_ASSOC);
    } else {
        // MySQL
        return mysqli_fetch_assoc($result);
    }
}

// Universal fetch all function
function db_fetch_all($result) {
    if ($result instanceof PDOStatement) {
        // PostgreSQL
        return $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // MySQL
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}

// Universal num rows function
function db_num_rows($result) {
    if ($result instanceof PDOStatement) {
        // PostgreSQL
        return $result->rowCount();
    } else {
        // MySQL
        return mysqli_num_rows($result);
    }
}

// Universal insert ID function
function db_insert_id($conn) {
    if (isPostgreSQL($conn)) {
        // PostgreSQL
        return $conn->lastInsertId();
    } else {
        // MySQL
        return mysqli_insert_id($conn);
    }
}

// Universal escape string function
function db_escape_string($conn, $string) {
    if (isPostgreSQL($conn)) {
        // PostgreSQL
        return $conn->quote($string);
    } else {
        // MySQL
        return mysqli_real_escape_string($conn, $string);
    }
}

// Universal close function
function db_close($conn) {
    if (isPostgreSQL($conn)) {
        // PostgreSQL - PDO doesn't need explicit close
        return true;
    } else {
        // MySQL
        return mysqli_close($conn);
    }
}
?>
