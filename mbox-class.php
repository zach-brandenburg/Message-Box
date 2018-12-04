#!/usr/bin/php
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

$mbox = new mbox("cs-ugrads.mbox");

if ($argc < 2) {
  for($i = 1; $i <= $mbox->mcount; $i++) {
    $msg = $mbox->message($i);
    printf("%2d %-45.45s '%.70s'\n", $i, $msg->from, $msg->subject);
  }
} else {
  if ($msg = $mbox->message((int)$argv[1])) {
    printf("From: %s\n", $msg->from);
    printf("Date: %s\n", $msg->date);
    printf("Subject: %s\n\n", $msg->subject);
    foreach($msg->body as $line)
      printf("%s\n", $line);
  } else printf("No such message.\n");
}
?>
