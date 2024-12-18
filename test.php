<?php
$pdo = new PDO('mysql:host=localhost;dbname=university_dashboard', 'root', '@Hxy080904997788nhim');

// Fetch all courses
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/courses') !== false) {
    $stmt = $pdo->query('SELECT id, course_name FROM courses');
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($courses);
    exit;
}

// Fetch all lecturers
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/lecturers') !== false) {
    $stmt = $pdo->query('SELECT id, name FROM lecturers');
    $lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($lecturers);
    exit;
}

// Fetch all schedules
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/schedules') !== false) {
    $stmt = $pdo->query('SELECT s.*, c.course_name, l.name AS lecturer_name FROM schedules s 
                         INNER JOIN courses c ON s.course_id = c.id
                         INNER JOIN lecturers l ON s.lecturer_id = l.id
                         ORDER BY s.schedule_date, s.start_time');
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($schedules);
    exit;
}

// Create schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $lecturer_id = $_POST['lecturer_id'];
    $room = $_POST['room'];
    $schedule_date = $_POST['schedule_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare('INSERT INTO schedules (course_id, lecturer_id, room, schedule_date, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$course_id, $lecturer_id, $room, $schedule_date, $start_time, $end_time, $status]);

    echo json_encode(['message' => 'Schedule created successfully']);
}

// Update schedule
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'];
    $course_id = $data['course_id'];
    $lecturer_id = $data['lecturer_id'];
    $room = $data['room'];
    $schedule_date = $data['schedule_date'];
    $start_time = $data['start_time'];
    $end_time = $data['end_time'];
    $status = $data['status'];

    $stmt = $pdo->prepare('UPDATE schedules SET course_id = ?, lecturer_id = ?, room = ?, schedule_date = ?, start_time = ?, end_time = ?, status = ? WHERE id = ?');
    $stmt->execute([$course_id, $lecturer_id, $room, $schedule_date, $start_time, $end_time, $status, $id]);

    echo json_encode(['message' => 'Schedule updated successfully']);
}

// Delete schedule
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'];
    $stmt = $pdo->prepare('DELETE FROM schedules WHERE id = ?');
    $stmt->execute([$id]);

    echo json_encode(['message' => 'Schedule deleted successfully']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule Management</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
    </style>
</head>
<body>

<h1>Manage Class Schedules</h1>

<!-- Form to add/edit schedules -->
<form id="schedule-form">
    <input type="hidden" id="id">
    <label for="course_name">Course Name</label>
    <input type="text" id="course_name" required>

    <label for="lecturer_name">Lecturer Name</label>
    <input type="text" id="lecturer_name" required>

    <label for="room">Room</label>
    <input type="text" id="room" required>

    <label for="schedule_date">Date</label>
    <input type="date" id="schedule_date" required>

    <label for="start_time">Start Time</label>
    <input type="time" id="start_time" required>

    <label for="end_time">End Time</label>
    <input type="time" id="end_time" required>

    <label for="status">Status</label>
    <select id="status" required>
        <option value="usual">Usual</option>
        <option value="make-up">Make-Up</option>
        <option value="no-class">No-Class</option>
    </select>

    <button type="submit">Save Schedule</button>
</form>

<!-- Schedule Table -->
<table>
    <thead>
        <tr>
            <th>Course Name</th>
            <th>Lecturer Name</th>
            <th>Room</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="schedule-list">
        <!-- Schedule rows will be inserted here dynamically -->
    </tbody>
</table>

<script>
    // Fetch and display schedule list
    fetch('http://localhost/handler.php/schedules')
        .then(res => res.json())
        .then(data => {
            const scheduleList = document.getElementById('schedule-list');
            scheduleList.innerHTML = '';
            data.forEach(schedule => {
                scheduleList.innerHTML += `
                    <tr>
                        <td>${schedule.course_name}</td>
                        <td>${schedule.lecturer_name}</td>
                        <td>${schedule.room}</td>
                        <td>${schedule.schedule_date}</td>
                        <td>${schedule.start_time} - ${schedule.end_time}</td>
                        <td>${schedule.status}</td>
                        <td>
                            <button onclick="editSchedule(${schedule.id})">Edit</button>
                            <button onclick="deleteSchedule(${schedule.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
        });

    // Handle form submission
    document.getElementById('schedule-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('id').value;
        if (id) {
            updateSchedule(id);
        } else {
            addSchedule();
        }
    });

    // Add schedule
    async function addSchedule() {
        const formData = {
            course_name: document.getElementById('course_name').value,
            lecturer_name: document.getElementById('lecturer_name').value,
            room: document.getElementById('room').value,
            schedule_date: document.getElementById('schedule_date').value,
            start_time: document.getElementById('start_time').value,
            end_time: document.getElementById('end_time').value,
            status: document.getElementById('status').value
        };

        const response = await fetch('http://localhost/handler.php/schedules', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const result = await response.json();
        alert(result.message);
        location.reload();
    }

    // Update schedule
    async function updateSchedule(id) {
        const formData = {
            id: id,
            course_name: document.getElementById('course_name').value,
            lecturer_name: document.getElementById('lecturer_name').value,
            room: document.getElementById('room').value,
            schedule_date: document.getElementById('schedule_date').value,
            start_time: document.getElementById('start_time').value,
            end_time: document.getElementById('end_time').value,
            status: document.getElementById('status').value
        };

        const response = await fetch(`http://localhost/handler.php/schedules/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const result = await response.json();
        alert(result.message);
        location.reload();
    }

    // Delete schedule
    async function deleteSchedule(id) {
        const response = await fetch(`http://localhost/handler.php/schedules/${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();
        alert(result.message);
        location.reload();
    }

    // Edit schedule (load data into the form)
    async function editSchedule(id) {
        const response = await fetch(`http://localhost/handler.php/schedules/${id}`);
        const data = await response.json();
        document.getElementById('id').value = data.id;
        document.getElementById('course_name').value = data.course_name;
        document.getElementById('lecturer_name').value = data.lecturer_name;
        document.getElementById('room').value = data.room;
        document.getElementById('schedule_date').value = data.schedule_date;
        document.getElementById('start_time').value = data.start_time;
        document.getElementById('end_time').value = data.end_time;
        document.getElementById('status').value = data.status;
    }
</script>

</body>
</html>
