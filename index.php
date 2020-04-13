<style type="text/css">
	*{
		font-family: quicksand;
	}
</style>
<link href='http://fonts.googleapis.com/css?family=Quicksand:400,700%7CPT+Serif:400,700' rel='stylesheet' type='text/css'>  
<?php 
	
	session_start();



	// session_destroy();
	include 'vendor/files/config.php';

	if (isset($_POST['register'])) {
		$username = htmlspecialchars($_POST['username']);
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars(mysqli_real_escape_string($link, $_POST['password']));

		$hash = password_hash($password, PASSWORD_DEFAULT);

		$save = mysqli_query($link, "INSERT INTO users (id, username, email, password) VALUES ('', '$username', '$email', '$hash')");

		if (mysqli_affected_rows($link) > 0) {
			$success = true;
		}else{
			$error = true;
		}

		
	}else if (isset($_POST['login'])) {
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars(mysqli_real_escape_string($link, $_POST['password']));

		$checkAccount = mysqli_query($link, "SELECT * FROM users WHERE email = '$email'");
		$checkFiles = mysqli_query($link, "SELECT * FROM files");
		$checkFilesArr = mysqli_fetch_array($checkFiles);
		

		if (mysqli_num_rows($checkAccount) === 1) {
			$rowData = mysqli_fetch_array($checkAccount);
			if (password_verify($password, $rowData['password'])) {
				$_SESSION['userSessionLogin'] = true;
				$_SESSION['email'] = $rowData['email'];
				$_SESSION['username'] = $rowData['username'];
				$_SESSION['user_id'] = $rowData['id'];
			}else{
				$errorLogin = true;
			}
		}else{
			$errorLoginAccount = true;
		}

		
	}else if (isset($_POST['upload'])) {
		$user_id = $_SESSION['user_id'];
		$namaFile = $_FILES['uploadFile']['name'];
		$ukuranFile = $_FILES['uploadFile']['size'];
		$error = $_FILES['uploadFile']['error'];
		$tmpName = $_FILES['uploadFile']['tmp_name'];

		$ektensiFile = ['jpeg', 'jpg', 'png', 'pdf', 'xls', 'doc', 'docx', 'ppt', 'zip', 'rar', 'txt', 'mp4'];
		$ektensiFiles = explode('.', $namaFile);
		$ektensiFiles = strtolower(end($ektensiFiles));

		if (!in_array($ektensiFiles, $ektensiFile)) {
			echo "<script src='js/sweetalert/sweetalert.min.js'></script>";
			echo "<script type='text/javascript'>
            setTimeout(function () {  
                swal({
                    icon: 'info',
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true,
                    className: 'red-bg',
                    buttons: false,
                    title: 'Ooooppss, Error',
                    text:  'Yang anda bukan type extensi yang kami sediakan!',
                    type: 'info',
                    timer: 2000,
                    showConfirmButton: false
                    });  
                    },10); 
                    window.setTimeout(function(){ 
                     window.location.replace('index');
                     } ,2000); 
            </script>";

			return false;
		}

		$namaFileBaru = uniqid();
		$namaFileBaru .= '.';
		$namaFileBaru .= $ektensiFiles;

		$saveData = mysqli_query($link, "INSERT INTO files (id, file, user_id) VALUES ('', '$namaFileBaru', '$user_id')");

		move_uploaded_file($tmpName, 'files/'.$namaFileBaru);

		if (mysqli_affected_rows($link) > 0) {
			$successUpload = true;
		}else{
			$errorUpload = true;
		}

		$files    = glob('files/*[".php", ".html"]');
		foreach ($files as $file) {
		    $lastModifiedTime   =filemtime($file);
		    $currentTime        =time();
		    $timeDiff           =abs($currentTime - $lastModifiedTime)/(60*60); // in hours
		    if(is_file($file) && $timeDiff > 10) // check if file is modified before 10 hours
		    unlink($file); // hapus file
		}

	}

 ?>
<!DOCTYPE html>
<html>
<head>
	<title>ItsZami - File Uploader</title>
	<!-- Mobile Specific Metas -->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="author" content="Zamzam Saputra">
	<meta name="keywords" content="Files Uploader, Img Upload Online, PDF Upload Online, Excel Upload Online, PPT Upload Online" />
	<meta name="description" content="Free unlimited upload your file and download your link file ">
	<meta name="og:image" content="images/icon.png" />
	<meta name="og:image:securel_url" content="images/icon.png" />
	<link rel="shortcut icon" href="images/icon.png" />
	<!-- Font-->
	<link rel="stylesheet" type="text/css" href="css/nunito-font.css">
	<link rel="stylesheet" type="text/css" href="fonts/material-design-iconic-font/css/material-design-iconic-font.min.css">
	<!-- Main Style Css -->
    <link rel="stylesheet" href="css/style.css"/>
    
