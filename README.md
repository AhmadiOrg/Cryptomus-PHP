# Cryptomus-PHP
A class to communicate with the Cryptomus web service

# How to use 

<code>
  
<?php
require_once('./cryptomus.php');

define('MERCHANT_UUID', 'put_your_merchent_id'); # put cryptomus merchent id
define('PAYMENT_KEY', 'put_your_payment_key'); # put cryptomus payment key

$cm = new Cryptomus(MERCHANT_UUID, PAYMENT_KEY);
?>

</code>
