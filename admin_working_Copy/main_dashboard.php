<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - News, Schedules & Events</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- AlpineJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.2/cdn.min.js" defer></script>
</head>
<body>
    <?php

        require_once 'function_copy.php';
        include('sidebar.php') ;

        // Fetch news, schedules, and events
        $news_items = get_news();
        $schedules = get_schedules();
        $events = getEvents();

        $username = "John Doe"; // Example username (replace with session variable)
    ?>

    <!-- Main Content -->
    <div class="main-content ">
        <!-- Welcome User -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold">Welcome, <?= htmlspecialchars($username); ?>!</h1>
                <p class="text-gray-600">Here's your dashboard overview.</p>
            </div>
            <div class="text-right">
                <p id="clock" class="text-2xl font-semibold"></p>
                <p id="date" class="text-gray-600"></p>
            </div>
        </div>

        <!-- Tabs -->
        <div x-data="{ tab: 'news' }">
            <!-- Tab Buttons -->
            <div class="flex space-x-4 mb-6">
                <button @click="tab = 'news'" 
                    :class="{'bg-red-800 text-white': tab === 'news', 'bg-white text-red-800': tab !== 'news'}"
                    class="px-6 py-3 rounded-lg font-medium hover:scale-105 transform transition-all">
                    News
                </button>
                <button @click="tab = 'schedules'" 
                    :class="{'bg-red-800 text-white': tab === 'schedules', 'bg-white text-red-800': tab !== 'schedules'}"
                    class="px-6 py-3 rounded-lg font-medium hover:scale-105 transform transition-all">
                    Schedules
                </button>
                <button @click="tab = 'events'" 
                    :class="{'bg-red-800 text-white': tab === 'events', 'bg-white text-red-800': tab !== 'events'}"
                    class="px-6 py-3 rounded-lg font-medium hover:scale-105 transform transition-all">
                    Events
                </button>
            </div>

            <!-- News Section -->
            <div x-show="tab === 'news'" class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold">Latest News</h2>
                    <a href="content_list.php" class="text-red-700 underline">View More</a>
                </div>
                <?php if (!empty($news_items)): ?>
                    <div class="space-y-4">
                        <?php foreach ($news_items as $news): ?>
                            <div class="p-4 rounded-lg shadow bg-gray-50 hover:bg-gray-100 transition">
                                <h3 class="text-xl font-bold"><?= htmlspecialchars($news['title']); ?></h3>
                                <p class="text-gray-600"><?= htmlspecialchars($news['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No news available.</p>
                <?php endif; ?>
            </div>

            <!-- Schedules Section -->
            <div x-show="tab === 'schedules'" class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold">Upcoming Schedules</h2>
                    <a href="ad_dashboard_copy.php" class="text-red-700 underline">View More</a>
                </div>
                <?php if (!empty($schedules)): ?>
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="p-3 text-left">Course</th>
                                <th class="p-3 text-left">Lecturer</th>
                                <th class="p-3 text-left">Room</th>
                                <th class="p-3 text-left">Date</th>
                                <th class="p-3 text-left">Start-Time</th>
                                <th class="p-3 text-left">End-Time</th>
                                <th class="p-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <?php
                                    $now = date("Y-m-d H:i:s");
                                    $status = htmlspecialchars($schedule['status']);

                                    // Display only relevant statuses
                                    if (in_array($status, ['usual', 'no-class', 'make-up'])):
                                        // Assign color based on status
                                        $statusColor = '';
                                        switch ($status) {
                                            case 'usual':
                                                $statusColor = 'text-green-500'; // Green
                                                break;
                                            case 'no-class':
                                                $statusColor = 'text-red-700'; // Red
                                                break;
                                            case 'make-up':
                                                $statusColor = 'text-yellow-400'; // Yellow
                                                break;
                                        }
                                    ?>
                                <tr class="hover:bg-gray-100 transition <?= strtotime($schedule['end_time']) < strtotime($now) ? 'text-gray-400 line-through' : '' ?>">
                                    <td class="p-3"><?= htmlspecialchars($schedule['course']); ?></td>
                                    <td class="p-3"><?= htmlspecialchars($schedule['lecturer']); ?></td>
                                    <td class="p-3"><?= htmlspecialchars($schedule['room']); ?></td>
                                    <td class="p-3"><?= htmlspecialchars(date('F j, Y', strtotime($schedule['schedule_date']))); ?></td>
                                    <td class="p-3"><?= htmlspecialchars(date('g:i A', strtotime($schedule['start_time']))); ?></td>
                                    <td class="p-3"><?= htmlspecialchars(date('g:i A', strtotime($schedule['end_time']))); ?></td>
                                    <td class="p-3 <?= $statusColor; ?>"><?= htmlspecialchars(ucfirst($status)); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No schedules available.</p>
                <?php endif; ?>
            </div>

            <!-- Events Section -->
            <div x-show="tab === 'events'" class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold">Upcoming Events</h2>
                    <a href="events_list.php" class="text-red-700 underline">View More</a>
                </div>
                <?php if (!empty($events)): ?>
                    <ul class="space-y-4">
                        <?php foreach ($events as $event): ?>
                            <li class="p-4 border-b hover:bg-gray-100 transition">
                                <h3 class="text-xl font-bold"><?= htmlspecialchars($event['title']); ?></h3>
                                <p><?= htmlspecialchars($event['description']); ?></p>
                                <p class="text-gray-400 text-sm">
                                    <strong>Date:</strong> <?= date('F j, Y', strtotime($event['event_date'])) ?><br>
                                    <strong>Start Time:</strong> <?= date('g:i A', strtotime($event['start_time'])) ?><br>
                                    <strong>End Time:</strong> <?= date('g:i A', strtotime($event['end_time'])) ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No events available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Clock Script -->
    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString();
            document.getElementById('date').innerText = now.toDateString();
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
