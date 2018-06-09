<?php
session_start();
header("Location: " . $_SESSION['ad']);
die();