<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class Article extends BlueAdminModel
{
    public $CLASS = \App\Models\Article::class;

    public $name_to_use = 'News Articles';

    public $title_field = 'title_nl';

    public $indexTableColumns = ['title_nl', 'is_published', 'published_at'];

    public $attributesToShow = ['title_nl', 'title_fr', 'is_published', 'published_at', 'author_id', 'created_at'];

    public $filepond = ['main', 'gallery'];

    public $belongsToMany = ['groups'];

    public $index_load = ['author', 'groups', 'media'];

    public $show_load = ['author', 'groups', 'media'];
}
