<?php
// File path for the CSV data
$dataFile = 'classroom_data.csv';

// Check and create the file if it doesn't exist
if (!file_exists($dataFile)) {
    $fileHandle = fopen($dataFile, 'w');
    fputcsv($fileHandle, ["ID", "FullName", "Gender", "DateOfBirth"]);
    fclose($fileHandle);
}

// Function to read data from CSV
function loadCSV($file) {
    $students = [];
    if (($fileHandle = fopen($file, 'r')) !== FALSE) {
        fgetcsv($fileHandle); // Skip the header line
        while (($row = fgetcsv($fileHandle)) !== FALSE) {
            list($ID, $FullName, $Gender, $DateOfBirth) = $row;
            $students[] = [
                "ID" => $ID,
                "FullName" => $FullName,
                "Gender" => $Gender,
                "DateOfBirth" => $DateOfBirth,
            ];
        }
        fclose($fileHandle);
    }
    return $students;
}

// Function to write data to CSV
function saveCSV($file, $data) {
    $fileHandle = fopen($file, 'w');
    fputcsv($fileHandle, ["ID", "FullName", "Gender", "DateOfBirth"]);
    foreach ($data as $row) {
        fputcsv($fileHandle, $row);
    }
    fclose($fileHandle);
}

// Handle adding a new student
if (isset($_POST['addStudent'])) {
    $newStudent = [
        'ID' => $_POST['studentID'],
        'FullName' => $_POST['name'],
        'Gender' => $_POST['sex'],
        'DateOfBirth' => $_POST['birthDate'],
    ];
    $students = loadCSV($dataFile);
    $students[] = $newStudent;
    usort($students, function($a, $b) {
        return $a['ID'] <=> $b['ID'];
    });
    saveCSV($dataFile, $students);
}

// Handle deleting a student
if (isset($_POST['removeStudent'])) {
    $idToRemove = $_POST['studentIDToRemove'];
    $students = loadCSV($dataFile);
    $students = array_filter($students, function($student) use ($idToRemove) {
        return $student['ID'] !== $idToRemove;
    });
    saveCSV($dataFile, $students);
}

// Read the data to display
$students = loadCSV($dataFile);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Member Manager</title>
</head>
<body>
<div class="container">
    <h1>Classroom Members</h1>

    <div class="form-section">
        <h2>Add New Member</h2>
        <form method="POST">
            <div class="form-group">
                <label for="studentID">Student ID</label>
                <input type="text" id="studentID" name="studentID" required pattern="\d+" title="Please enter numbers only." placeholder="Enter Student ID">
            </div>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter Full Name">
            </div>
            <div class="form-group">
                <label for="sex">Gender</label>
                <select id="sex" name="sex" required>
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="birthDate">Date of Birth</label>
                <input type="date" id="birthDate" name="birthDate" required>
            </div>
            <button type="submit" name="addStudent" class="btn">Add Member</button>
        </form>
    </div>

    <div class="table-section">
        <h2>Member List</h2>
        <table id="studentTable">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['ID']) ?></td>
                    <td><?= htmlspecialchars($student['FullName']) ?></td>
                    <td><?= htmlspecialchars($student['Gender']) ?></td>
                    <td><?= htmlspecialchars($student['DateOfBirth']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="studentIDToRemove" value="<?= htmlspecialchars($student['ID']) ?>">
                            <button type="submit" name="removeStudent" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn" onclick="downloadCSV()">Download CSV</button>
    </div>
</div>
</body>
</html>
