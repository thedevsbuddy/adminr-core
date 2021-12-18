<?php

namespace Devsbuddy\AdminrCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Devsbuddy\AdminrCore\Traits\CanManageFiles;
use Devsbuddy\AdminrCore\Traits\HasResponse;

class AdminrController extends Controller
{
    use CanManageFiles, HasResponse;
}
