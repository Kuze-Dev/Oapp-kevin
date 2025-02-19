<?php

namespace App\Livewire;

use Livewire\Component;

class ToastNotification extends Component
{
public $message = '';
public $type = '' ;
public $showToast = false;

protected $listeners = ['showToast' => 'show'];


public function show($message)
{
    $this->message = $message['message'];
    $this->type = $message['type'];

    // dd($message['type'] , $message['message']);
    $this->showToast = true;

    // Hide the toast after 3 seconds
    $this->dispatch('toastHide');
}

public function render()
{
    return view('livewire.toast-notification');
}
}
