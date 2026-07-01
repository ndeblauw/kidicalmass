<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class YearStat extends BlueAdminModel
{
    public $CLASS = \App\Models\YearStat::class;

    public $name_to_use = 'Jaarcijfers';

    public $title_field = 'year';

    public $indexTableColumns = ['year', 'participants'];

    public $attributesToShow = ['year', 'participants'];
}
