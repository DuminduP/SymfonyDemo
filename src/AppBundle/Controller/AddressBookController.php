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
        if ($address->getPicture() && file_exists($this->getParameter('photos_directory') . '/' . $address->getPicture())) {
            unlink($this->getParameter('photos_directory') . '/' . $address->getPicture());
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($address);
        $em->flush();
        $this->addFlash(
            'notice',
            'Address successfully deleted!'
        );
        return $this->redirectToRoute('address_list');
    }

    public function showOverviewAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository(Address::class);

        $query = $repository->createQueryBuilder("a")
            ->select("count(a.id)")
            ->getQuery();
        $data['allCount'] = $query->getSingleScalarResult();

        $query = $repository->createQueryBuilder("a")
            ->select("count(a.id)")
            ->where('a.picture is Null')
            ->getQuery();
        $data['noPictureCount'] = $query->getSingleScalarResult();

        $query = $repository->createQueryBuilder("a")
            ->select("c.name as country, count(a.id) as n")
            ->join('a.country', 'c')
            ->groupBy('a.country')
            ->getQuery();
        $data['countryCount'] = $query->getScalarResult();

        $em = $this->getDoctrine()->getManager();
        $sql = "Select
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int)  < 21 THEN 1 ELSE 0 END) AS [Under 21],
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int)  BETWEEN 21 AND 30 THEN 1 ELSE 0 END) AS [21 - 30],
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int)  BETWEEN 31 AND 40 THEN 1 ELSE 0 END) AS [31 - 40],
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int)  BETWEEN 41 AND 50 THEN 1 ELSE 0 END) AS [41 - 50],
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int) > 50 THEN 1 ELSE 0 END) AS [Over 50]
       from address a";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        $data['ageCount'] = $stmt->fetch();


        return $this->render('address_overview.html.twig', $data);
    }
}
