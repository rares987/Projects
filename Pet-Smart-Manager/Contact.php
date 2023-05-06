<?php //pornim sesiunea
      session_start();
      if (!isset($_SESSION['fail_to_login']))
      {
        $_SESSION['fail_to_login'] = '0';
      }
      if (!isset($_SESSION['logged_in_user']))
      {
        $_SESSION['logged_in_user'] = '0';
      }
      if (!isset($_SESSION['login_user']))
      {
        $_SESSION['login_user'] = '0';
      }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact</title>
        <link rel = "icon" href = 
      "https://cdn3.iconfinder.com/data/icons/animals-114/48/dog_animal_pet_canine-512.png" 
        type = "image/x-icon">
        <link rel="stylesheet" href="stylesAll.css">
        <link rel="stylesheet" href="styles_contact.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'> 
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;700&display=swap" rel="stylesheet">
      </head>
<body>
    <?php   //register method
        $dupUser=0;
        $pdo = new PDO('sqlite:database.db');
        if(isset($_POST['submitRegister']))
            {  
              if (empty(htmlspecialchars($_POST['password'])) || empty(htmlspecialchars($_POST['password'])) || empty(htmlspecialchars($_POST['username']))){ //verificam daca un camp este gol
                echo "<div class='isa_error' id='warning'>
                      <i class='fa fa-times-circle'></i>
                      Nice one, try again!
                      </div>
                      <script>
                      setTimeout(function(){
                        document.getElementById('warning').style.display = 'none';
                        },3000);
                        </script>";
              } 
              else if (htmlspecialchars($_POST['password']) !== htmlspecialchars($_POST['Repassword'])){   //parola e diferita de reconfirm parola
                        echo "<div class='isa_error' id='warning'>
                        <i class='fa fa-times-circle'></i>
                        Passwords are not the same.
                        </div>
                        <script>
                        setTimeout(function(){
                        document.getElementById('warning').style.display = 'none';
                        },3000);
                        </script>";
                }
                else{ //daca parolele sunt aceeleasi
                    $statement = $pdo->prepare(
                        "SELECT * FROM Users"
                    );
                    
                    $statement->execute();
                    
                    while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                        if (htmlspecialchars($row['username']) === htmlspecialchars($_POST['username'])){ //daca se gaseste un username cu user la fel =>eroare
                        echo "<div class='isa_error' id='warning'>
                        <i class='fa fa-times-circle'></i>
                        Username already exists.
                        </div>
                        <script>
                        setTimeout(function(){
                            document.getElementById('warning').style.display = 'none';
                            },3000);
                            </script>";
                        $dupUser=1;
                        }
                        
                    }
                    if ($dupUser === 0){  //daca nu sunt useri cu username dublu insereaza in database
                        $statement = $pdo->prepare(
                        "INSERT INTO Users (username, password) VALUES (:username, :password)"
                        );
                        $statement->execute([
                            ":username" => $_POST["username"],
                            ":password" => password_hash($_POST["password"], PASSWORD_DEFAULT)
                        ]);
                        echo "<div class='isa_success' id='succes'>
                        <i class='fa fa-times-circle'></i>
                        Account created successfully!
                        </div>
                        <script>
                        setTimeout(function(){
                            document.getElementById('succes').style.display = 'none';
                            },3000);
                            </script>";
                    }
                }
            }
    ?>

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
          <?php
            if ($_SESSION['logged_in_user'] == '1'){  //daca utilizatorul e autentificat
              echo "<li class='navbar__item'>
                  <a href='./dashboard.php' class='navbar__links'>Dashboard</a>
              </li>";
            }
          ?>
          <li class="navbar__item">
            <a href="./About.php" class="navbar__links">About</a>
          </li>
          <li class="navbar__item">
              <a href="./Contact.php" class="navbar__links">Contact Us</a>
          </li>
          <?php
            if ($_SESSION['logged_in_user'] == '0'){  //daca utilizatorul nu e autentificat
              echo "<li class='navbar__button'>
              <a href='#' class='buttonlog' id='Register'>Register</a>
            </li>
            <li class='navbar__button'>
              <a href='#' class='buttonlog' id='LogIn'>Log In</a>
            </li>";
            }else{  //daca utilizatorul e autentificat
              echo "<li class='navbar__button'><form action='logOut.php' method='post'>
                  <button class='buttonlog' name = 'Logout' type = 'submit'>Log Out</button></form></li>";
            }
          ?>
        </ul>
      </div>
    </nav>

  <div class="page-body">
    <div class="contact-section">
        <h2>Let's talk</h2>
        <p>
            Do you have some big ideas, a random message or any kind of feedback for our website? 
            <br>
            Then please reach out, we would love to hear more about you, your idea and how we can help!
        </p>
    </div>

    <div>
        <form class="contact-form" id="contact-form-id">      
            <input name="name" type="text" class="contact-data" placeholder="Name" />   
            <input name="email" type="text" class="contact-data" placeholder="Email" />
            <textarea name="text" class="contact-data" placeholder="Comment"></textarea>
            <br>
            <input type="submit" class="contact-data" value="SUBMIT" name="submit-feedback" id="submit-feedback-id"/>
        </form>
    </div>

    <div class="contact-data">
        <a href="https://www.facebook.com/" class="fa fa-facebook"></a>
        <a href="https://www.instagram.com/" class="fa fa-instagram"></a>
    </div>
  
  <div class="LogIn-modal">   
      <div class="modal-content">
          <div class="close" id = "close">+</div>
          <a class="navbar__logo"> <i class="fas fa-cat"></i>PSM </a>
          <form action="controller.php" method="post"> 
            <p><label for="username">Username:</label>
                  <input type="text" name="username" id="username" size="20" 
                  placeholder="Provide an username:" required/></p>
            <p><label for="password">Password:</label> 
                  <input type="password" name="password" id="password" size="20"
                  placeholder="Password" required/></p>
            <p><input type="submit" name ="submit" value="Log In"
                title="Apasati butonul pentru a expedia datele spre server" /></p>
          </form> 
      </div>
    </div>

    <div class="Register-modal">
        <div class="Remodal-content">
          <div class="close" id = "close2">+</div>
          <a class="navbar__logo"> <i class="fasolid fa-cat"></i>PSM </a>
              <form method="post" enctype='multipart/form-data'>
                <p><label for="username">Username:</label>
                      <input type="text" name="username" id="Regiusername" size="20" 
                      placeholder="Provide an username:" required/></p>

                <p><label for="password">Password:</label> 
                      <input type="password" name="password" id="Regipassword" size="20"
                      placeholder="Password" required/></p>

                <p><label for="password">Retype password:</label> 
                <input type="password" name="Repassword" id="ReRegipassword" size="20"
                placeholder="Re-type password" required/></p>
                <p><input type="submit" name ="submitRegister" value="Register"
                    title="Apasati butonul pentru a expedia datele spre server" /></p>
              </form>     
        </div>
    </div>

  <div id="footer">
    <p>PET SMART MANAGER 2022</p>
  </div>

    <script>
        var el = document.getElementById('LogIn');
        if (el){
        document.getElementById("LogIn").addEventListener("click", function () {
        document.querySelector(".LogIn-modal").style.display = "flex";
        });
        document.getElementById("close").addEventListener("click", function () {
        document.querySelector(".LogIn-modal").style.display = "none";
        });
        }
    </script>
    <script>
        var el = document.getElementById('Register');
        if (el){
        document.getElementById("Register").addEventListener("click", function () {
        document.querySelector(".Register-modal").style.display = "flex";
        });
        document.getElementById("close2").addEventListener("click", function () {
        document.querySelector(".Register-modal").style.display = "none";
        });
        }
    </script>

  <script>
    const menu = document.querySelector("#mobile-menu");
    const menuLinks = document.querySelector(".navbar__menu");

    menu.addEventListener("click", function () {
      menu.classList.toggle("is-active");
      menuLinks.classList.toggle("active");
    });
  </script>
  <script>
      document.querySelector('#contact-form-id').addEventListener('submit', e=>{
          e.preventDefault();
          let form = document.querySelector('#contact-form-id');
          const data = new URLSearchParams();
          for(const p of new FormData(form)){
              data.append(p[0], p[1]);
          }

          fetch('controller2.php', {
              method: 'POST',
              body: data
          }).then(response => response.text()).then(response =>{
              console.log(response);
              alert("Message send successfully, thank you for your feedback!");
          }).catch(error => console.log(error));
          
      });
  </script>
  <script>
  if ( window.history.replaceState ) {
      window.history.replaceState( null, null, window.location.href );
  }
  </script>
</body>
</html>