<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable('name', 'email', 'message', 'phone', 'page_url', 'honeypot')]
class ContactForm extends Model
{
    use HasFactory;

}
