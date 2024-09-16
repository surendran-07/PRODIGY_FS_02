<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "employee_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$edit_state = false;
$employee_to_edit = null;

// Add or Update Employee
if (isset($_POST['save_employee'])) {
    $name = $_POST['employeeName'];
    $position = $_POST['employeePosition'];
    $age = $_POST['employeeAge'];
    $department = $_POST['employeeDepartment'];

    if (isset($_POST['employee_id']) && $_POST['employee_id'] != '') {
        // Update existing employee
        $employee_id = $_POST['employee_id'];
        $stmt = $conn->prepare("UPDATE employees SET name=?, position=?, age=?, department=? WHERE id=?");
        $stmt->bind_param("ssisi", $name, $position, $age, $department, $employee_id);
    } else {
        // Add new employee
        $stmt = $conn->prepare("INSERT INTO employees (name, position, age, department) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $position, $age, $department);
    }
    
    $stmt->execute();
    $stmt->close();
}

// Edit Employee: Fetch the employee data to populate the form
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_state = true;

    $result = $conn->query("SELECT * FROM employees WHERE id=$id");
    $employee_to_edit = $result->fetch_assoc();
}

// Delete Employee
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM employees WHERE id=$id");
}

// Fetch Employees
$employees = $conn->query("SELECT * FROM employees");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Employee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center my-4">Admin Dashboard - Employee Management</h2>

        <!-- Employee Form -->
        <div class="employee-form">
            <h4><?php echo $edit_state ? 'Edit' : 'Add'; ?> Employee</h4>
            <form method="POST" action="">
                <input type="hidden" name="employee_id" value="<?php echo $edit_state ? $employee_to_edit['id'] : ''; ?>">
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" class="form-control" name="employeeName" placeholder="Employee Name" value="<?php echo $edit_state ? $employee_to_edit['name'] : ''; ?>" required>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="employeePosition" placeholder="Position" value="<?php echo $edit_state ? $employee_to_edit['position'] : ''; ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <input type="number" class="form-control" name="employeeAge" placeholder="Age" value="<?php echo $edit_state ? $employee_to_edit['age'] : ''; ?>" required>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="employeeDepartment" placeholder="Department" value="<?php echo $edit_state ? $employee_to_edit['department'] : ''; ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success" name="save_employee"><?php echo $edit_state ? 'Update' : 'Add'; ?> Employee</button>
            </form>
        </div>

        <!-- Employee List -->
        <div class="employee-list mt-4">
            <h4>Employee List</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Age</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $employees->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['position']; ?></td>
                            <td><?php echo $row['age']; ?></td>
                            <td><?php echo $row['department']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
