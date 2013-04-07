<?php
if(isset($_POST["ajax"])) {
  $surenchere = getSurenchere($_GET["idbien"]);
  echo json_encode(array("montantmax" => $surenchere["montantMax"], "nbcli" => $surenchere["nbCli"]));
}