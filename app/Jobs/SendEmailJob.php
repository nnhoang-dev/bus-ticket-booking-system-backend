<?php

namespace App\Jobs;

use App\Mail\ConfirmAccount;
use App\Mail\PasswordCustomer;
use App\Mail\PasswordEmployee;
use App\Mail\ResultTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $type;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($type, $email, $data)
    {
        $this->type = $type;
        $this->email = $email;
        $this->data = $data;
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->type) {
            case 'ConfirmAccount':
                Mail::to($this->email)->send(new ConfirmAccount($this->data, 'confirm-account'));
                break;
            case 'PasswordEmployee':
                // Mail::to($this->email)->send(new PasswordEmployee($this->data));
                Mail::to($this->email)->send(new PasswordEmployee($this->data));
                break;
            case 'PasswordCustomer':
                Mail::to($this->email)->send(new PasswordCustomer($this->data));
                break;
            case 'ResultTicket':
                Mail::to($this->email)->send(new ResultTicket($this->data));
                break;

            default:
                # code...
                break;
        }

        //
    }
}