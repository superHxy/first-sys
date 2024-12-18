<?php
require_once 'config_copy.php';


// Function to fetch events from the database
function get_schedules() {
    global $conn;
    $result = $conn->query("SELECT * FROM schedules ORDER BY schedule_date ASC,start_time ASC");
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC); // Ensure this returns an array of associative arrays
    }
    return [];
}




// Update schedule
function update_schedule($id, $course, $lecturer, $room, $date_time, $end_time, $status) {
    global $conn;
    $stmt = $conn->prepare(
        "UPDATE schedules SET course = ?, lecturer = ?, room = ?, date_time = ?, end_time = ?, status = ? WHERE id = ?"
    );
    $stmt->bind_param("ssssssi", $course, $lecturer, $room, $date_time, $end_time, $status, $id);
    return $stmt->execute();
}




function get_history() {
    global $conn;
    $query = "SELECT * FROM history ORDER BY end_time ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getEvents() {
    global $conn;
    $query = "SELECT * FROM events ORDER BY event_date ASC";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Error preparing query: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result; // Return result directly for easy looping
}


function delete_schedule($id) {
    global $conn; // Assuming $conn is your database connection
    $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->bind_param('i', $id);
    return $stmt->execute();
}

function get_schedule_by_id($id) {
    global $conn; // Assuming $conn is your database connection
    $stmt = $conn->prepare("SELECT * FROM schedules WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}



function delete_history_record($id) {
    global $conn;  // Use the global database connection

    // Prepare a statement to delete a specific record by ID
    $stmt = $conn->prepare("DELETE FROM history WHERE id = ?");
    $stmt->bind_param('i', $id);  // Bind the ID parameter as an integer
    $stmt->execute();  // Execute the statement

    if ($stmt->affected_rows > 0) {
        return true;  // Record deleted successfully
    } else {
        return false;  // Record not found or deletion failed
    }
}

// function_copy.php

function get_news() {
    global $conn;  // Use the existing $conn database connection

    $sql = "SELECT * FROM content";  // Assuming you have a table named `content` for news
    $result = $conn->query($sql);

    $news_items = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $news_items[] = $row;
        }
    }
    return $news_items;
}


function fetchAllNews($conn) {
    $sql = "SELECT id, title, description, file_path, created_at FROM content ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC); // Return an associative array
    }else{
        echo " Error";
    }
   
}


/**
 * Adds a new schedule to the database.
 *
 * @param string $course The course name.
 * @param string $lecturer The lecturer name.
 * @param string $room The room name.
 * @param string $schedule_date The date of the schedule (YYYY-MM-DD).
 * @param string $start_time The start time (HH:MM:SS).
 * @param string $end_time The end time (HH:MM:SS).
 * @param string $status The status of the schedule (e.g., 'usual', 'no-class', 'make-up').
 * @return bool True on success, false on failure.
 */
function add_schedule($course, $lecturer, $room, $schedule_date, $start_time, $end_time, $status) {
    global $conn; // Use the global $conn variable for the database connection

    $sql = "INSERT INTO schedules (course, lecturer, room, schedule_date, start_time, end_time, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the parameters to the SQL query
        $stmt->bind_param(
            'sssssss', 
            $course, 
            $lecturer, 
            $room, 
            $schedule_date, 
            $start_time, 
            $end_time, 
            $status
        );

        // Execute the query
        if ($stmt->execute()) {
            return true; // Success
        }
    }

    return false; // Failure
}

function transferSchedulesToHistory() {
    global $conn;

    // Start a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // Get current date and time
        $current_datetime = date('Y-m-d H:i:s');

        // Prepare statement to select schedules to transfer
        $select_stmt = $conn->prepare("
            SELECT * FROM schedules 
            WHERE CONCAT(schedule_date, ' ', end_time) < ?
        ");
        $select_stmt->bind_param('s', $current_datetime);
        $select_stmt->execute();
        $result = $select_stmt->get_result();

        // Counter for transferred schedules
        $transferred_count = 0;

        // Process each expired schedule
        while ($schedule = $result->fetch_assoc()) {
            // Insert into history table
            $insert_stmt = $conn->prepare("
                INSERT INTO schedule_history (
                    course, lecturer, room, schedule_date, 
                    start_time, end_time, status
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?
                )
            ");
            $insert_stmt->bind_param(
                'sssssss', 
                $schedule['course'], 
                $schedule['lecturer'], 
                $schedule['room'], 
                $schedule['schedule_date'], 
                $schedule['start_time'], 
                $schedule['end_time'], 
                $schedule['status']
            );
            $insert_stmt->execute();

            // Delete from original schedules table
            $delete_stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
            $delete_stmt->bind_param('i', $schedule['id']);
            $delete_stmt->execute();

            $transferred_count++;
        }

        // Commit the transaction
        $conn->commit();

        // Log the transfer
        error_log("Transferred $transferred_count schedules to history on " . date('Y-m-d H:i:s'));

        return $transferred_count;

    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();
        
        // Log the error
        error_log("Error transferring schedules: " . $e->getMessage());
        return false;
    }
}

// If this script is run directly
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    $transferred = transferSchedulesToHistory();
    
    if ($transferred !== false) {
        echo "Successfully transferred $transferred schedules to history.";
    } else {
        echo "An error occurred during schedule transfer.";
    }
}
?>
