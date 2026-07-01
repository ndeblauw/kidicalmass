<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class ContactForm extends BlueAdminModel
{
    public $CLASS = \App\Models\ContactForm::class;

    public $name_to_use = 'Contact Submissions';

    public $title_field = 'name';

    public $indexTableColumns = ['name', 'email', 'phone', 'message', 'page_url', 'created_at'];

    public $attributesToShow = ['name', 'email', 'phone', 'message', 'page_url', 'created_at'];
}
