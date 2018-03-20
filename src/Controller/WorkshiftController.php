<?php

namespace App\Controller;

use App\Entity\Schedule;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Controller\CrudController;
use Symfony\Component\Form\FormError;

class WorkshiftController extends CrudController
{
    protected $entity = "Workshift";

}