<?php
require_once '../../Model/staff_mod.php';
require_once '../../Model/db_connection.php';
require_once '../../Controller/fetchpending_con.php';
require_once '../../Model/citizen_mod.php';

// Initialize the Staff class
$staff = new Staff($conn);
$citizen = new Citizen($conn);
$defuctom_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$defuctom_ids = isset($_GET['id']) ? intval($_GET['id']) : null;

if (isset($_GET['id'])) {
    $defuctom_id = intval($_GET['id']);
    // Fetch schedule details
    $scheduleDetails = $staff->getScheduleDetails(NULL,NULL,$defuctom_id); // Fetch date, start_time, end_time based on baptism ID
    $scheduleDate = $scheduleDetails['schedule_date'];
    $startTime = $scheduleDetails['schedule_start_time'];
    $endTime = $scheduleDetails['schedule_end_time'];

    // Pass $scheduleDate to the function instead of undefined $selectedDate
    $priests = $citizen->getAvailablePriests($scheduleDate, $startTime, $endTime);
} else {
    echo "No baptism ID provided.";
    $priests = [];
}
$loggedInUserEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;
$r_status = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;

if (!$loggedInUserEmail) {
  header("Location: ../../index.php");
  exit();
}

// Redirect staff users to the staff page, not the citizen page
if ($r_status === "Citizen") {
  header("Location: ../PageCitizen/CitizenPage.php"); // Change to your staff page
  exit();
}
if ($r_status === "Admin") {
  header("Location: ../PageAdmin/AdminDashboard.php"); // Change to your staff page
  exit();
}if ($r_status === "Priest") {
  header("Location: ../PagePriest/index.php"); // Change to your staff page
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>ARGAO CHURCH MANAGEMENT SYSTEM</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link rel="icon" href="../assets/img/mainlogo.jpg" type="image/x-icon"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });

    </script>
    <style>
        .birthday-input {
    font-family: Arial, sans-serif;
    margin-bottom: 10px;
}

.birthday-input label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.birthday-selectors {
    display: flex;
    gap: 5px;
}


.birthday-selectors select {
    padding: 5px;
    border: 1px solid #0a58ca;
    border-radius: 5px;
    width: 100px;
    font-size: 14px;
    color: #555;
}

.birthday-selectors select:focus {
    outline: none;
    border-color: #0a58ca;
}


small {
    display: block;
    color: #555;
    margin-top: 5px;
}

    </style>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
 integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl7/1L_dstPt3HV5HzF6Gvk/e3s4Wz6iJgD/+ub2oU" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="../assets/css/demo.css" />
   
  </head>
  <body>   
  <?php  require_once 'sidebar.php'?>
      <div class="main-panel">
      <?php  require_once 'header.php'?>
        
     
        <div class="container">
            <div class="page-inner">
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Approval for Schedule Baptism</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="modalForm" method="POST" action="../../Controller/addbaptism_con.php">
                <div class="modal-body">
            <input type="hidden" name="defuctom_id" value="<?php echo htmlspecialchars($defuctom_id); ?>" />
                    <div class="form-group">
                        <label for="eventTitle">Payable Amount</label>
                        <input type="number" class="form-control" id="eventTitle" name="eventTitle" placeholder="Enter Amount">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Second Modal -->
