<?php

if(!isLogged() && $_SESSION["action"] != "connexion")
  header("Location:".queries("","",array()));