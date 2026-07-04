<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class Quote extends BlueAdminModel
{
    public $CLASS = \App\Models\Quote::class;

    public $name_to_use = 'Citaten';

    public $title_field = 'attribution';

    public $indexTableColumns = ['slot', 'attribution', 'visible'];

    public $attributesToShow = ['slot', 'quote', 'attribution', 'visible'];
}
