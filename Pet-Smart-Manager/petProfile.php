<?php //pornim sesiunea
session_start(); ?>

<?php
if ($_SESSION["logged_in_user"] == 0) {
    header("location: index.php");
    exit();
}
if ($_SESSION["login_user"] != $_GET["user"]) {
    header("location: index.php");
    exit();
}
if (isset($_GET["value"])) {
    $pet_id = $_GET["value"];
}
$pdo = new PDO("sqlite:database.db");

if (isset($_POST["add-friend-request"])) {
    $received_user = $_POST["owner-name"];
    $received_pet = $_POST["pet-name"];

    $stmt = $pdo->prepare(
        "SELECT * FROM Pets WHERE owner = :owner AND petname = :petname"
    );
    $stmt->execute([
        ":owner" => $_POST["owner-name"],
        ":petname" => $_POST["pet-name"],
    ]);
    $result = $stmt->fetchAll();

    if (count($result) > 0) {
        $stmt = $pdo->prepare(
            "SELECT * FROM Friends WHERE user1 = :user1 AND pet1 = :pet1 AND user2 = :user2 AND pet2 = :pet2"
        );
        $stmt->execute([
            ":user1" => $_SESSION["login_user"],
            ":pet1" => $pet_id,
            ":user2" => $received_user,
            ":pet2" => $received_pet,
        ]);
        $result = $stmt->fetchAll();
        if (count($result) == 0) {
            $stmt = $pdo->prepare(
                "INSERT INTO Friends (user1, pet1, user2, pet2) VALUES (:user1, :pet1, :user2, :pet2)"
            );
            $stmt->execute([
                ":user1" => $_SESSION["login_user"],
                ":pet1" => $pet_id,
                ":user2" => $received_user,
                ":pet2" => $received_pet,
            ]);
        }
    }
}

if (!empty($_POST["deletefriend"]) && is_array($_POST["deletefriend"])) {
    foreach ($_POST["deletefriend"] as $id => $petowner) {
        $pet_idd = explode("-", $id)[0];
        $user_id = explode("-", $id)[1];

        $stmt = $pdo->prepare(
            "DELETE FROM Friends WHERE user1 = :user1 AND pet1 = :pet1 AND user2 = :user2 AND pet2 = :pet2"
        );
        $stmt->execute([
            ":user1" => $_SESSION["login_user"],
            ":pet1" => $pet_id,
            ":user2" => $user_id,
            ":pet2" => $pet_idd,
        ]);
    }
}

if (!empty($_POST["deletefile"]) && is_array($_POST["deletefile"])) {
    echo "<script>console.log('aaaa')</script>";
    foreach ($_POST["deletefile"] as $id => $file) {
        unlink("uploads/$id");
        echo "<script>console.log('$file deleted')</script>";
    }
    header("Refresh:0");
}

    if(isset($_POST['finaledit']))
        {  
            $dir = scandir('uploads/');
            function debug_to_console($data) {
                $output = $data;
                if (is_array($output))
                    $output = implode(',', $output);
            
                echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
            }
            foreach ($dir as $file){
                $name = basename($file);
                $check_name = "." .$_SESSION['login_user']. "-" ."$_GET[value]";
                //debug_to_console($check_name);
                if (strpos($name,$check_name) !== false){
                    $new_name = str_replace("$_GET[value]",$_POST['pettnameform'],$name);
                    $new_new_name= 'uploads/' . $name;
                    $new_name_path= 'uploads/' .$new_name;
                    if(rename($new_new_name,$new_name_path)){
                        debug_to_console("Files renamed sucessfully");
                    }else{
                        debug_to_console("Files could not be renamed");
                    }
                }
            }
            if (!empty($_FILES["file"]["tmp_name"])){
                $statement = $pdo->prepare("UPDATE Pets SET petname = :petname, image = :image WHERE owner = :owner AND petname = :currentpet");
                $statement->execute(array(
                    ':owner' => $_SESSION['login_user'],
                    ':currentpet' => $_GET['value'],
                    ':petname' => $_POST['pettnameform'],
                    ':image' => file_get_contents($_FILES["file"]["tmp_name"])
                ));
            }
            else{
                $statement = $pdo->prepare("UPDATE Pets SET petname = :petname WHERE owner = :owner AND petname = :currentpet");
                $statement->execute(array(
                    ':owner' => $_SESSION['login_user'],
                    ':currentpet' => $_GET['value'],
                    ':petname' => $_POST['pettnameform']
                ));
            }
            $statement = $pdo->prepare("UPDATE Friends SET pet1 = :petname WHERE user1 = :owner");
            $statement->execute(array(
                ':owner' => $_SESSION['login_user'],
                ':petname' => $_POST['pettnameform']
            ));
            $statement = $pdo->prepare("UPDATE Calendar SET petname = :petname WHERE username = :owner");
            $statement->execute(array(
                ':owner' => $_SESSION['login_user'],
                ':petname' => $_POST['pettnameform']
            ));
            $statement = $pdo->prepare("UPDATE Restrictions SET petname = :petname WHERE username = :owner");
            $statement->execute(array(
                ':owner' => $_SESSION['login_user'],
                ':petname' => $_POST['pettnameform']
            ));
            header("location: dashboard.php");
    }
               
