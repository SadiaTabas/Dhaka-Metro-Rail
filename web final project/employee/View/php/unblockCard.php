<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
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
    <title>Passenger Transit Card Management - Dhaka Metro</title>
    <link rel="stylesheet" href="../css/employDashboard.css">
    <link rel="stylesheet" href="../../../passenger/view/dashboard.css">
</head>
<body>

<?php require_once('../../../admin/view/employeeheader.php'); ?>

<main class="main-content">
    <div class="user-welcome-bar">
        <div class="welcome-left">
            <h2>Passenger Transit Card Management</h2>
            <p>Manage passenger access permissions and card status across MRT Line-6</p>
        </div>
        <div class="welcome-right">
            <a href="employeDashboard.php" class="back-btn" style="text-decoration:none; padding:10px 16px; background:#f1f5f9; border-radius:6px; font-weight:600;">← Back to Dashboard</a>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'status_updated'): ?>
        <div class="alert-box success-alert">Passenger status updated successfully.</div>
    <?php endif; ?>

    <section class="section-box" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:25px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                        <th style="padding:12px; text-align:left;">User ID</th>
                        <th style="padding:12px; text-align:left;">Passenger Name</th>
                        <th style="padding:12px; text-align:left;">Mobile Number</th>
                        <th style="padding:12px; text-align:left;">Card Status</th>
                        <th style="padding:12px; text-align:left;">Update Permission</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($passengers && mysqli_num_rows($passengers) > 0) {
                        while($row = mysqli_fetch_assoc($passengers)) { 
                            $status = $row['Status'] ?? 'Active';
                            $isBlocked = ($status === 'Blocked');
                        ?>
                        <tr style="border-bottom:1px solid #e2e8f0;">
                            <td style="padding:12px;"><strong>#<?php echo $row['UserID']; ?></strong></td>
                            <td style="padding:12px; font-weight:600;"><?php echo htmlspecialchars($row['FirstName'] . " " . $row['LastName']); ?></td>
                            <td style="padding:12px;"><?php echo htmlspecialchars($row['Mobile']); ?></td>
                            <td style="padding:12px;">
                                <span style="display:inline-block; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:700; background:<?php echo $isBlocked ? '#fee2e2' : '#dcfce7'; ?>; color:<?php echo $isBlocked ? '#dc2626' : '#15803d'; ?>;">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td style="padding:12px;">
                                <form action="../../Controller/EmployeeController.php" method="POST" style="display:inline-flex; align-items:center; gap:8px;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['UserID']; ?>">
                                    <select name="status" style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:4px; font-size:13px;">
                                        <option value="Active" <?php if(!$isBlocked) echo 'selected';?>>Active (Unblocked)</option>
                                        <option value="Blocked" <?php if($isBlocked) echo 'selected';?>>Blocked (Suspended)</option>
                                    </select>
                                    <button type="submit" name="update_user_status" style="background:#006a4e; color:#fff; border:none; padding:7px 14px; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;" onclick="return confirm('Update status for this passenger?');">Save</button>
                                </form>
                            </td>
                        </tr>
                        <?php } 
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding:35px; color:#94a3b8;'>No passenger records registered yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require_once('footer.php'); ?>
</body>
</html>
