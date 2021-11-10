<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AddressBookController extends Controller
{
    /**
     * @Route("/address/list", name="addresslist")
     * @Method("GET")
     */

    public function showAddressList()
    {
        $genusName = 'Dumidu Perera';
        return $this->render('address_list.html.twig', array(
            'name' => $genusName,
        ));
    }
}