if (isset($_POST["send-event"])) {
    $year = date("Y", strtotime($_POST["event-date"]));
    $month = date("m", strtotime($_POST["event-date"]));
    $day = date("d", strtotime($_POST["event-date"]));

    $event_type = $_POST["event-type"];
    $statement = $pdo->prepare(
        "INSERT INTO Calendar (username, year, month, day, text, petname, type) VALUES (:username, :year, :month, :day, :text, :petname, :type)"
    );
    $statement->execute([
        ":username" => $_SESSION["login_user"],
        ":year" => $year,
        ":month" => $month,
        ":day" => $day,
        ":text" => $_POST["event-desc"],
        ":petname" => $_GET["value"],
        ":type" => $event_type,
    ]);
}
if (!empty($_POST["delete"]) && is_array($_POST["delete"])) {
    foreach ($_POST["delete"] as $id => $yyyymmdd) {
        $year = substr($id, 0, 4);
        $month = substr($id, 5, 2);
        $day = substr($id, 8, 2);
        $statement = $pdo->prepare(
            "DELETE FROM Calendar WHERE username = :username AND year = :year AND month = :month AND day = :day AND petname = :petname"
        );
        $statement->execute([
            ":username" => $_SESSION["login_user"],
            ":year" => $year,
            ":month" => $month,
            ":day" => $day,
            ":petname" => $_GET["value"],
        ]);
    }
}

if (!empty($_POST["delete2"]) && is_array($_POST["delete2"])) {
    foreach ($_POST["delete2"] as $id => $restrictie) {
        $statement = $pdo->prepare(
            "DELETE FROM Restrictions WHERE username = :username AND restriction = :restriction AND petname = :petname"
        );
        $statement->execute([
            ":username" => $_SESSION['login_user'],
            ":restriction" => $id,
            ":petname" => $_GET["value"],
        ]);
    }
}

// Set your timezone!!
date_default_timezone_set("Europe/Athens");
//if value is set in the URL, display it

// Get prev & next month
if (isset($_GET["ym"])) {
    $ym = $_GET["ym"];
} else {
    // This month
    $ym = date("Y-m");
}
//split ym into year and month
$year = substr($ym, 0, 4);
$month = substr($ym, 5, 2);
// Check format
$timestamp = strtotime($ym . "-01"); // the first day of the month
if ($timestamp === false) {
    $ym = date("Y-m");
    $timestamp = strtotime($ym . "-01");
}

// Today (Format:2018-08-8)
$today = date("Y-m-j");

// Title (Format:August, 2018)
$title = date("F, Y", $timestamp);

// Create prev & next month link
$prev = date("Y-m", strtotime("-1 month", $timestamp));
$next = date("Y-m", strtotime("+1 month", $timestamp));

// Number of days in the month
$day_count = date("t", $timestamp);

// 1:Mon 2:Tue 3: Wed ... 7:Sun
$str = date("N", $timestamp);

// Array for calendar
$weeks = [];
$week = "";

// Add empty cell(s)
$week .= str_repeat("<td></td>", $str - 1);
$statement = $pdo->prepare(
    "SELECT * FROM Calendar WHERE username = :username AND petname = :petname"
);

