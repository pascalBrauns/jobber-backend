<?php

namespace Jobber\Reactor\Type;

class Message {
  public string $subject;
  public array $payload;

  public function toArray() {
    return [
        'subject' => $this->subject,
        'payload' => $this->payload
    ];
  }

  public function toJSON() {
    return json_encode($this->toArray(), true);
  }

  static public function fromArray(array $data) {
    $message = new Message;
    $message->subject = $data['subject'];
    $message->payload = $data['payload'];
    return $message;
  }

  static public function fromJSON(string $json) {
    $data = json_decode($json, true);
    return Message::fromArray($data);
  }
}