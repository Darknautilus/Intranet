<?php

if(isLogged()) {
  
  echo $twig->render("admin_home.html", array());
}
else {
  header("Location:".queries("","",array()));
}