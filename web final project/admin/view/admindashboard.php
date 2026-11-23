<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}

if (!isset($_SESSION['UserID']) || $_SESSION['RoleID'] != 1) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

include "../model/AdminModel.php";

$totalStaff = getTotalStaff();
$totalRoutes = getTotalRoutes();
$totalPassengers = getTotalPassengers();
$totalBookings = getTotalBookings();
$totalRevenue = getTotalRevenue();

$routes = getAllRoutes();
$staff  = getAllStaff();

$stations = [
    "Uttara North", "Uttara Center", "Uttara South", "Pallabi",
    "Mirpur-11", "Mirpur-10", "Kazipara", "Shewrapara",
    "Agargaon", "Bijoy Sarani", "Farmgate", "Karwan Bazar",
    "Shahbagh", "Dhaka University", "Secretariat", "Motijheel", "Kamalapur"
];

include "adminheader.php"; 
?>

<link rel="stylesheet" href="admindashboard.css">

<main class="main-content">
    <div class="user-welcome-bar">
        <div class="welcome-left">
            <h2>Administrative Control Center</h2>
            <p>Dhaka Mass Rapid Transit Management • System Overview & Operational Controls</p>
        </div>
        <div class="welcome-right">
            <span class="status-badge status-confirmed">System Master Active</span>
        </div>
    </div>

    <?php
    if (isset($_GET['msg'])) {
        $m = $_GET['msg'];
        if ($m === 'staff_added') echo "<div class='alert-box success-alert'>Staff member added successfully.</div>";
        if ($m === 'route_added') echo "<div class='alert-box success-alert'>Route and fare configuration added successfully.</div>";
        if ($m === 'staff_deleted') echo "<div class='alert-box success-alert'>Staff record removed.</div>";
        if ($m === 'route_deleted') echo "<div class='alert-box success-alert'>Route removed successfully.</div>";
        if ($m === 'profile_updated') echo "<div class='alert-box success-alert'>Administrator profile updated.</div>";
        if ($m === 'password_updated') echo "<div class='alert-box success-alert'>Administrator password updated.</div>";
    }
    if (isset($_GET['error'])) {
        $e = $_GET['error'];
        if ($e === 'add_staff_failed') echo "<div class='alert-box error-alert'>Failed to add staff. Mobile number might already be in use.</div>";
        if ($e === 'password_length') echo "<div class='alert-box error-alert'>Password must be at least 6 characters long.</div>";
        if ($e === 'invalid_route') echo "<div class='alert-box error-alert'>Please specify valid stations and a positive fare amount.</div>";
        if ($e === 'same_stations') echo "<div class='alert-box error-alert'>Origin and destination stations cannot be the same.</div>";
    }
    ?>

    <!-- Overview Statistics -->
    <section class="section-box" style="margin-bottom: 25px;">
        <h3 class="section-title">📊 Operational Statistics (Live Database)</h3>
        <div class="info-cards-container" style="margin-top: 15px;">
            <div class="info-card">
                <div class="info-icon">👥</div>
                <h3>Active Passengers</h3>
                <p class="stat-highlight" style="font-size:26px; font-weight:800; color:#006a4e; margin:6px 0;"><?= $totalPassengers ?></p>
                <small>Registered Accounts</small>
            </div>
            <div class="info-card">
                <div class="info-icon">🧑‍💼</div>
                <h3>Staff Members</h3>
                <p class="stat-highlight" style="font-size:26px; font-weight:800; color:#1d75f0; margin:6px 0;"><?= $totalStaff ?></p>
                <small>Station & Desk Employees</small>
            </div>
            <div class="info-card">
                <div class="info-icon">🛤️</div>
                <h3>Configured Routes</h3>
                <p class="stat-highlight" style="font-size:26px; font-weight:800; color:#006a4e; margin:6px 0;"><?= $totalRoutes ?></p>
                <small>MRT Line-6 Station Pairs</small>
            </div>
            <div class="info-card">
                <div class="info-icon">🎫</div>
                <h3>Total Bookings</h3>
                <p class="stat-highlight" style="font-size:26px; font-weight:800; color:#d97706; margin:6px 0;"><?= $totalBookings ?></p>
                <small>Tickets Generated</small>
            </div>
            <div class="info-card">
                <div class="info-icon">💰</div>
                <h3>Total Revenue</h3>
                <p class="stat-highlight" style="font-size:26px; font-weight:800; color:#15803d; margin:6px 0;"><?= number_format($totalRevenue, 2) ?> ৳</p>
                <small>Confirmed Ticket Sales</small>
            </div>
        </div>
    </section>

    <!-- Route & Fare Management -->
    <section class="section-box" style="margin-bottom: 25px;">
        <h3 class="section-title">🛤️ Fare & Route Management</h3>
        <p class="section-subtitle">Configure operational stations, routes, and standard ticket fares</p>

        <div class="table-responsive" style="margin-top: 15px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Route ID</th>
                        <th>Origin Station</th>
                        <th>Destination Station</th>
                        <th>Fare Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if ($routes && mysqli_num_rows($routes) > 0) {
                    while ($r = mysqli_fetch_assoc($routes)) { ?>
                    <tr>
                        <td><strong>#<?= $r['RouteID'] ?></strong></td>
                        <td><?= htmlspecialchars($r['FromStation']) ?></td>
                        <td><?= htmlspecialchars($r['ToStation']) ?></td>
                        <td style="font-weight:700; color:#006a4e;"><?= number_format($r['Fare'], 2) ?> ৳</td>
                        <td>
                            <form method="post" action="../controller/admincontroller.php" style="margin:0;">
                                <input type="hidden" name="delete_route_id" value="<?= $r['RouteID'] ?>">
                                <button type="submit" class="btn-delete-row" onclick="return confirm('Delete route <?= htmlspecialchars($r['FromStation']) ?> to <?= htmlspecialchars($r['ToStation']) ?>?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php } 
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding:25px; color:#94a3b8;'>No routes found in database.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="admin-form-card" style="margin-top: 25px;">
            <h4>Add New Route</h4>
            <form id="addRouteForm" method="post" action="../controller/admincontroller.php" class="inline-form-grid">
                <div class="form-group">
                    <label>From Station</label>
                    <select name="from_station" required>
                        <option value="">Select Origin</option>
                        <?php foreach ($stations as $st): ?>
                            <option value="<?= htmlspecialchars($st) ?>"><?= htmlspecialchars($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>To Station</label>
                    <select name="to_station" required>
                        <option value="">Select Destination</option>
                        <?php foreach ($stations as $st): ?>
                            <option value="<?= htmlspecialchars($st) ?>"><?= htmlspecialchars($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fare (BDT)</label>
                    <input type="number" name="fare" placeholder="Fare (e.g. 50)" step="5" min="20" required>
                </div>
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" name="add_route" class="btn-add-action">+ Add Route</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Staff Management -->
    <section class="section-box">
        <h3 class="section-title">🧑‍💼 Employee & Staff Management</h3>
        <p class="section-subtitle">Manage station staff accounts authorized to inspect tickets and handle transit cards</p>

        <div class="table-responsive" style="margin-top: 15px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mobile Number</th>
                        <th>Staff Full Name</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if ($staff && mysqli_num_rows($staff) > 0) {
                    while ($s = mysqli_fetch_assoc($staff)) { ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['Mobile']) ?></strong></td>
                        <td><?= htmlspecialchars($s['FirstName'] . " " . $s['LastName']) ?></td>
                        <td><span class="status-badge" style="background:#e0f2fe; color:#0369a1;">Station Staff</span></td>
                        <td>
                            <form method="post" action="../controller/admincontroller.php" style="margin:0;">
                                <input type="hidden" name="delete_staff_mobile" value="<?= htmlspecialchars($s['Mobile']) ?>">
                                <button type="submit" class="btn-delete-row" onclick="return confirm('Remove staff member <?= htmlspecialchars($s['FirstName']) ?>?')">Remove Staff</button>
                            </form>
                        </td>
                    </tr>
                    <?php } 
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; padding:25px; color:#94a3b8;'>No staff members found.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="admin-form-card" style="margin-top: 25px;">
            <h4>Register New Staff Member</h4>
            <form id="addStaffForm" method="post" action="../controller/admincontroller.php" class="inline-form-grid">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile" placeholder="01XXXXXXXXX" maxlength="11" required>
                </div>
                <div class="form-group">
                    <label>Initial Password</label>
                    <input type="password" name="password" placeholder="Min 6 characters" required>
                </div>
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" name="add_staff" class="btn-add-action">+ Create Staff</button>
                </div>
            </form>
        </div>
    </section>
</main>

<script src="admindashboard.js"></script>
<?php include "footer.php"; ?>
