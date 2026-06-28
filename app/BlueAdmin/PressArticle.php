<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class PressArticle extends BlueAdminModel
{
    public $CLASS = \App\Models\PressArticle::class;

    public $name_to_use = 'Press Articles';

    public $title_field = 'title_nl';

    public $indexTableColumns = ['title_nl', 'outlet', 'published_at'];

    public $attributesToShow = ['title_nl', 'title_fr', 'outlet', 'url', 'published_at', 'author_id'];

    public $filepond = ['document'];

    public $belongsToMany = ['activities', 'articles', 'groups'];

    public $index_load = ['author', 'media'];

    public $show_load = ['author', 'media'];
}
