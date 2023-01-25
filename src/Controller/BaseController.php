<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * @method User getUser()
 */
class BaseController extends AbstractController
{
    public function checkTheSubmittedData($request): array{
        if($request->isMethod('GET')){
            $orderBy = $request->get('orderBy');
            $sort = $request->get('sort');
            $search = $request->get('search');
        }

        return [$orderBy,$sort, $search];
    }
}