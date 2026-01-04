<?php
// ----------------- DATABASE CONNECTION -----------------
$conn = mysqli_connect("localhost","root","","furfusion");
if(!$conn){
    die("Database Connection Failed: ".mysqli_connect_error());
}
session_start();

// ----------------- HANDLE FORM SUBMISSIONS -----------------

// Add Pet
if(isset($_POST['add_pet'])){
    $sql = "INSERT INTO pets 
    (name,type,breed,age,gender,size,temperament,health_status,vaccinated)
    VALUES(
    '".$_POST['name']."','".$_POST['type']."','".$_POST['breed']."',
    ".$_POST['age'].",'".$_POST['gender']."','".$_POST['size']."',
    '".$_POST['temperament']."','".$_POST['health']."',
    '".$_POST['vaccinated']."')";
    mysqli_query($conn,$sql);
    $msg = "Pet Added Successfully!";
}

// Add Adopter
if(isset($_POST['add_adopter'])){
    mysqli_query($conn,"INSERT INTO adopters
    (name,phone,email,preferred_type,preferred_size,preferred_temperament)
    VALUES
    ('".$_POST['name']."','".$_POST['phone']."','".$_POST['email']."',
    '".$_POST['type']."','".$_POST['size']."','".$_POST['temp']."')");
    $msg = "Adopter Added Successfully!";
}

// Adoption
if(isset($_POST['adopt_pet'])){
    mysqli_query($conn,"INSERT INTO adoptions (pet_id,adopter_id,adoption_date)
    VALUES(".$_POST['pet'].",".$_POST['adopter'].",CURDATE())");

    mysqli_query($conn,"UPDATE pets SET adoption_status='Adopted'
    WHERE pet_id=".$_POST['pet']);
    $msg = "Adoption Successful!";
}

// Health Records
if(isset($_POST['add_health'])){
    mysqli_query($conn,"INSERT INTO health_records
    (pet_id,vaccination_date,treatment,next_checkup)
    VALUES(".$_POST['pet'].",'".$_POST['vac']."','".$_POST['treat']."','".$_POST['next']."')");
    $msg = "Health Record Saved!";
}

// Breeding
if(isset($_POST['plan_breeding'])){
    mysqli_query($conn,"INSERT INTO breeding
    (pet_male,pet_female,status,due_date,litter_prediction)
    VALUES(".$_POST['male'].",".$_POST['female'].",'Planned','".$_POST['due']."',".$_POST['litter'].")");
    $msg = "Breeding Planned!";
}

// ----------------- PAGE ROUTER -----------------
$page = $_GET['page'] ?? 'home';
?>

<!DOCTYPE html>
<html>
<head>
    <title>FurFusion</title>
</head>
<body>
<h1>FurFusion – Intelligent Pet Adoption & Breeding</h1>

<?php if(isset($msg)) { echo "<p style='color:green'>$msg</p>"; } ?>

<!-- Navigation Menu -->
<a href="furfusion.php?page=home">Home</a> | 
<a href="furfusion.php?page=add_pet">Add Pet</a> |
<a href="furfusion.php?page=search_pet">Search Pets</a> |
<a href="furfusion.php?page=adopters">Adopters</a> |
<a href="furfusion.php?page=adopt">Adopt</a> |
<a href="furfusion.php?page=breeding">Breeding</a> |
<a href="furfusion.php?page=health">Health Records</a> |
<a href="furfusion.php?page=genetic_check">Genetic Feasibility</a>
<hr>

<?php
switch($page){

// ----------------- HOME -----------------
case "home":
    echo "<h2>Welcome to FurFusion</h2>
    <p>Use the menu above to navigate the system.</p>";
break;

// ----------------- ADD PET -----------------
case "add_pet": ?>
<h2>Add Pet</h2>
<form method="POST">
Name:<input name="name"><br>
Type:<input name="type"><br>
Breed:<input name="breed"><br>
Age:<input name="age" type="number"><br>
Gender:<select name="gender"><option>Male</option><option>Female</option></select><br>
Size:<select name="size"><option>Small</option><option>Medium</option><option>Large</option></select><br>
Temperament:<input name="temperament"><br>
Health:<input name="health"><br>
Vaccinated:<select name="vaccinated"><option>Yes</option><option>No</option></select><br>
<button name="add_pet">Add Pet</button>
</form>
<?php break;

// ----------------- SEARCH PET -----------------
case "search_pet": ?>
<h2>Search Pets</h2>
<form method="GET">
<input type="hidden" name="page" value="search_pet">
Breed:<input name="breed">
Status:<select name="status">
<option></option>
<option>Available</option>
<option>Adopted</option>
</select>
<button>Search</button>
</form>
<?php
$where = "WHERE 1";
if(!empty($_GET['breed'])) $where .= " AND breed='".$_GET['breed']."'";
if(!empty($_GET['status'])) $where .= " AND adoption_status='".$_GET['status']."'";
$q = mysqli_query($conn,"SELECT * FROM pets $where");
echo "<h3>Results:</h3>";
while($row = mysqli_fetch_assoc($q)){
    echo $row['pet_id']." - ".$row['name']." - ".$row['breed']." - ".$row['adoption_status']."<br>";
}
break;

// ----------------- ADD/VIEW ADOPTERS -----------------
case "adopters": ?>
<h2>Add Adopter</h2>
<form method="POST">
Name:<input name="name"><br>
Phone:<input name="phone"><br>
Email:<input name="email"><br>
Preferred Type:<input name="type"><br>
Preferred Size:<input name="size"><br>
Preferred Temperament:<input name="temp"><br>
<button name="add_adopter">Add</button>
</form>
<h3>All Adopters:</h3>
<?php
$q = mysqli_query($conn,"SELECT * FROM adopters");
while($a = mysqli_fetch_assoc($q)){
    echo $a['adopter_id']." - ".$a['name']."<br>";
}
break;

// ----------------- ADOPTION -----------------
case "adopt": ?>
<h2>Adopt a Pet</h2>
<form method="POST">
Pet ID:<input name="pet"><br>
Adopter ID:<input name="adopter"><br>
<button name="adopt_pet">Adopt</button>
</form>
<?php
$q = mysqli_query($conn,"SELECT * FROM adoptions");
echo "<h3>Adoption Records:</h3>";
while($a = mysqli_fetch_assoc($q)){
    echo "Pet: ".$a['pet_id']." | Adopter: ".$a['adopter_id']." | Date: ".$a['adoption_date']."<br>";
}
break;

// ----------------- BREEDING -----------------
case "breeding": ?>
<h2>Breeding</h2>
<form method="POST">
Male Pet ID:<input name="male"><br>
Female Pet ID:<input name="female"><br>
Due Date:<input type="date" name="due"><br>
Litter Prediction:<input name="litter" type="number"><br>
<button name="plan_breeding">Plan Breeding</button>
</form>
<h3>Breeding Records:</h3>
<?php
$q = mysqli_query($conn,"SELECT * FROM breeding");
while($b = mysqli_fetch_assoc($q)){
    echo "ID: ".$b['breeding_id']." - Male: ".$b['pet_male']." - Female: ".$b['pet_female']." - Status: ".$b['status']."<br>";
}
break;

// ----------------- HEALTH -----------------
case "health": ?>
<h2>Health Records</h2>
<form method="POST">
Pet ID:<input name="pet"><br>
Vaccination Date:<input type="date" name="vac"><br>
Treatment:<input name="treat"><br>
Next Checkup:<input type="date" name="next"><br>
<button name="add_health">Add Health Record</button>
</form>
<h3>All Health Records:</h3>
<?php
$q = mysqli_query($conn,"SELECT * FROM health_records");
while($r = mysqli_fetch_assoc($q)){
    echo $r['record_id']." - Pet: ".$r['pet_id']." - Next Checkup: ".$r['next_checkup']."<br>";
}
break;

// ----------------- GENETIC FEASIBILITY -----------------
case "genetic_check": ?>
<h2>Genetic Feasibility Checker</h2>
<form method="POST">
Breed Ratio:
<select name="ratio">
<option>50-50</option>
<option>70-30</option>
<option>20-80</option>
</select>
<button name="check_ratio">Check</button>
</form>
<?php
if(isset($_POST['check_ratio'])){
    $ratio = $_POST['ratio'];
    if($ratio=="50-50") echo "✔ Possible";
    elseif($ratio=="70-30") echo "⚠ Rare but Possible";
    else echo "❌ Not Genetically Possible";
}
break;

default:
echo "<h2>Welcome to FurFusion</h2>
<p>Use the menu above to navigate the system.</p>";
}
?>
</body>
</html>