</head>
<body>
	<div class="page-content">
		<div class="wizard-v5-content">
			<div class="wizard-form">
		        <form class="form-register" id="form-register" method="post" enctype="multipart/form-data">
		        	<div id="form-total">
		        		<!-- SECTION 1 -->
			          	<?php if (!isset($_SESSION['userSessionLogin'])): ?>
			          		  <h2>
			            	<span class="step-icon"><i class="zmdi zmdi-accounts-add"></i></span>
			            	<span class="step-text">Users Register</span>
			            </h2>
			            <section>
			                <div class="inner">
								<form method="post" action="">
									<div class="form-row">
										<div class="form-holder">
											<label for="username">Username</label>
											<input type="text" placeholder="ex: zamsyh" class="form-control" id="username" name="username" required="" minlength="3">
											<span><i class="zmdi zmdi-account"></i></span>
										</div>
										<div class="form-holder">
											<label for="email">Email</label>
											<input type="email" placeholder="ex: zamsyh@gmail.com" class="form-control" id="email" name="email" required="">
											<span><i class="zmdi zmdi-email"></i></span>
										</div>
									</div>
									<div class="form-row">
										<div class="form-holder form-holder-2">
											<label for="password">Password</label>
											<input type="password" placeholder="your password" class="form-control" id="password" name="password" required="" minlength="5">
											<span><i class="zmdi zmdi-key"></i></span>
										</div>
									</div>
									<div class="form-row">
										<div class="form-holder form-holder-2">
											<?php if (isset($success)): ?>
												<p style="color: white;">Register Successfully, now login and upload your files</p>
											<?php endif ?>
											<?php if (isset($error)): ?>
												<p style="color: white;">Register Failed, Something went wrong..</p>
											<?php endif ?>
										</div>

									</div>
									<div class="form-row">
										<div class="form-holder">
											<button type="submit" name="register" style="padding: 10px; border-radius: 5px; font-family: sans-serif; background-color: #3CB371; color: white; border: none; width: 40%;">Register Now</button>
										</div>
									</div>
								</form>
							</div>
			            </section>
			          	<?php endif ?>
						<!-- SECTION 2 -->
			            <?php if (!isset($_SESSION['userSessionLogin'])): ?>
			            	<h2>
			            	<span class="step-icon"><i class="zmdi zmdi-account-circle"></i></span>
			            	<span class="step-text">Users Login</span>
			            </h2>
			            <section>
			                <div class="inner">
								<form method="post" action="">
									<div class="form-row">
										<div class="form-holder">
											<label for="email">Email</label>
											<input type="email" placeholder="ex: zamsyh@gmail.com" class="form-control" id="email" name="email" required="">
											<span><i class="zmdi zmdi-email"></i></span>
										</div>
										<div class="form-holder">
											<label for="password">Password</label>
											<input type="password" class="form-control" id="password" name="password" placeholder="your password.." required="" minlength="5">
											<span><i class="zmdi zmdi-key"></i></span>
										</div>
									</div>
									<div class="form-row">
										<div class="form-holder form-holder-2">
											<?php if (isset($errorLogin)): ?>
												<p style="color: white;">Login failed, wrong password</p>
											<?php endif ?>
											<?php if (isset($errorLoginAccount)): ?>
												<p style="color: white;">Logn failed, email not found </p>
											<?php endif ?>
										</div>

									</div>
									<div class="form-row">
										<div class="form-holder">
											<button type="submit" name="login" style="padding: 10px; border-radius: 5px; font-family: sans-serif; background-color: #3CB371; color: white; border: none; width: 40%;">Login</button>
										</div>
									</div>
								</form>
							</div>
			            </section>
			            <?php endif ?>
			            <!-- SECTION 3 -->
			            <h2>
			            	<?php if (!isset($_SESSION['userSessionLogin'])): ?>
			            		<span class="step-icon"><i class="zmdi zmdi-cloud-upload"></i></span>
			            		<span class="step-text">Upload File</span>
			            	<?php endif ?>
			            	<?php if (isset($_SESSION['userSessionLogin'])): ?>
								<p></p>
			            	<?php endif ?>
			            </h2>
			            <section>
			               <div class="inner">
								<?php if (!isset($_SESSION['userSessionLogin'])): ?>
									<div class="form-row">
										<div class="form-holder">
											<h3>Authentication Required! <p>Anda harus login terlebih dahulu, sebelum upload file</p></h3>
											
										</div>
									</div>
								<?php endif ?>


								<?php if (isset($_SESSION['userSessionLogin'])): ?>
									<form method="post" action="" enctype="multipart/form-data">
									<p style="color: white; margin-left: 20px; margin-top: 1%;">Welcome, <?= $_SESSION['email'] ?></p>
									<div class="form-row">
										<div class="form-holder">
											<input type="file" name="uploadFile" id="upload" style="color: white;" required="">
										</div>
									</div>
									<div class="form-row">
										<p style="color: white; margin-left: 20px; margin-top: -3%;">Supported File : jpeg, jpg, png, pdf, xls, doc, docx, ppt, mp4</p>
									</div>
									<div class="form-row">
										<div class="form-holder">
											<?php 
												$user_id = $_SESSION['user_id'];
												$checkFiles = mysqli_query($link, "SELECT * FROM files ORDER BY id DESC");
												$checkFilesArr = mysqli_fetch_array($checkFiles);
												 ?>


											<?php if (isset($successUpload)): ?>
												<p style="color: white;">Successfully upoad, your link <br><a href="<?= 'files/'. $checkFilesArr['file'] ?>">http://localhost/allproject/project/files-uploader/files/<?= $checkFilesArr['file'] ?></a></p>
											<?php endif ?>
											<?php if (isset($errorLoginAccount)): ?>
												<p style="color: white;">Login failed, email not found </p>
											<?php endif ?>
										</div>
									</div>
									<div class="form-row">
										<div class="form-holder">
											<button type="submit" name="upload" style="padding: 10px; border-radius: 5px; font-family: sans-serif; background-color: #3CB371; color: white; border: none;  margin-left: 4%; margin-top: -5%;">Upload Now</button>
										</div>
									</div>
								</form>
								<?php endif ?>
							</div>
			            </section>
		        	</div>
		        </form>
			</div>
		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="js/jquery.steps.js"></script>
	<script src="js/main.js"></script>

	<!-- Ajax Users Register -->
</body>
</html>
