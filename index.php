<!DOCTYPE html>
<html>
<head>
 <title>  </title>
 <meta charset='utf-8'>
 
 <style>
 .container {
  display: table;
  width: 100%;
}

.left-half {
  background-color: Aqua;
  position: absolute;
  left: 0px;
  width: 25%;
}

.right-half {
  background-color: Teal;
  position: absolute;
  right: 0px;
  width: 75%;
}

 </style>
 <script>
 </script>
</head>
<body>

<?php


class mesg {

  public $from = "";
  public $subject = "";
  public $date = "";

  public $body = [];

  public function __construct($email) {
    $emailLines    = explode("\n",$email);
    $this->from    = explode(' ',$emailLines[1])[1];
    $this->subject = substr($emailLines[3],9);
    $this->date    = substr($emailLines[2],6);
    for($i=5;$i<count($emailLines);$i++)
      array_push($this->body,$emailLines[$i]);
  }
}

class mbox {

  public $mcount = 0;
  public $emails = [];

  public function __construct($file) {
    $fileString = file_get_contents($file);
    $e          = explode("From ",$fileString);
    $emails     = [];
    $flag       = 0;
    for($i=1;$i<count($e);$i++){
      if($flag == 1){
        $flag = 0;
        continue;
      }
      if(substr($e[$i],-1)=='>'){
        $e[$i] = $e[$i] . $e[$i+1];
        $flag = 1;
      }
      array_push($emails,$e[$i]);
    }
    $this->emails = $emails;
    $this->mcount = count($emails);
  }
  
  public function message($num) {
    $m = new mesg($this->emails[$num-1]);
    return $m;
  }
}
?>
<section class="container">
  <div class="left-half">

<?php
$mbox = new mbox("cs-ugrads.mbox");

for($i = 1; $i <= $mbox->mcount; $i++) {
  $msg = $mbox->message($i);
  echo "<div> \n <table>\n<tbody>\n<tr>\n <th>" . $msg->from . "\n</tr>\n <tr>\n" . "<td>" . $msg->subject . "\n</tr>\n</tbody>\n</table>\n</div>";
}

?>

</div>

<div class="right-half">
<?php
   echo "From: ". $msg->from . "<br>";
   echo "Date: ", $msg->date . "<br>";
   echo "Subject: ". $msg->subject . "<br>";
    foreach($msg->body as $line)
      echo $line . "<br>";
?>

</div>
</section>
</body>
</html>