$statement->execute([
    ":username" => $_SESSION["login_user"],
    ":petname" => $pet_id,
]);
$rows = [];
while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
    $rows[] = [
        "year" => $row["year"],
        "month" => $row["month"],
        "day" => $row["day"],
        "text" => $row["text"],
        "type" => $row["type"],
    ];
}

for ($day = 1; $day <= $day_count; $day++, $str++) {
    $date = $ym . "-" . $day;

    if ($today == $date) {
        $week .= '<td class="today">';
    } else {
        $week .= "<td>";
    }
    $week .= "<div>";
    $week .= "<p>";
    $week .= $day;
    $week .= "</p>";
    if ($day < 10) {
        $day = "0" . $day;
    }
    $week .=
        '<form method="post">
    <button type="submit" name="delete[' .
        $year .
        "-" .
        $month .
        "-" .
        $day .
        ']" class="btn-link-delete">Delete</button> </form>';
    foreach ($rows as $row) {
        if (
            $row["year"] == $year &&
            $row["month"] == $month &&
            $row["day"] == $day
        ) {
            if ($row["type"] == "Medical") {
                $week .=
                    '<div class="medical-event-text">' .
                    htmlspecialchars($row["text"]) .
                    "</div>";
            } elseif ($row["type"] == "Feeding") {
                $week .=
                    '<div class="feeding-event-text">' .
                    htmlspecialchars($row["text"]) .
                    "</div>";
            } elseif ($row["type"] == "Life Event") {
                $week .=
                    '<div class="life-event-text">' . htmlspecialchars($row["text"]) . "</div>";
            } elseif ($row["type"] == "Other") {
                $week .=
                    '<div class="other-event-text">' . htmlspecialchars($row["text"]) . "</div>";
            }
        }
    }

    $week .= "</div>";
    $week .= "</td>";

    // Sunday OR last day of the month
    if ($str % 7 == 0 || $day == $day_count) {
        // last day of the month
        if ($day == $day_count && $str % 7 != 0) {
            // Add empty cell(s)
            $week .= str_repeat("<td></td>", 7 - ($str % 7));
        }

        $weeks[] = "<tr>" . $week . "</tr>";

        $week = "";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet-Profile</title>
    <link rel = "icon" href = 
      "https://cdn3.iconfinder.com/data/icons/animals-114/48/dog_animal_pet_canine-512.png" 
        type = "image/x-icon">
    <link rel="stylesheet" href="stylesAll.css">
    <link rel="stylesheet" href="styles_petProfile.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <!--Navbar-->
    <nav class="navbar">
         <div class="navbar__container">
            <a class="navbar__logo"> <i class="fas fa-cat"></i>PSM </a>
            <div class="navbar__toggle" id="mobile-menu">
               <span class="bar"></span>
               <span class="bar"></span>
               <span class="bar"></span>
            </div>
            <ul class="navbar__menu">
               <li class="navbar__item">
                  <a href="./index.php" class="navbar__links">Home</a>
               </li>
               <?php if ($_SESSION["logged_in_user"] == "1") {
                   echo "<li class='navbar__item'>
                        <a href='./dashboard.php' class='navbar__links'>Dashboard</a>
                     </li>";
               } ?>
               <li class="navbar__item">
                  <a href="./About.php" class="navbar__links">About</a>
               </li>
               <li class="navbar__item">
                  <a href="./Contact.php" class="navbar__links">Contact Us</a>
               </li>
               <li>
               <?php echo "<li class='navbar__button'><form action='logOut.php' method='post'>
                  <button class='buttonlog' name = 'Logout' type = 'submit'>Log Out</button></form></li>"; ?>
                </li>
            </ul>
         </div>
      </nav>
    <!--a section to display data about the pet-->
    <div class="profile-container">
        <div class="profile-details">
                    <div class="col1">
                        <?php
                        $statement = $pdo->prepare(
                            "SELECT * FROM Pets WHERE petname = :pet_id"
                        );
                        $statement->bindValue(":pet_id", $pet_id);
                        $statement->execute();
                        $row = $statement->fetch();
                        echo '<img src="data:image/png;base64,' .
                            base64_encode($row["image"]) .
                            '" class="pd-image" alt="pet-image">';
                        ?>
                            <?php echo "<h3>" . htmlspecialchars($pet_id) . "</h3>"; ?>
                            <button name = "editPet" id="edit-pet">Edit</button>
                            <form method = "post">
                                <button type ="submit" name ="deletePet">Delete</button>
                                <?php
                                    if (isset($_POST["deletePet"])){
                                        function debug_to_console($data) {
                                            $output = $data;
                                            if (is_array($output))
                                                $output = implode(',', $output);
                                        
                                            echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
                                        }
                                        $dir = scandir('uploads/');
                                        foreach ($dir as $file){
                                            $name = basename($file);
                                            $check_name = "." .$_SESSION['login_user']. "-" ."$_GET[value]";
                                            if (strpos($name,$check_name) !== false){
                                                $new_name_path= 'uploads/' .$name;
                                                if (unlink($new_name_path)){
                                                    debug_to_console("Files removed");
                                                }else{
                                                    debug_to_console("Files could not be removed");
                                                }
                                            }
                                        }
                                        $statement = $pdo->prepare(
                                            "DELETE FROM Pets WHERE owner = :username AND petname = :petname"
                                        );
                                        $statement->execute([
                                            ":username" => $_SESSION['login_user'],
                                            ":petname" => $_GET["value"],
                                        ]);

                                        $statement = $pdo->prepare(
                                            "DELETE FROM Calendar WHERE username = :username AND petname = :petname"
                                        );
                                        $statement->execute([
                                            ":username" => $_SESSION['login_user'],
                                            ":petname" => $_GET["value"],
                                        ]);

                                        $statement = $pdo->prepare(
                                            "DELETE FROM Restrictions WHERE username = :username AND petname = :petname"
                                        );
                                        $statement->execute([
                                            ":username" => $_SESSION['login_user'],
                                            ":petname" => $_GET["value"],
                                        ]);
                                        $statement = $pdo->prepare(
                                            "DELETE FROM Friends WHERE user1 = :username AND pet1 = :petname"
                                        );
                                        $statement->execute([
                                            ":username" => $_SESSION['login_user'],
                                            ":petname" => $_GET["value"],
                                        ]);
                                        echo ("<script>location.href='dashboard.php'</script>");
                                    }
                                ?>
                            </form>
                            <a href = "rss.xml" target = "_blank"> 
                                <img src="images/Feed-icon.svg.png" style="width: 25px;" alt="rss-icon">
                            </a>
                            <?php   //sharing to twitter
                                $statement = $pdo->prepare(
                                    "SELECT * FROM Calendar WHERE petname = :petname AND username = :username AND type = :type"
                                );
                                $statement->execute([
                                    ":petname" => $pet_id,
                                    ":username" => $_GET['user'],
                                    ":type" => "Life Event",
                                ]);    
                                $message="I would like to share with you these events of my pet," .$_GET["value"]. "!  ";
                                while ($row = $statement ->fetch(PDO::FETCH_ASSOC)){
                                    $message .= $row['text'] . ": ";
                                    $message .= $row['day'] . "-";
                                    $message .= $row['month'] . "-";
                                    $message .= $row['year'] ."  ";
                                }
                                $redirect = "https://twitter.com/intent/tweet?original_referer=
                                http%3A%2F%2Ffiddle.jshell.net%2F_display%2F&text=" .$message. "&url
                                =URL";
                            ?>
                            <a href = "<?php echo $redirect ?>" target = "_blank" onclick="return Share.me(this)"> 
                                <img src="images/Twitter-Logo.png" style="width: 30px;" alt="rss-icon">
                            </a>
                    </div>
                    <div class="col2">
                        <button name="edit" class="profile-button" id="see-info">See Info</button>
                        
                        <div class="dropdown">
                            <button onclick="myFunction()" class="dropbtn">See Friends</button>
                            <div id="myDropdown" class="dropdown-content">
                                <?php
                                $statement = $pdo->prepare(
                                    "SELECT user2, pet2 FROM Friends WHERE user1=:user1 AND pet1 = :pet1"
                                );
                                $statement->execute([
                                    ":user1" => $_SESSION["login_user"],
                                    ":pet1" => $pet_id,
                                ]);
                                $rows = $statement->fetchAll();
                                foreach ($rows as $row) {
                                    echo "<a href='petProfile.php?pet_id=" .
                                        htmlspecialchars($row["pet2"]) .
                                        "'>" .
                                        htmlspecialchars($row["pet2"]) .
                                        " (owner: " .
                                        htmlspecialchars($row["user2"]) .
                                        ")</a><br>";
                                    echo "<form method='post'><button name='deletefriend[$row[pet2]-$row[user2]]' type='submit'>Delete</button></form>";
                                }
                                ?>
                            </div>
                        </div>
                        <button class="profile-button" id="add-friend">Add a Friend</button>
                        </div>
        </div>
    </div>       
<?php
$web_url = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
if (strpos($web_url, "&") !== false) {
    $web_url = str_replace("&", "&amp;", $web_url);
}

$str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$str .= "<rss version=\"2.0\">\n";
$str .= "<channel>\n";
$str .= "<title>Important Moments for " . $pet_id . "</title>\n";
$str .= "<link> $web_url </link>\n";
$str .=
    "<description>Here you can see all important moments that " .
    $pet_id .
    " has had</description>\n";
$str .= "<language >en-US </language>\n";
$pdo = new PDO("sqlite:database.db");

$statement = $pdo->prepare(
    "SELECT * FROM Calendar WHERE type = 'Life Event' AND petname = :petname AND username = :owner ORDER BY year, month, day desc"
);
$statement->bindValue(":petname", $pet_id);
$statement->bindValue(":owner", $_SESSION["login_user"]);
$statement->execute();
$rows = $statement->fetchAll();

foreach ($rows as $row) {
    $str .= "<item>\n";
    $str .=
        "<title>" .
        $row["year"] .
        "-" .
        $row["month"] .
        "-" .
        $row["day"] .
        "</title>\n";
    $str .= "<description>" . $row["text"] . "</description>\n";
    $str .= "<link>" . htmlspecialchars($web_url) . "</link>\n";
    $str .= "</item>\n";
}
$str .= "</channel>\n";
$str .= "</rss>\n";
file_put_contents("rss.xml", $str);
?>
               

    <div class="calendar" >
    <div class="containerr">
        <div class="calendar-header">
        <ul class="list-inline">
            <li id="top-of-calendar"class="list-inline-item"><a href="?ym=<?= $prev ?>&value=<?= $pet_id ?>&user=<?= $_SESSION[
    "login_user"
] ?>#topofpage" class="btn btn-link">&lt; prev</a></li>
            <li class="list-inline-item"><span class="title"><?= $title ?></span></li>
            <li class="list-inline-item"><a href="?ym=<?= $next ?>&value=<?= $pet_id ?>&user=<?= $_SESSION[
    "login_user"
] ?>#topofpage" class="btn btn-link">next &gt;</a></li>
            
        </ul>
        <div class="calendar-button">
            <button class="profile-button" id="add-event">Add Event</button>
        </div>
        </div>
        <div style="overflow-x:auto;">
            <table>
            <thead>
                <tr>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                    <th>Sunday</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($weeks as $week) {
                    echo $week;
                } ?>
            </tbody>
            </table>
        </div>
    </div>
    </div>
    <div class="add-friend-modal">
         <div class="modal-content">
            <div class="close-friend">+</div>
            <p class="simple-text">Add a friend to your list</p>
            <form method="post">
               <input type="text" placeholder="Owner name" name="owner-name" required/><br />
               <input type="text" placeholder="Pet name" name="pet-name" required/><br />
               <input type="submit" name="add-friend-request" value="Add Friend To List">
            </form>
         </div>
    </div>
    <div class="bg-modal">
         <div class="modal-content">
            <form method="post">
            <div class="close">+</div>
            <p class="simple-text">Add an event!</p>
            <br />
            <input id="event-desc" type="text" placeholder="Description" name="event-desc" required /><br />
            <input type="date" id="event-date-id" name="event-date"
                min="2020-01-01" max="2050-12-31" required>
                <br />
            <select name="event-type" id="event-type-id">
                <option value="Life Event">Life Event</option>
                <option value="Medical">Medical</option>
                <option value="Feeding">Feeding</option>          
                <option value="Other">Other</option>
            </select>
            <label for="event-type-id">Type</label>
            <br />
            <input type="submit" name="send-event" value="Add Event"><br />
            </form>
         </div>
      </div>
      
      <div class="edit-modal">
         <div class="edit-content">
            <div class="close" id = "close2">+</div>
            <p class="simple-text">Edit pet!</p>
            <form method="post" enctype='multipart/form-data'>
               <input id="nume" type="text" placeholder="Name" name="pettnameform" required/><br />
               <input
                  id="imagine"
                  type="file"
                  accept="image/*"
                  name="file"
                  />
                  <?php 
                  ?>
               <input type="submit" name="finaledit" value="Edit" />
            </form>
         </div>
      </div>

      <div class="Info">
         <div class="Info-content">
            <div class="close-Info">+</div>
            <div class="Info-header">
                <div class="Restrictions-header">
                    <h3>Restrictions</h3>
                        <form method = "post">
                            <input id="restri-text" type="text" style="width: 135px" placeholder="restriction" name="restri-text" />
                            <input type="submit" name="final" style="display:none;">
                            <?php
                                $pdo = new PDO('sqlite:database.db'); 
                                if (isset($_POST['final'])){
                                    //check if the restriction already exists
                                    $statement = $pdo->prepare(
                                        "SELECT * FROM Restrictions WHERE username = :user_name AND petname = :pet_name AND restriction = :restr"
                                    );
                                    $statement -> execute([
                                        ":user_name" => $_GET["user"],
                                        ":pet_name" => $_GET["value"],
                                        ":restr" => $_POST["restri-text"]
                                    ]);
                                    $result = $statement->fetchAll();
                                    if (count($result) > 0){
                                        echo "<script>alert('Restriction already exists');</script>";
                                    }else{
                                        $statement = $pdo->prepare("INSERT INTO Restrictions (username, petname, restriction) VALUES (:user_name, :pet_name, :restr)");
                                        $statement ->execute(array(
                                            ":user_name" => $_GET["user"],
                                            ":pet_name" => $_GET["value"],
                                            ":restr" => $_POST["restri-text"]
                                        ));
                                    }
                                }
                                $statement = $pdo->prepare(
                                    "SELECT * FROM Restrictions WHERE username = :user_name AND petname = :pet_name"
                                );
                                $statement -> execute([
                                    ":user_name" => $_GET["user"],
                                    ":pet_name" => $_GET["value"]
                                ]);
                                $result = $statement->fetchAll();
                                foreach ($result as $row) {
                                    echo '<p>'.htmlspecialchars($row['restriction']).' </p>';
                                    echo '<button type ="submit" style = "float: right; "name ="delete2[' .$row['restriction'].
                                    ']">Delete</button><br / >';
                                }
                            ?>
                        </form>
                    
                </div>
                <div class="Medical-history-header">
                    <h3>Medical History</h3>
                    <?php
                        $pdo = new PDO('sqlite:database.db');
                        $statement = $pdo->prepare(
                            "SELECT * FROM Calendar WHERE username = :user_name AND petname = :pet_name AND type = :type"
                        );
                        $statement -> execute([
                            ":user_name" => $_GET["user"],
                            ":pet_name" => $_GET["value"],
                            ":type" => "Medical"
                        ]);
                        $result = $statement->fetchAll();
                        foreach ($result as $row) {
                            echo '<p>'.$row['text'].' </p>';
                             echo '<p>'.$row['day'].'-</p>';
                             echo '<p>'.$row['month'].'-</p>';
                             echo '<p>'.$row['year'].'</p> <br / >';
                        }
                    ?>
                </div>
            </div>
         </div>
      </div>
    
        <form method="post" enctype="multipart/form-data">
             <input type="file" name="fileToUpload" id="fileToUpload" required>
             <input type="submit" value="Upload" name="submit">
        </form>
    <?php if (isset($_POST["submit"])) {
        $target_dir = "uploads/.$_SESSION[login_user]" . "-" . $_GET["value"];
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        if (file_exists($target_file)) {
            echo "This file already exists";
            $uploadOk = 0;
        }
        if (
            $imageFileType != "jpg" &&
            $imageFileType != "png" &&
            $imageFileType != "jpeg" &&
            $imageFileType != "gif" &&
            $imageFileType != "mp3" &&
            $imageFileType != "mp4"
        ) {
            echo "Types of files allowed: jpg, jpeg, png, gif, mp3, mp4";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (
                move_uploaded_file(
                    $_FILES["fileToUpload"]["tmp_name"],
                    $target_file
                )
            ) {
                echo "The file " .
                    basename($_FILES["fileToUpload"]["name"]) .
                    " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } ?>
    <div class="gallery">
        <div class="heading">
            <h3>Photos</h3>
        </div>
        <div class="box">
                <?php
                $dir = "uploads/";
                $files = scandir($dir);
                $current_user =
                    "." . "$_SESSION[login_user]" . "-" . "$_GET[value]";
                foreach ($files as $file) {
                    if (
                        strpos($file, ".png") ||
                        strpos($file, ".jpg") ||
                        strpos($file, ".jpeg") ||
                        strpos($file, ".gif")
                    ) {
                        if (str_starts_with($file, $current_user)) {
                            echo "<div class='photos'>";
                            echo "<img src='$dir/$file' alt='$file' />";
                            echo "</div>";
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='delete' value='$file'>";
                            echo "<input type='submit' value='^^Delete^^' name='deletefile[$file]'>";
                            echo "</form>";
                        }
                    }
                }
                ?>
                <?php
                echo "<h3>Audio</h3>";
                $dir = "uploads/";
                $files = scandir($dir);
                $current_user =
                    "." . "$_SESSION[login_user]" . "-" . "$_GET[value]";
                foreach ($files as $file) {
                    if (
                        strpos($file, ".mp3") &&
                        str_starts_with($file, $current_user)
                    ) {
                        echo "<div class='audio'>";
                        echo "<audio controls>";
                        echo "<source src='$dir/$file' type='audio/mpeg'>";
                        echo "</audio>";
                        echo "</div>";
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='delete' value='$file'>";
                        echo "<input type='submit' value='^^Delete^^' name='deletefile[$file]'>";
                        echo "</form>";
                    }
                }
                ?>
                <?php
                echo "<h3>Videos</h3>";

                $dir = "uploads/";
                $files = scandir($dir);
                $current_user =
                    "." . "$_SESSION[login_user]" . "-" . "$_GET[value]";

                foreach ($files as $file) {
                    if (
                        strpos($file, ".mp4") &&
                        str_starts_with($file, $current_user)
                    ) {
                        echo "<div class='video'>";
                        echo "<video width='320' height='240' controls>";
                        echo "<source src='$dir/$file' type='video/mp4'>";
                        echo "Your browser does not support the video tag.";
                        echo "</video>";
                        echo "</div>";
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='delete' value='$file'>";
                        echo "<input type='submit' value='^^Delete^^' name='deletefile[$file]'>";
                        echo "</form>";
                    }
                }
                ?>
        </div>
    </div>
    <?php  ?>
    <div id="footer">
        <p>PET SMART MANAGER 2022</p>
    </div>
      
    <script>
        const menu = document.querySelector("#mobile-menu");
        const menuLinks = document.querySelector(".navbar__menu");
    
        menu.addEventListener("click", function () {
          menu.classList.toggle("is-active");
          menuLinks.classList.toggle("active");
        });
      </script>
      <script>
         document.getElementById("add-event").addEventListener("click", function () {
         document.querySelector(".bg-modal").style.display = "flex";
         });
         document.querySelector(".close").addEventListener("click", function () {
         document.querySelector(".bg-modal").style.display = "none";
         });
         
      </script>
      <script>
         document.getElementById("see-info").addEventListener("click", function () {
         document.querySelector(".Info").style.display = "flex";
         });
         document.querySelector(".close-Info").addEventListener("click", function () {
         document.querySelector(".Info").style.display = "none";
         });
      </script>
        <script>
            var el = document.getElementById('edit-pet');
            if (el){
            document.getElementById("edit-pet").addEventListener("click", function () {
            document.querySelector(".edit-modal").style.display = "flex";
            });
            document.getElementById("close2").addEventListener("click", function () {
            document.querySelector(".edit-modal").style.display = "none";
            });
        }
      </script>
      <script>
document.querySelector(".close-friend").addEventListener("click", function () {
         document.querySelector(".add-friend-modal").style.display = "none";
         });
         document.getElementById("add-friend").addEventListener("click", function () {
         document.querySelector(".add-friend-modal").style.display = "flex";
         });
      </script>
      
      <script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
   </script>
  }
} 
   </script>
   <script>
        /* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
    </script>
</body>
</html>
