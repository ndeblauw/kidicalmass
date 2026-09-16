<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class Quote extends BlueAdminModel
{
    public $CLASS = \App\Models\Quote::class;

    public $name_to_use = 'Citaten';

    public $title_field = 'attribution_nl';

    public $indexTableColumns = ['slot', 'attribution_nl', 'visible'];

    public $attributesToShow = ['slot', 'quote_nl', 'quote_fr', 'attribution_nl', 'attribution_fr', 'visible'];
}
