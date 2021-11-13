<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use AppBundle\Form\AddressType;
use AppBundle\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class AddressBookController extends Controller
{
    public function showAddressListAction()
    {
        $addresses = $this->getDoctrine()
            ->getRepository(Address::class)
            ->findAll();
        return $this->render('address_list.html.twig', array(
            'addresses' => $addresses,
        ));
    }

    public function addAddressAction(Request $request, FileUploader $fileUploader)
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $photoFileName = $fileUploader->upload($pictureFile);
                $address->setPicture($photoFileName);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();

            $this->addFlash(
                'notice',
                'Address successfully added!'
            );

            return $this->redirectToRoute('address_list');
        }

        return $this->render('manage_address.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAddressAction(Request $request, $id, FileUploader $fileUploader)
    {
        $address = $this->getDoctrine()
            ->getRepository(Address::class)
            ->find($id);
        if (!$address) {
            throw $this->createNotFoundException('The adderss does not exist');
        }
        if (!$request->isMethod('POST') && $address->getPicture() && file_exists($this->getParameter('photos_directory') . '/' . $address->getPicture())) {
            $address->setPicture(
                new File($this->getParameter('photos_directory') . '/' . $address->getPicture())
            );
        }
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $photoFileName = $fileUploader->upload($pictureFile);
                if ($address->getPicture() && file_exists($this->getParameter('photos_directory') . '/' . $address->getPicture())) {
                    unlink($this->getParameter('photos_directory') . '/' . $address->getPicture());
                }
                $address->setPicture($photoFileName);
            } else {
                if ($request->get('deletePicture')) {
                    if ($address->getPicture() && file_exists($this->getParameter('photos_directory') . '/' . $address->getPicture())) {
                        unlink($this->getParameter('photos_directory') . '/' . $address->getPicture());
                    }
                    $address->setPicture(null);
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();

            $this->addFlash(
                'notice',
                'Address successfully updated!'
            );

            return $this->redirectToRoute('address_list');
        }

        return $this->render('manage_address.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function removeAddressAction($id)
    {
        $address = $this->getDoctrine()
            ->getRepository(Address::class)
            ->find($id);
        if (!$address) {
            throw $this->createNotFoundException('The adderss does not exist');
        }
        if ($address->getPicture()) {
            unlink($this->getParameter('photos_directory') . '/' . $address->getPicture());
        }

        
        $em = $this->getDoctrine()->getManager();
        $em->remove($address);
        //$em->flush();
        $this->addFlash(
            'notice',
            'Address successfully deleted!'
        );
            return $this->redirectToRoute('address_list');
    }
}
