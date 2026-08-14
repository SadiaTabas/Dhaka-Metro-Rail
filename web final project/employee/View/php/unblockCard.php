<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['RoleID'] != 2) {
    header("Location: login.php");
    exit();
}

include "../../Model/EmployeeModel.php";
$passengers = getAllPassengers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Management - Dhaka Metro</title>
    <link rel="stylesheet" href="../css/employDashboard.css">
</head>

<body>
  
    <?php require_once('../../../admin/view/employeeheader.php'); ?>

    <div class="dashboard-container">
        <h2>Passenger Card Management</h2>
        
        <section class="dashboard-section">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($passengers)) { ?>
                        <tr>
                            <td><?php echo $row['UserID']; ?></td>
                            <td><?php echo $row['FirstName'] . " " . $row['LastName']; ?></td>
                            <td><?php echo $row['Mobile']; ?></td>
                            <td><?php echo $row['Status'] ?? 'Active'; ?></td>
                            <td class="action-buttons">
                                <form action="../../Controller/EmployeeController.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['UserID']; ?>">
                                    <select name="status">
                                        <option value="Active" <?php if(($row['Status'] ?? 'Active')=='Active') echo 'selected';?>>Active</option>
                                        <option value="Blocked" <?php if($row['Status']=='Blocked') echo 'selected';?>>Blocked</option>
                                    </select>
                                    <button type="submit" name="update_user_status" class="btn-update">Update Status</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

   
    <?php require_once('footer.php'); ?>
</body>
</html>
