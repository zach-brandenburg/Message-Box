#!/usr/bin/php
<?php

// Feel free to add other public/private/protected members to these classes, the
// following are only the required public members/functions.

// Class defining an individual message in a mailbox
class mesg {
  // Public members, contain contents of the From:, Subject: and Date: lines:
  public $from = "";
  public $subject = "";
  public $date = "";
  // Array of lines containing the message body.
  public $body = [];

  // Constructor that creates a mesg object.  I'd recommend passing it the
  // entire array of lines from the mailbox and a referenced index to where
  // the individual message starts.
  public function __construct($email) {
    $emailLines    = explode("\n",$email);
    $this->from    = explode(' ',$emailLines[1])[1];
    $this->subject = substr($emailLines[3],9);
    $this->date    = substr($emailLines[2],6);
    for($i=5;$i<count($emailLines);$i++)
      array_push($this->body,$emailLines[$i]);
  }
}

// Loads a mailbox in mbox format from a file.
class mbox {
  // Contains a count of the number of messages in the mailbox
  public $mcount = 0;
  public $emails = [];
  // Creates a mailbox from $file
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
  
  // Returns message $num of class mesg or null if $num is out of range.
  public function message($num) {
    $m = new mesg($this->emails[$num-1]);
    return $m;
  }
}


// Example code that demonstrates how to use the above classes from the command
// line.
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
