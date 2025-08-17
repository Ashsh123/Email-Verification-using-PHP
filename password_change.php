<?php
session_start();
$page_title = "Password Change Update";
include('includes/header.php');
include('includes/navbar.php');

?>
<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php
                if(isset($_SESSION['status'])){
                    ?>
                   <div class="alert alert-success">
                        <h5><?= $_SESSION['status'];?></h5>
                    </div>
                    <?php
                    unset($_SESSION['status']);
                }
                ?>  
            <div class="card">
                <div class="card-header">
                    <h5>
                        Change Password
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="password_reset_code.php" method="POST">
                        <input type="hidden" name="password_token" value="<?php if(isset($_GET['token'])){ echo $_GET['token']; } ?>" >
                        
                        <input type="hidden" name="email" value="<?php if(isset($_GET['email'])){ echo $_GET['email']; } ?>" >

                        <div class="form-group mb-3">
                            <label for="">Email</label>
                            <input type="text" name="email_display" value="<?php if(isset($_GET['email'])){ echo $_GET['email']; } ?>" class="form-control" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="">New Password</label>
                            <input type="text" name="new_password" class="form-control" placeholder="Enter your new password..." required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="">Confirm Password</label>
                            <input type="text" name="confirm_password" class="form-control" placeholder="Enter your confirm password..." required>
                        </div>
                        <div class="form-group mb-3">
                            <button type="submit" name="password_update" class="btn btn-success w-100">Update Password</button>
                    </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<?php
include('includes/footer.php');
?>