<div class="modal fade" id="myModals" tabindex="-1" role="dialog" aria-labelledby="modalLabel2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel2">Approval for Schedule Baptism</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="modalForm2" method="POST" action="../../Controller/updatepayment_con.php">
                <div class="modal-body">
                    <input type="hidden" name="fpriest_id" value="<?php echo htmlspecialchars($defuctom_id); ?>" />
                    <div class="form-group">
                        <label for="priestSelect2">Select Priest</label>
                        <select class="form-control" id="priestSelect2" name="eventType">
                            <option value="" disabled selected>Select Priest</option>
                            <!-- Populate priests in the dropdown -->
                            <?php if (!empty($priests)): ?>
                                <?php foreach ($priests as $priest): ?>
                                    <option value="<?php echo htmlspecialchars($priest['citizend_id']); ?>">
                                        <?php echo htmlspecialchars($priest['fullname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No priests available for the selected time</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Funeral View Information Form</div>
                    </div>
                    <div class="row"> <!-- Ensure row is wrapping both columns -->
    <!-- Priest Name Section -->
    <div class="col-md-6 col-lg-4">
        <div class="form-group">
            <label for="priestName">Priest Name</label>
            <div class="d-flex align-items-center">
                <input type="text" class="form-control" id="priestName" name="priestName" value="<?php echo $Priest; ?>" readonly />

                <?php if ($Pending == 'Pending'): ?>
                    <!-- If the priest is pending, show a disabled button -->
                    <button type="button" class="btn btn-warning" disabled>Waiting for Approval</button>

                <?php elseif ($Pending == 'Approved'): ?>
                    <!-- If the priest is accepted, display a message instead of a button -->
                    <span class="text-success">Has been Approved</span>

                <?php elseif ($Pending == 'Declined'): ?>
                    <!-- If the priest is declined, enable the button to trigger the modal -->
                    <button type="button" data-toggle="modal" data-target="#myModals" class="btn btn-danger">Priest Declined - Click to Reassign</button>

                <?php else: ?>
                    <!-- Default button state (e.g., if no status is set) -->
                    <button type="button" data-toggle="modal" data-target="#myModals" class="btn btn-success">Priest Assign</button>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Reason for Decline Section -->
    <div class="col-md-6 col-lg-4">
        <?php if ($Pending === 'Declined') : ?>
            <!-- Display the reason for declining -->
            <div class="form-group">
                <label for="declineReason">Reason for Decline:</label>
                <textarea class="form-control" id="declineReason" rows="3" readonly><?php echo htmlspecialchars($declineReason); ?></textarea>
            </div>
        <?php endif; ?>
    </div>
</div>




                    <div class="card-body">
                   
    <div class="row">
        <div class="col-md-6 col-lg-4">
            <!-- Date -->
            <div class="form-group">
                <label for="date">Date</label>
                <input type="text" class="form-control" id="date" name="date" value="<?php echo $pendingItem['schedule_date'] ?? ''; ?>" readonly />
              </div>

            <!-- Firstname of Deceased Person -->
            <div class="form-group">
                <label for="firstname">Firstname of Deceased Person</label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter Firstname" value="<?php echo $firstname; ?>" readonly />

            </div>

            <!-- Lastname of Deceased Person -->
            <div class="form-group">
                <label for="lastname">Last Name of Deceased Person</label>
                <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Enter Middlename" value="<?php echo $middlename; ?>" />
 </div>

            <!-- Middlename of Deceased Person -->
            <div class="form-group">
                <label for="middlename">Middle Name of Deceased Person</label>
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter Lastname" value="<?php echo $lastname; ?>" />
 </div>

            <!-- Address -->
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address"><?php echo $pendingItem['d_address'] ?? ''; ?></textarea>
              </div>

            <!-- Gender -->
            <div class="form-group">
                    <label>Gender</label><br />
                    <div class="d-flex">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="flexRadioDefault1" value="Male" <?php echo (isset($pendingItem['d_gender']) && $pendingItem['d_gender'] == 'Male') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="flexRadioDefault1">Male</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="flexRadioDefault2" value="Female" <?php echo (isset($pendingItem['d_gender']) && $pendingItem['d_gender'] == 'Female') ? 'checked' : ''; ?> />
                            <label class="form-check-label" for="flexRadioDefault2">Female</label>
                        </div>
                </div>
            </div>

            <!-- Cause of Death -->
            <div class="form-group">
                <label for="religion">Cause of Death (skip this if you don't know)</label>
                <input type="text" class="form-control" id="cause_of_death" name="cause_of_death" placeholder="" value="<?php echo $pendingItem['cause_of_death'] ?? ''; ?>"/>
            </div>

            <!-- Marital Status -->
            <div class="form-group">
                <label for="marital_status">Marital Status</label>
                <input type="text" class="form-control" id="marital_status" name="marital_status" placeholder="" value="<?php echo $pendingItem['marital_status'] ?? ''; ?>"/>
         

            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <!-- Start Time -->
            <div class="form-group">
                <label for="start_time">Start Time</label>
                <input type="text" class="form-control" id="date" name="date" value="<?php echo $startTime; ?>" readonly />   </div>

            <!-- Place of Birth -->
            <div class="form-group">
                <label for="pbirth">Place of Birth</label>
                <input type="text" class="form-control" id="pbirth" name="place_of_birth" placeholder="Enter Place of Birth"  value="<?php echo $pendingItem['place_of_birth'] ?? ''; ?>" />
           
            </div>

            <!-- Date of Birth -->
            <div class="form-group">
                <label>Date of Birth</label>
                <div class="birthday-selectors">
            <select id="months" name="month">
                <option value="">Month</option>
                <option value="01"<?php echo ($month == '01') ? 'selected' : ''; ?>>January</option>
                <option value="02"<?php echo ($month == '02') ? 'selected' : ''; ?>>February</option>
                <option value="03"<?php echo ($month == '03') ? 'selected' : ''; ?>>March</option>
                <option value="04"<?php echo ($month == '04') ? 'selected' : ''; ?>>April</option>
                <option value="05"<?php echo ($month == '05') ? 'selected' : ''; ?>>May</option>
                <option value="06"<?php echo ($month == '06') ? 'selected' : ''; ?>>June</option>
                <option value="07"<?php echo ($month == '07') ? 'selected' : ''; ?>>July</option>
                <option value="08"<?php echo ($month == '08') ? 'selected' : ''; ?>>August</option>
                <option value="09"<?php echo ($month == '09') ? 'selected' : ''; ?>>September</option>
                <option value="10"<?php echo ($month == '10') ? 'selected' : ''; ?>>October</option>
                <option value="11"<?php echo ($month == '11') ? 'selected' : ''; ?>>November</option>
                <option value="12"<?php echo ($month == '12') ? 'selected' : ''; ?>>December</option>
            </select>

            <select id="days" name="day">
    <option value="">Day</option>
    <?php for ($i = 1; $i <= 31; $i++): ?>
        <option value="<?php echo sprintf('%02d', $i); ?>" <?php echo ($day == sprintf('%02d', $i)) ? 'selected' : ''; ?>>
            <?php echo $i; ?>
        </option>
    <?php endfor; ?>
</select>

<select id="years" name="year">
    <option value="">Year</option>
    <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
        <option value="<?php echo $i; ?>" <?php echo ($year == $i) ? 'selected' : ''; ?>>
            <?php echo $i; ?>
        </option>
    <?php endfor; ?>
</select>
                </div>
           
            </div>

            <!-- Father's Fullname -->
            <div class="form-group">
                    <label for="father_name">Father's Fullname</label>
                    <input type="text" class="form-control" id="father_name" name="father_fullname" value="<?php echo $pendingItem['father_fullname'] ?? ''; ?>" />
                </div>
                <div class="form-group">
                    <label for="mother_name">Mother's Fullname</label>
                    <input type="text" class="form-control" id="mother_name" name="mother_fullname" value="<?php echo $pendingItem['mother_fullname'] ?? ''; ?>" />
                </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <!-- End Time -->
            <div class="form-group">
                <label for="end_time">End Time</label>
                <input type="text" class="form-control" id="date" name="date" value="<?php echo $endTime; ?>" readonly />
            </div>

            <!-- Parents Residence -->
            <div class="form-group">
                <label for="parents_residence">Parents Residence</label>
                <textarea class="form-control" id="parents_residence" name="parents_residence" placeholder="Enter Parents Residence"><?php echo $pendingItem['parents_residence'] ?? ''; ?></textarea>
         
            </div>

            <!-- Place of Death -->
            <div class="form-group">
                <label for="place_of_death">Place of Death</label>
                <input type="text" class="form-control" id="place_of_death" name="place_of_death" placeholder="Enter Place" value="<?php echo $pendingItem['place_of_death'] ?? ''; ?>" />
              
            </div>

            <!-- Date of Death -->
            <div class="form-group">
                <label>Date of Death</label>
                <div class="birthday-selectors">
            <select id="months" name="month">
                <option value="">Month</option>
                <option value="01"<?php echo ($months == '01') ? 'selected' : ''; ?>>January</option>
                <option value="02"<?php echo ($months == '02') ? 'selected' : ''; ?>>February</option>
                <option value="03"<?php echo ($months == '03') ? 'selected' : ''; ?>>March</option>
                <option value="04"<?php echo ($months == '04') ? 'selected' : ''; ?>>April</option>
                <option value="05"<?php echo ($months == '05') ? 'selected' : ''; ?>>May</option>
                <option value="06"<?php echo ($months == '06') ? 'selected' : ''; ?>>June</option>
                <option value="07"<?php echo ($months == '07') ? 'selected' : ''; ?>>July</option>
                <option value="08"<?php echo ($months == '08') ? 'selected' : ''; ?>>August</option>
                <option value="09"<?php echo ($months == '09') ? 'selected' : ''; ?>>September</option>
                <option value="10"<?php echo ($months == '10') ? 'selected' : ''; ?>>October</option>
                <option value="11"<?php echo ($months == '11') ? 'selected' : ''; ?>>November</option>
                <option value="12"<?php echo ($months == '12') ? 'selected' : ''; ?>>December</option>
            </select>

            <select id="days" name="day">
    <option value="">Day</option>
    <?php for ($i = 1; $i <= 31; $i++): ?>
        <option value="<?php echo sprintf('%02d', $i); ?>" <?php echo ($days == sprintf('%02d', $i)) ? 'selected' : ''; ?>>
            <?php echo $i; ?>
        </option>
    <?php endfor; ?>
</select>

<select id="years" name="year">
    <option value="">Year</option>
    <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
        <option value="<?php echo $i; ?>" <?php echo ($years == $i) ? 'selected' : ''; ?>>
            <?php echo $i; ?>
        </option>
    <?php endfor; ?>
</select>
                </div>
             
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card-action">
    <?php
$event_name = isset($pendingItem['roles']) ? $pendingItem['roles'] : '';

if ($event_name === 'Online') {
    // Button for online approval (triggers modal)
    echo '<button type="button" data-toggle="modal" data-target="#myModal" class="btn btn-success">Approve</button>';
} else if ($event_name === 'Walkin') {
    // Button for walk-in approval (automatic approval)
    echo '<button type="button" class="btn btn-success approve-btn" data-id="' . $defuctom_ids . '">Approve</button>';

}
?>
    <button type="button" class="btn btn-danger decline-btn" data-id="<?php echo htmlspecialchars($defuctom_id); ?>" >Decline</button>
        <button type="button" class="btn btn-danger" onclick="window.location.href='your_cancel_url.php'">Cancel</button>
         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
     document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.approve-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var defuctom_ids = this.getAttribute('data-id');

            // Automatic approval for walk-in
            Swal.fire({
                title: 'Are you sure?',
                text: "This action will approve the request!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '../../Controller/updatepayment_con.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            Swal.fire(
                                'Approved!',
                                'The baptism request has been approved.',
                                'success'
                            ).then(() => {
                                window.location.href = 'StaffSoloSched.php';
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'There was an issue approving the request.',
                                'error'
                            );
                        }
                    };
                    xhr.send('defuctom_ids=' + encodeURIComponent(defuctom_ids));
                }
            });
        });
    });

    // Decline button logic remains the same
});

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.decline-btn').addEventListener('click', function() {
        var defuctom_id = this.getAttribute('data-id');
       

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, decline it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../../Controller/updatepayment_con.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        Swal.fire(
                            'Declined!',
                            'The baptism request has been declined.',
                            'success'
                        ).then(() => {
                            // Redirect after approval
                            window.location.href = 'StaffSoloSched.php';
                        });
                    } else {
                        console.error("Error response: ", xhr.responseText); // Log error response
                        Swal.fire(
                            'Error!',
                            'There was an issue declining the request.',
                            'error'
                        );
                    }
                };

                // Send both baptismfill_id and citizen_id
                xhr.send('defuctomfill_id=' + encodeURIComponent(defuctom_id));
            }
        });
    });
});
</script>
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Popper.js (required for Bootstrap 4) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Sweet Alert -->
  <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="../assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>


    <!-- jQuery Vector Maps -->
    <script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="../assets/js/plugin/jsvectormap/world.js"></script>


    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

  </body>
</html>
